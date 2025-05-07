<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        {{-- <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" /> --}}
                        <img src="{{ asset('storage/image/pt.png') }}" alt="pt.png" class="h-8">

                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>


                <!-- Dropdown Menejemen Produk (Menggunakan Alpine.js) -->
                <div x-data="{ open: false }" class="hidden sm:-my-px sm:ms-10 sm:flex relative">
                    <button @click="open = !open" @click.away="open = false"
                        class="px-4 py-2 text-gray-700 hover:text-gray-900 focus:outline-none flex items-center text-sm font-medium">
                        User
                        <svg class="w-4 h-4 ml-2 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open"
                        class="absolute left-0 top-full mt-2 w-48 bg-white border rounded-md shadow-lg py-2 z-50">
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('permission.index')" :active="request()->routeIs('permission.index')">
                                {{ __('Permissions') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.index')">
                                {{ __('Roles') }}
                            </x-nav-link>
                        </div>
                        <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                            <x-nav-link :href="route('user.index')" :active="request()->routeIs('user.index')">
                                {{ __('User') }}
                            </x-nav-link>
                        </div>
                    </div>
                </div>
                <div x-data="{ open: false }" class="hidden sm:-my-px sm:ms-10 sm:flex relative">
                    <button @click="open = !open" @click.away="open = false"
                        class="px-4 py-2 text-gray-700 hover:text-gray-900 focus:outline-none flex items-center text-sm font-medium">
                        Menejemen Produk
                        <svg class="w-4 h-4 ml-2 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open"
                        class="absolute left-0 top-full mt-2 w-48 bg-white border rounded-md shadow-lg py-2 z-50">
                        <a href="{{ route('barang.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Nama Barang</a>
                        <a href="{{ route('kategori.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Kategori</a>
                        <a href="{{ route('satuan.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Satuan</a>
                        <a href="{{ route('produk.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Produk</a>
                    </div>
                </div>
                {{-- Trnasaksi Dropdown --}}
                <div x-data="{ open: false }" class="hidden sm:-my-px sm:ms-10 sm:flex relative">
                    <button @click="open = !open" @click.away="open = false"
                        class="px-4 py-2 text-gray-700 hover:text-gray-900 focus:outline-none flex items-center text-sm font-medium">
                        S&D
                        <svg class="w-4 h-4 ml-2 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open"
                        class="absolute left-0 top-full mt-2 w-48 bg-white border rounded-md shadow-lg py-2 z-50">
                        <a href="{{ route('supplier.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Supplier</a>
                        <a href="{{ route('costumer.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Costumer</a>
                    </div>
                </div>
                <div x-data="{ open: false }" class="hidden sm:-my-px sm:ms-10 sm:flex relative">
                    <button @click="open = !open" @click.away="open = false"
                        class="px-4 py-2 text-gray-700 hover:text-gray-900 focus:outline-none flex items-center text-sm font-medium">
                        PO
                        <svg class="w-4 h-4 ml-2 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open"
                        class="absolute left-0 top-full mt-2 w-48 bg-white border rounded-md shadow-lg py-2 z-50">
                        <a href="{{ route('polaporan.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">LPO</a>
                        {{-- <a href="{{ route('penjualan.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Penjualan</a> --}}

                    </div>
                </div>
                <div x-data="{ open: false }" class="hidden sm:-my-px sm:ms-10 sm:flex relative">
                    <button @click="open = !open" @click.away="open = false"
                        class="px-4 py-2 text-gray-700 hover:text-gray-900 focus:outline-none flex items-center text-sm font-medium">
                        Transaksi
                        <svg class="w-4 h-4 ml-2 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open"
                        class="absolute left-0 top-full mt-2 w-48 bg-white border rounded-md shadow-lg py-2 z-50">
                        <a href="{{ route('pesanan.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Pesanan Masuk</a>
                        <a href="{{ route('pembelian.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Pembelian</a>
                        <a href="{{ route('stok.report.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Report Pembelian</a>
                        <a href="{{ route('penjualan.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Penjualan</a>
                        <a href="{{ route('laporan.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Laporan</a>
                        <a href="{{ route('laporan.report_stock') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Report Stok</a>
                    </div>
                </div>
                <div x-data="{ open: false }" class="hidden sm:-my-px sm:ms-10 sm:flex relative">
                    <button @click="open = !open" @click.away="open = false"
                        class="px-4 py-2 text-gray-700 hover:text-gray-900 focus:outline-none flex items-center text-sm font-medium">
                        Laporan
                        <svg class="w-4 h-4 ml-2 transition-transform duration-200" :class="{ 'rotate-180': open }"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open"
                        class="absolute left-0 top-full mt-2 w-48 bg-white border rounded-md shadow-lg py-2 z-50">
                        <a href="{{ route('stok.report.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Report Pembelian</a>
                        <a href="{{ route('laporan.stockout_report') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Report Penjualan</a>
                        <a href="{{ route('laporan.index') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Laporan</a>
                        <a href="{{ route('laporan.report_stock') }}"
                            class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Report Stok</a>
                    </div>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
