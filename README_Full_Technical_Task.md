# 🧪 Technical Task: System Integration & Data Consistency

## 🎯 Objective
You are tasked with integrating two existing PHP systems (Laravel and CodeIgniter). The goal is to:
- Create a script to **map data** between both systems
- **Identify discrepancies or mismatches**
- **Correct** inconsistencies

---

## 🧱 System 1 – Laravel API

This system manages **mobile services** and their linked **products**.

### 🔗 Entities

#### `Service`
- `id` (int)
- `mobile_number` (UK format, string)
- `network` (enum: `O2`, `EE`, `Plan.com`)
- `start_date` (date)
- `end_date` (date)
- `created_at`, `updated_at`
- 🔗 **Belongs to one `ServiceProduct`**

#### `ServiceProduct`
- `id` (int)
- `type` (enum: `2GB`, `5GB`, `20GB`, `100GB`)
- `price` (decimal)
- `start_date`, `end_date`, `created_at`, `updated_at`

### 🛠 Provided:
- Fully functional **REST API**
- A **MySQL database dump** (~2000 services with linked products)
- Docker-ready service with database

### 📡 Laravel API Endpoints

> Base URL: `http://localhost:8000/api`

| Endpoint                                      | Description                                |
|-----------------------------------------------|--------------------------------------------|
| `GET /api/services`                           | List all services (paginated, 10 per page) |
| `GET /api/services?limit=25`                  | List services with custom page size        |
| `GET /api/service-products`                   | List all service products (paginated)      |
| `GET /api/service-products?limit=20`          | List service products with custom limit    |
| `GET /api/service-products/by-service/{id}`   | Get service product linked to a service    |

---

## 🧱 System 2 – CodeIgniter (MariaDB)

This system contains **customer and service records**, structured differently.

### 🔗 Entities

#### `Customer`
- `id`, `name`, `company`, `email`

#### `Service`
- `id`, `customer_id`, `network`, `mobile_number`

#### `Product`
- `id`, `label` (enum: `2GB`, `5GB`, `20GB`, `100GB`)

#### `Service_Product`
- `id`, `service_id`, `product_id`, `amount` (price)

#### `Mapping`
- `type` (1 = service, 2 = service product)
- `local_id` (ID from System 2)
- `external_id` (matching ID from System 1)

### 💡 Notes:
- `Service` is linked to **one `Service_Product`**
- `Service_Product` is linked to **one `Product`**
- **Mobile number** is the primary relational key between both systems
- Mapping table is initially **absent**
- Dataset includes **intentional inconsistencies**

### 🛠 Provided:
- MariaDB dump (no mapping, with data errors)
- Functional **entities/models** to load, search, and filter data
- Docker-ready setup

### 🧪 CodeIgniter Test Command

Use this CLI command to test service and product linkage:

```bash
php spark service:list
```
OR 
If you are not in the the docker terminal
```bash
docker exec -it codeigniter_app php spark service:list
```
---

## 🐳 Docker Setup
You will receive:
- `docker-compose.yml` that sets up:
  - Laravel + MySQL (`system1`)
  - CodeIgniter + MariaDB (`system2`)
- Preconfigured `.env` files

---

## 🛠 Setup Instructions

### 🐳 Prerequisites
- Docker & Docker Compose installed

### 🚀 Step-by-step

1. Clone/download the project
2. Place schema and sample data in:
   - `./db/system1/schema.sql` + `sample_data.sql`
   - `./db/system2/schema.sql` + `sample_data.sql`
3. Run the environment:

```bash
docker-compose up --build
```

4. Laravel API will be available at:
   - `http://localhost:8000/api/services`

5. CodeIgniter app will be running at:
   - `http://localhost:8080`

---

## 📂 Project Structure

```
.
├── docker-compose.yml
├── db/
│   ├── system1/
│   │   ├── schema.sql
│   │   └── sample_data.sql
│   └── system2/
│       ├── schema.sql
│       └── sample_data.sql
├── laravel/
│   ├── Dockerfile
│   ├── app/Models/...
│   └── ...
├── codeigniter/
│   ├── Dockerfile
│   ├── app/Models/...
│   ├── app/Commands/SyncServices.php
│   └── ...
```

---

## 🧠 Your Tasks

Create a **CodeIgniter CLI command**:

```bash
php spark sync:services
```

This command should:
1. 🔍 **Query** Laravel’s API for all services and service products
2. 🔗 **Compare** with existing records in CodeIgniter using the `mobile_number`
3. 🧾 **Display** mismatches and missing records or discorpancies
4. 🧱 **Create the `mapping` table** (if missing)
5. 🔧 **Insert missing mappings**

Bonus:

6. ✅ **Fix inconsistencies** on the sync:services script correct the inconsistencies on system2 DB (e.g., wrong product or amount)

Create a **MariaDB Query**:
1. ✅ **MariaDB Query** Maria Db query for system2 to query from mapping any service with product Id 2


---

## 📂 Deliverables

You will receive:
- 📦 `laravel/`: Fully working Laravel API project
- 📦 `codeigniter/`: CodeIgniter project with entity models (query-ready)
- 🗃️ SQL dumps for `system1` and `system2`
- 🐳 `docker-compose.yml`
- 📄 `README.md` with setup and usage instructions
