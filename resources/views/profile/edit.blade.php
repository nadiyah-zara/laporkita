<x-public-layout title="Ubah Profil - LaporKita">

    <main class="min-h-screen bg-[#f4f7fb] pb-16">

        {{-- ============================================================= --}}
        {{-- 1. COVER BANNER (Otomatis menampilkan Cover yang diunggah)    --}}
        {{-- ============================================================= --}}
        <div class="relative h-48 sm:h-64 overflow-hidden bg-gradient-to-r from-[#06182e] via-[#0b2b52] to-[#1d4ed8]">
            @if ($user->cover_photo)
            <img src="{{ asset('storage/' . $user->cover_photo) }}" alt="Cover" class="absolute inset-0 h-full w-full object-cover" />
            @else
            <div class="pointer-events-none absolute -right-20 -top-20 h-80 w-80 rounded-full bg-blue-400/20 blur-3xl"></div>
            <div class="pointer-events-none absolute left-10 bottom-0 h-40 w-40 rounded-full bg-blue-600/20 blur-2xl"></div>
            @endif
        </div>

        {{-- ============================================================= --}}
        {{-- 2. HEADER INFO PROFIL (AVATAR BUNDAR OVERLAPPING BANNER)      --}}
        {{-- ============================================================= --}}
        <div class="mx-auto max-w-7xl px-6">
            <div class="relative -mt-16 sm:-mt-20 flex flex-col sm:flex-row items-center sm:items-end justify-between gap-4 border-b border-slate-200 pb-8">

                <div class="flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
                    {{-- Foto Avatar Profil --}}
                    <div class="relative">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0c203b&color=ffffff&bold=true&size=160' }}"
                            alt="{{ $user->name }}"
                            class="h-28 w-28 sm:h-32 sm:w-32 rounded-full border-4 border-white bg-white object-cover shadow-xl" />
                        <span class="absolute bottom-1 right-2 h-5 w-5 rounded-full border-2 border-white bg-emerald-500" title="Akun Aktif"></span>
                    </div>

                    <div class="pt-2 sm:pt-0">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[#0c203b]">
                                {{ $user->name }}
                            </h1>
                            <span class="inline-flex items-center gap-1 rounded-full border border-blue-100 bg-blue-50 px-2.5 py-0.5 text-xs font-bold text-blue-700">
                                {{ ucfirst($user->role ?? 'Masyarakat') }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs sm:text-sm text-slate-500">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-500 shadow-sm">
                        Anggota sejak {{ $user->created_at ? $user->created_at->format('M Y') : '2026' }}
                    </span>
                </div>

            </div>
        </div>

        {{-- ============================================================= --}}
        {{-- 3. CONTAINER FORM PROFIL LENGKAP                              --}}
        {{-- ============================================================= --}}
        <div class="mx-auto max-w-7xl px-6 py-10" x-data="{ tab: 'profil' }">
            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-12">

                {{-- SIDEBAR TABS KIRI --}}
                <div class="space-y-1.5 rounded-2xl border border-slate-100 bg-white p-3 shadow-sm lg:col-span-3">
                    <button type="button"
                        @click="tab = 'profil'"
                        :class="tab === 'profil' ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600' : 'text-slate-600 hover:bg-slate-50 font-semibold'"
                        class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-xs transition-all sm:text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Ubah Profil</span>
                    </button>

                    <button type="button"
                        @click="tab = 'password'"
                        :class="tab === 'password' ? 'bg-blue-50 text-blue-700 font-bold border-l-4 border-blue-600' : 'text-slate-600 hover:bg-slate-50 font-semibold'"
                        class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-xs transition-all sm:text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Ubah Password</span>
                    </button>

                    <button type="button"
                        @click="tab = 'hapus'"
                        :class="tab === 'hapus' ? 'bg-rose-50 text-rose-700 font-bold border-l-4 border-rose-600' : 'text-slate-600 hover:bg-slate-50 font-semibold'"
                        class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-xs transition-all sm:text-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Hapus Akun</span>
                    </button>
                </div>

                {{-- KONTEN UTAMA KANAN --}}
                <div class="lg:col-span-9 space-y-6">

                    {{-- ===================== TAB 1: UBAH PROFIL LENGKAP ===================== --}}
                    <div x-show="tab === 'profil'"
                        x-data="{
                            avatarPreview: null,
                            coverPreview: null
                         }"
                        class="space-y-8 rounded-3xl border border-slate-100 bg-white p-7 shadow-xl shadow-slate-200/50 sm:p-10">

                        {{-- ALERT KUNING LENGKAPI PROFIL (SESUAI GAMBAR 2) --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs shadow-sm">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-amber-500 font-bold text-white text-base">
                                    !
                                </div>
                                <p class="leading-relaxed text-amber-900">
                                    <strong class="font-bold">Mohon Lengkapi Profil Anda</strong> terlebih dahulu untuk mengonfirmasi email <span class="font-bold">{{ $user->email }}</span> dan nomor telepon Anda, serta melindungi akun dan mengakses seluruh fitur layanan LaporKita.
                                </p>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">Ubah Profil</h2>
                            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Kelola informasi pribadi, identitas diri, dan data kontak akun Anda.</p>
                        </div>

                        @if (session('status') === 'profile-updated')
                        <div class="flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-xs font-semibold text-emerald-800 shadow-sm">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Perubahan profil Anda berhasil disimpan!</span>
                        </div>
                        @endif

                        {{-- KOTAK VERIFIKASI AKUN (SESUAI GAMBAR 1) --}}
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 rounded-2xl border border-blue-100 bg-blue-50/50 p-5">
                            <div>
                                <h3 class="text-sm font-bold text-[#0c203b]">Verifikasi Akun</h3>
                                <p class="mt-0.5 text-xs text-slate-500">Demi menjaga keamanan informasi Anda, lakukan verifikasi alamat email.</p>
                            </div>
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="inline-flex shrink-0 items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-blue-700">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span>VERIFIKASI EMAIL</span>
                                </button>
                            </form>
                            @else
                            <span class="inline-flex items-center gap-1 rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800">
                                ✓ Email Terverifikasi
                            </span>
                            @endif
                        </div>

                        {{-- FORM UTAMA DENGAN ENCTYPE MULTIPART (UNTUK FOTO) --}}
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-8">
                            @csrf
                            @method('patch')

                            {{-- ==================== BAGIAN 1: INFORMASI PUBLIK ==================== --}}
                            <div class="space-y-6">
                                <div class="border-b border-slate-100 pb-2">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#0c203b]">Informasi Publik</h3>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Nama Lengkap <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10 @error('name') border-red-500 @enderror" />
                                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Username
                                        </label>
                                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" placeholder="Contoh: deagoyes"
                                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10 @error('username') border-red-500 @enderror" />
                                        @error('username') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                        Bio Singkat
                                    </label>
                                    <textarea id="bio" name="bio" rows="3" placeholder="Tuliskan deskripsi singkat mengenai diri Anda..."
                                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10">{{ old('bio', $user->bio) }}</textarea>
                                </div>

                                {{-- UPLOAD FOTO PROFIL (152x152) & GAMBAR SAMPUL (1440x350) --}}
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start pt-2">

                                    {{-- FOTO PROFIL --}}
                                    <div class="md:col-span-4" x-data="{ avatarPreview: '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}' }">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Foto Profil <span class="text-[11px] font-normal text-slate-400">(152x152)</span>
                                        </label>

                                        {{-- Input File Tetap Ada di Form (Permanen) --}}
                                        <input type="file"
                                            name="avatar"
                                            x-ref="avatarInput"
                                            class="hidden"
                                            accept="image/*"
                                            @change="
                                                   const file = $event.target.files[0];
                                                   if(file) {
                                                       const r = new FileReader();
                                                       r.onload = (e) => { avatarPreview = e.target.result; };
                                                       r.readAsDataURL(file);
                                                   }
                                               ">

                                        <div class="mt-2 relative">
                                            {{-- Kotak Klik Upload / Preview --}}
                                            <div @click="$refs.avatarInput.click()"
                                                class="flex flex-col items-center justify-center w-36 h-36 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:border-blue-500 hover:bg-blue-50/30 transition-all bg-slate-50/50 overflow-hidden relative group">

                                                <template x-if="avatarPreview">
                                                    <img :src="avatarPreview" class="w-full h-full object-cover" alt="Foto Profil" />
                                                </template>

                                                <template x-if="!avatarPreview">
                                                    <div class="text-center">
                                                        <span class="text-3xl text-slate-400 font-light">+</span>
                                                        <span class="block text-[11px] text-slate-400 font-medium mt-1">Pilih Foto</span>
                                                    </div>
                                                </template>

                                                {{-- Overlay Ubah Foto Saat Kursor Diarahkan --}}
                                                <div x-show="avatarPreview"
                                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-[11px] font-bold">
                                                    Ganti Foto
                                                </div>
                                            </div>

                                            {{-- Tombol Batal Ganti Foto --}}
                                            <button type="button"
                                                x-show="avatarPreview && avatarPreview !== '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}'"
                                                @click="avatarPreview = '{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}'; $refs.avatarInput.value = ''"
                                                class="absolute -top-2 -right-2 bg-rose-600 text-white rounded-full p-1 shadow-md hover:bg-rose-700">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        @error('avatar') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    {{-- GAMBAR SAMPUL --}}
                                    <div class="md:col-span-8" x-data="{ coverPreview: '{{ $user->cover_photo ? asset('storage/' . $user->cover_photo) : '' }}' }">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Gambar Sampul <span class="text-[11px] font-normal text-slate-400">(1440x350)</span>
                                        </label>

                                        {{-- Input File Tetap Ada di Form (Permanen) --}}
                                        <input type="file"
                                            name="cover_photo"
                                            x-ref="coverInput"
                                            class="hidden"
                                            accept="image/*"
                                            @change="
                                                   const file = $event.target.files[0];
                                                   if(file) {
                                                       const r = new FileReader();
                                                       r.onload = (e) => { coverPreview = e.target.result; };
                                                       r.readAsDataURL(file);
                                                   }
                                               ">

                                        <div class="mt-2 relative">
                                            <div @click="$refs.coverInput.click()"
                                                class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:border-blue-500 hover:bg-blue-50/30 transition-all bg-slate-50/50 overflow-hidden relative group">

                                                <template x-if="coverPreview">
                                                    <img :src="coverPreview" class="w-full h-full object-cover" alt="Sampul" />
                                                </template>

                                                <template x-if="!coverPreview">
                                                    <div class="text-center">
                                                        <span class="text-3xl text-slate-400 font-light">+</span>
                                                        <span class="block text-[11px] text-slate-400 font-medium mt-1">Unggah Banner Sampul Profil</span>
                                                    </div>
                                                </template>

                                                <div x-show="coverPreview"
                                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity text-white text-xs font-bold">
                                                    Ganti Sampul
                                                </div>
                                            </div>

                                            <button type="button"
                                                x-show="coverPreview && coverPreview !== '{{ $user->cover_photo ? asset('storage/' . $user->cover_photo) : '' }}'"
                                                @click="coverPreview = '{{ $user->cover_photo ? asset('storage/' . $user->cover_photo) : '' }}'; $refs.coverInput.value = ''"
                                                class="absolute -top-2 -right-2 bg-rose-600 text-white rounded-full p-1 shadow-md hover:bg-rose-700">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        @error('cover_photo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                </div>
                            </div>

                            {{-- ==================== BAGIAN 2: DATA PRIBADI (SESUAI GAMBAR 2 & 3) ==================== --}}
                            <div class="space-y-6 pt-6">
                                <div class="border-b border-slate-100 pb-2">
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-[#0c203b]">Data Pribadi & Kontak</h3>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Alamat Email <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10 @error('email') border-red-500 @enderror" />
                                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            No Handphone <span class="text-rose-500">*</span>
                                        </label>
                                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 082267177099"
                                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10 @error('phone') border-red-500 @enderror" />
                                        @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label for="birth_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Tanggal Lahir
                                        </label>
                                        <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->birth_date) }}"
                                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10" />
                                    </div>

                                    <div>
                                        <label for="nik" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            No. Induk Kependudukan (NIK KTP)
                                        </label>
                                        <input type="text" id="nik" name="nik" value="{{ old('nik', $user->nik) }}" maxlength="16" placeholder="16 Digit NIK KTP Anda"
                                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10" />
                                    </div>
                                </div>

                                {{-- RADIO JENIS KELAMIN --}}
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                        Jenis Kelamin
                                    </label>
                                    <div class="flex items-center gap-6">
                                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                            <input type="radio" name="gender" value="laki-laki" {{ old('gender', $user->gender) === 'laki-laki' ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500" />
                                            <span>Laki-laki</span>
                                        </label>
                                        <label class="inline-flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                                            <input type="radio" name="gender" value="perempuan" {{ old('gender', $user->gender) === 'perempuan' ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500" />
                                            <span>Perempuan</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="occupation" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Pekerjaan
                                        </label>
                                        <select id="occupation" name="occupation" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10">
                                            <option value="">Pilih Pekerjaan</option>
                                            <option value="PNS" {{ old('occupation', $user->occupation) === 'PNS' ? 'selected' : '' }}>Pegawai Negeri Sipil (PNS)</option>
                                            <option value="Karyawan Swasta" {{ old('occupation', $user->occupation) === 'Karyawan Swasta' ? 'selected' : '' }}>Karyawan Swasta</option>
                                            <option value="Wiraswasta" {{ old('occupation', $user->occupation) === 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta / Pengusaha</option>
                                            <option value="Mahasiswa" {{ old('occupation', $user->occupation) === 'Mahasiswa' ? 'selected' : '' }}>Pelajar / Mahasiswa</option>
                                            <option value="Lainnya" {{ old('occupation', $user->occupation) === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="disability" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Penyandang Disabilitas?
                                        </label>
                                        <select id="disability" name="disability" class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10">
                                            <option value="Tidak" {{ old('disability', $user->disability) === 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                            <option value="Ya" {{ old('disability', $user->disability) === 'Ya' ? 'selected' : '' }}>Ya</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                        Tempat Tinggal Saat Ini (Kota / Kabupaten)
                                    </label>
                                    <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}" placeholder="Contoh: Kota Medan, Sumatera Utara"
                                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10" />
                                </div>

                                <div>
                                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                        Alamat Lengkap (Address)
                                    </label>
                                    <textarea id="address" name="address" rows="3" placeholder="Jl. Melati No. 12, RT 02 / RW 05, Kelurahan..."
                                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10">{{ old('address', $user->address) }}</textarea>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="postal_code" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Kode Pos (Postal Code)
                                        </label>
                                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" placeholder="Contoh: 20155"
                                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10" />
                                    </div>

                                    <div>
                                        <label for="website" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                            Website / Media Sosial
                                        </label>
                                        <input type="url" id="website" name="website" value="{{ old('website', $user->website) }}" placeholder="https://..."
                                            class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10" />
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <label class="inline-flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="extra_contact_confirmed" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" />
                                        <span class="text-xs text-slate-600 font-medium">Apakah Anda ingin menambahkan pihak yang dapat dikonfirmasi?</span>
                                    </label>
                                </div>

                            </div>

                            {{-- TOMBOL SIMPAN (BIRU SAMUDRA) --}}
                            <div class="flex justify-end pt-6 border-t border-slate-100">
                                <button type="submit" class="rounded-xl bg-blue-600 px-9 py-3.5 text-sm font-bold uppercase tracking-wider text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-700 hover:shadow-blue-600/40">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ===================== TAB 2: UBAH PASSWORD ===================== --}}
                    <div x-show="tab === 'password'" x-cloak class="space-y-6 rounded-3xl border border-slate-100 bg-white p-7 shadow-xl shadow-slate-200/50 sm:p-10">
                        <div>
                            <h2 class="text-2xl font-extrabold tracking-tight text-[#0c203b]">Ubah Password</h2>
                            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Gunakan kata sandi yang kuat dan acak untuk menjaga keamanan akun Anda.</p>
                        </div>

                        @if (session('status') === 'password-updated')
                        <div class="flex items-center gap-2 rounded-xl border border-emerald-100 bg-emerald-50 p-4 text-xs font-semibold text-emerald-800 shadow-sm">
                            <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Password berhasil diperbarui!</span>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}" class="max-w-xl space-y-5">
                            @csrf
                            @method('put')

                            <div>
                                <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Kata Sandi Saat Ini</label>
                                <input type="password" id="current_password" name="current_password" required placeholder="Masukkan kata sandi lama Anda"
                                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10 @error('current_password', 'updatePassword') border-red-500 @enderror" />
                                @error('current_password', 'updatePassword') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="update_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Kata Sandi Baru</label>
                                <input type="password" id="update_password" name="password" required placeholder="Minimal 8 karakter"
                                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10 @error('password', 'updatePassword') border-red-500 @enderror" />
                                @error('password', 'updatePassword') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Konfirmasi Kata Sandi Baru</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ketik ulang kata sandi baru"
                                    class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-blue-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-600/10" />
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="rounded-xl bg-blue-600 px-7 py-3.5 text-sm font-bold uppercase tracking-wider text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-700">
                                    Perbarui Password
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ===================== TAB 3: HAPUS AKUN ===================== --}}
                    <div x-show="tab === 'hapus'" x-cloak class="space-y-6 rounded-3xl border border-rose-100 bg-white p-7 shadow-xl shadow-slate-200/50 sm:p-10">
                        <div>
                            <h2 class="text-2xl font-extrabold tracking-tight text-rose-600">Hapus Akun</h2>
                            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Setelah akun Anda dihapus, semua data dan riwayat laporan Anda akan dihapus secara permanen.</p>
                        </div>

                        <div class="rounded-2xl border border-rose-100 bg-rose-50 p-5 text-xs leading-relaxed text-rose-700">
                            Tindakan ini tidak dapat dibatalkan. Pastikan semua laporan aduan Anda sudah selesai sebelum menghapus akun ini.
                        </div>

                        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun Anda secara permanen?');">
                            @csrf
                            @method('delete')

                            <div class="max-w-md space-y-4">
                                <div>
                                    <label for="delete_password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">Masukkan Password untuk Konfirmasi</label>
                                    <input type="password" id="delete_password" name="password" required placeholder="Kata sandi akun Anda"
                                        class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 p-3.5 text-sm text-slate-800 focus:border-rose-600 focus:bg-white focus:outline-none focus:ring-4 focus:ring-rose-600/10" />
                                </div>
                                <button type="submit" class="rounded-xl bg-rose-600 px-6 py-3.5 text-sm font-bold uppercase tracking-wider text-white shadow-lg shadow-rose-600/30 transition-all hover:bg-rose-700">
                                    Hapus Akun Saya Permanen
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>

    </main>

</x-public-layout>