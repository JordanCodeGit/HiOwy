<x-layouts.auth title="Pengaturan Sekolah" body-class="bg-surface font-body-md text-on-surface antialiased min-h-screen" :medium-fonts="true">
    <div class="min-h-screen w-full flex flex-col lg:flex-row">
        <aside class="w-full lg:w-2/5 bg-primary-container text-on-primary flex flex-col justify-between items-center text-center relative overflow-hidden p-6 sm:p-8 lg:p-12">
            <x-auth.panel-header />
            <div class="my-auto py-space-lg relative z-10 flex flex-col items-center justify-center max-w-sm mx-auto">
                <div class="auth-mascot-stage relative mb-space-md flex items-center justify-center">
                    <div class="absolute w-60 h-60 rounded-full bg-secondary/20 blur-2xl pointer-events-none"></div>
                    <x-auth.mascot pose="7" class="relative object-contain source-drop-shadow-xl" /></div>
                <div class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-secondary/20 text-secondary-fixed text-label-sm font-label-sm mb-space-sm shadow-xs"><span class="material-symbols-outlined text-[16px]">waving_hand</span><span class="">Selamat Datang, Guru BK!</span></div>
                <h1 class="font-headline-lg text-headline-lg text-surface-container-lowest mb-space-xs text-center">Senang Bertemu denganmu!</h1>
                <p class="font-body-md text-body-md text-primary-fixed-dim text-center leading-relaxed">Mari siapkan ruang yang nyaman dan aman untuk mendengarkan serta mendampingi setiap suara siswa kita.</p></div>
            <x-auth.version />
        </aside>
        <main class="w-full lg:w-3/5 bg-surface-container-lowest flex flex-col justify-between min-h-screen px-margin-sm sm:px-margin lg:px-margin-lg py-margin">
            <div class="flex flex-col w-full min-h-full flex-1 justify-between">
                <!-- Top Auxiliary Navigation Row -->
                <div class="w-full flex items-center justify-between pb-space-lg mb-space-md border-b border-transparent">
                    <a class="inline-flex items-center gap-space-xs text-on-surface-variant hover:text-on-surface transition-colors font-label-md text-label-md group" href="{{ route('onboarding.role') }}">
                        <span class="material-symbols-outlined text-[18px] group-hover:-translate-x-1 transition-transform">arrow_back</span>
                        <span class="">Kembali ke Pilih Peran</span>
                    </a>
                    <div class="inline-flex items-center gap-space-xs text-on-surface-variant font-label-md text-label-md">

                    </div>
                </div>
                <!-- Centered Content Wrapper -->
                <div class="w-full max-w-xl mx-auto flex flex-col my-auto">
                    <!-- Active Role Indicator Pill -->

                    <!-- Page Header -->
                    <div class="mb-space-lg text-center">
                        <h1 class="font-headline-lg text-headline-lg text-primary-container tracking-tight mb-space-xs">
                            Pengaturan Unit Sekolah
                        </h1>
                        <p class="font-body-md text-body-md text-on-surface-variant">
                            Masukkan informasi sekolah untuk menghubungkan akun konseling Anda.
                        </p>
                    </div>
                    <!-- Form Section -->
                    <form class="flex flex-col gap-space-md" id="unitForm" method="POST" action="{{ route('frontend.unavailable', ['page' => 'school']) }}" data-page="school" data-next-url="{{ route('bk.dashboard') }}" x-on:submit.prevent="submitPreview($event)">
                        @csrf
                        <!-- 1. School Name Field -->
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-md text-label-md text-on-surface font-semibold flex items-center justify-between" for="schoolName">
                                <span class="">Nama Sekolah <span class="text-error">*</span></span>
                                <span class="text-outline text-label-sm font-normal">Sinkronisasi Instansi</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px] pointer-events-none">domain</span>
                                <input class="w-full pl-11 pr-4 py-3 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-xl shadow-xs outline-hidden focus:bg-surface-container-low transition-all" id="schoolName" name="school_name" placeholder="Contoh: SMA Negeri 1 Teladan" required="" type="text" value="SMA Negeri 1 Teladan" autocomplete="organization">
                            </div>
                            <span class="font-body-sm text-body-sm text-outline px-1">
                                Nama resmi sekolah sesuai DAPODIK / Kemenag.
                            </span>
                        </div>
                        <!-- 2. School Education Level Select -->
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-md text-label-md text-on-surface font-semibold" for="schoolLevel">
                                Jenjang Sekolah <span class="text-error">*</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-outline text-[20px] pointer-events-none">school</span>
                                <select class="w-full pl-11 pr-10 py-3 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-xl shadow-xs outline-hidden focus:bg-surface-container-low appearance-none cursor-pointer transition-all" id="schoolLevel" name="school_level" required="">
                                    <option value="sd">SD (Sekolah Dasar)</option>
                                    <option value="smp">SMP (Sekolah Menengah Pertama)</option>
                                    <option selected="" value="sma_smk">SMA/SMK (Sekolah Menengah Atas / Kejuruan)</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3.5 text-outline text-[20px] pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <!-- 3. School Physical Address Field -->
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-md text-label-md text-on-surface font-semibold" for="schoolAddress">
                                Alamat Sekolah <span class="text-error">*</span>
                            </label>
                            <div class="relative flex items-start">
                                <span class="material-symbols-outlined absolute left-3.5 top-3 text-outline text-[20px] pointer-events-none">location_on</span>
                                <textarea class="w-full pl-11 pr-4 py-2.5 bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-xl shadow-xs outline-hidden focus:bg-surface-container-low resize-none transition-all" id="schoolAddress" name="school_address" placeholder="Jl. Pendidikan No. 45, Kebayoran Baru, Jakarta Selatan" required="" rows="2" autocomplete="street-address">Jl. Pendidikan No. 45, Kebayoran Baru, Jakarta Selatan</textarea>
                            </div>
                        </div>
                        <!-- Info Callout: Security / Multi-tenancy Isolation -->

                        <!-- Action Button Group -->
                        <div class="flex flex-col gap-space-sm pt-space-sm">
                            <button class="w-full py-3.5 px-space-lg bg-primary-container hover:bg-black text-on-primary rounded-xl font-label-lg text-label-lg flex items-center justify-center gap-space-xs transition-all shadow-md hover:shadow-lg active:scale-[0.99] cursor-pointer" id="submitBtn" type="submit">
                                <span class="">Masuk Dashboard</span>
                                <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                            </button>

                        </div>
                    </form>
                    <!-- Bottom Secondary Info / Footer -->

                </div>
                <!-- Interactive Feedback Script -->

            </div>
    </main></div>
    </x-layouts.auth>
