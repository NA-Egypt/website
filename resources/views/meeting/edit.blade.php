<x-layout>
    
    <x-section-head>{{__('messages.Edit') . ' ' . __('messages.Meeting')}}</x-section-head>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('meeting.update', $meeting->id) }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf
            @method('PUT')
    
            <x-forms.select :$groups name="group_id" label="{{ __('messages.Group Name') }}" value="{{ $meeting->group_id }}" />
            <x-forms.select :$topics name="topic_id" label="{{ __('messages.Meeting Topic') }}" value="{{ $meeting->topic_id }}" />
    
            <div class="row align-items-end">
                <div class="col-md-3">
                    <x-forms.select :$days name="day_id" label="{{ __('messages.Day') }}" value="{{ $meeting->day_id }}" />
                </div>
                <div class="col-md-3">
                    <x-forms.input name="start_time" label="{{ __('messages.From') }}" type="time" value="{{ $meeting->start_time }}" />
                </div>
                <div class="col-md-3">
                    <x-forms.input name="end_time" label="{{ __('messages.To') }}" type="time" value="{{ $meeting->end_time }}" />
                </div>
                <div class="col-md-3">
                    <x-forms.input name="capacity" label="{{ __('messages.Capacity')}}" type="number" value="{{ $meeting->capacity }}"/>
                </div>
            </div>
    
            <x-forms.textarea name="notes" label="{{ __('messages.Notes') }}">{{ $meeting->notes }}</x-forms.textarea>

            <div class="m-3 p-3">
                <div class="row g-4 align-items-center">

                    <!-- Type Switch -->
                    <div class="col-md-4">
                        <label for="meeting-type" class="form-label">{{__("messages.Type")}}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="type" value="open">
                            <input
                                    name="type"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="meeting-type"
                                    value="closed"
                                    {{ old('type', $meeting->type ?? 'closed') === 'closed' ? 'checked' : '' }}
                            >
                        </div>
                        <label class="mb-0" id="label-type" for="meeting-type">
                            {{ old('type', $meeting->type ?? 'closed') === 'closed' ? __('messages.closed') : __('messages.open') }}
                        </label>
                    </div>

                    <!-- Language Switch -->
                    <div class="col-md-4">
                        <label for="lang-switch" class="form-label">{{__("messages.Language")}}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="lang" value="english">
                            <input
                                    name="lang"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="lang-switch"
                                    value="arabic"
                                    {{ old('lang', $meeting->lang ?? 'arabic') === 'arabic' ? 'checked' : '' }}
                            >
                        </div>
                        <label class="mb-0" id="label-lang" for="lang-switch">
                            {{ old('lang', $meeting->lang ?? 'arabic') === 'arabic' ? __("messages.arabic") : __("messages.english") }}
                        </label>
                    </div>

                    <!-- Status Switch -->
                    <div class="col-md-4">
                        <label for="status-switch" class="form-label">Status</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="status" value="suspended">
                            <input
                                    name="status"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="status-switch"
                                    value="available"
                                    {{ old('status', $meeting->status ?? 'available') === 'available' ? 'checked' : '' }}
                            >
                        </div>
                        <label class="mb-0" id="label-status" for="status-switch">
                            {{ old('status', $meeting->status ?? 'available') === 'available' ? __("messages.available") : __("messages.suspended") }}
                        </label>
                    </div>

                </div>
            </div>

            <div class="form-group">
                <label>Options</label>
                @foreach ($options as $option)
                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="options[]"
                            value="{{ $option->id }}"
                            class="form-check-input"
                            id="option-{{ $option->id }}"
                            {{ in_array($option->id, old('options', $meeting->options->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="option-{{ $option->id }}">
                            @if(app()->getLocale() === 'ar')
                                {{$option->ar_name}}
                            @else
                                {{$option->en_name}}
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>

            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Update') }}" />
        </form>
    </div>

    <script>
        let translations = {
            open: @json(__('messages.open')),
            closed: @json(__('messages.closed')),
            arabic: @json(__('messages.arabic')),
            english: @json(__('messages.english')),
            Available: @json(__('messages.available')),
            Suspended: @json(__('messages.suspended')),
        };
        document.addEventListener('DOMContentLoaded', function () {
            const switches = [
                {
                    input: document.getElementById('meeting-type'),
                    label: document.getElementById('label-type'),
                    on: translations.closed,
                    off: translations.open
                },
                {
                    input: document.getElementById('lang-switch'),
                    label: document.getElementById('label-lang'),
                    on: translations.arabic,
                    off: translations.english
                },
                {
                    input: document.getElementById('status-switch'),
                    label: document.getElementById('label-status'),
                    on: translations.Available,
                    off: translations.Suspended
                }
            ];

            switches.forEach(({input, label, on, off}) => {
                // update on load
                label.textContent = input.checked ? on : off;

                // update when toggled
                input.addEventListener('change', function() {
                    label.textContent = input.checked ? on : off;
                });
            });
        });
    </script>
</x-layout>