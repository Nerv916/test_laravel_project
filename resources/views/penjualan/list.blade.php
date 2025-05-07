<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Penjualan') }}
            </h2>
            {{-- <a href="#"
                class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Beli</a> --}}
        </div>

    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message></x-message>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="180">Order Date</th>
                        <th class="px-6 py-3 text-left" width="180">Nomor Surat</th>
                        <th class="px-6 py-3 text-left" width="180">Cust</th>
                        <th class="px-6 py-3 text-left" width="180">Total</th>
                        <th class="px-6 py-3 text-center" width="250">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if ($orders->isNotEmpty())
                        @foreach ($orders as $order)
                            <tr class="border-b">
                                <td class="px-6 py-3 text-left">{{ $order->order_date ?? '-' }}</td>
                                <td class="px-6 py-3 text-left">
                                    {{ $order->no_surat ?? '-' }}
                                    @if ($order->parent_id)
                                        <div class="text-xs text-blue-600 font-semibold mt-1">
                                            Order Lanjutan dari #{{ $order->parent_id }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-left">{{ $order->name_cust ?? 'Tidak Diketahui' }}</td>
                                <td class="px-6 py-3 text-left">
                                    Rp. {{ number_format($order->total_harga_jual, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('penjualan.show', $order->approved_id) }}"
                                            class="text-white bg-blue-500 rounded-md px-3 py-2 hover:bg-blue-700">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>

            </table>
        </div>
    </div>
</x-app-layout>
