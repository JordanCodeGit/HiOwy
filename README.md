# HiOwy — frontend autentikasi, onboarding, dan portal BK/Kesiswaan

Konversi HTML dari `public/design_acuan` ke Blade, Tailwind CSS 4, dan Alpine.js melalui Vite. HTML dan aset acuan tetap tersedia. Font Montserrat, Plus Jakarta Sans, serta Material Symbols tetap dimuat dari Google Fonts seperti desain sumber.

| Halaman | Path | Named route | Maskot |
| --- | --- | --- | --- |
| Login | `/login` | `login` | `maskot_1.png` |
| Sign Up | `/signup` | `register` | `maskot_1.png` |
| Setup 2FA | `/two-factor/setup` | `two-factor.setup` | `maskot_6.png` |
| Verifikasi 2FA | `/two-factor/verify` | `two-factor.challenge` | `maskot_6.png` |
| Lupa Kata Sandi | `/forgot-password` | `password.request` | `maskot_6.png` |
| Pilih Peran | `/onboarding/role` | `onboarding.role` | `maskot_9.png` |
| Pengaturan Sekolah | `/onboarding/school` | `onboarding.school` | `maskot_7.png` |

Path `/` mengarah ke Login. Untuk pengembangan aset, jalankan `npm install` lalu `npm run dev`; untuk menghasilkan aset statis gunakan `npm run build`.

## Alur yang ditetapkan pemilik proyek

- Sign Up → Setup 2FA → Pilih Peran → Pengaturan Sekolah khusus Guru BK.
- Login → Verifikasi 2FA.
- Tim Kesiswaan menuju pratinjau portal Kesiswaan; Wali Kelas mendapat pesan bahwa halaman tujuan belum tersedia.

Alur ini menggantikan aturan provisioning akun oleh operator pada dokumen pendukung untuk lingkup tujuh halaman ini. Pemilihan peran hanya navigasi frontend; belum memberikan hak akses.

## Batas integrasi backend

Form saat ini menampilkan pesan bahwa proses belum terintegrasi. Tautan **Lanjutkan pratinjau** pada pesan membuka halaman berikutnya tanpa membuat akun, memverifikasi identitas, atau mengaktifkan 2FA. Interaksi input disimpan dalam memori halaman, tanpa penyimpanan browser. POST fallback pada `FrontendPreviewController` hanya mengarahkan kembali dengan pesan; tidak membaca, menyimpan, maupun mem-flash isian form.

Integrasi berikut masih diperlukan:

- Pendaftaran dan login sungguhan, validasi server, throttling, serta sesi staf dengan batas idle 30 menit.
- Penyedia OAuth Google; saat ini tombol Google hanya menampilkan pemberitahuan.
- Pembuatan secret dan QR TOTP per akun, konfirmasi aktivasi, challenge saat login, recovery codes, serta penanganan percobaan gagal. QR di halaman Setup adalah ilustrasi sumber dan diberi keterangan; kolom secret/kode cadangan belum berisi kode yang bisa dipakai. Tombol salin/unduh belum menyalin atau membuat kode palsu.
- Pengiriman email reset dan pengaturan masa berlaku token 15 menit sesuai copy sumber, beserta halaman penggantian sandi yang belum termasuk desain ini.
- Penyimpanan peran dan sekolah, otorisasi server untuk akses BK, serta dashboard masing-masing peran. Route tampilan saat ini terbuka untuk peninjauan frontend.
- Tujuan nyata untuk bantuan IT, ketentuan layanan, privasi, dan protokol keamanan.

## Catatan migrasi visual

Warna, ukuran, tipografi, radius, layout, dan utility bersumber dari HTML. Token dipindahkan ke `resources/css/app.css`; utility v3 yang berubah di v4 disesuaikan nilainya, termasuk shadow, outline, blur, dan sibling spacing. Komponen bersama hanya mencakup layout dokumen, maskot, dan pemberitahuan.

Penyesuaian yang disetujui: badge menjadi **Akses Khusus Staf Sekolah**, penambahan tautan **Daftar akun** pada Login, pelepasan kunci ukuran 1280×1024 pada Pilih Peran, serta penggunaan maskot lokal. Branding lama diganti menjadi HiOwy. Contoh status keamanan sukses/gagal tidak diperlakukan sebagai hasil proses nyata.

Pengujian website, pemeriksaan browser, perbandingan screenshot, dan debug diserahkan kepada pemilik proyek sesuai permintaan. Kesesuaian visual belum divalidasi; tidak ada klaim pixel-perfect.

## Tahap 2 — Portal Guru BK

| Halaman | Path | Named route |
| --- | --- | --- |
| Dashboard / Laporan | `/bk` | `bk.dashboard` |
| Kelola, termasuk Undang Staf | `/bk/manage` | `bk.manage` |
| Halaman Pelajari | `/bk/learn` | `bk.learn` |
| Statistik | `/bk/statistics` | `bk.statistics` |
| Arsip | `/bk/archive` | `bk.archive` |
| Log Akses | `/bk/access-log` | `bk.access-log` |
| Profil & Keamanan | `/bk/profile` | `bk.profile` |
| Contoh Detail Laporan | `/bk/reports/preview` | `bk.report` |

Layout `components/layouts/bk.blade.php` memakai sidebar Dashboard BK dan dropdown notifikasi/profil dari desain `notif_profile_dashboard_bk_desktop` di seluruh halaman. Wordmark HiOwy! berbagi komponen dengan tahap 1. Badge akses dan versi telah dihapus dari sidebar sesuai revisi. Foto sumber disalin tanpa perubahan ke `public/images/bk`; Google Fonts tetap membutuhkan internet.

Interaksi Alpine berada di `resources/js/bk.js`. Tersedia pencarian/filter data contoh, pilihan baris, tab, modal, pratinjau materi, tampil/sembunyi kata sandi, serta drag-and-drop dan tombol atas/bawah untuk urutan kategori/lokasi. Kategori permanen “Masalah Pribadi & Lainnya” tetap terkunci di akhir sesuai HTML. Urutan hanya tersimpan di memori halaman. Penanda notifikasi contoh yang dibaca menggunakan satu flag `sessionStorage` agar konsisten saat pindah halaman; isi laporan dan kredensial tidak disimpan di sana.

Pemilik proyek menyetujui QR per lokasi beserta jumlah akses, serta identitas/lampiran/jam tepat pada detail laporan sebagai **alur baru yang menggantikan batas dokumen lama untuk tampilan tahap 2**. Data, angka statistik, akun, dan riwayat pada halaman berasal dari contoh HTML. Jumlah baris lokasi aktual adalah tujuh; badge disesuaikan dengan baris yang tersedia. Hanya desain detail laporan #SR-2024-8042 tersedia. Tab/status, periode, dan halaman data lain membutuhkan dataset backend; tidak dibuat data kasus tambahan.

Form dan tombol simpan, undang/hapus akses staf, kirim pesan/ringkasan, arsip, unduh/regenerasi QR, ekspor, ubah sandi, cabut sesi, dan recovery 2FA memberi pemberitahuan **belum terintegrasi**. QR sekolah merupakan ilustrasi SVG sumber, bukan QR operasional. Recovery codes tidak dibuat atau ditampilkan sebagai kode aktif. Route BK tetap berisi data contoh dan belum dilindungi autentikasi; sebelum memuat data nyata diperlukan autentikasi, otorisasi per sekolah/peran, validasi server, penyimpanan, audit log, serta endpoint operasi tersebut.

Halaman verifikasi 2FA dan pengaturan sekolah menyediakan tautan **Lanjutkan pratinjau** ke Dashboard BK. Ini tidak memverifikasi login, memberi peran BK, atau menyimpan sekolah. Portal Kesiswaan tersedia pada tahap 3. Portal Wali Kelas belum tersedia.

## Tahap 3 - Portal Kesiswaan

| Halaman | Path | Named route |
| --- | --- | --- |
| Laporan | `/kesiswaan` | `kesiswaan.dashboard` |
| Detail | `/kesiswaan/reports/{id}` | `kesiswaan.report` |
| Statistik | `/kesiswaan/statistics` | `kesiswaan.statistics` |
| Arsip | `/kesiswaan/archive` | `kesiswaan.archive` |
| Log Akses | `/kesiswaan/access-log` | `kesiswaan.access-log` |
| Profil | `/kesiswaan/profile` | `kesiswaan.profile` |
| Pelajari publik | `/s/smakita/pelajari` | `public.learn` |

Sidebar, header, tipografi, efek hover, dan transisi memakai komponen BK. Tidak ada menu/rute Kelola maupun pengelolaan Pelajari untuk Kesiswaan. Pelajari publik tersedia melalui menu profil; hanya dua materi aktif sumber yang ditampilkan. Lonceng notifikasi, ekspor PDF/CSV, kontak pelapor, komunikasi, penerusan ke Wali Kelas, penugasan konselor, pengaturan urgensi, dan catatan rahasia BK tidak dirender. Kolom/filter ingin dihubungi hanya Ya/Tidak.

`KesiswaanPreview` menyaring fixture HTML berdasarkan sekolah contoh serta `visible_to_kesiswaan` atau `shared_by_bk` sebelum diteruskan ke Blade. Jenis Fisik, Verbal, Pengucilan, Cyberbullying, dan Pelecehan terlihat; Masalah Pribadi/Lainnya perlu dibagikan BK. Angka kartu, sidebar, tab, dan statistik dihitung dari cakupan tersebut. Dataset sekarang memuat lima laporan aktif yang terlihat, lima arsip, dan satu laporan Lainnya yang tidak dibagikan (ID 6, menghasilkan 404). Tidak ada fixture dibagikan BK pada sumber saat ini. Tiket di luar akses disamarkan menjadi Laporan lain dalam log. Periode contoh tetap September-Oktober 2024; acuan filter relatif adalah 24 Oktober 2024.

Detail selain ID 1 menggunakan informasi baris sumber; narasi lengkap yang tidak tersedia diberi keterangan. Catatan Kesiswaan belum memiliki data contoh. Filter, tab, pilihan baris, dan dialog konfirmasi berjalan lokal; simpan status/catatan, tandai iseng, arsip, serta keamanan akun tetap memberi pesan belum terintegrasi. Ini penyaringan pratinjau server, bukan implementasi autentikasi/otorisasi produksi; tidak ada data nyata atau perubahan basis data.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
