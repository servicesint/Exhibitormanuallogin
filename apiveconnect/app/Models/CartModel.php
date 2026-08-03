<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'event_id',
        'sub_event_id',
        'vendor_id',
        'item_name',
        'item_code',
        'dimensions',
        'vendor_rate',
        'early_bird_price_inr',
        'early_bird_price_usd',
        'sale_price_inr',
        'sale_price_usd',
        'early_bird_date',
        'item_image',
        'description',
        'is_deleted',
        'deleted_at',
        'deleted_by',
    ];

    public function addToCart(
        int $vendorId,
        int $itemId,
        int $subEventId,
        int $quantity,
        float $price,
        float $originalPrice = 0.00,
        bool $isEarlyBird = false
    ): bool {
        $existing = $this->db->table('cart')
            ->where('vendor_id', $vendorId)
            ->where('item_id', $itemId)
            ->where('sub_event_id', $subEventId)
            ->get()
            ->getRow();
        if ($existing) {
            return (bool) $this->db->table('cart')
                ->where('id', $existing->id)
                ->update([
                    'quantity'       => $existing->quantity + $quantity,
                    'price'          => number_format($price, 2, '.', ''),
                    'original_price' => number_format($originalPrice, 2, '.', ''),
                    'is_early_bird'  => $isEarlyBird ? 1 : 0,
                ]);
        }
        return (bool) $this->db->table('cart')->insert([
            'vendor_id'      => $vendorId,
            'item_id'        => $itemId,
            'sub_event_id'   => $subEventId,
            'quantity'       => $quantity,
            'price'          => number_format($price, 2, '.', ''),
            'original_price' => number_format($originalPrice, 2, '.', ''),
            'is_early_bird'  => $isEarlyBird ? 1 : 0,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    public function getItems(int $vendorId, bool $isInternational = false): array
    {
        $items = $this->db->table('cart c')
            ->select("
            c.id,
            c.item_id,
            c.quantity,
            c.price,
            c.original_price,
            c.is_early_bird,
            i.item_name,
            i.item_image,
            i.is_deleted,
            i.description
        ")
            ->join('items i', 'i.id = c.item_id', 'left')
            ->where('c.vendor_id', $vendorId)
            ->orderBy('c.id', 'DESC')
            ->get()
            ->getResultArray();
        return array_map(fn($item) => [
            'id'             => (int)   $item['id'],
            'item_id'        => (int)   $item['item_id'],
            'item_name'      =>         $item['item_name'],
            'quantity'       => (int)   $item['quantity'],
            'price'          => (float) $item['price'],
            'original_price' => (float) ($item['original_price'] ?? 0),
            'is_early_bird'  => (bool)  ($item['is_early_bird'] ?? false),
            'product_img'    =>         trim($item['item_image'] ?? ''),
            'description'    =>         $item['description'] ?? '',
            'is_deleted'     =>         (bool) $item['is_deleted'],
        ], $items);
    }

    public function additional_furniture()
    {
        $payload    = JwtPayload::get();
        $vendorId   = $payload->sub ?? null;
        $subEventId = $payload->sub_event_id ?? null;
        if (!$vendorId || !$subEventId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => false, 'code' => 401, 'message' => 'Unauthorized.', 'data' => null]);
        }
        $isInternational = $this->resolveIsInternational($vendorId);
        $currencySymbol  = $isInternational ? '$' : '₹';
        try {
            $items = $this->db->table('items')
                ->select('
                id,
                item_name,
                item_image,
                early_bird_date,
                early_bird_price_inr,
                early_bird_price_usd,
                sale_price_inr,
                sale_price_usd,
                hike_percentage_inr,
                hike_percentage_usd
            ')
                ->where('sub_event_id', $subEventId)
                ->where('is_deleted', 0)
                ->orderBy('item_name', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', '[additional_furniture] ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => false, 'code' => 500, 'message' => 'Failed to load furniture list.', 'data' => null]);
        }

        $today     = date('Y-m-d');
        $inventory = array_map(function ($item) use ($today, $isInternational) {

            $isEarlyBird = !empty($item['early_bird_date']) && ($today <= $item['early_bird_date']);
            $salePrice = $isInternational
                ? (float) $item['sale_price_usd']
                : (float) $item['sale_price_inr'];
            $price = $isEarlyBird
                ? ($isInternational ? (float) $item['early_bird_price_usd'] : (float) $item['early_bird_price_inr'])
                : $salePrice;
            $hikePercentage = $isInternational
                ? (float) $item['hike_percentage_usd']
                : (float) $item['hike_percentage_inr'];
            if ($hikePercentage > 0) {
                $price = $price + ($price * $hikePercentage / 100);
            }
            return [
                'id'             => (int) $item['id'],
                'item_name'      => $item['item_name'],
                'item_image'     => $item['item_image'],
                'price'          => round($price, 2),
                'sale_price'     => round($salePrice, 2),
                'is_early_bird'  => $isEarlyBird,
                'hike_percentage' => $hikePercentage,
            ];
        }, $items);
        return $this->response
            ->setStatusCode(200)
            ->setJSON([
                'status'  => true,
                'code'    => 200,
                'message' => 'Furniture list fetched successfully.',
                'data'    => [
                    'inventory'       => $inventory,
                    'currency_symbol' => $currencySymbol,
                ]
            ]);
    }

    public function removeItem(int $vendorId, int $itemId): bool
    {
        return (bool) $this->db->table('cart')
            ->where('id', $itemId)
            ->where('vendor_id', $vendorId)
            ->delete();
    }

    public function clearCart(int $vendorId, int $subEventId): bool
    {
        return (bool) $this->db->table('cart')
            ->where('vendor_id', $vendorId)
            ->where('sub_event_id', $subEventId)
            ->delete();
    }
}
