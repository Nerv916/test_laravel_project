<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Supplier') }}
            </h2>
            <div>
                <a href="{{ route('supplier.create') }}"
                    class="bg-slate-700 text-sm rounded-md
                 px-5 py-4
                text-white">Tambah
                    Supplier</a>
                <a href="{{ route('supplier.import') }}"
                    class="bg-slate-700 text-sm rounded-md
                 px-5 py-4
                text-white">Import
                    Supplier</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-message></x-message>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="60">No</th>
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left" width="180">Alamat</th>
                        <th class="px-6 py-3 text-left" width="180">Kontak</th>
                        <th class="px-6 py-3 text-left" width="180">PIC</th>
                        <th class="px-6 py-3 text-left" width="180">Npwp</th>
                        <th class="px-6 py-3 text-left" width="180">Pajak</th>
                        <th class="px-6 py-3 text-left" width="180">Created_at</th>
                        <th class="px-6 py-3 text-center" width="180">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if ($supplier->isNotEmpty())
                        @foreach ($supplier as $sply)
                            <tr class="border-b">
                                <td class="px-6 py-3 text-left">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $sply->id_supplier }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $sply->name }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $sply->alamat }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $sply->kontak }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $sply->pic }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $sply->npwp }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ number_format($sply->pajak * 100, 0) }}%
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ \Carbon\Carbon::parse($sply->created_at)->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('supplier.edit', ['id' => $sply->id]) }}"
                                            class="bg-slate-700 text-sm rounded-md text-white px-3 py-2 hover:bg-slate-800">
                                            Edit
                                        </a>
                                        <a href="javascript:void(0);"
                                            onclick="event.preventDefault(); deleteSupplier({{ $sply->id }});"
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
                {{ $supplier->links() }}
            </div>
        </div>
    </div>
    <x-slot name="script">
        <script type="text/javascript">
            function deleteSupplier(id) {
                if (confirm("Apakah kamu yakin ingin menghapus data ini?")) {
                    $.ajax({
                        url: '{{ route('supplier.destroy') }}',
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
