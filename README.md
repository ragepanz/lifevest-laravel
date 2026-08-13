# 🦺 Life Vest Tracker - GMF AeroAsia

Aplikasi pemantauan tanggal kedaluwarsa pelampung penyelamat (life vest) pesawat secara real-time untuk tim Engineering & Maintenance GMF AeroAsia.

---

## 🛠️ Tech Stack yang Digunakan

*   **PHP 8.3 & Laravel 12** - Kerangka backend utama (MVC, API, dan Database ORM).
*   **Vite 7, Tailwind CSS v4, & Blade** - Penyusun antarmuka pengguna (UI) modern dengan Dark/Light Mode.
*   **Ghostscript & Tesseract OCR / Google Cloud Vision** - Ekstraksi teks otomatis (OCR) dari dokumen PDF LOPA.
*   **MySQL / SQLite** - Penyimpan data utama (pesawat, kursi, dan log audit).
*   **Laravel Excel** - Import & export template data menggunakan Microsoft Excel (.xlsx).

---

## ✨ Fitur-Fitur Utama

1.  **Dashboard Visual Cerdas** - Grafik ringkasan status kelayakan pelampung seluruh pesawat secara real-time.
2.  **Smart PDF Scanner (OCR AI)** - Scan PDF LOPA (Inventory Check) untuk mencatat tanggal kedaluwarsa secara otomatis.
3.  **Peta Kursi Interaktif (Seat Map)** - Klik & pilih kursi kabin (tunggal, kolom, baris, atau massal) untuk mengubah tanggal garansi.
4.  **Ekspor PDF & Cetak Formulir** - Cetak visual warna peta kursi pesawat atau cetak formulir kosong untuk inspeksi manual di lapangan.
5.  **Batch Input (Salin-Tempel Massal)** - Salin data tanggal kedaluwarsa dari Excel dan tempelkan langsung ke kolom sistem.
6.  **Audit Trail & Keamanan** - Pembatasan akses dengan 3 role (Superadmin, Admin, User) serta pencatatan otomatis setiap riwayat perubahan data.

---

## ⚙️ Cara Instalasi (Local Development)

Ikuti langkah mudah berikut untuk menjalankan aplikasi di komputer Anda:

### 1. Prasyarat System
Pastikan komputer Anda sudah terinstal:
*   [PHP >= 8.2](https://www.php.net/)
*   [Composer](https://getcomposer.org/)
*   [Node.js & NPM](https://nodejs.org/)
*   [Ghostscript](https://www.ghostscript.com/) (diperlukan untuk fungsi Scanner PDF)

### 2. Kloning & Masuk ke Folder Proyek
```bash
git clone https://github.com/ragepanz/lifevest-laravel.git
cd lifevest-laravel
```

### 3. Instal Dependensi
```bash
# Instal library backend PHP
composer install

# Instal library frontend JavaScript
npm install
```

### 4. Setup Environment (.env)
```bash
# Salin konfigurasi environment default
cp .env.example .env

# Generate security key aplikasi
php artisan key:generate
```
> **Catatan:** Buka file `.env` yang baru dibuat, lalu sesuaikan koneksi database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) dan path aplikasi Ghostscript (`GHOSTSCRIPT_PATH`).

### 5. Jalankan Migrasi & Seeder Database
```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan Aplikasi
Jalankan kedua perintah ini pada dua terminal terpisah:
```bash
# Terminal 1: Menjalankan Laravel Backend
php artisan serve
```
```bash
# Terminal 2: Menjalankan Vite Frontend
npm run dev
```
Buka peramban web dan akses alamat **`http://localhost:8000`**.

---

## 🔑 Kredensial Akun Default

Gunakan salah satu akun berikut untuk masuk ke sistem:

| Peran (Role) | Email | Kata Sandi (Password) |
| :--- | :--- | :--- |
| **Superadmin** (Hak Penuh + Kelola User) | `superadmin@tnp.com` | `superadmintnp` |
| **Admin** (Kelola Kursi & Armada) | `admin@tnp.com` | `admintnp` |
| **User** (Hanya Lihat & Unduh Laporan) | `user@tnp.com` | `usertnp` |

---

## 💡 Cara Penggunaan

### A. Memperbarui Tanggal Kedaluwarsa Kursi secara Manual
1.  Masuk ke menu **Fleet**, lalu pilih pesawat yang diinginkan (contoh: PK-GIA).
2.  Di peta kursi interaktif, pilih kursi (klik, drag, atau pilih per baris/kolom).
3.  Klik tombol **Set Date** atau tekan **Enter** pada keyboard.
4.  Masukkan tanggal kedaluwarsa baru, lalu simpan.

### B. Memperbarui Menggunakan PDF Scanner (Otomatis)
1.  Klik menu **Smart Scanner** di panel navigasi.
2.  Pilih maskapai & registrasi pesawat target.
3.  Unggah dokumen PDF LOPA berisi tanggal garansi pelampung.
4.  Sistem AI akan mengekstrak data secara otomatis. Review hasil pembacaan lalu klik **Simpan ke Database**.

### C. Mencetak Formulir Inspeksi Lapangan
1.  Buka detail pesawat di menu **Fleet**.
2.  Klik tombol **Print Blank Form**.
3.  Gunakan cetakan kertas tersebut untuk mencatat nomor seri dan tanggal kedaluwarsa secara manual langsung di kabin pesawat.
