SELECT services.id, services.customer_id, services.network, services.mobile_number FROM services
LEFT JOIN mapping ON (mapping.local_id = services.id AND mapping.type = 1)
WHERE mapping.external_id IN (
    SELECT mapping.external_id FROM mapping
    LEFT JOIN service_products ON (service_products.id = mapping.local_id AND mapping.type = 2)
    WHERE service_products.product_id = 2);