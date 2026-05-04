# Sistem Manajemen Kategori Buku - UTS Pemrograman Web 2

**Nama:** Ari Maulida Aprilia
**NIM:** 60324068

## Deskripsi Aplikasi
Aplikasi berbasis web sederhana ini dikembangkan untuk mengelola data Kategori Buku di perpustakaan. Aplikasi ini mendukung fitur CRUD (Create, Read, Update, Delete) secara penuh menggunakan PHP Native (Procedural/OOP), MySQLi dengan implementasi *Prepared Statements* untuk keamanan database, dan antarmuka responsif menggunakan Bootstrap 5.

## Cara Instalasi dan Menjalankan Aplikasi
1. Unduh atau *clone* repository ini.
2. Pindahkan folder aplikasi ke dalam direktori server lokal (misal: `C:\xampp\htdocs\uts-perpustakaan`).
3. Buka phpMyAdmin (http://localhost/phpmyadmin) dan buat database baru dengan nama `uts_perpustakaan_[NIM]`.
4. Import file `uts_perpustakaan_60324068.sql` yang terdapat pada repository ini ke dalam database yang baru dibuat.
5. Buka file `config/database.php` dan sesuaikan kredensial koneksi database jika diperlukan (host, username, password, dan nama database).
6. Buka browser dan jalankan aplikasi melalui URL: `http://localhost/uts_60324068` (sesuaikan dengan nama folder).

## Struktur Folder
```text
 uts_60324068/
├── config/
│   └── database.php
├── create.php
├── delete.php
├── edit.php
├── index.php
├── readme.md
└── uts_perpustakaan_60324068.sql
```

## Link Repositori github
`https://github.com/apill20/uts-web2-60324068.git`