CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  company VARCHAR(100),
  email VARCHAR(100)
);

CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_id INT,
  network ENUM('O2', 'EE', 'Plan.com'),
  mobile_number VARCHAR(20),
  FOREIGN KEY (customer_id) REFERENCES customers(id)
);

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label ENUM('1GB', '3GB', '5GB', '25GB', '60GB', '100GB')
);

CREATE TABLE service_products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  service_id INT,
  product_id INT,
  amount DECIMAL(10, 2),
  FOREIGN KEY (service_id) REFERENCES services(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

-- This table is not pre-populated; created by sync script
CREATE TABLE mapping (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type TINYINT NOT NULL,         -- 1 = service, 2 = service_product
  local_id INT NOT NULL,
  external_id INT NOT NULL
);
