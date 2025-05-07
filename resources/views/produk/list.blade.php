<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Produk') }}
            </h2>
            <div>
                <a href="{{ route('produk.create') }}"
                    class="bg-slate-700 text-sm rounded-md px-5 py-4
            text-white">Tambah Barang</a>
                <a href="{{ route('produk.import') }}"
                    class="bg-slate-700 text-sm rounded-md px-5 py-4
            text-white">import</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('produk.index') }}" class="mb-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang..."
                    class="border rounded px-3 py-2 w-1/3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded ml-2">
                    Cari
                </button>
            </form>


            <x-message></x-message>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="60">No</th>
                        <th class="px-6 py-3 text-left">Nama Barang</th>
                        <th class="px-6 py-3 text-left">Merek</th>
                        <th class="px-6 py-3 text-left" width="180">Kategori</th>
                        <th class="px-6 py-3 text-left" width="180">Satuan</th>
                        <th class="px-6 py-3 text-left" width="180">Pabrikasi</th>
                        <th class="px-6 py-3 text-left" width="180">NIE</th>
                        <th class="px-6 py-3 text-left" width="180">Created_at</th>
                        <th class="px-6 py-3 text-center" width="180">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if ($produk->isNotEmpty())
                        @foreach ($produk as $prdk)
                            <tr class="border-b">
                                <td class="px-6 py-3 text-left">
                                    {{ ($produk->currentPage() - 1) * $produk->perPage() + $loop->iteration }}
                                </td>

                                <td class="px-6 py-3 text-left">
                                    {{ $prdk->barang->nama }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $prdk->merek }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $prdk->kategori->name ?? 'Tidak ada kategori' }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $prdk->satuan->name ?? 'Tidak ada satuan' }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $prdk->supplier->name ?? 'Tidak ada supplier' }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $prdk->nie }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ \Carbon\Carbon::parse($prdk->created_at)->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('produk.edit', ['id' => $prdk->id]) }}"
                                            class="bg-slate-700 text-sm rounded-md text-white px-3 py-2 hover:bg-slate-800">
                                            Edit
                                        </a>
                                        <a href="javascript:void(0);"
                                            onclick="event.preventDefault(); deleteProduk({{ $prdk->id }});"
                                            class="bg-red-700 text-sm rounded-md text-white px-3 py-2 hover:bg-red-800">
                                            Delete
                                        </a>
                                    </div>
                                </td>


                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="my-3">
                {{ $produk->links() }}
            </div>
        </div>
    </div>
    <x-slot name="script">
        <script type="text/javascript">
            function deleteProduk(id) {
                if (confirm("Apakah kamu yakin ingin menghapus data ini?")) {
                    $.ajax({
                        url: '{{ route('produk.destroy') }}',
                        type: 'DELETE',
                        data: {
                            id: id
                        },
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        success: function(response) {
                            if (response.status) {
                                alert("Data berhasil dihapus!");
                                window.location.reload();
                            } else {
                                alert("Gagal menghapus data!");
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            alert("Terjadi kesalahan, coba lagi!");
                        }
                    });
                }
            }
        </script>
    </x-slot>
</x-app-layout>
