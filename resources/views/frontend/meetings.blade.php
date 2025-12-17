<x-frontend.layout>
<x-section-head>{{__('messages.Recovery Meetings')}}</x-section-head>
<div class="container min-vh-100 d-flex flex-column justify-content-topcenter align-items-center">
    <div class="w-100" style="max-width: 1140px;">
    <div class="container px-4 py-3 justify-content-center">
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12">
                <form method="GET" action="{{ route('frontend.meetings') }}">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">{{ __('messages.Filter Options') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            <div class="col-12">
                                <x-filter.select :options="$days" name="day" label="{{ __('messages.Day') }}" />
                            </div>
                            <div class="col-12">
                                <x-filter.select :options="$groups" name="group" label="{{__('messages.Group')}}" :disabled="!!request('serviceBody')" />
                            </div>
                            <div class="col-12">
                                <x-filter.select :options="$serviceBodies" name="serviceBody" label="{{__('messages.Service Body')}}" :disabled="!!request('group') || !!request('city') || !!request('neighborhood')" />
                            </div>
                            <div class="col-12">
                                <x-forms.label name="type" label="{{__('messages.Type')}}" />
                                <select name="type" data-allow-clear="true" class="select2 form-control" {{ request('group') ? 'disabled' : '' }}>
                                    <option value="">{{__('messages.Choose Type')}}</option>
                                    <option value="open" {{ request('type') == 'open' ? 'selected' : '' }}>
                                        {{ __('messages.open') }}
                                    </option>
                                    <option value="closed" {{ request('type') == 'closed' ? 'selected' : '' }}>
                                        {{ __('messages.closed') }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-12">
                                <x-filter.select :options="$cities" name="city" label="{{__('messages.City')}}" :disabled="!!request('serviceBody') || !!request('group')" />
                            </div>
                            <div class="col-12">
                                <x-filter.select :options="$neighborhoods" name="neighborhood" label="{{__('messages.Neighborhood')}}" :disabled="!!request('serviceBody') || !!request('group')" />
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center align-items-center mt-4" >
                            <button class="btn btn-outline-primary px-4 mx-3" type="submit">{{__('messages.Filter')}}</button>
                            <a href="{{ route('frontend.meetings') }}" class="btn btn-outline-danger px-4 mx-3">{{__('messages.Clear Filters')}}</a>
                        </div>
                    </div>
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
        <div class="row justify-content-center">
            <div class="d-flex justify-content-center mb-3">
                <a href="{{ route('exportMeetingsToPDF', request()->query()) }}" class="btn btn-primary" style="max-width: 340px; width: 100%; text-align: center;">
                {{__('messages.downloadmeetingspdf')}}
                <x-fas-file-pdf style="width:16px; height:16px;"/>
                </a>
            </div>
            <div class="d-flex justify-content-center mb-3">
                <a href="{{ route('exportMeetingsToCSV', request()->query()) }}" class="btn btn-primary" style="max-width: 340px; width: 100%; text-align: center;">
                {{__('messages.downloadmeetingscsv')}}
                <x-fas-file-csv style="width:16px; height:16px;"/>
                </a>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="d-flex justify-content-center mb-3">
                <div class="position-relative" style="max-width: 340px; width: 100%;">
                    <input type="search" id="search-input" class="form-control ps-5" placeholder="{{ __('messages.Search meetings') }}...">
                    <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted">
                        <x-fas-search style="width:14px; height:14px;" />
                    </span>
                </div>
            </div>
        </div>
        <div class="container px-4 mt-4">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-3 g-4">
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
.meeting-item, .meeting-item-suspended {
    border-radius: 8px;
    padding: 1rem;
    background-color: #f9f9f9;
    transition: box-shadow 0.3s ease;
    border-left: 5px solid #1e40af;
    border-right: 5px solid #1e40af;
}
.meeting-item:hover, .meeting-item-suspended:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.select2-container--default .select2-selection--single {
    height: 38px;
    padding: 6px 12px;
    font-size: 1rem;
}
</style>
</x-frontend.layout>