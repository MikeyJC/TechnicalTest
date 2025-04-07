<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ServiceModel;
use App\Models\ServiceProductModel;
use App\Models\ProductModel;

class ListServices extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'service:list';
    protected $description = 'List all services with product info.';

    public function run(array $params)
    {
        $services = new ServiceModel();
        $products = new ServiceProductModel();
        $productLabels = new ProductModel();

        $all = $services->findAll();

        foreach ($all as $service) {
            $sp = $products->where('service_id', $service['id'])->first();
            $product = $sp ? $productLabels->find($sp['product_id']) : null;

            CLI::write("Service #{$service['id']} | {$service['network']} | {$service['mobile_number']}", 'yellow');
            if ($product) {
                CLI::write("  → Product: {$product['label']} ({$sp['amount']} GBP)", 'green');
            } else {
                CLI::write("  → No product info", 'red');
            }
        }

        CLI::write("\n✅ Total services listed: " . count($all), 'cyan');
    }
}