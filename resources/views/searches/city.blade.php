<x-layout>

    <x-backhead>Groups in {{ $city->name }}</x-backhead>
    
    <div class="d-flex justify-content-center ">
        <input type="search" id="search-input" class="rounded-xl bg-white/10 border border-white/10 px-3 py-2 mb-4 w-50" placeholder="Search">
    </div>

    <ul>
        <x-dashboard.card-group :$groups />
    </ul>

</x-layout>