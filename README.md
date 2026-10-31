# ☕ NUSA POS - Sistem Kasir & Manajemen Cafe / Resto Modern

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js" />
  <img src="https://img.shields.io/badge/Database-SQLite%20%7C%20MySQL-003B57?style=for-the-badge&logo=sqlite&logoColor=white" alt="Database" />
  <img src="https://img.shields.io/badge/License-Commercial%20Ready-F59E0B?style=for-the-badge" alt="License" />
</p>

Aplikasi **Point of Sale (POS) & Manajemen Cafe / Resto** berbasis web yang cepat, responsif, dan kaya fitur. Dirancang untuk mempercepat transaksi di jam ramai (*rush hour*), mencegah kebocoran kas, menghemat tenaga waiter dengan pemesanan mandiri QR Meja (e-Menu), serta mendukung cetak struk kasir dan tiket dapur ke printer thermal (58mm & 80mm).

---

## ✨ Fitur Unggulan

### 1. 🖥️ Layar Kasir POS Berkecepatan Tinggi
- Antarmuka responsif ramah layar sentuh (*touchscreen*) maupun shortcut keyboard.
- Pencarian menu instan dengan filter kategori (Coffee, Non-Coffee, Food, Dessert).
- Tombol nominal uang cepat (*Pas, 10rb, 20rb, 50rb, 100rb, 200rb*) untuk mempercepat perhitungan kembalian tunai.
- Multi-metode pembayaran: **Tunai**, **QRIS Dinamis**, dan **Transfer Bank**.
- Otomatis menghitung Pajak Resto (PB1 10%) dan Service Charge opsional.

### 2. 💵 Sistem Shift Kasir & Audit Laci (Cash Drawer)
- Mengadopsi format struk ritel standar (*CODK, CODC, dan CODT*).
- **Buka Kasir (Open Shift):** Pencatatan uang modal awal kembalian di laci kasir beserta struk modal awal.
- **Closing Shift:** Rekap otomatis total omzet per kasir, target uang fisik laci, dan deteksi selisih kas (*Pas, Surplus, atau Minus*).
- Riwayat riil audit kas drawer per tanggal dan per kasir.

### 3. 🧾 Pisah Bayar (Split Bill)
- Fitur bagi tagihan 50:50 instan untuk tamu rombongan yang ingin membayar patungan.
- Menghasilkan 2 nomor invoice dan 2 struk cetak terpisah (**BILL 1** dan **BILL 2**) tanpa duplikasi pengurangan inventaris stok.

### 4. 📱 e-Menu & Pesanan Mandiri Tamu (QR Code Meja)
- Setiap meja memiliki barcode QR unik.
- Tamu cukup scan menggunakan kamera smartphone tanpa perlu unduh aplikasi.
- Tamu dapat memilih menu, menambah catatan kustom, checkout, dan langsung membayar via QRIS.
- Pesanan meja otomatis masuk ke layar kasir dan dapur secara *real-time*.

### 5. 🍳 Tiket Dapur & Bar (Kitchen Display Ticket)
- Struk pesanan khusus tim koki dapur dan barista yang terpisah dari struk tamu.
- Tanpa mencantumkan nominal harga, fokus pada nama menu, kuantitas besar, nomor meja, dan catatan kustom (*misal: "Less sugar", "Pedas level 3"*).

### 6. 🖨️ Kontrol Thermal Printer Lengkap
- Pilihan ukuran kertas thermal: **58mm** (mini portable/Bluetooth) dan **80mm** (desktop kasir USB/LAN).
- Tombol **Test Print** untuk verifikasi koneksi printer langsung dari bar kasir.
- Opsi **Auto-Print** (otomatis memicu dialog cetak sesaat setelah transaksi sukses).

### 7. 📦 Audit Stok Dapur & Bahan Baku
- Pengurangan stok otomatis saat menu terjual.
- Fitur penyesuaian stok (*Stock Opname*) dan input penerimaan barang dari supplier.
- Peringatan stok menipis (*Low-Stock Alert*).
- Tombol instan **Toggle Sold Out** bila menu habis di tengah operasional.

### 8. 🎟️ Kupon & Voucher Promo
- Pembuatan kode voucher diskon persentase (%) atau potongan nominal rupiah (Rp).
- Pengaturan minimal belanja, kuota maksimal penggunaan, dan batas periode promo.

### 9. 👥 Kelola Pengguna & Sistem Persetujuan (Approval)
- 3 Peran Tugas: **Manager Resto (Admin)**, **Kasir POS**, dan **Auditor Stok**.
- Pendaftaran akun staf baru dilengkapi alur verifikasi/persetujuan (*approval*) admin sebelum akun dapat digunakan untuk transaksi.

### 10. 📊 Laporan Finansial & Omzet
- Ringkasan omzet harian, mingguan, dan bulanan.
- Laporan rincian metode pembayaran (Tunai vs Digital QRIS).
- Rekapitulasi perolehan pajak resto PB1 dan laporan siap cetak untuk pembukuan.

---

## 🛠️ Tech Stack

- **Backend:** Laravel 11.x (PHP 8.2+)
- **Frontend:** Blade Templating, Tailwind CSS, Alpine.js
- **Database:** SQLite (default ringan tanpa setup) / MySQL & MariaDB
- **Icons:** FontAwesome 6 Pro / Free
- **Tunneling:** Cloudflare Tunnel (Opsional untuk akses online publik dengan HTTPS aman)

---

## 🚀 Panduan Instalasi Lokal

### 1. Clone Repository
```bash
git clone https://github.com/USERNAME/kasir-app.git
cd kasir-app
```

### 2. Install Dependency PHP & JavaScript
```bash
composer install
npm install && npm run build
```

### 3. Konfigurasi Environment (`.env`)
Salin file template konfigurasi:
```bash
cp .env.example .env
```
Generate kunci aplikasi enkripsi:
```bash
php artisan key:generate
```

### 4. Setup Database & Data Awal (Migration & Seeder)
```bash
# Buat database SQLite kosong (jika menggunakan SQLite)
type nul > database\database.sqlite

# Jalankan migrasi dan seeder data awal
php artisan migrate --seed
```

### 5. Jalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: **`http://localhost:8000`**

---

## 🔐 Akun Login Default (Hasil Seeder)

| Peran (Role) | Email Login | Password |
| :--- | :--- | :--- |
| **Manager Resto (Admin)** | `admin@resto.com` | `admin123` |
| **Kasir POS (Cashier)** | `kasir@resto.com` | `password` |

---

## 📄 Lisensi
Proyek ini dibuat dan dikembangkan untuk keperluan operasional retail & resto siap pakai (*commercial-ready*).
Silakan disesuaikan dengan kebutuhan bisnis Anda.
