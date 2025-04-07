<?php
namespace App\Models;

use CodeIgniter\Model;

class ServiceProductModel extends Model
{
    protected $table = 'service_products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['service_id', 'product_id', 'amount'];

    
    public function withProduct()
    {
        return $this->select('service_products.*, products.label AS product_label')
                    ->join('products', 'products.id = service_products.product_id');
    }
}
