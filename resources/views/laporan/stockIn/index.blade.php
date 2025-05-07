<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Laporan Stock In') }}
            </h2>
            {{-- <a href="#"
                class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Beli</a> --}}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="w-full ">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="180">No Order</th>
                        <th class="px-6 py-3 text-left" width="180">No Surat</th>
                        <th class="px-6 py-3 text-left" width="180">Customer</th>
                        <th class="px-6 py-3 text-left" width="180">Tanggal</th>
                        <th class="px-6 py-3 text-center" width="250">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($approvedOrders as $order)
                        <tr class="border-b">
                            <td class="px-6 py-3 text-left">{{ $order->no_spo }}
                                @if ($order->parent_id)
                                    <div class="text-xs text-blue-600 font-semibold mt-1">Order Lanjutan dari
                                        #{{ $order->no_surat }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-left">{{ $order->no_surat ?? '-' }}</td>
                            <td class="px-6 py-3 text-left">{{ $order->name_cust ?? '-' }}</td>
                            <td class="px-6 py-3 text-left">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('stok.report.detail', $order->id) }}"
                                    class="text-white bg-blue-500 rounded-md px-3 py-2 hover:bg-blue-700">
                                    Detail Produk
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
