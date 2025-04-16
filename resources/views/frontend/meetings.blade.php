<x-frontend.layout>

    <x-section-head>{{__('messages.Recovery Meetings')}}</x-section-head>

    <form method="GET" action="{{ route('frontend.meetings') }}">
        <div class="row g-4">
            <div class="col-md-4">
                <x-filter.select :options="$days" name="day" label="{{ __('messages.Day') }}" />
            </div>
            <div class="col-md-4">
                <x-filter.select :options="$groups" name="group" label="{{__('messages.Group')}}" />
            </div>
            <div class="col-md-4">
                <x-filter.select :options="$serviceBodies" name="serviceBody" label="{{__('messages.Service Body')}}" />
            </div>
            
        </div>
        <div class="row g-6 mt-2">
            
            <div class="col-md-4">
                <div class="d-flex align-items-center mb-2">
                    <span class="me-2"></span>
                    <label class="fw-bold" for="type" id = type>{{__('messages.Type')}}</label>
                </div>
                <select name="type" data-allow-clear="true" class="select2">
                    <option value="">{{__('messages.Choose Type')}}</option>
                    <option value="open" {{ request('type') == 'open' ? 'selected' : '' }}>
                        {{ __('messages.open') }}
                    </option>
                    <option value="closed" {{ request('type') == 'closed' ? 'selected' : '' }}>
                        {{ __('messages.closed') }}
                    </option>
                </select>
            </div>

            <div class="col-md-4">
                <x-filter.select :options="$neighborhoods" name="neighborhood" label="{{__('messages.Neighborhood')}}" />
            </div>

            <div class="col-md-4">
                <x-filter.select :options="$cities" name="city" label="{{__('messages.City')}}" />
            </div>

        </div>
        
        <div class="d-flex justify-content-center align-items-center m-3" >
            {{-- <button class="btn btn-outline-dark px-4 mx-3" type="submit">{{__('messages.Filter')}}</button> --}}
            <a href="{{ route('frontend.meetings') }}" class="btn btn-outline-dark px-4">{{__('messages.Clear Filters')}}</a>
        </div>

    </form>

    @if($meetings->isEmpty())
        <p>{{__('messages.No meetings found')}}</p>
    @else

        <ul style="padding: 0px; width: 100%;">
            <x-filter.filter-card :$meetings />
        </ul>
    @endif
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
</x-frontend.layout>