<?php

namespace App\Support;

use RuntimeException;
use Sastrawi\Stemmer\StemmerFactory;
use Sastrawi\Stemmer\StemmerInterface;
use Sastrawi\StopWordRemover\StopWordRemoverFactory;

class BullyingNlpClassifier
{
    /**
     * Kamus normalisasi kata gaul / singkatan / bahasa Jawa (Ngapak) -> bahasa Indonesia baku.
     * Diselaraskan dengan notebook pelatihan klasifikasi_aduan_bullying_RTX3050_GPU.ipynb.
     *
     * @var array<string, string>
     */
    private const SLANG_DICT = [
        // kata ganti & negasi
        'akuu' => 'aku', 'aq' => 'aku', 'gw' => 'aku', 'gue' => 'aku', 'nyong' => 'aku', 'inyong' => 'aku',
        'gak' => 'tidak', 'ga' => 'tidak', 'gk' => 'tidak', 'nggak' => 'tidak', 'ngga' => 'tidak', 'nggk' => 'tidak',
        'enggak' => 'tidak', 'engga' => 'tidak', 'nda' => 'tidak', 'tdk' => 'tidak', 'ora' => 'tidak', 'ra' => 'tidak',
        'gaada' => 'tidak ada',
        'blm' => 'belum', 'jngn' => 'jangan', 'jgn' => 'jangan', 'aja' => 'saja', 'aj' => 'saja', 'bae' => 'saja', 'tok' => 'saja',
        // singkatan umum chat
        'yg' => 'yang', 'yng' => 'yang', 'dgn' => 'dengan', 'krn' => 'karena', 'karna' => 'karena', 'krna' => 'karena',
        'trs' => 'terus', 'trus' => 'terus', 'trss' => 'terus', 'tp' => 'tapi', 'tpi' => 'tapi', 'jd' => 'jadi', 'jdi' => 'jadi', 'lg' => 'lagi',
        'bgt' => 'banget', 'bngt' => 'banget', 'bangey' => 'banget', 'bnget' => 'banget',
        'skrg' => 'sekarang', 'skrng' => 'sekarang', 'skrang' => 'sekarang', 'sekrang' => 'sekarang',
        'udh' => 'sudah', 'udah' => 'sudah', 'udahh' => 'sudah', 'dah' => 'sudah', 'sdh' => 'sudah', 'uda' => 'sudah', 'wis' => 'sudah', 'uwis' => 'sudah',
        'blg' => 'bilang', 'bilng' => 'bilang', 'bilangin' => 'bilang',
        'ngmng' => 'ngomong', 'ngomg' => 'ngomong', 'ngmg' => 'ngomong', 'ngoong' => 'ngomong', 'ngomng' => 'ngomong', 'ngomohg' => 'ngomong', 'omong' => 'ngomong',
        'td' => 'tadi', 'tdi' => 'tadi', 'tadii' => 'tadi', 'bsk' => 'besok', 'bsok' => 'besok', 'besuk' => 'besok',
        'kmrn' => 'kemarin', 'kemaren' => 'kemarin', 'kemren' => 'kemarin', 'kmarin' => 'kemarin', 'kemareen' => 'kemarin', 'kearen' => 'kemarin', 'kemarren' => 'kemarin', 'kejaren' => 'kemarin',
        'hr' => 'hari', 'pdhl' => 'padahal', 'msh' => 'masih', 'masi' => 'masih', 'msih' => 'masih', 'isih' => 'masih',
        'knp' => 'kenapa', 'kenpa' => 'kenapa', 'org' => 'orang', 'wong' => 'orang', 'gmn' => 'bagaimana', 'gimana' => 'bagaimana',
        'kepriwe' => 'bagaimana', 'priwe' => 'bagaimana', 'kl' => 'kalau', 'kalo' => 'kalau', 'klo' => 'kalau', 'kalok' => 'kalau',
        'sm' => 'sama', 'sma' => 'sama', 'dr' => 'dari', 'utk' => 'untuk', 'bkn' => 'bukan', 'emg' => 'memang',
        'bkin' => 'bikin', 'bikn' => 'bikin', 'nyari' => 'cari', 'nyarii' => 'cari', 'dikit' => 'sedikit', 'gitu' => 'begitu', 'gini' => 'begini',
        'plg' => 'pulang', 'pulng' => 'pulang', 'ortu' => 'orang tua', 'mamah' => 'ibu', 'mama' => 'ibu', 'bapak' => 'ayah',
        // kata sekolah yang sering salah ketik
        'kls' => 'kelas', 'kela' => 'kelas', 'kelass' => 'kelas', 'kels' => 'kelas',
        'tmn' => 'teman', 'tmen' => 'teman', 'temen' => 'teman', 'temenn' => 'teman', 'temennya' => 'temannya', 'temenya' => 'temannya', 'tmnnya' => 'temannya',
        'sekolh' => 'sekolah', 'skolah' => 'sekolah', 'sekllah' => 'sekolah', 'sekopah' => 'sekolah', 'skolh' => 'sekolah', 'sekolsh' => 'sekolah',
        'plajaran' => 'pelajaran', 'pelaajran' => 'pelajaran', 'peljaran' => 'pelajaran', 'pelqjaran' => 'pelajaran', 'pelajraan' => 'pelajaran', 'pelaajaran' => 'pelajaran',
        'istrahat' => 'istirahat', 'istirht' => 'istirahat', 'istrhat' => 'istirahat', 'istiraht' => 'istirahat', 'istirwhat' => 'istirahat',
        'kakel' => 'kakak kelas', 'bk' => 'bk', 'uks' => 'uks', 'pr' => 'pr', 'osis' => 'osis',
        // emosi & dampak
        'takutt' => 'takut', 'tkt' => 'takut', 'taukt' => 'takut', 'takuut' => 'takut', 'tajut' => 'takut', 'takkut' => 'takut',
        'crita' => 'cerita', 'ceritaa' => 'cerita', 'nangis' => 'menangis', 'gemeter' => 'gemetar',
        'kepikiran' => 'kepikiran', 'kpikiran' => 'kepikiran', 'kepikirn' => 'kepikiran', 'kepikiarn' => 'kepikiran', 'kepikoran' => 'kepikiran',
        'kepikiraan' => 'kepikiran', 'keikiran' => 'kepikiran', 'kepikirna' => 'kepikiran', 'kepikifan' => 'kepikiran',
        'ketenu' => 'ketemu', 'keteum' => 'ketemu', 'kettemu' => 'ketemu', 'keetmu' => 'ketemu', 'ketmeu' => 'ketemu', 'keteemu' => 'ketemu', 'keyemu' => 'ketemu',
        'merem' => 'tidur', 'melek' => 'terjaga', 'mumet' => 'pusing', 'lara' => 'sakit', 'cape' => 'capek', 'capekk' => 'capek',
        'baper' => 'bawa perasaan', 'baperan' => 'bawa perasaan', 'caper' => 'cari perhatian', 'ilfeel' => 'ilfil',
        // bahasa Jawa Ngapak
        'ning' => 'di', 'neng' => 'di', 'endi' => 'mana', 'salahe' => 'salahnya', 'kudu' => 'harus', 'meneng' => 'diam', 'kaya' => 'seperti', 'kayak' => 'seperti',
        'kuwe' => 'itu', 'iki' => 'ini', 'kae' => 'itu', 'sing' => 'yang', 'rasane' => 'rasanya', 'penak' => 'enak', 'tenan' => 'sungguh',
        'wingi' => 'kemarin', 'dina' => 'hari', 'maning' => 'lagi', 'dheweke' => 'dia', 'ndelok' => 'lihat', 'bali' => 'pulang', 'mung' => 'hanya',
        'sinau' => 'belajar', 'digagas' => 'dipedulikan', 'digugu' => 'didengar', 'omongna' => 'bicarakan', 'diterusna' => 'diteruskan',
        'diteruske' => 'diteruskan', 'rampung' => 'selesai', 'jebule' => 'ternyata', 'mandeg' => 'berhenti', 'cah' => 'anak', 'nanggung' => 'menanggung',
        'jarene' => 'katanya', 'guyon' => 'bercanda', 'wani' => 'berani', 'pekok' => 'bodoh', 'liwat' => 'lewat', 'mben' => 'dulu', 'ben' => 'biar',
        'carane' => 'caranya', 'meh' => 'hampir', 'ngono' => 'begitu', 'ngene' => 'begini', 'lah' => '', 'yo' => 'ya', 'diulangi' => 'diulang',
        // kata kasar -> bentuk baku
        'asw' => 'anjing', 'anj' => 'anjing', 'anjr' => 'anjing', 'anjir' => 'anjing', 'njir' => 'anjing', 'bgst' => 'bangsat',
        'jancuk' => 'jancuk', 'ndasmu' => 'kepalamu', 'cangkemmu' => 'mulutmu', 'kampret' => 'kampret',
        // media sosial
        'gc' => 'grup', 'notif' => 'notifikasi', 'mabar' => 'main bersama', 'hp' => 'ponsel', 'dm' => 'pesan', 'chatnya' => 'chat',
    ];

    /** @var array<int, string> */
    private const NEGASI = ['tidak', 'belum', 'jangan', 'bukan', 'tanpa', 'kurang'];

    /** @var array<int, string> */
    private const EXTRA_STOPWORDS = [
        'aku', 'kamu', 'dia', 'mereka', 'kita', 'kami', 'nya', 'sih', 'deh', 'dong', 'kok', 'loh', 'lho', 'woy', 'btw', 'duh',
        'nih', 'tuh', 'ya', 'yah', 'kan', 'pun', 'si', 'kak', 'eh', 'oh', 'ah', 'wkwk', 'hehe', 'gitu', 'begitu', 'begini',
        'terus', 'tapi', 'jadi', 'lagi', 'saja', 'sudah', 'yang', 'dan', 'di', 'ke', 'dari', 'ini', 'itu', 'ada', 'sama',
        'mau', 'bisa', 'pas', 'waktu', 'kalau', 'banget', 'sekarang', 'tadi', 'kemarin', 'besok', 'hari', 'sampai',
    ];

    /**
     * Indikator Krisis / Darurat Mutlak (Child Protection & Permendikbudristek 46/2023):
     * Menangani kasus akut & kalimat pendek/OOV seperti kekerasan seksual berat, KSBE/sextortion,
     * risiko bunuh diri/self-harm, ancaman kekerasan mematikan/balas dendam fatal, senjata, luka berat, dan teror.
     *
     * Format: [label => [regex, checkNegation]]
     *
     * @var array<string, array{0: string, 1: bool}>
     */
    private const CRITICAL_DARURAT_PATTERNS = [
        // Kekerasan seksual berat & eksploitasi seksual (luring & daring / KSBE / sextortion)
        'kekerasan_seksual_berat' => ['/\b(di)?perkosa\w*|memperkosa|pemerkosaan|rudapaksa|di?cabul\w*|pencabulan|di?sodomi|disetubuh\w*|hubungan\s+seksual|dipaksa\s+(berhubungan|tidur|buka\s+baju|telanjang|ciuman|cium)|hamil\b/u', true],
        'bagian_tubuh_sensitif_paksa' => ['/\b(kelamin|kemaluan|alat\s+vital|payudara|tete\w*|buah\s+dada|vagina|penis|area\s+sensitif|bagian\s+sensitif|bagian\s+intim|bagian\s+pribadi)\b.{0,55}\b(pegang|dipegang|raba|diraba|remas|diremas|cubit|dicubit|jepit|dijepit|paksa|dipaksa|gesek|tunjuk|foto|difoto|memfoto|potret|dipotret|memotret|rekam|direkam|merekam|video|upload|diupload|unggah|diunggah|sebar|disebar)\b|\b(pegang|dipegang|raba|diraba|remas|diremas|cubit|dicubit|jepit|dijepit|gerayang\w*|foto|difoto|memfoto|potret|dipotret|memotret|rekam|direkam|merekam|intip|mengintip|diintip)\b.{0,45}\b(kelamin|kemaluan|alat\s+vital|payudara|tete\w*|dada|pantat|bokong|paha|area\s+sensitif|bagian\s+sensitif|bagian\s+intim|bagian\s+pribadi)\b/u', true],
        'eksploitasi_konten_seksual' => ['/\b(foto|video|gambar|konten|rekam|direkam|difoto|memfoto|memotret|sebar|disebar|upload|diupload|unggah|minta|meminta|paksa|ancam\w*)\b.{0,45}\b(telanjang|bugil|tanpa\s+baju|tanpa\s+busana|tidak\s+senonoh|tak\s+senonoh|asusila|vulgar|syur|porno\w*|mandi|ganti\s+baju|area\s+sensitif|bagian\s+sensitif)\b|\b(telanjang|bugil|tanpa\s+baju|foto\s+tidak\s+senonoh|video\s+tidak\s+senonoh|foto\s+syur|video\s+syur|sextortion)\b/u', false],
        // Self-harm & risiko bunuh diri
        'risiko_bunuh_diri' => ['/\b(bunuh\s+diri|akhiri\s+hidup|mengakhiri\s+hidup|ingin\s+mati|pengen\s+mati|mau\s+mati|lebih\s+baik\s+mati|tidak\s+(mau|ingin|kuat)\s+hidup|ga\s*k?\s*(mau|kuat)\s+hidup|gantung\s+diri|lompat\s+dari\s+(gedung|atap|lantai)|minum\s+racun|overdosis)\b/u', false],
        'self_harm' => ['/\b(melukai\s+diri|lukai\s+diri|sakiti\s+diri|menyakiti\s+diri|sayat\s+(tangan|nadi|pergelangan|lengan)|silet\s+(tangan|nadi|lengan)|potong\s+nadi|memotong\s+nadi|self[\s-]?harm|cutting)\b/u', true],
        // Ancaman / niat melukai fatal atau membunuh orang lain (termasuk balas dendam mematikan)
        'ancaman_kekerasan_mematikan' => ['/\b(potong|memotong|sayat|menyayat|gorok|menggorok)\s+(nadi\w*|leher\w*|urat\s+nadi)|\b(ingin|mau|akan|niat|balas\s+dendam)\b.{0,40}\b(membunuh|bunuh|memotong\s+nadi\w*|potong\s+nadi\w*|menusuk|tusuk|membacok|bacok|menggorok|gorok|meracuni|racun|membakar|bakar)\b/u', true],
        // Kekerasan fisik berat, luka serius, senjata, pengeroyokan
        'senjata_kekerasan' => ['/\b(pisau|silet|cutter|celurit|clurit|parang|golok|senjata\s+tajam|sajam|dibacok|bacok|ditusuk|tusuk|disayat|sayat|dibakar|disiram\s+air\s+panas|disundut\s+rokok)\b/u', true],
        'kekerasan_fisik_berat' => ['/\b(dicekik|mencekik|cekik|dibanting|banting|dikeroyok|keroyok|pengeroyokan|dianiaya|penganiayaan|diinjak|injak|babak\s+belur|berdarah|pingsan|patah\s+tulang|gegar\s+otak|masuk\s+(rs|rumah\s+sakit|igd|ugd)|luka\s+(parah|robek|serius|dalam))\b/u', true],
        'kekerasan_fisik_luka_berulang' => ['/\b(dipukul\w*|memukul|mukulin|ditendang\w*|ditampar\w*|ditonjok\w*|dihajar\w*)\b.{0,45}\b(berdarah|darah|memar|lebam|bengkak|luka|pingsan|patah|setiap\s+hari|tiap\s+hari|berkali[\s-]?kali|terus[\s-]?menerus|sampai\s+(jatuh|pingsan|luka|berdarah|hamil))\b|\b(setiap\s+hari|tiap\s+hari)\b.{0,30}\b(dipukul\w*|ditendang\w*|ditampar\w*|dihajar\w*|dianiaya)\b/u', true],
        // Ancaman nyawa / teror serius / penyekapan / penculikan
        'ancaman_nyawa_sekap_teror' => ['/\b(diancam\s+(dibunuh|bunuh|pakai\s+pisau|sajam)|mau\s+dibunuh|akan\s+dibunuh|ancaman\s+nyawa|disekap|dikurung\s+di\s+(toilet|gudang|kamar\s+mandi)|diculik|penculikan)\b|\b(ancaman|diancam|mengancam)\b.{0,65}\b(teror|diteror|meneror)\b|\b(teror|diteror|meneror)\b.{0,65}\b(ancaman|diancam|mengancam|trauma|tidak\s+(ingin|mau|berani)\s+(berangkat|masuk)\s+(ke\s+)?sekolah)\b/u', true],
    ];

    /**
     * Indikator Eskalasi Sedang/Penting:
     * Kekerasan fisik tunggal, pemalakan/pemerasan, ancaman/teror, pelecehan, atau dampak psikologis berat.
     *
     * @var array<string, array{0: string, 1: bool}>
     */
    private const MODERATE_SEDANG_PATTERNS = [
        'kekerasan_fisik' => ['/\b(dipukul\w*|mukulin|ditendang\w*|ditampar\w*|ditonjok\w*|dijambak\w*|dihajar\w*|diludahi|didorong\s+sampai\s+jatuh|dilempar\s+(batu|kursi|sepatu|benda))\b/u', true],
        'pelecehan' => ['/\b(dileceh\w*|pelecehan|dicium\s+paksa|dipegang\s+paksa|sentuhan\s+tidak\s+pantas|tidak\s+senonoh|tak\s+senonoh|asusila|area\s+sensitif|bagian\s+sensitif)\b/u', true],
        'pemerasan_ancaman_teror' => ['/\b(dipalak\w*|malak|pemalakan|diperas|pemerasan|diminta\s+uang\s+paksa|diancam|mengancam|ngancem|ancaman|teror|diteror|meneror|intimidasi)\b/u', true],
        'dampak_psikologis_berat' => ['/\b(tidak\s+(berani|ingin|mau)\s+(berangkat\s+|masuk\s+)?(ke\s+)?sekolah|ga\s*k?\s*(berani|ingin|mau)\s+(berangkat\s+|masuk\s+|buat\s+)?(ke\s+)?sekolah|mogok\s+sekolah|takut\s+(masuk|berangkat|ke)\s+sekolah|susah\s+tidur|tidak\s+bisa\s+tidur|trauma|depresi|serangan\s+panik)\b/u', false],
    ];

    /**
     * Kata umpatan / penghinaan martabat berat untuk mendeteksi perundungan verbal berat
     * (menjadi Sedang bila muncul >= 3 kata hinaan berat sekaligus, atau >= 1 disertai korban menangis/trauma).
     *
     * @var array<int, string>
     */
    private const SEVERE_VERBAL_SLURS = [
        'miskin', 'fakir', 'busung lapar', 'rumah kardus', 'melarat', 'kere',
        'gonore', 'sifilis', 'hiv', 'aids', 'penyakit kelamin', 'kurap', 'kudis',
        'gagar otak', 'gegar otak', 'cacat', 'autis', 'idiot', 'gila', 'sakit jiwa',
        'bodoh', 'tolol', 'bego', 'goblok', 'anjing', 'bangsat', 'jancuk', 'asu',
        'babi', 'monyet', 'pelacur', 'lonte', 'jalang', 'murahan', 'sampah', 'haram', 'yatim',
    ];

    /** @var array<string, mixed>|null */
    private static ?array $model = null;

    private static ?string $loadedWeightsPath = null;

    private static ?StemmerInterface $stemmer = null;

    /** @var array<string, bool>|null */
    private static ?array $stopwordsMap = null;

    /** @var array<string, string> */
    private static array $stemCache = [];

    /**
     * Prediksi dampak aduan bullying (0 = Dampak Ringan, 1 = Dampak Berat) beserta probabilitas & tingkat urgensi.
     * Menggabungkan model terlatih Linear SVM (model_terpilih.joblib) + lapisan keselamatan krisis (Hybrid Safety Guardrail)
     * agar kasus kekerasan seksual, self-harm, senjata, dan kekerasan fisik berat/berulang tidak pernah lolos sebagai Rendah.
     *
     * @return array{
     *     label: int,
     *     label_name: string,
     *     prob_ringan: float,
     *     prob_berat: float,
     *     svm_prob_berat: float,
     *     confidence: float,
     *     predicted_urgency: string,
     *     model_version: string,
     *     risk_source: string,
     *     matched_signals: array<int, string>,
     *     clean_text: string
     * }
     */
    public static function classify(string $text, ?string $category = null): array
    {
        $model = self::loadModel();
        $clean = self::preprocess($text);

        preg_match_all('/\b\w\w+\b/u', $clean, $matches);
        $tokens = $matches[0] ?? [];

        $features = $tokens;
        $tokenCount = count($tokens);
        for ($i = 0; $i < $tokenCount - 1; $i++) {
            $features[] = $tokens[$i].' '.$tokens[$i + 1];
        }

        /** @var array<string, int> $vocab */
        $vocab = $model['vocab'];
        /** @var array<int, float> $idf */
        $idf = $model['idf'];
        $sublinear = (bool) ($model['sublinear_tf'] ?? true);

        $counts = [];
        foreach ($features as $feat) {
            if (isset($vocab[$feat])) {
                $idx = $vocab[$feat];
                $counts[$idx] = ($counts[$idx] ?? 0) + 1;
            }
        }

        $vec = [];
        $normSq = 0.0;
        foreach ($counts as $idx => $cnt) {
            $tf = $sublinear ? (1.0 + log((float) $cnt)) : (float) $cnt;
            $w = $tf * (float) $idf[$idx];
            $vec[$idx] = $w;
            $normSq += $w * $w;
        }

        if ($normSq > 0.0) {
            $invNorm = 1.0 / sqrt($normSq);
            foreach ($vec as $idx => $w) {
                $vec[$idx] = $w * $invNorm;
            }
        }

        /** @var array<int, array{coef: array<int, float>, intercept: float, a: float, b: float}> $folds */
        $folds = $model['folds'];
        $probSum = 0.0;
        foreach ($folds as $fold) {
            $z = (float) $fold['intercept'];
            $coef = $fold['coef'];
            foreach ($vec as $idx => $w) {
                $z += ((float) $coef[$idx]) * $w;
            }
            $exponent = ((float) $fold['a']) * $z + ((float) $fold['b']);
            if ($exponent > 500.0) {
                $p1 = 0.0;
            } elseif ($exponent < -500.0) {
                $p1 = 1.0;
            } else {
                $p1 = 1.0 / (1.0 + exp($exponent));
            }
            $probSum += $p1;
        }

        $svmProbBerat = count($folds) > 0 ? ($probSum / count($folds)) : 0.0;
        $svmProbBerat = min(1.0, max(0.0, $svmProbBerat));

        // Evaluasi Lapisan Keselamatan Krisis (Hybrid Safety Guardrail)
        $rawLower = self::normalizeForRiskScan($text);
        $criticalSignals = self::detectRiskSignals($rawLower, self::CRITICAL_DARURAT_PATTERNS);
        $moderateSignals = self::detectRiskSignals($rawLower, self::MODERATE_SEDANG_PATTERNS);
        $severeVerbalDetected = self::detectSevereVerbalAbuse($rawLower);

        $probBerat = $svmProbBerat;
        $riskSource = 'linear_svm';
        $matchedSignals = [];

        if ($criticalSignals !== []) {
            $probBerat = max($svmProbBerat, 0.95);
            $riskSource = 'hybrid_red_flag_darurat';
            $matchedSignals = $criticalSignals;
        } elseif (count($moderateSignals) >= 2) {
            // Kombinasi >= 2 sinyal serius (mis. dipukul + tidak berani sekolah, atau ancaman/teror + trauma) -> Darurat
            $probBerat = max($svmProbBerat, 0.85);
            $riskSource = 'hybrid_red_flag_darurat';
            $matchedSignals = $moderateSignals;
        } elseif ($moderateSignals !== []) {
            $probBerat = max($svmProbBerat, 0.68);
            $riskSource = 'hybrid_red_flag_sedang';
            $matchedSignals = $moderateSignals;
        } elseif ($severeVerbalDetected) {
            $probBerat = max($svmProbBerat, 0.68);
            $riskSource = 'hybrid_verbal_berat_sedang';
            $matchedSignals = ['perundungan_verbal_berat'];
        }

        $probRingan = 1.0 - $probBerat;
        $label = $probBerat >= 0.5 ? 1 : 0;
        $confidence = max($probRingan, $probBerat);

        return [
            'label' => $label,
            'label_name' => $label === 1 ? 'Dampak Berat' : 'Dampak Ringan',
            'prob_ringan' => round($probRingan, 4),
            'prob_berat' => round($probBerat, 4),
            'svm_prob_berat' => round($svmProbBerat, 4),
            'confidence' => round($confidence, 4),
            'predicted_urgency' => self::mapProbabilityToUrgency($probBerat),
            'model_version' => (string) ($model['model_version'] ?? 'linear-svm-bullying-v2').'-hybrid',
            'risk_source' => $riskSource,
            'matched_signals' => $matchedSignals,
            'clean_text' => $clean,
        ];
    }

    /**
     * Petakan probabilitas Dampak Berat dari model NLP ke 3 tingkat urgensi HiOwy:
     * - P(Dampak Berat) >= 0.80 -> Darurat
     * - 0.50 <= P(Dampak Berat) < 0.80 -> Sedang
     * - P(Dampak Berat) < 0.50 -> Rendah
     */
    public static function mapProbabilityToUrgency(float $probBerat): string
    {
        if ($probBerat >= 0.80) {
            return 'Darurat';
        }

        if ($probBerat >= 0.50) {
            return 'Sedang';
        }

        return 'Rendah';
    }

    /**
     * Tentukan tingkat urgensi akhir dengan menjaga prinsip keselamatan:
     * laporan yang sejak awal ditandai 'Darurat' oleh pelapor tidak pernah diturunkan oleh model NLP.
     */
    public static function resolveUrgency(float $probBerat, ?string $currentUrgency = null): string
    {
        $normalized = strtolower(trim((string) $currentUrgency));
        if (in_array($normalized, ['darurat', 'urgent'], true)) {
            return 'Darurat';
        }

        return self::mapProbabilityToUrgency($probBerat);
    }

    /**
     * Pra-pemrosesan teks identik dengan pipeline TF-IDF pada notebook pelatihan:
     * case folding -> noise & emoji cleaning -> tokenisasi -> normalisasi slang/Ngapak -> stopword removal (jaga negasi) -> stemming Sastrawi.
     */
    public static function preprocess(string $text): string
    {
        $t = mb_strtolower($text, 'UTF-8');

        // Hapus emoji & simbol grafis
        $t = (string) preg_replace('/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{FE0F}\x{200D}\x{2190}-\x{21FF}]+/u', ' ', $t);
        // Hapus URL dan mention
        $t = (string) preg_replace('/http\S+|www\.\S+|@\w+/u', ' ', $t);
        $t = str_replace('janc*k', 'jancuk', $t);
        // Normalisasi kata ulang angka 2: deg2an -> degdegan, teman2 -> teman teman
        $t = (string) preg_replace('/\b(\w+?)2an\b/u', '$1$1an', $t);
        $t = (string) preg_replace('/\b([a-z]+)2\b/u', '$1 $1', $t);
        // Buang angka & tanda baca
        $t = (string) preg_replace('/[^a-z\s]/u', ' ', $t);
        // Pangkas huruf berulang >= 3 (gakkk -> gak, bangettt -> banget)
        $t = (string) preg_replace('/([a-z])\1{2,}/u', '$1', $t);
        $t = trim((string) preg_replace('/\s+/u', ' ', $t));

        if ($t === '') {
            return '';
        }

        $tokens = explode(' ', $t);

        // Normalisasi kamus slang / typo / bahasa Jawa Ngapak
        $normalized = [];
        foreach ($tokens as $tok) {
            $rep = self::SLANG_DICT[$tok] ?? $tok;
            if ($rep === '') {
                continue;
            }
            foreach (explode(' ', $rep) as $part) {
                if ($part !== '') {
                    $normalized[] = $part;
                }
            }
        }

        // Stopword removal (tanpa membuang kata negasi)
        $stopwords = self::stopwordsMap();
        $filtered = [];
        foreach ($normalized as $tok) {
            if (strlen($tok) > 1 && ! isset($stopwords[$tok])) {
                $filtered[] = $tok;
            }
        }

        // Stemming Sastrawi dengan cache token
        $stemmer = self::stemmer();
        $stemmed = [];
        foreach ($filtered as $tok) {
            if (! isset(self::$stemCache[$tok])) {
                self::$stemCache[$tok] = $stemmer->stem($tok);
            }
            if (self::$stemCache[$tok] !== '') {
                $stemmed[] = self::$stemCache[$tok];
            }
        }

        return implode(' ', $stemmed);
    }

    private static function normalizeForRiskScan(string $text): string
    {
        $t = mb_strtolower($text, 'UTF-8');
        $t = (string) preg_replace('/([a-z])\1{2,}/u', '$1', $t);
        $t = (string) preg_replace('/[^a-z0-9\s-]/u', ' ', $t);

        return trim((string) preg_replace('/\s+/u', ' ', $t));
    }

    /**
     * Deteksi perundungan verbal berat / caci maki martabat yang ekstrem:
     * - >= 3 kata umpatan/stigma berat dalam satu aduan (meski singkat), ATAU
     * - >= 2 kata umpatan/stigma berat disertai dampak emosional (menangis / nangis / hancur / stres).
     */
    private static function detectSevereVerbalAbuse(string $normalizedText): bool
    {
        if ($normalizedText === '') {
            return false;
        }

        $slurHits = 0;
        foreach (self::SEVERE_VERBAL_SLURS as $slur) {
            $escaped = preg_quote($slur, '/');
            if (preg_match('/\b'.$escaped.'\b/u', $normalizedText)) {
                $slurHits++;
            }
        }

        if ($slurHits >= 3) {
            return true;
        }

        if ($slurHits >= 2 && preg_match('/\b(menangis|nangis|trauma|hancur|mengurung\s+diri|tidak\s+mau\s+keluar)\b/u', $normalizedText)) {
            return true;
        }

        return false;
    }

    /**
     * @param  array<string, array{0: string, 1: bool}>  $patterns
     * @return array<int, string>
     */
    private static function detectRiskSignals(string $normalizedText, array $patterns): array
    {
        if ($normalizedText === '') {
            return [];
        }

        $signals = [];
        foreach ($patterns as $name => [$regex, $checkNegation]) {
            if (! preg_match_all($regex, $normalizedText, $allMatches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            foreach ($allMatches[0] as $match) {
                $offset = (int) $match[1];
                if ($checkNegation) {
                    $prefix = substr($normalizedText, max(0, $offset - 24), min(24, $offset));
                    if (preg_match('/\b(tidak|tak|gak|ga|gk|nggak|ngga|enggak|bukan|belum|tanpa|jangan)\s+(pernah\s+|sampai\s+|sampe\s+)?$/u', $prefix)) {
                        continue;
                    }
                }
                $signals[] = $name;
                break;
            }
        }

        return $signals;
    }

    /**
     * @return array<string, mixed>
     */
    private static function loadModel(): array
    {
        $path = (string) config('services.ml.weights_path', base_path('ml_model/model_weights.json'));

        if (self::$model !== null && self::$loadedWeightsPath === $path) {
            return self::$model;
        }

        if (! is_file($path)) {
            throw new RuntimeException("Berkas bobot model NLP tidak ditemukan di {$path}.");
        }

        $decoded = json_decode((string) file_get_contents($path), true);
        if (! is_array($decoded) || ! isset($decoded['vocab'], $decoded['idf'], $decoded['folds'])) {
            throw new RuntimeException('Format berkas bobot model NLP tidak valid.');
        }

        self::$model = $decoded;
        self::$loadedWeightsPath = $path;

        return self::$model;
    }

    private static function stemmer(): StemmerInterface
    {
        if (self::$stemmer === null) {
            self::$stemmer = (new StemmerFactory)->createStemmer();
        }

        return self::$stemmer;
    }

    /**
     * @return array<string, bool>
     */
    private static function stopwordsMap(): array
    {
        if (self::$stopwordsMap !== null) {
            return self::$stopwordsMap;
        }

        $words = (new StopWordRemoverFactory)->getStopWords();
        $map = [];
        foreach ($words as $w) {
            $map[$w] = true;
        }
        foreach (self::EXTRA_STOPWORDS as $w) {
            $map[$w] = true;
        }
        foreach (self::NEGASI as $w) {
            unset($map[$w]);
        }

        self::$stopwordsMap = $map;

        return self::$stopwordsMap;
    }
}
