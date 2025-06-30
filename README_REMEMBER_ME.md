# Fitur "Tetap Ingat Saya" - Ling-Ling Pet Shop

## Deskripsi

Fitur "Tetap Ingat Saya" memungkinkan pengguna untuk tetap login selama 12 jam tanpa perlu memasukkan email dan password setiap kali mengakses website.

## Cara Kerja

1. **Login dengan Remember Me**: Saat login, user dapat mencentang checkbox "Tetap ingat saya"
2. **Token Aman**: Sistem akan membuat token acak yang aman dan menyimpannya di database
3. **Cookie**: Token dan email disimpan dalam cookie yang aman (secure dan httponly)
4. **Auto-Login**: Saat user mengunjungi website lagi, sistem akan otomatis login jika cookie masih valid
5. **Logout**: Saat logout, token akan dihapus dari database dan cookie akan dihapus

## Setup Database

Jalankan query SQL berikut di database MySQL:

```sql
-- Menambahkan kolom remember_token ke tabel pelanggan
ALTER TABLE `pelanggan` ADD COLUMN `remember_token` VARCHAR(255) NULL AFTER `foto_profil`;

-- Menambahkan index untuk optimasi query
CREATE INDEX `idx_remember_token` ON `pelanggan` (`remember_token`);
```

## Fitur Keamanan

- **Token Aman**: Menggunakan `random_bytes(32)` untuk generate token yang tidak bisa ditebak
- **Cookie Secure**: Cookie diset dengan flag `secure` dan `httponly` untuk keamanan
- **Database Validation**: Token divalidasi di database, bukan hanya di cookie
- **Auto-Cleanup**: Token dihapus otomatis saat logout atau token tidak valid

## File yang Dimodifikasi

1. `auth/login.php` - Menambahkan logika remember me dan auto-login
2. `auth/logout.php` - Menghapus token dan cookie saat logout
3. `add_remember_token.sql` - Query untuk menambah kolom database

## Cara Penggunaan

1. Buka halaman login
2. Masukkan email/nomor telepon dan password
3. Centang checkbox "Tetap ingat saya"
4. Klik tombol "Masuk"
5. User akan tetap login selama 12 jam atau sampai logout

## Auto-Fill Form

- Jika user sudah pernah menggunakan "tetap ingat saya", email akan otomatis terisi saat membuka halaman login
- Checkbox "tetap ingat saya" akan otomatis tercentang

## Durasi Cookie

- Cookie berlaku selama 12 jam
- Setelah 12 jam, user harus login ulang
- User dapat logout manual kapan saja untuk menghapus cookie

## Troubleshooting

- Jika fitur tidak berfungsi, pastikan kolom `remember_token` sudah ditambahkan ke database
- Pastikan browser mendukung cookie
- Jika ada masalah keamanan, token akan otomatis dihapus dari database
