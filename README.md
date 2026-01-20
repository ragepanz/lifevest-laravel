# 🛡️ Life Vest Tracker - GMF AeroAsia

Aplikasi pelacakan tanggal kedaluwarsa life vest untuk armada pesawat GMF AeroAsia.

---

## 📋 Daftar Isi

- [Cara Menjalankan](#-cara-menjalankan)
- [Panduan Penggunaan](#-panduan-penggunaan)
- [Keyboard Shortcuts](#-keyboard-shortcuts)
- [Menambahkan Pesawat Baru](#-menambahkan-pesawat-baru)

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

## � SET TANGGAL EXPIRY

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

## ✈️ Menambahkan Pesawat Baru

### Langkah 1: Tambah Config
📁 **File:** `config/aircraft_layouts.php`

```php
return [
    // ... pesawat lain ...

    'PK-XXX' => [
        'type' => 'A330-300',  // Tipe pesawat
        'icon' => '🛩️',        // Icon (emoji)
    ],
];
```

### Langkah 2: Buat Template Blade
📁 **File:** `resources/views/aircraft/a330-xxx.blade.php`

Copy dari template yang mirip (misal `a330-gpz.blade.php`) lalu edit:

1. **Ganti tipe pesawat di header:**
```blade
<span class="info-value">A330-300</span>
```

2. **Sesuaikan section class (Business/Economy):**
```blade
<!-- Business Class - Rows 6-11 -->
<section class="cabin-section">
    <h2>💼 Business Class - Rows 6-11</h2>
    ...
</section>

<!-- Economy Class - Rows 21-50 -->
<section class="cabin-section">
    <h2>🪑 Economy Class - Rows 21-50</h2>
    ...
</section>
```

3. **Sesuaikan baris dan kolom:**
```blade
@foreach([6, 7, 8, 9, 10, 11] as $row)
    <!-- render seats -->
@endforeach
```

4. **Untuk skip baris tertentu:**
```blade
@if($row == 24)
    @continue
@endif
```

5. **Untuk kolom berbeda per baris:**
```blade
@php
    if ($row == 55) {
        $rowCols = ['D', 'F', 'G'];  // Hanya 3 kursi
    } else {
        $rowCols = ['A', 'C', 'D', 'E', 'F', 'G', 'H', 'K'];
    }
@endphp
```

### Langkah 3: Update Routing Controller
📁 **File:** `app/Http/Controllers/AircraftController.php`

```php
$template = match ($registration) {
    'PK-GFD' => 'aircraft.b737',
    'PK-GIA' => 'aircraft.b777-gia',
    'PK-GIF' => 'aircraft.b777-gif',
    'PK-GHE' => 'aircraft.a330',
    'PK-GPZ' => 'aircraft.a330-gpz',
    'PK-XXX' => 'aircraft.a330-xxx',  // ← Tambahkan ini
    default => 'aircraft.show',
};
```

### Langkah 4: Pastikan Template Memiliki Script Config
Di bagian akhir template, pastikan ada:

```blade
@push('scripts')
    <script>
        window.AIRCRAFT_CONFIG = {
            registration: '{{ $registration }}',
            updateUrl: '{{ route('aircraft.updateSeats', $registration) }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
@endpush
```

### Langkah 5: Test
1. Refresh browser
2. Buka dashboard - pesawat baru muncul di fleet
3. Klik pesawat baru - pastikan seat map tampil
4. Test select kursi dan set tanggal

---

## 📁 Struktur File Penting

```
lifevest-laravel/
├── config/
│   └── aircraft_layouts.php      # Config registrasi pesawat
├── app/Http/Controllers/
│   ├── DashboardController.php   # Logic dashboard
│   └── AircraftController.php    # Logic seat map & routing
├── resources/views/
│   ├── layouts/app.blade.php     # Master layout
│   ├── dashboard.blade.php       # Halaman dashboard
│   ├── aircraft/
│   │   ├── b737.blade.php        # Template B737 (PK-GFD)
│   │   ├── b777-gia.blade.php    # Template B777 (PK-GIA)
│   │   ├── b777-gif.blade.php    # Template B777 (PK-GIF)
│   │   ├── a330.blade.php        # Template A330-900 (PK-GHE)
│   │   └── a330-gpz.blade.php    # Template A330-300 (PK-GPZ)
│   └── components/
│       ├── cockpit-section.blade.php  # Komponen cockpit (reusable)
│       ├── seat-cell.blade.php        # Komponen 1 kursi
│       ├── toolbar.blade.php          # Toolbar selection
│       ├── status-legend.blade.php    # Legend warna
│       └── date-modal.blade.php       # Modal input tanggal
└── resources/
    ├── css/
    │   ├── style.css             # CSS global
    │   └── dashboard.css         # CSS dashboard
    └── js/
        └── app.js                # JavaScript interaksi
```

---

## 🛠️ Teknologi

- **Backend:** Laravel 11
- **Frontend:** Vanilla CSS & JavaScript
- **Database:** MySQL
- **Build Tool:** Vite

---

## 📞 Kontak

Untuk pertanyaan atau bantuan, hubungi tim IT GMF AeroAsia.
