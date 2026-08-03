<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table      = 'order_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'order_id',
        'order_number',
        'item_id',
        'item_name',
        'item_image',
        'unit_price',
        'quantity',
        'line_total',
        'created_at',
    ];
}