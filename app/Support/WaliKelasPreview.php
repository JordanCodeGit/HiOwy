<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WaliKelasPreview
{
    private const DEFAULT_SUMMARIES = [
        [
            'id' => 1,
            'school' => 'smakita',
            'class' => '7B',
            'counselor' => 'Dra. Nurul Hidayah',
            'role' => 'Guru BK',
            'time' => 'Hari ini, 09:30 WIB',
            'status' => 'unread',
            'content' => 'Mohon observasi interaksi sosial siswa selama kerja kelompok mata pelajaran IPA. Berikan ruang aman dan dukungan afektif jika tampak menarik diri saat dinamika presentasi kelas berlangsung. Seluruh pemantauan dilakukan secara natural tanpa memberikan sorotan atau beban pertanyaan tambahan kepada siswa.',
            'excerpt' => 'Mohon observasi interaksi sosial siswa selama kerja kelompok mata pelajaran IPA. Berikan ruang aman dan dukungan afektif jika tampak menarik diri saat dinamika presentasi kelas berlangsung.',
            'read_at' => null,
            'detail' => 'Mohon observasi interaksi sosial siswa selama kerja kelompok mata pelajaran IPA. Berikan ruang aman dan dukungan afektif jika tampak menarik diri saat dinamika presentasi kelas berlangsung. Seluruh pemantauan dilakukan secara natural tanpa memberikan sorotan atau beban pertanyaan tambahan kepada siswa.',
        ],
        [
            'id' => 2,
            'school' => 'smakita',
            'class' => '7B',
            'counselor' => 'Sarah Jenkins, M.Pd.',
            'role' => 'Konselor BK Inti',
            'time' => 'Kemarin, 14:15 WIB',
            'status' => 'unread',
            'content' => 'Rekomendasi pendampingan belajar personal pasca evaluasi tengah semester. Perlu koordinasi non-formal dengan guru pengampu matematika terkait penyesuaian beban tugas harian selama masa pemulihan konsentrasi. Hindari konfirmasi terbuka di depan forum siswa.',
            'excerpt' => 'Rekomendasi pendampingan belajar personal pasca evaluasi tengah semester. Perlu koordinasi non-formal dengan guru pengampu matematika terkait penyesuaian beban tugas harian selama masa pemulihan konsentrasi.',
            'read_at' => null,
            'detail' => 'Rekomendasi pendampingan belajar personal pasca evaluasi tengah semester. Perlu koordinasi non-formal dengan guru pengampu matematika terkait penyesuaian beban tugas harian selama masa pemulihan konsentrasi. Hindari konfirmasi terbuka di depan forum siswa.',
        ],
        [
            'id' => 3,
            'school' => 'smakita',
            'class' => '7B',
            'counselor' => 'Dra. Nurul Hidayah',
            'role' => 'Guru BK',
            'time' => '22 Okt 2024, 11:05 WIB',
            'status' => 'unread',
            'content' => 'Catatan observasi jam istirahat kedua: Disarankan penguatan peran teman sebaya positif di meja baris ketiga guna meminimalisir rasa keterasingan tanpa memicu atensi berlebih dari siswa lainnya. Wali kelas diharapkan menyapa hangat saat memasuki ruang.',
            'excerpt' => 'Catatan observasi jam istirahat kedua: Disarankan penguatan peran teman sebaya positif di meja baris ketiga guna meminimalisir rasa keterasingan tanpa memicu atensi berlebih dari siswa lainnya.',
            'read_at' => null,
            'detail' => 'Catatan observasi jam istirahat kedua: Disarankan penguatan peran teman sebaya positif di meja baris ketiga guna meminimalisir rasa keterasingan tanpa memicu atensi berlebih dari siswa lainnya. Wali kelas diharapkan menyapa hangat saat memasuki ruang.',
        ],
        [
            'id' => 4,
            'school' => 'smakita',
            'class' => '7B',
            'counselor' => 'Sarah Jenkins, M.Pd.',
            'role' => 'Konselor BK Inti',
            'time' => '19 Okt 2024, 08:40 WIB',
            'status' => 'read',
            'content' => 'Tindak lanjut kehadiran harian kelas: Siswa telah menunjukkan keteraturan presensi pagi. Mohon apresiasi verbal wajar dan konsisten saat presensi tanpa mengungkit ketidakhadiran pekan sebelumnya. Situasi terpantau semakin membaik.',
            'excerpt' => 'Tindak lanjut kehadiran harian kelas: Siswa telah menunjukkan keteraturan presensi pagi. Mohon apresiasi verbal wajar dan konsisten saat presensi tanpa mengungkit ketidakhadiran pekan sebelumnya.',
            'read_at' => '19 Okt 2024, 10:15 WIB',
            'detail' => 'Tindak lanjut kehadiran harian kelas: Siswa telah menunjukkan keteraturan presensi pagi. Mohon apresiasi verbal wajar dan konsisten saat presensi tanpa mengungkit ketidakhadiran pekan sebelumnya. Perhatikan dinamika kelompok saat kerja kelompok mata pelajaran IPA, pastikan tidak ada pengabaian atau pengucilan pasif saat pembagian peran tugas.',
        ],
        [
            'id' => 5,
            'school' => 'smakita',
            'class' => '7B',
            'counselor' => 'Dra. Nurul Hidayah',
            'role' => 'Guru BK',
            'time' => '15 Okt 2024, 13:20 WIB',
            'status' => 'read',
            'content' => 'Koordinasi kegiatan ekstrakurikuler kepramukaan: Mohon bantuan mendampingi peralihan regu agar tercipta lingkungan sosial yang lebih inklusif dan kondusif untuk pengembangan kepercayaan diri.',
            'excerpt' => 'Koordinasi kegiatan ekstrakurikuler kepramukaan: Mohon bantuan mendampingi peralihan regu agar tercipta lingkungan sosial yang lebih inklusif dan kondusif untuk pengembangan kepercayaan diri.',
            'read_at' => '15 Okt 2024, 15:00 WIB',
            'detail' => 'Koordinasi kegiatan ekstrakurikuler kepramukaan: Mohon bantuan mendampingi peralihan regu agar tercipta lingkungan sosial yang lebih inklusif dan kondusif untuk pengembangan kepercayaan diri.',
        ],
        [
            'id' => 6,
            'school' => 'smakita',
            'class' => '7B',
            'counselor' => 'Sarah Jenkins, M.Pd.',
            'role' => 'Konselor BK Inti',
            'time' => '11 Okt 2024, 10:00 WIB',
            'status' => 'read',
            'content' => 'Panduan monitoring berkala suasana kelas: Pastikan atmosfer kelas tetap suportif dan tidak ada pelabelan negatif pasca klarifikasi kesalahpahaman antarkelompok belajar.',
            'excerpt' => 'Panduan monitoring berkala suasana kelas: Pastikan atmosfer kelas tetap suportif dan tidak ada pelabelan negatif pasca klarifikasi kesalahpahaman antarkelompok belajar.',
            'read_at' => '11 Okt 2024, 11:30 WIB',
            'detail' => 'Panduan monitoring berkala suasana kelas: Pastikan atmosfer kelas tetap suportif dan tidak ada pelabelan negatif pasca klarifikasi kesalahpahaman antarkelompok belajar.',
        ],
        [
            'id' => 7,
            'school' => 'smakita',
            'class' => '7B',
            'counselor' => 'Dra. Nurul Hidayah',
            'role' => 'Guru BK',
            'time' => '05 Okt 2024, 15:45 WIB',
            'status' => 'read',
            'content' => 'Rekomendasi penataan tempat duduk rotasi bulanan: Penyesuaian formasi meja belajar untuk mempererat kolaborasi antar rekan sebaya yang heterogen telah berjalan efektif.',
            'excerpt' => 'Rekomendasi penataan tempat duduk rotasi bulanan: Penyesuaian formasi meja belajar untuk mempererat kolaborasi antar rekan sebaya yang heterogen telah berjalan efektif.',
            'read_at' => '05 Okt 2024, 16:10 WIB',
            'detail' => 'Rekomendasi penataan tempat duduk rotasi bulanan: Penyesuaian formasi meja belajar untuk mempererat kolaborasi antar rekan sebaya yang heterogen telah berjalan efektif.',
        ],
        [
            'id' => 8,
            'school' => 'smakita',
            'class' => '7B',
            'counselor' => 'Sarah Jenkins, M.Pd.',
            'role' => 'Konselor BK Inti',
            'time' => '28 Sep 2024, 09:15 WIB',
            'status' => 'read',
            'content' => 'Catatan awal pembinaan semester: Seluruh langkah preventif terkait etika pergaulan dan komunikasi santun di kelas telah diinsersikan saat jam perwalian mingguan.',
            'excerpt' => 'Catatan awal pembinaan semester: Seluruh langkah preventif terkait etika pergaulan dan komunikasi santun di kelas telah diinsersikan saat jam perwalian mingguan.',
            'read_at' => '28 Sep 2024, 11:00 WIB',
            'detail' => 'Catatan awal pembinaan semester: Seluruh langkah preventif terkait etika pergaulan dan komunikasi santun di kelas telah diinsersikan saat jam perwalian mingguan.',
        ],
    ];

    private function getStoragePath(): string
    {
        return storage_path('framework/walas_summaries.json');
    }

    public function getTeacherClass(): string
    {
        if (Auth::check()) {
            $staff = rescue(fn() => DB::table('staff_members')->where('email', Auth::user()->email)->first(), null, false);
            if ($staff && !empty($staff->class_name)) {
                return $staff->class_name;
            }
        }
        return '7B';
    }

    public function all(): array
    {
        $path = $this->getStoragePath();
        $class = $this->getTeacherClass();

        if (file_exists($path)) {
            $data = json_decode(file_get_contents($path), true);
            if (is_array($data) && count($data) > 0) {
                foreach ($data as &$item) {
                    $item['class'] = $class;
                }
                return $data;
            }
        }

        $items = self::DEFAULT_SUMMARIES;
        foreach ($items as &$item) {
            $item['class'] = $class;
        }

        @file_put_contents($path, json_encode($items, JSON_PRETTY_PRINT));
        return $items;
    }

    /**
     * Mengambil seluruh ringkasan binaan kelas guru aktif.
     */
    public function summaries(): Collection
    {
        return collect($this->all());
    }

    public function findSummary(int $id): array
    {
        return $this->summaries()->firstWhere('id', $id) ?? abort(404);
    }

    public function toggleRead(int $id): array
    {
        $items = $this->all();
        $updated = null;

        foreach ($items as &$item) {
            if ((int)$item['id'] === (int)$id) {
                if ($item['status'] === 'read') {
                    $item['status'] = 'unread';
                    $item['read_at'] = null;
                } else {
                    $item['status'] = 'read';
                    $item['read_at'] = now()->translatedFormat('d M Y, H:i') . ' WIB';
                }
                $updated = $item;
                break;
            }
        }

        @file_put_contents($this->getStoragePath(), json_encode($items, JSON_PRETTY_PRINT));
        return $updated ?? abort(404);
    }

    public function markAllAsRead(): void
    {
        $items = $this->all();
        $now = now()->translatedFormat('d M Y, H:i') . ' WIB';

        foreach ($items as &$item) {
            if ($item['status'] === 'unread') {
                $item['status'] = 'read';
                $item['read_at'] = $now;
            }
        }

        @file_put_contents($this->getStoragePath(), json_encode($items, JSON_PRETTY_PRINT));
    }
}
