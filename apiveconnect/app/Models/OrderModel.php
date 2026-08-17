<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table      = 'orders';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'order_number',
        'exhibitor_id',
        'sub_event_id',
        'subtotal',
        'tax',
        'total',
        'currency',
        'is_international',
        'payment_method',
        'payment_status',
        'payment_reference',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'payment_currency',
        'payment_amount',
        'payment_response',
        'quotation_amount',
        'amount_transferred',
        'reason_for_difference',
        'neft_proof_image',
        'order_status',
        'notes',
        'paid_at',
        'created_at',
        'updated_at',
        'ex_created_by',
    ];

    public function createOrderFromCart(int $vendorId, int $exhbitior_contact_person, int $subEventId, array $cartItems, array $totals, bool $isInternational, array $extra = []): ?int
    {
        $this->db->transStart();
        $orderNumber = $this->generateOrderNumber();
        $orderId = $this->insert([
            'order_number'      => $orderNumber,
            'exhibitor_id'      => $vendorId,
            'sub_event_id'      => $subEventId,
            'subtotal'          => $totals['subtotal'],
            'tax'               => $totals['tax'],
            'total'             => $totals['total'],
            'currency'          => $isInternational ? 'USD' : 'INR',
            'is_international'  => $isInternational ? 1 : 0,
            'payment_method'    => $extra['payment_method']    ?? null,
            'payment_status'    => 'pending',
            'quotation_amount'  => $extra['quotation_amount']  ?? null,
            'order_status'      => 'pending',
            'created_at'        => date('Y-m-d H:i:s'),
            'ex_created_by'        => $exhbitior_contact_person,
        ]);

        if (!$orderId) {
            $this->db->transRollback();
            return null;
        }
        $orderItemsModel = new OrderItemModel();
        foreach ($cartItems as $item) {
            $orderItemsModel->insert([
                'order_id'   => $orderId,
                'order_number' => $orderNumber,
                'item_id'    => $item['item_id'],
                'item_name'  => $item['item_name'],
                'item_image' => $item['product_img'] ?? '',
                'unit_price' => $item['price'],
                'quantity'   => $item['quantity'],
                'line_total' => $item['price'] * $item['quantity'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $this->db->table('order_status_history')->insert([
            'order_id'   => $orderId,
            'status'     => 'pending',
            'remarks'    => 'Order created',
            'changed_by' => 'vendor_' . $vendorId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->transComplete();

        return $this->db->transStatus() ? $orderId : null;
    }

    public function getOrdersByVendor(int $vendorId): array
    {
        return $this->where('exhibitor_id', $vendorId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getLastOrderWithItems(int $vendorId, int $id): ?array
    {
        $order = $this->where('exhibitor_id', $vendorId)
            ->where('id', $id)
            ->orderBy('id', 'DESC')
            ->first();

        if (!$order) {
            return null;
        }

        $orderItemsModel = new OrderItemModel();
        $order['items'] = $orderItemsModel->where('order_id', $order['id'])->where('order_number', $order['order_number'])
            ->orderBy('id', 'DESC')
            ->findAll();

        return $order;
    }

    private function generateOrderNumber(): string
    {
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        return "ORD-{$date}-{$random}";
    }
}
