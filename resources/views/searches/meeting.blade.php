<x-layout>

    <x-backhead>Meetings in {{ $group->name  }}</x-backhead>

    <div class="d-flex justify-content-center ">
        <input type="search" id="search-input" class="rounded-xl bg-white/10 border border-white/10 px-3 py-2 mb-4 w-50" placeholder="Search">
    </div>

    @if ($meetings->isEmpty())
        <p class="flex gap-3 p-4 bg-white rounded  border border-primary meeting-item mb-3">No meetings found for this group.</p>
    @else
        <ul>
            <x-dashboard.card-meeting :$meetings />
        </ul>
    @endif

</x-layout>