<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pembelian') }}
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
                    @if ($approveds->isNotEmpty())
                        @foreach ($approveds as $po)
                            <tr class="border-b">
                                <td class="px-6 py-3 text-left">{{ $po->order_date }}</td>
                                <td class="px-6 py-3 text-left">{{ $po->no_surat }}
                                    @if ($po->parent_id)
                                        <div class="text-xs text-blue-600 font-semibold mt-1">Order Lanjutan dari
                                            #{{ $po->no_surat }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-left">{{ $po->name_cust ?? 'Tidak Diketahui' }}</td>
                                <td class="px-6 py-3 text-left">Rp {{ number_format($po->total_belanja, 2) }}</td>




                                <td class="px-6 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('pembelian.show', $po->id) }}"
                                            class="text-white bg-blue-500 rounded-md px-3 py-2 hover:bg-blue-700">
                                            Detail
                                        </a>
                                        {{-- <button onclick="approvePreorder({{ $po->id }})"
                                        class="text-white bg-slate-500 rounded-md px-3 py-2 hover:bg-slate-700">
                                        Proses
                                    </button> --}}

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
