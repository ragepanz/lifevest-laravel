# 🛡️ Life Vest Tracker - GMF AeroAsia

Aplikasi pelacakan tanggal kedaluwarsa life vest untuk armada pesawat GMF AeroAsia.

---

## 📋 Daftar Isi

- [Cara Menjalankan](#-cara-menjalankan)
- [Panduan Penggunaan](#-panduan-penggunaan)
- [Keyboard Shortcuts](#-keyboard-shortcuts)
- [Menambahkan Pesawat Baru](#-menambahkan-pesawat-baru)
- [Struktur File](#-struktur-file-penting)

---

## 🚀 Cara Menjalankan

### Prasyarat
- PHP 8.1+
- Composer
- Node.js & npm
- MySQL/MariaDB (via Laragon/XAMPP)

### Langkah-langkah

```bash
# 1. Install dependencies
composer install
npm install

# 2. Setup database
cp .env.example .env
php artisan key:generate

# 3. Edit .env - sesuaikan database
DB_DATABASE=lifevest_tracker
DB_USERNAME=root
DB_PASSWORD=

# 4. Jalankan migration
php artisan migrate

# 5. Jalankan server (buka 2 terminal)
# Terminal 1:
php artisan serve

# Terminal 2:
npm run dev
```

Buka http://localhost:8000

---

## 📖 Panduan Penggunaan

### Dashboard
- Menampilkan **Fleet Overview** (ringkasan status semua pesawat)
- Menampilkan **Fleet Status** per tipe (B737, B777, A330)
- **Search**: Cari pesawat berdasarkan registrasi
- **Filter**: Filter berdasarkan tipe pesawat
- **Fleet Manager**: Kelola data pesawat (Tambah/Edit/Hapus) via tombol di kanan atas
- Klik kartu pesawat untuk masuk ke halaman seat map

---

## 🖱️ SELECT KURSI

| Aksi | Fungsi |
|------|--------|
| **Klik biasa** | Pilih 1 kursi (hapus selection sebelumnya) |
| **Ctrl + Klik** | Tambah kursi ke selection (multi-select) |
| **Shift + Klik** | Pilih range dari kursi terakhir ke kursi ini |
| **Klik nomor BARIS** | Pilih semua kursi di baris tersebut |
| **Klik huruf KOLOM** | Pilih semua kursi di kolom tersebut |

---

## ❌ UNSELECT / HAPUS SELECTION

| Aksi | Fungsi |
|------|--------|
| **Klik area kosong** | Hapus semua selection |
| **Tekan ESC** | Hapus semua selection |
| **Ctrl + Klik kursi** | Hapus kursi dari selection (toggle) |
| **Klik "Clear Selection"** | Hapus semua selection |

---

## 📅 SET TANGGAL EXPIRY

1. Pilih kursi yang ingin di-update (bisa multi-select)
2. Klik tombol **"Set Date"** di toolbar
3. Pilih tanggal expiry life vest dari calendar
4. Klik **"Apply"** untuk menyimpan

> 💡 **Catatan:** Bisa update banyak kursi sekaligus!

---

## ⌨️ KEYBOARD SHORTCUTS

| Shortcut | Fungsi |
|----------|--------|
| **Ctrl + A** | Pilih SEMUA kursi |
| **Enter** | Buka dialog Set Date (jika ada kursi terpilih) |
| **Escape (ESC)** | Tutup dialog / Hapus selection |

---

## 🎨 ARTI WARNA STATUS

| Warna | Status | Keterangan |
|-------|--------|------------|
| 🟢 **HIJAU** | Safe | Expiry > 6 bulan lagi |
| 🟡 **KUNING** | Warning | Expiry 3-6 bulan lagi |
| 🔴 **MERAH** | Critical | Expiry < 3 bulan lagi |
| 🟣 **UNGU** | Expired | Sudah melewati tanggal expiry |
| ⚪ **ABU-ABU** | No Data | Belum ada tanggal expiry |

---

## ⚙️ Fleet Manager

Halaman **Fleet Manager** (`/fleet`) adalah pusat kontrol data pesawat. Di sini Anda bisa:

1.  **Monitoring Armada**: Melihat daftar seluruh pesawat, cari (Search), dan urutkan (Sort).
2.  **Tambah Pesawat**: Input otomatis kapital, validasi unik, dan auto-detect layout.
3.  **Edit Pesawat**: Mengubah status (Active/Prolong).
4.  **Hapus Pesawat**: Hapus data permanen.

---

## ✈️ Menambahkan Pesawat Baru
### 1. Via Fleet Manager (Cara Utama)
Untuk menambahkan pesawat dengan layout yang sudah ada:

1.  Buka menu **Fleet Manager** di dashboard (atau akses `/fleet`).
2.  Klik tombol **"+ Add New Aircraft"**.
3.  Isi form:
    -   **Registration**: Nomor registrasi (misal: PK-GPC)
    -   **Type**: Tipe pesawat (misal: A330-300)
    -   **Layout**: Pilih layout kursi yang sesuai dari dropdown.
    -   **Status**: Pilih Active atau Prolong.
4.  Klik **Save**. Pesawat akan langsung muncul di dashboard.

---

### 2. Menambahkan Layout Baru (Advanced)
Jika Anda memiliki pesawat dengan konfigurasi kursi yang **belum pernah ada** (template kursi baru):

1. **Buat Template Blade**:
   Copy file di `resources/views/aircraft/` (misal `a330-300c.blade.php`), rename jadi `a330-XXX.blade.php`, lalu edit kursinya.
2. **Auto-Detect**:
   Sistem akan **otomatis** mendeteksi file baru tersebut.
   Saat tambah pesawat di Fleet Manager, pilihan `a330-XXX` akan langsung muncul di dropdown layout.
3. **Konfigurasi Baris**:
   Edit `config/aircraft_class_rows.php` untuk menentukan mana baris bisnis/ekonomi.

> 💡 **Tip:** Nama file template akan otomatis dikonversi jadi nama layout (contoh: `a330-XXX.blade.php` -> `A330 XXX`).

---

## 📁 Struktur File Penting

```
lifevest-laravel/
├── config/
│   └── aircraft_class_rows.php   # Config class type per layout
├── database/seeders/
│   └── AircraftSeeder.php        # Initial Data Pesawat & Layouts
├── app/Http/Controllers/
│   ├── DashboardController.php   # Logic dashboard
│   ├── FleetController.php       # Logic CRUD Pesawat (Fleet Manager)
│   └── AircraftController.php    # Logic seat map & update expiry
├── resources/views/
│   ├── layouts/app.blade.php     # Master layout + Navbar
│   ├── dashboard.blade.php       # Halaman dashboard
│   ├── aircraft/
│   │   ├── b737-e46.blade.php    # B737 Layout (46 rows)
│   │   ├── b737-e47.blade.php    # B737 Layout (47 rows)
│   │   ├── b737-e48.blade.php    # B737 Layout (48 rows)
│   │   ├── b777-2class.blade.php # B777 2-Class
│   │   ├── b777-3class.blade.php # B777 3-Class (First)
│   │   ├── a330-900a.blade.php   # A330-900 Layout A
│   │   ├── a330-900b.blade.php   # A330-900 Layout B
│   │   ├── a330-300a.blade.php   # A330-300 Layout A
│   │   ├── a330-300b.blade.php   # A330-300 Layout B
│   │   ├── a330-300c.blade.php   # A330-300 Layout C (All Economy)
│   │   ├── a330-200a.blade.php   # A330-200 Layout A
│   │   └── a330-200b.blade.php   # A330-200 Layout B
│   └── components/
│       ├── cockpit-section.blade.php
│       ├── seat-cell.blade.php
│       ├── toolbar.blade.php
│       ├── status-legend.blade.php
│       └── date-modal.blade.php
└── resources/
    ├── css/
    │   ├── style.css             # CSS global + Navbar
    │   └── dashboard.css         # CSS dashboard
    └── js/
        └── app.js                # JavaScript interaksi
```

---

## 🛠️ Teknologi

- **Backend:** Laravel 12
- **Frontend:** Vanilla CSS & JavaScript (Glassmorphism UI)
- **Database:** MySQL
- **Build Tool:** Vite
- **Timezone:** Asia/Jakarta (GMT+7)

---

## 📊 Fleet Overview

| Tipe | Jumlah Registrasi | Layout |
|------|-------------------|--------|
| **B737-800** | 40+ | e46, e47, e48 |
| **B737 MAX 8** | 1 | e46 |
| **B777-300** | 8 | 2-Class, 3-Class |
| **A330-900** | 5 | 900a, 900b |
| **A330-300** | 14 | 300a, 300b, 300c, Cargo |
| **A330-341** | 2 | 300c |
| **A330-200** | 5 | 200a, 200b |
| **ATR72-600** | 3 | atr72 |

