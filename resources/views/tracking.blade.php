<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Tracking - {{ config('app.name', 'LogiTrack') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col">

    {{-- Header --}}
    <header x-data="{ mobileOpen: false }" class="fixed top-0 inset-x-0 z-50 bg-white/80 dark:bg-gray-950/80 backdrop-blur-sm border-b border-gray-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 lg:h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl tracking-tight">
                    <svg class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <span class="text-gray-900 dark:text-white">Logi<span class="text-amber-600">Track</span></span>
                </a>
                <nav class="hidden lg:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">Beranda</a>
                    <a href="{{ route('tracking.index') }}" class="text-sm font-semibold text-amber-600 border-b-2 border-amber-600 pb-1">Tracking</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">Masuk</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">Daftar</a>
                    @endauth
                </nav>
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        <div x-show="mobileOpen" @click.outside="mobileOpen = false" x-cloak x-transition:enter="duration-200 ease-out" x-transition:enter-start="-translate-y-2 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" class="lg:hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950">
            <div class="px-4 py-4 space-y-3">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Beranda</a>
                <a href="{{ route('tracking.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-amber-600 bg-amber-50 dark:bg-amber-900/20">Tracking</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="block text-center px-3 py-2 rounded-lg text-sm font-semibold bg-amber-600 text-white hover:bg-amber-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Tracking Content --}}
    <main class="flex-1 pt-24 lg:pt-28 pb-16">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Lacak Pengiriman</h1>
                <p class="mt-2 text-gray-500 dark:text-gray-400">Masukkan nomor tracking untuk mengetahui status pengiriman Anda.</p>
            </div>

            {{-- Search Form --}}
            <form method="GET" action="{{ route('tracking.track') }}" class="flex gap-3">
                <input type="text" name="tracking_number" value="{{ request('tracking_number') }}" placeholder="Masukkan nomor tracking (contoh: ID-20260629ABCD)" class="flex-1 px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition text-sm">
                <button type="submit" class="px-6 py-3 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-600/25 transition whitespace-nowrap">Lacak</button>
            </form>

            {{-- Results --}}
            @isset($shipment)
                <div class="mt-10 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm divide-y divide-gray-200 dark:divide-gray-800">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pengiriman</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($shipment->status === 'delivered') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300
                                @elseif($shipment->status === 'in_transit') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300
                                @elseif($shipment->status === 'picked_up') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300
                                @else bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $shipment->status)) }}
                            </span>
                        </div>
                        <div class="text-2xl font-bold tracking-wider text-gray-900 dark:text-white">{{ $shipment->tracking_number }}</div>
                    </div>
                    <div class="p-6 grid sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Pengirim</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->sender->name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Penerima</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->receiver_name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Rute</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->rate->route->origin }} → {{ $shipment->rate->route->destination }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Layanan</span>
                            <p class="font-medium text-gray-900 dark:text-white capitalize">{{ $shipment->rate->type }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Berat Tagihan</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->chargeable_weight }} kg</p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Estimasi Sampai</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $shipment->rate->estimated_days }} hari kerja</p>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Total Biaya</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($shipment->total_shipping_fee, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @elseif(request('tracking_number'))
                <div class="mt-10 p-6 bg-red-50 dark:bg-red-900/20 rounded-2xl border border-red-200 dark:border-red-800 text-center">
                    <p class="text-red-600 dark:text-red-400 font-medium">Pengiriman dengan nomor "{{ request('tracking_number') }}" tidak ditemukan.</p>
                    <p class="mt-1 text-sm text-red-500 dark:text-red-400">Periksa kembali nomor tracking Anda.</p>
                </div>
            @endisset
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-900 dark:bg-black text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-sm">
            &copy; {{ date('Y') }} LogiTrack. All rights reserved.
        </div>
    </footer>

    
</body>
</html>
