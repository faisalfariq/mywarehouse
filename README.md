# MyWarehouse - Inventory Management System

Sistem manajemen inventaris lengkap berbasis Laravel **monolith**: menyediakan antarmuka web modern (Blade + Bootstrap) **dan** REST API profesional (standar JSON, Bearer Token, Swagger).

## Mode Aplikasi

- **Web Monolith**: Semua fitur manajemen (user, produk, lokasi, mutasi, log) tersedia melalui antarmuka web yang user-friendly, login session-based, notifikasi swal, AJAX, dan validasi modern.
- **REST API**: Semua fitur utama juga tersedia sebagai REST API (endpoint `/api/v1/...`) dengan autentikasi Bearer Token (Sanctum), response JSON terstandar, dan dokumentasi Swagger/OpenAPI siap diimport ke Postman.

## Fitur

- **Manajemen User** - CRUD lengkap untuk data user (web & API)
- **Manajemen Produk** - Katalog produk dengan kategori dan satuan yang terstruktur (web & API)
- **Manajemen Lokasi** - Pelacakan lokasi gudang dan penyimpanan (web & API)
- **Mutasi Stok** - Pencatatan keluar/masuk stok dengan validasi (web & API)
- **Log Aktivitas** - Audit trail yang lengkap (web & API read-only)
- **REST API** - API lengkap dengan autentikasi Bearer Token
- **Swagger Documentation** - Dokumentasi API otomatis, siap diimport ke Postman

## Teknologi yang Digunakan

- **Backend**: Laravel 11 (PHP 8.2)
- **Database**: MySQL 8.0 (struktur normalized)
- **Authentication**: Laravel Sanctum (Bearer Tokens) & session-based (web)
- **API Documentation**: OpenAPI/Swagger (L5-Swagger)
- **Frontend**: Blade Templates + Bootstrap + jQuery
- **Containerization**: Docker & Docker Compose

## Struktur Database

### Tabel Master & Relasi
- **product_categories** - Master kategori produk
- **product_units** - Master satuan produk
- **products** - Data produk (relasi ke kategori & satuan)
- **locations** - Data lokasi/gudang
- **mutations** - Transaksi keluar/masuk stok
- **app_logs** - Log aktivitas
- **users** - Data user

### Relasi Utama
- Produk memiliki relasi ke kategori dan satuan (foreign key)
- Mutasi menghubungkan produk, lokasi, dan user
- Relasi produk-lokasi untuk pelacakan stok
- Audit trail untuk semua operasi

## Panduan Cepat

### Menggunakan Docker (Direkomendasikan)

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd mywarehouse
   ```

2. **Build & jalankan container**
   ```bash
   docker-compose up -d --build
   ```

3. **Jalankan migrasi & seeder**
   ```bash
   docker-compose exec app php artisan migrate:fresh --seed
   ```

4. **Akses aplikasi**
   - Web: http://localhost:8000
   - Dokumentasi API: http://localhost:8000/api/documentation

### Setup Manual

1. **Install dependency**
   ```bash
   composer install
   npm install
   ```

2. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Setup database**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Jalankan server development**
   ```bash
   php artisan serve
   ```

## Dokumentasi API

### Autentikasi

Semua endpoint API (kecuali login/register) membutuhkan Bearer Token.

#### Login

```
 