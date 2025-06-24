<x-frontend.layout>
<div class="container min-vh-100 d-flex flex-column justify-content-center align-items-center py-4">
    <div class="w-100" style="max-width: 1140px;">

    <x-section-head>{{__('messages.Recovery Meetings')}}</x-section-head>
    <div class="container px-4 py-3 justify-content-center">
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <form method="GET" action="{{ route('frontend.meetings') }}">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mt-2">
                    <div class="col">
                        <x-filter.select :options="$days" name="day" label="{{ __('messages.Day') }}" />
                    </div>
                    <div class="col">
                        <x-filter.select :options="$groups" name="group" label="{{__('messages.Group')}}" />
                    </div>
                    <div class="col">
                        <x-filter.select :options="$serviceBodies" name="serviceBody" label="{{__('messages.Service Body')}}" />
                    </div>
                    <div class="col">
                        <label class="fw-bold d-block mb-2" for="type">{{__('messages.Type')}}</label>
                        <select name="type" data-allow-clear="true" class="select2 form-control">
                            <option value="">{{__('messages.Choose Type')}}</option>
                            <option value="open" {{ request('type') == 'open' ? 'selected' : '' }}>
                                {{ __('messages.open') }}
                            </option>
                            <option value="closed" {{ request('type') == 'closed' ? 'selected' : '' }}>
                                {{ __('messages.closed') }}
                            </option>
                        </select>
                    </div>
                    <div class="col">
                        <x-filter.select :options="$neighborhoods" name="neighborhood" label="{{__('messages.Neighborhood')}}" />
                    </div>
                    <div class="col">
                        <x-filter.select :options="$cities" name="city" label="{{__('messages.City')}}" />
                    </div>
                </div>
                
                <div class="d-flex justify-content-center align-items-center m-3" >
                    <button class="btn btn-outline-primary px-4 mx-3" type="submit">{{__('messages.Filter')}}</button>
                    <a href="{{ route('frontend.meetings') }}" class="btn btn-outline-danger px-4">{{__('messages.Clear Filters')}}</a>
                </div>

                </form>
            </div>
        </div>
    </div>
    @if($meetings->isEmpty())
        <div class="row justify-content-center mt-4">
            <div class="col-auto">
                <p class="text-center text-muted">{{ __('messages.No meetings found') }}</p>
            </div>
        </div>
    @else

        <div class="container px-4 mt-4">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                        <x-filter.filter-card :$meetings />
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("search-input");
        if (!searchInput) return;

        searchInput.addEventListener("input", function () {
            const keyword = this.value.trim().toLowerCase();
            const meetingSections = document.querySelectorAll(".meeting-item, .meeting-item-suspended");

            meetingSections.forEach(section => {
                const content = section.textContent.toLowerCase();
                const visible = content.includes(keyword);
                section.style.display = visible ? "block" : "none";
            });
        });
    });
</script>
<script>

 document.addEventListener("DOMContentLoaded", function() {
    const form = document.querySelector("form");
    if (!form) return;

    document.querySelectorAll("select").forEach(select => {
        select.addEventListener("change", function() {
            form.submit();
        });
    });

    //     document.querySelectorAll('#crouton ul li a').forEach(link => {
    //         link.addEventListener('click', function() {
    //             //event.preventDefault();
    //             document.getElementById('day-input').value = link.getAttribute('data-day');
    //             //link.closest('form').submit();
    //             form.submit();
    //         });
    //     });

    // Ensure Select2 works correctly
    $('.select2').on('change', function() {
        form.submit();
    });
});
</script>
<style>
.px-4 {
    padding-left: 1rem!important;
    padding-right: 1rem!important;
}
</style>
</x-frontend.layout>