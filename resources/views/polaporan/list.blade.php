<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('List Preorder') }}
            </h2>
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
                        <th class="px-6 py-3 text-left" width="180">Status</th>
                        <th class="px-6 py-3 text-center" width="250">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if ($preorder->isNotEmpty())
                        @foreach ($preorder as $po)
                            <tr class="border-b">
                                <td class="px-6 py-3 text-left">{{ $po->order_date }}</td>
                                <td class="px-6 py-3 text-left">{{ $po->no_surat }}</td>
                                <td class="px-6 py-3 text-left">{{ $po->customer->name ?? 'Tidak Diketahui' }}</td>
                                <td class="px-6 py-3 text-left">Rp {{ number_format($po->total_belanja, 2) }}</td>
                                <td class="px-6 py-3 text-left">{{ $po->status }}
                                    @if ($po->parent_id)
                                        <div class="text-xs text-blue-600 font-semibold mt-1">Order Lanjutan dari
                                            #{{ $po->no_surat }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('polaporan.show', $po->id) }}"
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
            <div class="my-3">
                {{-- {{ $kategori->links() }} --}}
            </div>
        </div>
    </div>


</x-app-layout>
{{-- <script>
    function approvePreorder(id) {
        const refName = `itemLoader-${id}`;
        const itemLoaderEl = document.querySelector(`[x-ref="${refName}"]`);

        if (!itemLoaderEl || !itemLoaderEl.__x) {
            alert('Gagal mengambil data item dari Alpine.');
            return;
        }

        const items = itemLoaderEl.__x.getUntracked().items;

        fetch(`/approve-preorder/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    items
                })
            })
            .then(response => response.ok ? response.json() : response.text().then(t => {
                throw new Error(t)
            }))
            .then(data => {
                if (data.error) {
                    alert('Gagal: ' + data.error);
                } else {
                    alert(data.message);
                    location.reload();
                }
            })
            .catch(error => {
                alert('Error: ' + error.message);
            });
    }
</script> --}}
