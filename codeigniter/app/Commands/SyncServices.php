<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ServiceModel;
use App\Models\ServiceProductModel;
use App\Models\ProductModel;

class SyncServices extends BaseCommand
{

    protected $group       = 'Custom';
    protected $name        = 'sync:services';
    protected $description = 'Syncs all services and service products between Laravel and CodeIgniter databases.';

    private int $totalServices      = 0;
    private int $totalDiscrepancies = 0;

    public function run(array $params): void
    {
        // Query Laravel
        $laravelServices = $this->getLaravelServices();
        if (empty($laravelServices)) {
            CLI::error("No services found");
            exit();
        } else {
            CLI::write("Services Found", "green");
        }

        // Create Mapping table
        $db = db_connect();
        $sql = "CREATE TABLE IF NOT EXISTS mapping (
                  id INT AUTO_INCREMENT PRIMARY KEY,
                  type TINYINT NOT NULL,
                  local_id INT NOT NULL,
                  external_id INT NOT NULL);";
        if ($db->query($sql)) {
            CLI::write("Mapping table created.");
        } else {
            CLI::error("Failed to create Mapping table.");
        }

        // Query CI
        $ciServices = new ServiceModel();
        $ciServiceProducts = new ServiceProductModel();
        $ciProductLabels = new ProductModel();

        $all = $ciServices->findAll();

        // Compare and Display results
        foreach ($laravelServices as $service) {
            $this->totalServices++;
            $ciService = null;
            foreach ($all as $obj) {
                if ($obj['mobile_number'] === $service['mobile_number']) {
                    $ciService = $obj;
                    break;
                }
            }
            unset($obj);
            $sp = $ciServiceProducts->where('service_id', $ciService['id'])->first();
            $ciProduct = $sp ? $ciProductLabels->find($sp['product_id']) : null;
            $discrepancies = $this->compareRecords($service, $ciService, $sp, $ciProduct);

            // Resolve Discrepancies
            if (in_array('resolve', $params) && !empty($discrepancies)) {
                CLI::write("Resolving discrepancy...");
                $sql = "UPDATE `services` 
                        INNER JOIN `service_products` ON `service_products`.`service_id` = `services`.`id` 
                        INNER JOIN `products` ON `products`.`label` = :label:
                        SET `services`.`network` = :network:,
                            `service_products`.`product_id` = `products`.`id`,
                            `service_products`.`amount` = :price:
                        WHERE `services`.`mobile_number` = :mobile_number:;
                        ";
                $result = $db->query($sql, [
                    'label' => $service['service_product']['type'],
                    'network' => $service['network'],
                    'mobile_number' => $service['mobile_number'],
                    'price' => $service['service_product']['price']
                ]);
                if ($result) {
                    CLI::write("Discrepancy resolved.", 'green' );
                } else {
                    CLI::error("Failed to resolve discrepancy.");
                }
            }

            // Insert into Mapping Table
            $sql = "INSERT INTO mapping (`type`, `local_id`, `external_id`) 
                    SELECT * FROM (SELECT :type: AS `type`, :local_id: AS `local_id`, :external_id: AS `external_id`) AS tmp
                    WHERE NOT EXISTS (SELECT `id` FROM mapping WHERE `type` = :type: AND `local_id` = :local_id: AND `external_id` = :external_id:);";
            $db->query($sql, [
                'type' => '1',
                'local_id' => $ciService['id'],
                'external_id' => $service['id']
            ]);
            $db->query($sql, [
                'type' => '2',
                'local_id' => $sp['id'],
                'external_id' => $service['service_product']['id']
            ]);


        }
        CLI::write("\n Total discrepancies/missing records found: {$this->totalDiscrepancies}", "red");
        CLI::write(" Total services mapped: {$this->totalServices}", "green");
    }

    /**
     * @return array
     */
    private function getLaravelServices(): array
    {
        $services = [];
        $limit = "200";
        $url = "http://laravel:8000/api/services?limit=$limit";
        CLI::write("Fetching services...");
        while (!is_null($url)) {
            CLI::write("Service URL: ".$url);
            $response = json_decode($this->callLaravel($url), true);
            if (is_null($response['next_page_url'])) {
                $url = null;
            } else {
                $url = $response['next_page_url']."&limit=$limit";
            }
            if (is_array($response['data'])) {
                foreach ($response['data'] as $data) {
                    $services[] = $data;
                }
            }
        }
        return $services;
    }

    /**
     * @param string $url
     * @return bool|string
     */
    private function callLaravel(string $url = ''): bool|string
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_TIMEOUT => 0,
        ));
        $response = curl_exec($curl);
        if (curl_error($curl)) {
            CLI::error("ERROR: ".curl_error($curl));
            CLI::error("ERRNO: ".curl_errno($curl));
        }
        curl_close($curl);
        return $response;
    }

    /**
     * @param $service
     * @param $ciService
     * @param $ciServiceProduct
     * @param $ciProduct
     * @return array
     */
    private function compareRecords($service, $ciService, $ciServiceProduct, $ciProduct): array
    {
        $discrepancies = [];
        if (!$ciService) {
            CLI::write("Service #{$service['id']} | {$service['network']} | {$service['mobile_number']} | {$service['start_date']} | {$service['end_date']} | ", 'yellow');
            CLI::write("  → Missing Data", 'red');
        } else {
            $discrepancies = $this->fieldComparison($discrepancies, $service['network'], $ciService['network'], 'Network');
            $discrepancies = $this->fieldComparison($discrepancies, $service['service_product']['type'], $ciProduct['label'], 'Product Type');
            $discrepancies = $this->fieldComparison($discrepancies, $service['service_product']['price'], $ciServiceProduct['amount'], 'Product Price');
            if(!empty($discrepancies)) {
                $this->totalDiscrepancies++;
                CLI::write("Service #{$service['id']} | {$service['network']} | {$service['mobile_number']} | {$service['start_date']} | {$service['end_date']} | ", 'yellow');
                foreach ($discrepancies as $disc) {
                    CLI::write("  → Discrepancy with {$disc['label']}: {$disc['sideA']} | {$disc['sideB']}", 'red');
                }
            }
        }
        return $discrepancies;
    }

    /**
     * @param $discrepancies
     * @param $sideA
     * @param $sideB
     * @param $label
     * @return mixed
     */
    private function fieldComparison($discrepancies, $sideA, $sideB, $label): mixed
    {
        if($sideA != $sideB) {
            $discrepancies[] = ['sideA' => $sideA, 'sideB' => $sideB, 'label' => $label];
        }
        return $discrepancies;
    }
}