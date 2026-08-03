<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table      = 'cart';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'vendor_id',
        'item_id',
        'sub_event_id',
        'quantity',
        'price',
        'created_at',
    ];

    public function addToCart(int $vendorId, int $itemId, int $subEventId, int $quantity, float $price): bool
    {
        $existing = $this->db->table('cart')
            ->where('vendor_id', $vendorId)
            ->where('item_id', $itemId)
            ->where('sub_event_id', $subEventId)
            ->get()
            ->getRow();

        if ($existing) {
            return (bool) $this->db->table('cart')
                ->where('id', $existing->id)
                ->update(['quantity' => $existing->quantity + $quantity]);
        }

        return (bool) $this->db->table('cart')->insert([
            'vendor_id'    => $vendorId,
            'item_id'      => $itemId,
            'sub_event_id' => $subEventId,
            'quantity'     => $quantity,
            'price'        => number_format($price, 2, '.', ''),
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function getItems(int $vendorId, bool $isInternational = false): array
    {
        $priceField = $isInternational ? 'sale_price_usd' : 'sale_price_inr';

        $items = $this->db->table('cart c')
            ->select("
                c.id,
                c.item_id,
                c.quantity,
                c.price,
                i.item_name,
                i.item_image,
                i.description,
                i.{$priceField} as unit_price
            ")
            ->join('items i', 'i.id = c.item_id', 'left')
            ->where('c.vendor_id', $vendorId)
            ->where('i.is_deleted', 0)
            ->orderBy('c.id', 'DESC')
            ->get()
            ->getResultArray();

        return array_map(fn($item) => [
            'id'          => (int) $item['id'],
            'item_id'     => (int) $item['item_id'],
            'item_name'   => $item['item_name'],
            'quantity'    => (int) $item['quantity'],
            'price'       => (float) $item['price'],
            'unit_price'  => (float) $item['unit_price'],
            'product_img' => trim($item['item_image'] ?? ''),
            'description' => $item['description'] ?? '',
        ], $items);
    }

    public function removeItem(int $vendorId, int $cartId): bool
    {
        return (bool) $this->db->table('cart')
            ->where('id', $cartId)
            ->where('vendor_id', $vendorId)
            ->delete();
    }


    public function updateQuantity(int $vendorId, int $cartId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($vendorId, $cartId);
        }

        return (bool) $this->db->table('cart')
            ->where('id', $cartId)
            ->where('vendor_id', $vendorId)
            ->update(['quantity' => $quantity]);
    }

    public function getCartCount(int $vendorId): int
    {
        return (int) $this->db->table('cart c')
            ->join('items i', 'i.id = c.item_id', 'left')
            ->where('c.vendor_id', $vendorId)
            ->where('i.is_deleted', 0)
            ->countAllResults();
    }

    public function clearCart(int $vendorId, int $subEventId): bool
    {
        return (bool) $this->db->table('cart')
            ->where('vendor_id', $vendorId)
            ->where('sub_event_id', $subEventId)
            ->delete();
    }
}
