<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Nama Barang') }}
            </h2>
            <div>
                <a href="{{ route('barang.create') }}"
                    class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Tambah Barang</a>
                <a href="{{ route('barang.import') }}"
                    class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Import Barang</a>
            </div>
        </div>

    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('barang.index') }}" class="mb-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang..."
                    class="border px-3 py-2 rounded w-1/3">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">
                    Cari
                </button>
            </form>


            <x-message></x-message>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="60">No</th>
                        <th class="px-6 py-3 text-left">Nama Barang</th>
                        <th class="px-6 py-3 text-left" width="180">Created</th>
                        <th class="px-6 py-3 text-center" width="180">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if ($barang->isNotEmpty())
                        @foreach ($barang as $br)
                            <tr class="border-b">
                                <td class="px-6 py-4">
                                    {{ ($barang->currentPage() - 1) * $barang->perPage() + $loop->iteration }}
                                </td>
                                <td class="px-6 py-4">{{ $br->nama }}</td>
                                <td class="px-6 py-3 text-left">
                                    {{ \Carbon\Carbon::parse($br->created_at)->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('barang.edit', ['id' => $br->id]) }}"
                                            class="bg-slate-700 text-sm rounded-md text-white px-3 py-2 hover:bg-slate-800">
                                            Edit
                                        </a>
                                        <a href="javascript:void(0);"
                                            onclick="event.preventDefault(); deleteBarang({{ $br->id }});"
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
            <div class="mt-3">
                {{ $barang->links() }}
            </div>
        </div>
    </div>
    <x-slot name="script">
        <script type="text/javascript">
            function deleteBarang(id) {
                if (confirm("Apakah kamu yakin ingin menghapus data ini?")) {
                    $.ajax({
                        url: '{{ route('barang.destroy') }}',
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
