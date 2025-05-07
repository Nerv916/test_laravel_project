<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User') }}
            </h2>
            <a href="{{ route('user.create') }}"
                class="bg-slate-700 text-sm rounded-md px-5 py-4
                text-white">Create</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <x-message></x-message>
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="60">No</th>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Roles</th>
                        <th class="px-6 py-3 text-left" width="180">Created</th>
                        <th class="px-6 py-3 text-center" width="180">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if ($users->isNotEmpty())
                        @foreach ($users as $usr)
                            <tr class="border-b">
                                <td class="px-6 py-3 text-left">
                                    {{ $usr->id }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $usr->name }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $usr->email }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ $usr->roles->pluck('name')->implode(', ') }}
                                </td>
                                <td class="px-6 py-3 text-left">
                                    {{ \Carbon\Carbon::parse($usr->created_at)->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('user.edit', $usr->id) }}"
                                        class="bg-slate-700 text-sm rounded-md text-white px-3 py-2 hover:bg-slate-800">Edit</a>
                                    <a href="javascript:void(0);" onclick="deleteUser({{ $usr->id }})"
                                        class="bg-red-700 text-sm rounded-md text-white px-3 py-2 hover:bg-red-800">Delete</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="my-3">
                {{-- {{ $users->links() }} --}}
            </div>
        </div>
    </div>
    <x-slot name="script">
        <script type="text/javascript">
            function deleteUser(id) {
                if (confirm("Apakah kamu yakin ingin menghapus data ini?")) {
                    $.ajax({
                        url: '{{ route('user.destroy') }}',
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
