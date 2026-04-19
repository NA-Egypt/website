<x-layout>
    
    <x-backhead>{{__('messages.Edit') . ' ' . __('messages.Meeting')}}</x-backhead>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <form action="{{ route('meeting.update', $meeting->id) }}" method="post" class="meeting-form">
                    @csrf
                    @method('PUT')

                    @auth
                        @can('is-super-admin')

                            <x-forms.select :$groups name="group_id" label="{{ __('messages.Group Name') }}" value="{{ $meeting->group_id }}" />
                        @else
                            <input type="hidden" name="group_id"  value="{{ $meeting->group_id }}"/>
                        @endcan
                    @endauth

                    <x-forms.select multiple="multiple" :$topics name="topics[]" label="{{ __('messages.Meeting Topic') }}" :value="$meeting->topics" />
    
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

                    <div class="switches-container">
                        <div class="switch-item">
                            <span class="switch-label">{{__("messages.Type")}}</span>
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
                            <span class="switch-value" id="label-type">
                                {{ old('type', $meeting->type ?? 'closed') === 'closed' ? __('messages.closed') : __('messages.open') }}
                            </span>
                        </div>
                        <div class="switch-item">
                            <span class="switch-label">{{__("messages.Language")}}</span>
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
                            <span class="switch-value" id="label-lang">
                                {{ old('lang', $meeting->lang ?? 'arabic') === 'arabic' ? __("messages.arabic") : __("messages.english") }}
                            </span>
                        </div>
                        <div class="switch-item">
                            <span class="switch-label">Status</span>
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
                            <span class="switch-value" id="label-status">
                                {{ old('status', $meeting->status ?? 'available') === 'available' ? __("messages.available") : __("messages.suspended") }}
                            </span>
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
        </div>
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

<style>
/* RTL Form Container */
[dir="rtl"] .form-container {
    width: 100%;
    max-width: 800px;
}

[dir="rtl"] .meeting-form {
    direction: rtl;
    text-align: right;
}

/* RTL Form Controls */
[dir="rtl"] .form-control,
[dir="rtl"] .form-select {
    text-align: right;
    direction: rtl;
}

/* RTL Switches Section */
.switches-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin: 2rem 0;
    padding: 1rem;
}

.switch-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}

[dir="rtl"] .switch-item {
    text-align: center;
}

/* RTL Form Check Fixes */
[dir="rtl"] .form-check {
    text-align: center;
    padding-right: 0;
    padding-left: 0;
    margin-bottom: 0.5rem;
}

[dir="rtl"] .form-check-input {
    margin-right: 0;
    margin-left: 0;
    position: relative;
}

[dir="rtl"] .form-check-label {
    padding-right: 0;
    padding-left: 0;
    margin-top: 0.5rem;
    display: block;
}

/* RTL Row and Column Fixes */
[dir="rtl"] .row {
    direction: rtl;
}

[dir="rtl"] .col-md-3,
[dir="rtl"] .col-md-4 {
    text-align: right;
    padding-right: 0.75rem;
    padding-left: 0.75rem;
}

/* RTL Options Section */
[dir="rtl"] .options-section {
    direction: rtl;
    text-align: right;
}

[dir="rtl"] .options-section .form-check {
    text-align: right;
    justify-content: flex-end;
}

/* Debug styles - remove after fixing */
[dir="rtl"] .switches-section {
    border: 2px solid red !important;
    background: rgba(255,0,0,0.1) !important;
}

[dir="rtl"] .switch-group {
    border: 1px solid blue !important;
    background: rgba(0,0,255,0.1) !important;
    margin: 5px !important;
}

[dir="rtl"] .form-check {
    border: 1px solid green !important;
}
</style>
