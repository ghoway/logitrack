<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LogiTrack') }} - Solusi Pengiriman Terpercaya</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-100">

    {{-- Header --}}
    <header x-data="{ mobileOpen: false }" class="fixed top-0 inset-x-0 z-50 bg-white/80 dark:bg-gray-950/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl tracking-tight">
                    <svg class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <span class="text-gray-900 dark:text-white">Logi<span class="text-amber-600">Track</span></span>
                </a>

                {{-- Desktop Nav --}}
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-sm font-semibold text-amber-600 border-b-2 border-amber-600 pb-1">Beranda</a>
                    <a href="{{ route('tracking.index') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">Tracking</a>
                    @auth
                        <a href="/admin" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">Dashboard</a>
                    @else
                        <a href="/admin/login" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">Masuk</a>
                        <a href="/admin/register" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">Daftar</a>
                    @endauth
                </nav>

                {{-- Mobile Hamburger --}}
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" @click.outside="mobileOpen = false" x-cloak x-transition:enter="duration-200 ease-out" x-transition:enter-start="-translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" class="lg:hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
            <div class="px-4 py-4 space-y-3">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-amber-600 bg-amber-50 dark:bg-amber-900/20">Beranda</a>
                <a href="{{ route('tracking.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Tracking</a>
                @auth
                    <a href="/admin" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">Keluar</button>
                    </form>
                @else
                    <a href="/admin/login" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Masuk</a>
                    <a href="/admin/register" class="block text-center px-3 py-2 rounded-lg text-sm font-semibold bg-amber-600 text-white hover:bg-amber-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Hero --}}
    <section class="relative pt-28 lg:pt-36 pb-20 lg:pb-28 overflow-hidden">
        <div class="absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-gradient-to-br from-amber-50/50 via-white to-orange-50/30 dark:from-gray-900 dark:via-gray-950 dark:to-gray-900"></div>
            <div class="absolute top-0 -left-40 w-96 h-96 bg-amber-300/20 dark:bg-amber-500/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 -right-40 w-96 h-96 bg-orange-300/20 dark:bg-orange-500/5 rounded-full blur-3xl"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 mb-6">Logistik & Pengiriman</span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">
                    Kirim Paketmu dengan
                    <span class="text-amber-600">Aman & Terpercaya</span>
                </h1>
                <p class="mt-5 text-base sm:text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                    LogiTrack hadir untuk memudahkan pengiriman barang Anda ke seluruh Indonesia. Lacak status pengiriman secara real-time dan nikmati tarif kompetitif.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="/admin/register" class="inline-flex items-center px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-600/25 hover:shadow-amber-600/40 transition-all">Mulai Kirim Sekarang</a>
                    <a href="{{ route('tracking.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-semibold rounded-xl transition">Lacak Pengiriman</a>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="py-16 bg-gray-50 dark:bg-gray-900/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-3xl sm:text-4xl font-bold text-amber-600">{{ number_format($stats['shipments']) }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total Pengiriman</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl sm:text-4xl font-bold text-amber-600">{{ number_format($stats['delivered']) }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Berhasil Dikirim</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl sm:text-4xl font-bold text-amber-600">{{ number_format($stats['routes']) }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Rute Aktif</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl sm:text-4xl font-bold text-amber-600">{{ number_format($stats['customers']) }}</div>
                    <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pelanggan</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">Mengapa Memilih LogiTrack?</h2>
                <p class="mt-4 text-gray-500 dark:text-gray-400">Kami menyediakan layanan pengiriman terbaik dengan berbagai keunggulan.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lacak Real-Time</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Pantau posisi paket Anda setiap saat dengan sistem tracking yang akurat.</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Harga Kompetitif</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Dapatkan tarif terbaik dengan perhitungan biaya yang transparan dan akurat.</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aman & Terpercaya</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Setiap paket ditangani dengan prosedur keamanan ketat dan asuransi pengiriman.</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cakupan Luas</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Jaringan pengiriman mencakup berbagai kota besar di seluruh Indonesia.</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Estimasi Tepat</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Dapatkan estimasi waktu pengiriman yang akurat sesuai dengan rute yang dipilih.</p>
                </div>
                <div class="p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-lg transition">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Dukungan 24/7</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Tim support kami siap membantu Anda kapan saja melalui berbagai saluran komunikasi.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 bg-gradient-to-r from-amber-600 to-orange-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl font-bold text-white">Siap Mengirimkan Paket Anda?</h2>
            <p class="mt-4 text-lg text-amber-100">Daftar sekarang dan nikmati kemudahan pengiriman barang ke seluruh Indonesia.</p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/admin/register" class="inline-flex items-center px-6 py-3 bg-white text-amber-700 text-sm font-semibold rounded-xl shadow-lg hover:bg-amber-50 transition-all">Daftar Gratis</a>
                <a href="{{ route('tracking.index') }}" class="inline-flex items-center px-6 py-3 border border-white/40 text-white text-sm font-semibold rounded-xl hover:bg-white/10 transition">Lacak Pengiriman</a>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">Cara Kerja</h2>
                <p class="mt-4 text-gray-500 dark:text-gray-400">Hanya 3 langkah mudah untuk mengirimkan paket Anda.</p>
            </div>
            <div class="grid sm:grid-cols-3 gap-8 lg:gap-12">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <span class="text-2xl font-bold text-amber-600">1</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Akun</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Buat akun Anda secara gratis dan lengkapi data diri.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <span class="text-2xl font-bold text-amber-600">2</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Buat Pengiriman</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Isi detail paket, pilih rute, dan lakukan pembayaran.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <span class="text-2xl font-bold text-amber-600">3</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lacak & Terima</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Lacak pengiriman secara real-time hingga paket tiba.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 dark:bg-black text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="sm:col-span-2 lg:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-white">
                        <svg class="w-7 h-7 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        LogiTrack
                    </a>
                    <p class="mt-3 text-sm leading-relaxed">Solusi pengiriman barang terpercaya untuk memenuhi kebutuhan logistik Anda.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Layanan</h4>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li><a href="{{ route('tracking.index') }}" class="hover:text-white transition">Tracking</a></li>
                        <li><a href="#" class="hover:text-white transition">Cek Tarif</a></li>
                        <li><a href="#" class="hover:text-white transition">Pengiriman Darat</a></li>
                        <li><a href="#" class="hover:text-white transition">Pengiriman Laut</a></li>
                        <li><a href="#" class="hover:text-white transition">Pengiriman Udara</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Perusahaan</h4>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-white transition">Karir</a></li>
                        <li><a href="#" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Kontak</h4>
                    <ul class="mt-4 space-y-3 text-sm">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                            Jakarta, Indonesia
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                            hello@logitrack.id
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                            +62 21 1234 5678
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-gray-800 text-center text-sm">
                &copy; {{ date('Y') }} LogiTrack. All rights reserved.
            </div>
        </div>
    </footer>

    
</body>
</html>
