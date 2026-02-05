# 🛡️ Life Vest Tracker - GMF AeroAsia

Aplikasi pelacakan tanggal kedaluwarsa life vest untuk armada pesawat GMF AeroAsia.

---

## 📋 Daftar Isi

- [Cara Menjalankan](#-cara-menjalankan)
- [Panduan Penggunaan](#-panduan-penggunaan)
- [Keyboard Shortcuts](#-keyboard-shortcuts)
- [PDF Export & Blank Form](#-pdf-export--blank-form)
- [Fleet Manager](#️-fleet-manager)
- [Airlines Management](#-airlines-management)
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
- Menampilkan **Fleet Status** per Airline, lalu per Tipe (Garuda Indonesia → B737, A330, dll)
- **Filter**: Klik tombol "🔍 Filter" untuk menampilkan panel filter:
  - Filter berdasarkan **Airline**
  - Filter berdasarkan **Type** pesawat
  - Filter berdasarkan **Status** (Active/Prolong)
  - Filter berdasarkan **Health** (Safe/Warning/Critical)
- **Fleet Manager**: Kelola data pesawat & airline via tombol "Manage Fleet" di navbar
- **Dark/Light Mode**: Toggle tema via tombol 🌙/☀️ di navbar
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

## 📄 PDF Export & Blank Form

### Export PDF
Export seat map sebagai PDF report dengan warna status dan tanggal expiry.
- Klik tombol **"Export PDF"** di toolbar halaman seat map
- PDF akan terbuka di tab baru

### Blank Form
Export formulir kosong untuk teknisi (kotak lebih besar untuk tulisan tangan).
- Klik tombol **"Blank Form"** di toolbar halaman seat map
- Form menyediakan kolom untuk pengisian manual di lapangan

---

## ⚙️ Fleet Manager

Halaman **Fleet Manager** (`/fleet`) adalah pusat kontrol data pesawat dan airline. Tersedia dalam format **Tab**:

### Tab Aircraft
- **Monitoring Armada**: Melihat daftar seluruh pesawat
- **Filter**: Filter berdasarkan Airline, Type, Status
- **Tambah Pesawat**: Input dengan pilihan airline
- **Edit Pesawat**: Mengubah status (Active/Prolong) saja (Airline, Type & Registration terkunci)
- **Hapus Pesawat**: Hapus data permanen

### Tab Airlines
- **Daftar Airline**: Melihat semua maskapai yang terdaftar
- **Tambah Airline**: Input nama dan kode IATA
- **Edit Airline**: Mengubah nama dan kode
- **Hapus Airline**: Hapus airline (hanya jika tidak ada pesawat terkait)

---

## 🏢 Airlines Management

Sistem mendukung **multi-airline** dengan fitur:

1. **Airline Grouping**: Dashboard menampilkan pesawat dikelompokkan per maskapai
2. **Dynamic Airlines**: Tambah/hapus airline sesuai kebutuhan
3. **Default Airline**: Garuda Indonesia (kode: GA)

### Menambah Airline Baru
1. Buka **Fleet Manager** → Tab **Airlines**
2. Klik **"+ Add New Airline"**
3. Isi nama (contoh: Citilink) dan kode IATA (contoh: QG)
4. Klik **Save**

---

## ✈️ Menambahkan Pesawat Baru

### 1. Via Fleet Manager (Cara Utama)
Untuk menambahkan pesawat dengan layout yang sudah ada:

1. Buka menu **Fleet Manager** di dashboard (atau akses `/fleet`).
2. Klik tombol **"+ Add New Aircraft"**.
3. Isi form:
    - **Airline**: Pilih maskapai (Garuda Indonesia, Citilink, dll)
    - **Registration**: Nomor registrasi (misal: PK-GPC)
    - **Type**: Tipe pesawat (misal: A330-300)
    - **Layout**: Pilih layout kursi yang sesuai dari dropdown
    - **Status**: Pilih Active atau Prolong
4. Klik **Save**. Pesawat akan langsung muncul di dashboard.

---

### 2. Menambahkan Layout Baru (Advanced)
Jika Anda memiliki pesawat dengan konfigurasi kursi yang **belum pernah ada** (template kursi baru):

1. **Buat Template Blade**:
   - Copy file di `resources/views/aircraft/` (misal `a330-300c.blade.php`), rename jadi `a330-XXX.blade.php`
   - Template ini adalah wrapper yang memanggil partial
2. **Buat Partial**:
   - Copy file di `resources/views/aircraft/partials/` (misal `a330-300a.blade.php`)
   - Edit konfigurasi kursi di partial tersebut
3. **Auto-Detect**:
   Sistem akan **otomatis** mendeteksi file baru tersebut.
   Saat tambah pesawat di Fleet Manager, pilihan `a330-XXX` akan langsung muncul di dropdown layout.
4. **Konfigurasi Baris**:
   Edit `config/aircraft_class_rows.php` untuk menentukan mana baris bisnis/ekonomi.

> 💡 **Tip:** Template di `aircraft/` hanya menyediakan struktur halaman. Logika seat map ada di `aircraft/partials/`.

---

## 📁 Struktur File Penting

```
lifevest-laravel/
├── config/
│   └── aircraft_class_rows.php   # Config class type per layout
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── AircraftSeeder.php    # Initial Data Pesawat
│       └── AirlineSeeder.php     # Initial Data Airlines
├── app/
│   ├── Models/
│   │   ├── Aircraft.php          # Model pesawat (belongsTo Airline)
│   │   ├── Airline.php           # Model airline (hasMany Aircraft)
│   │   └── Seat.php              # Model seat/kursi
│   └── Http/Controllers/
│       ├── DashboardController.php   # Logic dashboard (grouped by airline)
│       ├── FleetController.php       # Logic CRUD Pesawat & Airlines
│       ├── AircraftController.php    # Logic seat map & update expiry
│       └── ReportController.php      # Logic PDF Export & Blank Form
├── resources/views/
│   ├── layouts/app.blade.php     # Master layout + Navbar + Dark Mode
│   ├── dashboard.blade.php       # Halaman dashboard (with filters)
│   ├── fleet/                    # Fleet Manager views
│   ├── aircraft/                 # Template wrapper per layout
│   │   ├── b737-e46.blade.php
│   │   ├── b777-3class.blade.php
│   │   ├── a330-300a.blade.php
│   │   └── ... (16 layouts)
│   ├── aircraft/partials/        # ⭐ Seat map layouts (reusable)
│   │   ├── b737-e46.blade.php    # B737 seat configuration
│   │   ├── b777-3class.blade.php # B777 3-class configuration
│   │   ├── a330-300a.blade.php   # A330-300 Layout A
│   │   ├── a330-300cargo.blade.php # A330-300 Cargo
│   │   └── ... (16 partials)
│   ├── reports/                  # ⭐ PDF Reports
│   │   ├── seat-map.blade.php    # PDF Export template
│   │   └── blank-form.blade.php  # Blank Form template
│   └── components/               # Reusable Blade components
│       ├── cockpit-section.blade.php
│       ├── seat-cell.blade.php
│       ├── toolbar.blade.php     # Sticky toolbar + Export buttons
│       ├── status-legend.blade.php
│       ├── aircraft-header-info.blade.php
│       └── date-modal.blade.php
└── resources/
    ├── css/
    │   ├── style.css             # CSS global + Dark/Light mode
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
| **A320-200** | 50 | a320a |
| **ATR72-600** | 3 | atr72 |

---

## 🏢 Airlines

| Airline | Kode IATA |
|---------|-----------|
| Garuda Indonesia | GA |
| Citilink | QG |

