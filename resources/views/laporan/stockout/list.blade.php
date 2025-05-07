<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Laporan Stok Out') }}
            </h2>
            {{-- <a href="#"
                class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Beli</a> --}}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <table class="w-full">
                <thead class="bg-gray-200">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="180">No Surat</th>
                        <th class="px-6 py-3 text-left" width="180">Nama Customer</th>
                        <th class="px-6 py-3 text-left" width="180">Total Harga Beli</th>
                        <th class="px-6 py-3 text-left" width="180">Total Harga Jual</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @foreach ($totals as $approvedId => $data)
                        <tr>
                            <td class="border px-4 py-2">{{ $data['approved']->no_surat ?? '-' }}</td>
                            <td class="border px-4 py-2">{{ $data['approved']->name_cust ?? '-' }}</td>
                            <td class="border px-4 py-2">Rp. {{ number_format($data['total_harga_beli'], 0, ',', '.') }}
                            </td>
                            <td class="border px-4 py-2">Rp. {{ number_format($data['total_harga_jual'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
