# Life Vest Tracker - GMF AeroAsia

Aplikasi untuk memantau, mencatat, dan mengelola data life vest pesawat secara lebih mudah. Sistem ini membantu tim Engineering dan Maintenance melihat status kursi, tanggal kedaluwarsa, hasil scan PDF, laporan, dan data armada dalam satu tempat.

---

## Tech Stack

### Backend
- PHP 8.3
- Laravel 12
- MySQL / SQLite
- Laravel Excel
- DomPDF
- Tesseract OCR
- Google Cloud Vision

### Frontend
- Blade
- Vite
- Tailwind CSS v4
- Axios

### Tools Pendukung
- Composer
- Node.js dan NPM
- Ghostscript
- PHPUnit

---

## Fitur

### 1. Dashboard
- Ringkasan status life vest per pesawat
- Filter data berdasarkan registrasi, airline, tipe pesawat, status, dan kesehatan
- Ringkasan replacement plan per part number
- Statistik cepat untuk memantau kondisi armada

### 2. Peta Kursi Pesawat
- Tampilan seat map yang interaktif
- Pilih satu kursi, banyak kursi, satu baris, satu kolom, atau semua kursi sekaligus
- Ubah tanggal kedaluwarsa secara massal
- Simpan perubahan langsung ke database
- Tersedia shortcut keyboard untuk mempercepat kerja

### 3. Smart PDF Scanner
- Upload PDF atau gambar hasil scan
- Sistem membaca data secara otomatis dengan OCR
- Hasil scan bisa direview sebelum disimpan
- Data bisa diekspor ke Excel
- Hasil scan bisa langsung disimpan ke database
- Menampilkan gambar asli scan untuk perbandingan manual

### 4. Bulk Import
- Import data aircraft, seat, dan user dari spreadsheet
- Mendukung file XLSX dan CSV
- Tersedia template import resmi
- Membantu input data dalam jumlah besar dengan cepat

### 5. Export dan Report
- Export replacement plan ke Excel
- Export summary dashboard
- Export activity log
- Export data per pesawat ke PDF
- Cetak blank form untuk inspeksi lapangan

### 6. Fleet Manager
- Kelola data aircraft
- Kelola data airline
- Tambah, edit, dan hapus data armada
- Lihat daftar armada dengan filter yang mudah dipakai

### 7. User Management
- Kelola akun pengguna
- Atur role user
- Suspend dan unsuspend akun
- Hapus akun jika diperlukan

### 8. Profile Settings
- Lihat informasi akun
- Ubah password
- Pengaturan dasar akun

### 9. Audit Trail
- Setiap perubahan data dicatat otomatis
- Riwayat aktivitas bisa dilihat dan diekspor
- Membantu kebutuhan pelacakan dan audit

---

## Cara Instalasi

### 1. Persyaratan
Pastikan komputer sudah terpasang:
- PHP 8.2 atau lebih baru
- Composer
- Node.js dan NPM
- Database MySQL atau SQLite
- Ghostscript

### 2. Clone repository
```bash
git clone https://github.com/ragepanz/lifevest-laravel.git
cd lifevest-laravel
```

### 3. Install dependency
```bash
composer install
npm install
```

### 4. Siapkan file environment
```bash
copy .env.example .env
php artisan key:generate
```

Lalu sesuaikan isi `.env` untuk:
- Database
- `GHOSTSCRIPT_PATH`
- `TESSERACT_PATH`
- API key OCR atau AI jika dipakai

### 5. Jalankan migrasi dan seeder
```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan aplikasi
Buka dua terminal:

```bash
php artisan serve
```

```bash
npm run dev
```

Akses aplikasi di:

```bash
http://localhost:8000
```

---

## Cara Penggunaan

### Login
Gunakan akun default dari seeder untuk masuk ke sistem.

### Melihat dashboard
Setelah login, halaman utama akan menampilkan ringkasan kondisi armada dan status life vest.

### Mengelola pesawat
Masuk ke menu Fleet untuk melihat daftar aircraft dan airline. Admin tertentu bisa menambah, mengubah, atau menghapus data sesuai hak akses.

### Mengubah data kursi
Buka detail pesawat, pilih kursi pada seat map, lalu ubah tanggal kedaluwarsa yang diperlukan.

### Menggunakan batch input
Buka menu batch input, lalu tempel data dari Excel untuk mengisi tanggal secara massal.

### Menggunakan PDF scanner
Buka menu Smart PDF Scanner, unggah file PDF atau gambar, review hasil ekstraksi, lalu simpan ke database atau export ke Excel.

### Mengelola user
Superadmin dapat membuka menu User Management untuk membuat akun baru, mengubah role, atau suspend akun.

### Mengunduh laporan
Semua user yang punya akses bisa mengunduh laporan PDF dan Excel dari menu report yang tersedia.

---

## Akun Default

- Superadmin: `superadmin@tnp.com` / `superadmintnp`
- Admin: `admin@tnp.com` / `admintnp`
- User: `user@tnp.com` / `usertnp`

---

## Role Akses

- Superadmin: akses penuh
- Admin: kelola data operasional sesuai izin
- User: hanya lihat data dan unduh laporan

---

## Catatan Penting

- Pastikan path Ghostscript dan Tesseract sesuai dengan komputer yang dipakai
- Jika OCR tidak jalan, cek kembali konfigurasi `.env`
- Untuk development, jalankan backend dan frontend secara bersamaan
