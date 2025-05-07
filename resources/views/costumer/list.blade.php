<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Customer') }}
            </h2>
            <div>
                <a href="{{ route('costumer.create') }}"
                    class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Tambah Customer</a>
                <a href="{{ route('costumer.import') }}"
                    class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Import Customer</a>
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
                        <th class="px-6 py-3 text-left">Nama Instansi Pemesan</th>
                        <th class="px-6 py-3 text-left" width="180">Alamat</th>
                        <th class="px-6 py-3 text-left" width="180">PIC</th>
                        <th class="px-6 py-3 text-left" width="180">Kontak</th>
                        <th class="px-6 py-3 text-left" width="180">No NPWP</th>
                        {{-- <th class="px-6 py-3 text-left" width="180">Created_at</th> --}}
                        <th class="px-6 py-3 text-center" width="180">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if ($costumer->isNotEmpty())
                        @foreach ($costumer as $cstmr)
                            <tr class="border-b">
                                <td class="px-6 py-3 text-left">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $cstmr->name }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $cstmr->alamat }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $cstmr->pic }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $cstmr->kontak }}
                                </td>

                                <td class="px-6 py-3 text-left">
                                    {{ $cstmr->npwp }}
                                </td>
                                {{-- <td class="px-6 py-3 text-left">
                                    {{ \Carbon\Carbon::parse($cstmr->created_at)->format('d M, Y') }}
                                </td> --}}
                                <td class="px-6 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('costumer.edit', ['id' => $cstmr->id]) }}"
                                            class="bg-slate-700 text-sm rounded-md text-white px-3 py-2 hover:bg-slate-800">
                                            Edit
                                        </a>
                                        <a href="javascript:void(0);"
                                            onclick="event.preventDefault(); deleteCostumer({{ $cstmr->id }});"
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
                {{ $costumer->links() }}
            </div>
        </div>
    </div>
    <x-slot name="script">
        <script type="text/javascript">
            function deleteCostumer(id) {
                if (confirm("Apakah kamu yakin ingin menghapus data ini?")) {
                    $.ajax({
                        url: '{{ route('costumer.destroy') }}',
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
