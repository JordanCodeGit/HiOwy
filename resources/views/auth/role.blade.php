<x-layouts.auth title="Pilih Peran" body-class="bg-background font-body-md text-on-surface min-h-screen" :medium-fonts="false">
    <div class="min-h-screen w-full flex flex-col lg:flex-row">
        <aside class="w-full lg:w-2/5 bg-primary-container text-on-primary flex flex-col justify-between p-6 sm:p-8 lg:p-12">
            <div class="flex flex-col flex-1 justify-between">
                <x-auth.panel-header />
                <div class="flex flex-col items-center justify-center text-center my-auto py-space-lg">
                    <div class="auth-mascot-stage relative flex items-center justify-center mb-6"><x-auth.mascot pose="9" class="object-contain source-drop-shadow-lg" /></div>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-surface-container-lowest/10 backdrop-blur-xl text-secondary-fixed font-label-md text-label-md font-semibold mb-4 tracking-wide shadow-xs"><span class="material-symbols-outlined text-[16px]">auto_awesome</span><span class="">Selamat Datang di HiOwy!</span></div>
                    <h1 class="font-headline-lg text-headline-lg text-on-primary tracking-tight mb-3 leading-tight">Yuk! Pilih Peran Kamu</h1>
                    <p class="font-body-md text-body-md text-on-primary-container leading-relaxed max-w-sm">Pilih peran akunmu untuk mulai mendampingi, melindungi, dan menciptakan ruang yang aman bagi setiap suara siswa.</p></div>
            </div>
            <x-auth.version /></aside>
        <div class="w-full lg:w-3/5 bg-background flex flex-col justify-between min-h-screen">
            <header class="h-16 w-full px-margin lg:px-margin-lg flex items-center justify-between bg-surface/80 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
                <div class="flex items-center gap-space-md"></div>
            </header>
            <main class="flex-1 w-full px-margin lg:px-margin-lg py-space-xl flex flex-col justify-center">
                <div class="flex flex-col w-full">
                    <div class="w-full max-w-4xl mx-auto flex flex-col gap-space-lg">
                        <!-- Top Context & Triage Status Bar -->

                        <!-- Heading Hierarchy -->
                        <div class="flex flex-col gap-space-xs">
                            <h2 class="font-headline-lg text-headline-lg text-on-surface tracking-tight text-center">Pilih Peran Akun Anda</h2>
                        </div>
                        <!-- Interactive Selectable Role Cards -->
                        <div class="grid grid-cols-1 gap-space-md" id="role-selection-group" role="radiogroup" aria-label="Peran akun"><!-- Option 1: Guru Bimbingan Konseling (Default Active) -->
                            <div class="role-card relative p-space-lg rounded-xl shadow-xs transition-all duration-200 cursor-pointer hover:bg-surface-container-lowest/90 hover:shadow-md" id="role-gurubk" role="radio" tabindex="0" x-bind:aria-checked="selectedRole === 'bk'" x-on:click="selectedRole = 'bk'" x-on:keydown.space.prevent="selectedRole = 'bk'" x-on:keydown.enter.prevent="selectedRole = 'bk'" x-on:keydown.arrow-down.prevent="selectAdjacentRole('bk', 1)" x-on:keydown.arrow-up.prevent="selectAdjacentRole('bk', -1)" x-bind:class="selectedRole === 'bk' ? 'ring-2 ring-secondary bg-surface-container-low/40' : 'bg-surface-container-lowest'">
                                <div class="flex items-start justify-between gap-space-md">
                                    <div class="flex items-start gap-space-md">
                                        <div class="w-12 h-12 rounded-xl bg-secondary text-on-secondary flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[26px]">volunteer_activism</span>
                                        </div>
                                        <div class="flex flex-col gap-space-xs">
                                            <span class="font-title-lg text-title-lg text-on-surface font-semibold">Guru Bimbingan Konseling (BK)</span>
                                            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                                                Menangani konseling siswa, chat 2 arah rahasia, dan pendampingan kasus psikologis.
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Radio Indicator -->
                                    <div class="radio-indicator shrink-0 w-6 h-6 rounded-full flex items-center justify-center mt-1 text-on-primary" x-bind:class="selectedRole === 'bk' ? 'bg-secondary text-white' : 'bg-surface-container-high text-transparent'">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Option 2: Tim Kesiswaan -->
                            <div class="role-card relative p-space-lg rounded-xl shadow-xs transition-all duration-200 cursor-pointer hover:bg-surface-container-lowest/90 hover:shadow-md" id="role-kesiswaan" role="radio" tabindex="0" x-bind:aria-checked="selectedRole === 'kesiswaan'" x-on:click="selectedRole = 'kesiswaan'" x-on:keydown.space.prevent="selectedRole = 'kesiswaan'" x-on:keydown.enter.prevent="selectedRole = 'kesiswaan'" x-on:keydown.arrow-down.prevent="selectAdjacentRole('kesiswaan', 1)" x-on:keydown.arrow-up.prevent="selectAdjacentRole('kesiswaan', -1)" x-bind:class="selectedRole === 'kesiswaan' ? 'ring-2 ring-secondary bg-surface-container-low/40' : 'bg-surface-container-lowest'">
                                <div class="flex items-start justify-between gap-space-md">
                                    <div class="flex items-start gap-space-md">
                                        <div class="w-12 h-12 rounded-xl bg-surface-container-high text-on-surface flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[26px]">shield</span>
                                        </div>
                                        <div class="flex flex-col gap-space-xs">
                                            <span class="font-title-lg text-title-lg text-on-surface font-semibold">Tim Kesiswaan</span>
                                            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                                                Menangani ketertiban sekolah, mediasi insiden fisik, dan penegakan disiplin.
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Radio Indicator -->
                                    <div class="radio-indicator shrink-0 w-6 h-6 rounded-full flex items-center justify-center mt-1" x-bind:class="selectedRole === 'kesiswaan' ? 'bg-secondary text-white' : 'bg-surface-container-high text-transparent'">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Option 3: Wali Kelas -->
                            <div class="role-card relative p-space-lg rounded-xl shadow-xs transition-all duration-200 cursor-pointer hover:bg-surface-container-lowest/90 hover:shadow-md" id="role-walikelas" role="radio" tabindex="0" x-bind:aria-checked="selectedRole === 'wali_kelas'" x-on:click="selectedRole = 'wali_kelas'" x-on:keydown.space.prevent="selectedRole = 'wali_kelas'" x-on:keydown.enter.prevent="selectedRole = 'wali_kelas'" x-on:keydown.arrow-down.prevent="selectAdjacentRole('wali_kelas', 1)" x-on:keydown.arrow-up.prevent="selectAdjacentRole('wali_kelas', -1)" x-bind:class="selectedRole === 'wali_kelas' ? 'ring-2 ring-secondary bg-surface-container-low/40' : 'bg-surface-container-lowest'">
                                <div class="flex items-start justify-between gap-space-md">
                                    <div class="flex items-start gap-space-md">
                                        <div class="w-12 h-12 rounded-xl bg-surface-container-high text-on-surface flex items-center justify-center shrink-0">
                                            <span class="material-symbols-outlined text-[26px]">groups</span>
                                        </div>
                                        <div class="flex flex-col gap-space-xs">
                                            <span class="font-title-lg text-title-lg text-on-surface font-semibold">Wali Kelas</span>
                                            <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                                                Memantau iklim kelas binaan dan menerima ringkasan penanganan dari BK.
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Radio Indicator -->
                                    <div class="radio-indicator shrink-0 w-6 h-6 rounded-full flex items-center justify-center mt-1" x-bind:class="selectedRole === 'wali_kelas' ? 'bg-secondary text-white' : 'bg-surface-container-high text-transparent'">
                                        <span class="material-symbols-outlined text-[16px]">check</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Interactive Role Protocol Callout -->

                        <!-- Action Controls -->
                        <div class="pt-space-sm flex flex-col sm:flex-row items-center justify-between gap-space-md">
                            <a class="w-full sm:w-auto px-space-lg py-3 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high font-label-lg text-label-lg transition-colors flex items-center justify-center gap-2 order-2 sm:order-1" href="{{ route('login') }}">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                Kembali ke Login
                            </a>
                            <button class="w-full sm:w-auto px-space-xl py-3.5 rounded-lg bg-primary text-on-primary hover:bg-primary-container font-label-lg text-label-lg font-bold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 order-1 sm:order-2" id="continue-button" x-on:click="continueRole($el.dataset.schoolUrl, $el.dataset.kesiswaanUrl)" data-school-url="{{ route('onboarding.school') }}" data-kesiswaan-url="{{ route('kesiswaan.dashboard') }}" type="button">
                                <span id="btn-text" class="" x-text="roleButtonLabel">Lanjutkan sebagai Guru BK →</span>
                            </button>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="h-16 w-full px-margin lg:px-margin-lg flex items-center justify-between text-on-surface-variant font-body-sm text-body-sm"></footer>
    </div></div>
    </x-layouts.auth>
