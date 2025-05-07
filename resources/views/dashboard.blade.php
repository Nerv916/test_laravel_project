<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-8">

                    <!-- Penjualan -->
                    <div>
                        <label class="block font-semibold mb-1">Filter Penjualan:</label>
                        <select id="filterPenjualan" class="border p-2 rounded">
                            <option value="harian">Harian</option>
                            <option value="mingguan">Mingguan</option>
                            <option value="bulanan" selected>Bulanan</option>
                        </select>
                        <div class="w-full max-w-4xl mx-auto h-96 relative">
                            <div id="loadingPenjualan"
                                class="absolute inset-0 flex items-center justify-center bg-white/70 z-10 hidden">
                                <span class="text-gray-500">Loading...</span>
                            </div>
                            <canvas id="lineChartPenjualan" class="absolute inset-0 z-0"></canvas>
                        </div>
                    </div>

                    <!-- Barang Terlaris -->
                    <div>
                        <label class="block font-semibold mb-1">Barang Terlaris (Top 10):</label>
                        <div class="w-full max-w-4xl mx-auto h-96 relative">
                            <div id="loadingBarang"
                                class="absolute inset-0 flex items-center justify-center bg-white/70 z-10 hidden">
                                <span class="text-gray-500">Loading...</span>
                            </div>
                            <canvas id="chartBarangTerlaris" class="absolute inset-0 z-0"></canvas>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filterPenjualan = document.getElementById('filterPenjualan');
        const loadingPenjualan = document.getElementById('loadingPenjualan');
        const loadingBarang = document.getElementById('loadingBarang');

        let chartPenjualan;
        let chartBarang;

        function showLoading(el) {
            el.classList.remove('hidden');
        }

        function hideLoading(el) {
            el.classList.add('hidden');
        }

        function initCharts() {
            const ctxLine = document.getElementById('lineChartPenjualan').getContext('2d');
            const ctxBar = document.getElementById('chartBarangTerlaris').getContext('2d');

            if (chartPenjualan) chartPenjualan.destroy();
            if (chartBarang) chartBarang.destroy();

            chartPenjualan = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Total Penjualan',
                        data: [],
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            chartBarang = new Chart(ctxBar, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Total Terjual',
                        data: [],
                        backgroundColor: 'rgba(153, 102, 255, 0.5)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function fetchPenjualan() {
            showLoading(loadingPenjualan);
            fetch(`/penjualan-periode?filter=${filterPenjualan.value}`)
                .then(res => res.json())
                .then(data => {
                    const penjualan = data.penjualan || [];
                    chartPenjualan.data.labels = penjualan.map(i => i.tanggal);
                    chartPenjualan.data.datasets[0].data = penjualan.map(i => parseFloat(i.total));
                    chartPenjualan.update();
                })
                .catch(err => console.error("Fetch Penjualan Error:", err))
                .finally(() => hideLoading(loadingPenjualan));
        }

        function fetchBarang() {
            showLoading(loadingBarang);
            fetch(`/barang-terlaris?limit=10`)
                .then(res => res.json())
                .then(data => {
                    const barang = data.barang || [];
                    chartBarang.data.labels = barang.map(i => i.nama_barang);
                    chartBarang.data.datasets[0].data = barang.map(i => parseInt(i.total_terjual));
                    chartBarang.update();
                })
                .catch(err => console.error("Fetch Barang Error:", err))
                .finally(() => hideLoading(loadingBarang));
        }

        // Debounce
        function debounce(func, delay) {
            let timer;
            return function(...args) {
                clearTimeout(timer);
                timer = setTimeout(() => func.apply(this, args), delay);
            };
        }

        const debouncedFetchPenjualan = debounce(fetchPenjualan, 500);

        filterPenjualan.addEventListener('change', debouncedFetchPenjualan);

        initCharts();
        fetchPenjualan();
        fetchBarang();
    });
</script>
