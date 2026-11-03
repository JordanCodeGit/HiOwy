# Hiowy! — Ketentuan Tampilan Mobile untuk Staf (BK, Kesiswaan, Wali Kelas)

Dokumen ini turunan dari `HIOWY_SPEC.md` (SPEC bagian 10: wajib berfungsi di layar 360 px) dan `FRONTEND.md` bagian 5 ("laptop first, usable on phone"; sidebar menjadi hamburger, tabel menjadi kartu). Bagian 2 SPEC (aturan non-negotiable) berlaku untuk semua yang ada di sini.

Keterangan penanda:
- **(saran)**: tidak tertulis di SPEC atau FRONTEND, perlu dikonfirmasi tim.
- **(belum diputuskan)**: menunggu keputusan tim atau konfirmasi Bu Isna dan Bu Ika.

Login dan 2FA tidak dibahas di sini.

---

## A. Ketentuan umum (ketiga peran)

### 1. Layar dan tata letak
- Lebar minimum **360 px**. Halaman tidak boleh bergeser ke samping. Tabel lebar diubah menjadi kartu, bukan digulir horizontal.
- Satu kolom. Panel dua kolom di laptop (misalnya detail laporan) ditumpuk dari atas ke bawah.
- Breakpoint **(saran)**: di bawah 640 px = tampilan HP, 640 sampai 1024 px = tablet (sidebar bisa diciutkan jadi ikon), di atas itu = laptop.
- Uji di 360, 390, dan 412 px, mode potret dan lanskap.

### 2. Navigasi
- Sidebar menjadi **menu hamburger** (laci dari kiri), sesuai FRONTEND. Isi menu mengikuti peran. Item aktif ditandai dengan teks tebal dan ikon, bukan hanya warna.
- Header menempel di atas: hamburger, nama sekolah, avatar inisial. Dari avatar dibuka **Profil** dan **Keluar**.
- Elemen header laptop yang tidak dipakai di HP: pil "Tahun Ajaran" dan kolom "Pencarian cepat". Pencarian kode tiket dipindah ke halaman Laporan dan Arsip.
- Tombol kembali jelas di halaman detail. Posisi scroll dan filter dipertahankan saat kembali ke daftar.
- Untuk konsistensi antar peran, semua memakai hamburger.

### 3. Sentuhan dan teks
- Target sentuh minimal **44×44 px** dengan jarak antar tombol yang cukup.
- Teks isi 16 px, teks bantu 14 px. Kolom input **16 px** supaya iOS tidak memperbesar layar sendiri.
- Kontras WCAG 2.1 AA. Badge, status, dan toggle selalu memakai **ikon atau teks**, tidak hanya warna.
- Animasi dimatikan saat `prefers-reduced-motion` aktif.

### 4. Dialog, form, dan keyboard
- Dialog konfirmasi dan filter tampil sebagai **panel dari bawah (bottom sheet)**. Form panjang tampil sebagai halaman penuh.
- Tombol aksi utama selalu terlihat tanpa harus scroll dan tidak tertutup keyboard.
- Galat ditampilkan di bawah kolom dengan teks dan `aria-live`.
- Isi form (catatan, balasan, ringkasan) **tidak disimpan di localStorage atau cookie** **(saran)**. Beri peringatan sebelum meninggalkan halaman yang masih berisi teks belum tersimpan.

### 5. Data dan privasi
- Tidak ada jam pengiriman tepat di laporan. Selalu **tanggal + bagian hari** (pagi, siang, sore, malam).
- Elemen yang tidak boleh dipakai suatu peran **tidak dirender sama sekali**, dan server tidak mengirim datanya.
- Semua teks cerita, catatan, dan pesan di-escape (`{{ }}`, tidak pernah `{!! !!}`).
- Sesi habis setelah 30 menit tidak aktif. Saat HP dikunci lalu dibuka kembali, tampilkan pesan sesi habis dan arahkan ke login.
- Opsi **sembunyikan cuplikan cerita** di daftar, untuk dipakai di tempat ramai **(saran, belum diputuskan)**.
- Tautan email darurat dibuka di HP, login, lalu kembali ke tujuan semula.
- Tidak ada IP atau data perangkat yang ditampilkan.

### 6. Performa dan keadaan khusus
- Tanpa gambar atau pustaka berat di daftar. Pakai kerangka (skeleton) saat memuat.
- Daftar panjang memakai tombol **"Muat lebih banyak"**, bukan scroll tak berujung.
- Selalu sediakan keadaan: kosong, memuat, koneksi putus (dengan tombol "Coba lagi"), dan sesi habis.

---

## B. Guru BK (mobile)

**Menu hamburger (6):** Laporan, Kelola, Halaman Pelajari, Statistik, Arsip, Log Akses. Profil dari avatar.

### 1. Laporan
- **Strip ringkasan** (belum dibaca, `danger_flag` belum dibaca, tanpa update lebih dari 3 hari) berupa kartu kecil yang bisa digeser ke samping. Kartu `danger_flag` paling mencolok dan bisa diketuk untuk memfilter.
- **Tab** (Belum dibaca / Sedang ditangani / Selesai) bisa digeser dan menempel di bawah header. Tab aktif memakai garis bawah dan teks tebal.
- **Pencarian** kode tiket satu baris, di sebelahnya tombol **Filter** dengan lencana jumlah filter aktif. Lima filter (jenis, peran, urgensi, kontak, rentang tanggal) dibuka di bottom sheet dengan tombol Terapkan dan Reset. Filter aktif tampil sebagai chip yang bisa dihapus.
- **Kartu laporan** (seluruh kartu bisa diketuk):
  1. Badge urgensi. Untuk `danger_flag` ditambah garis tebal di sisi kiri kartu.
  2. Jenis kejadian dan peran pelapor.
  3. Cuplikan maksimal 2 baris.
  4. Dikirim: `24 Okt · Pagi`.
  5. Kontak (Anonim / Minta dihubungi) dan "Ditangani oleh".
- Penanda **belum dibaca** dan **tanpa update 3+ hari** berupa ikon kecil dengan teks.
- **Aksi massal "Tandai iseng"**: tidak ada kotak centang secara bawaan. Masuk lewat tombol **Pilih** atau tekan lama, lalu muncul bar bawah ("3 dipilih · Tandai iseng · Batal") dengan dialog konfirmasi.
- Urutan sama dengan laptop: `danger_flag`, darurat, perlu perhatian, biasa, belum diklasifikasi, lalu terlama.

### 2. Detail laporan
Susunan satu kolom, dari atas:
1. Header: nomor laporan, badge urgensi, status, jenis, peran, dikirim.
2. Status ringkas: "Langkah 2 dari 4: Dibaca", bukan empat kotak berjajar.
3. Isi laporan: tanggal kejadian, lokasi, pihak terlibat, cerita.
4. Kontak pelapor, dalam bagian yang bisa dibuka-tutup. Tombol "Hubungi" membuka WhatsApp.
5. Percakapan dengan pelapor, dengan keterangan "Ini bukan chat langsung".
6. Catatan internal.
7. Ringkasan yang diteruskan ke wali kelas.
8. Riwayat aktivitas.

Pola yang disarankan:
- Bagian-bagian tersebut dijadikan tab **Isi | Percakapan | Catatan | Riwayat** supaya halaman tidak terlalu panjang.
- Tombol **Aksi** menempel di bawah dan membuka bottom sheet berisi: ubah status, tugaskan, konfirmasi label urgensi, teruskan ke wali kelas, tandai iseng, arsipkan. Iseng dan arsip memakai konfirmasi. **Tidak ada tombol hapus.**
- Kotak balasan menempel di atas keyboard saat tab Percakapan aktif.
- Form teruskan ke wali kelas berupa halaman penuh dengan peringatan "tanpa detail pengidentifikasi".
- Membuka laporan "Belum dibaca" otomatis menjadi "Dibaca" dan tercatat di log.

### 3. Kelola
- Empat tab (Jenis Kejadian, Lokasi Kejadian, QR Sekolah, Undang Staf) berupa baris yang bisa digeser.
- **Jenis dan Lokasi:** daftar kartu. Urutan diatur dengan tombol naik/turun berukuran 44 px, bukan seret. Tiap kartu menampilkan label, badge status, dan (untuk jenis) badge "Terlihat Kesiswaan" atau "Hanya BK". Tambah dan Ubah dibuka sebagai halaman penuh dengan pratinjau tampilan siswa di bagian bawah. Tidak ada tombol Hapus, hanya nonaktifkan.
- **QR Sekolah:** QR di atas, kode sekolah dengan tombol salin, lalu tombol **Unduh poster PDF** dan **Unduh PNG** selebar layar. Tidak ada daftar QR per lokasi dan tidak ada hitungan akses. Tidak ada tombol buat ulang kode (khusus operator).
- **Undang Staf:** perlu disesuaikan dengan keputusan bahwa akun staf dibuat operator.

### 4. Halaman Pelajari
- Daftar pengumuman berupa kartu dengan judul, cuplikan, tanggal, dan toggle Tampil/Sembunyi (dengan teks status).
- Tombol **Tambah pengumuman** menempel di bawah. Form (judul dan isi, penghitung karakter) dibuka sebagai halaman penuh, dengan pratinjau di bawahnya.
- Tidak ada statistik pembaca dan kolom target penerima.

### 5. Statistik
- Kartu angka tersusun 2 kolom atau bisa digeser.
- Setiap grafik satu per baris dengan lebar penuh. Donut dengan legenda di bawahnya.
- Rincian muncul saat diketuk, bukan saat diarahkan kursor.
- Sediakan ringkasan dalam bentuk tabel untuk pembaca layar **(saran)**.
- Rentang tanggal dibuka di bottom sheet.
- Angka kurang dari 3 digabung atau disembunyikan **(saran)**.

### 6. Arsip
- Kartu seperti Laporan, ditambah alasan arsip (selesai atau iseng). Tanpa kotak centang dan tanpa hapus.
- Filter (alasan, jenis, tanggal) di bottom sheet.
- Keterangan kebijakan retensi sebagai teks info, bukan tombol.

### 7. Log Akses
- Setiap entri berupa kartu: tanggal dan bagian hari (atau jam:menit **(saran)**), nama pengguna dan peran, badge aksi, nomor laporan.
- Filter peran dan rentang waktu di bottom sheet. Tidak ada kolom IP dan perangkat.
- Hanya baca. Tidak ada isi laporan.

### 8. Profil
- Satu kolom, empat bagian yang bisa dibuka-tutup: Data akun, Kata sandi, 2FA, Sesi.
- Kolom kata sandi ada tombol tampilkan/sembunyikan.
- Kode pemulihan ditampilkan sekali, dengan tombol salin.
- "Keluar dari semua perangkat" memakai konfirmasi.
- Email hanya baca (ubah lewat operator).

---

## C. Kesiswaan (mobile)

Kerangka sama dengan BK, tetapi lebih sederhana.

**Menu hamburger (4):** Laporan, Arsip, Statistik, Log Akses. Profil dari avatar. Tidak ada Kelola dan Halaman Pelajari.

| Halaman | Perbedaan di HP dibanding BK |
|---|---|
| **Laporan** | Strip ringkasan dan jumlah tab hanya menghitung laporan yang boleh ia baca. Filter jenis hanya berisi jenis yang terlihat (`visible_to_kesiswaan`). Chip kontak "Anonim / Minta dihubungi" tidak dirender **(belum diputuskan)**. "Ditangani oleh" hanya teks. Laporan yang dibagikan BK memakai penanda "Dibagikan BK" **(saran)**. Pilih massal untuk iseng tetap ada. |
| **Detail laporan** | Tab **Percakapan** tidak ada. Bagian **Kontak pelapor** tidak ada. Bottom sheet Aksi hanya berisi empat hal: ubah status, tulis catatan, tandai iseng, arsipkan. Tidak ada tugaskan, konfirmasi urgensi, teruskan ke wali kelas, dan balasan. Apakah catatan BK terbaca **(belum diputuskan)**. |
| **Arsip** | Hanya laporan yang boleh dibaca. Tidak ada aksi massal. |
| **Statistik** | Agregat saja. Cakupan jenis **(belum diputuskan)**. Tombol ekspor tidak dirender **(belum diputuskan)**. |
| **Log Akses** | Laporan yang tidak ia lihat tampil sebagai "Laporan lain" **(saran)**. |
| **Profil** | Sama dengan BK, tanpa blok notifikasi darurat. |

Laporan yang tidak boleh dibaca memberi **404**. Jika BK menarik kembali pembagian saat laporan sedang dibuka, tampilkan "Laporan ini sudah tidak tersedia untukmu" **(saran)**.

---

## D. Wali Kelas (mobile)

Peran paling sederhana dan paling mungkin dipakai di HP, jadi dirancang **mobile first** **(saran)**.

**Navigasi:** tanpa hamburger. Cukup bar atas dengan judul "Ringkasan untuk kamu" dan avatar. Dari avatar dibuka **Profil** dan **Keluar**.

### 1. Ringkasan diteruskan
- Daftar kartu: tanggal diteruskan, pengirim (Guru BK), cuplikan 2 baris.
- Penanda belum dibaca (titik dengan teks) dan tab Belum dibaca / Sudah dibaca **(saran, butuh kolom `read_at` di `forwards`)**.
- Terbaru di atas, dengan tombol "Muat lebih banyak".
- Keadaan kosong: "Belum ada ringkasan yang diteruskan kepadamu".
- Tidak ada filter, pencarian, nomor laporan, jenis kejadian, urgensi, atau data pelapor.

### 2. Baca ringkasan
- Teks ringkasan penuh dengan huruf 16 sampai 18 px, jarak baris lega, dan lebar baca nyaman.
- Kotak pengingat di atas atau bawah teks **(saran, redaksi direview Bu Ika)**: ringkasan untuk membantu pendampingan, jangan mencari tahu pelapor, jangan dibagikan.
- Tombol **Kembali** dan opsional **Tandai sudah dibaca** **(saran)**.
- Hanya baca. Tidak ada balas, ubah, teruskan, atau hapus.
- Ringkasan wali kelas lain memberi **404**.

### 3. Profil
- Sama dengan peran lain: data akun, ganti kata sandi, 2FA, sesi. Tanpa blok notifikasi darurat.

---

## E. Ringkasan elemen mobile per peran

| Elemen | BK | Kesiswaan | Wali Kelas |
|---|---|---|---|
| Navigasi | Hamburger, 6 menu | Hamburger, 4 menu | Bar atas saja |
| Strip ringkasan geser | Ada | Ada (terbatas) | Tidak ada |
| Filter di bottom sheet | 5 filter | 3 sampai 4 filter | Tidak ada |
| Kartu laporan | Ya | Ya | Kartu ringkasan |
| Pilih massal iseng | Ya | Ya | Tidak ada |
| Tab detail: Isi, Percakapan, Catatan, Riwayat | Semua | Tanpa Percakapan | Tidak ada |
| Bottom sheet Aksi | 6 aksi | 4 aksi | Tidak ada |
| Kontak pelapor | Ya (dilipat) | Tidak ada | Tidak ada |
| Kelola, Halaman Pelajari | Ya | Tidak ada | Tidak ada |

---

## F. Ketentuan selesai (mobile)

- Berfungsi di 360 px tanpa scroll horizontal.
- Semua target sentuh 44 px dan input 16 px.
- Tidak ada jam tepat, IP, atau data pelapor yang bocor ke peran yang tidak berhak.
- Elemen terlarang tidak ada di DOM.
- Dialog konfirmasi berfungsi untuk aksi berisiko.
- Keadaan kosong, memuat, galat, dan sesi habis tersedia.
- Feature test izin akses lolos, dan Vio punya skenario uji untuk tiap halaman.
