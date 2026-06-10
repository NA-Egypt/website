<x-layout>
    
    <x-backhead>{{__('messages.Add') . ' ' . __('messages.Service Body')}}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('serviceBody.store') }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf

            <x-forms.input name="ar_name" label="{{ __('messages.Service Body Arabic Name')}}"/>
            <x-forms.input name="en_name" label="{{ __('messages.Service Body English Name')}}"/>
            <x-forms.textarea name="description" label="{{ __('messages.Description')}}"/>
            <x-forms.select :$days name="day_id" label="{{ __('messages.Day')}}"/>
            <div class="row align-items-end">
                <div class="col-md-6">
                    <x-forms.input name="start_time" label="{{ __('messages.From')}}" type="time" />
                </div>
                <div class="col-md-6">
                    <x-forms.input name="end_time" label="{{ __('messages.To')}}" type="time" />
                </div>
            </div>

            <div class="mb-4 mt-3">
                <h6 class="fw-bold mb-3" style="color: var(--text-primary);"><i class="bi bi-calendar-week me-2 text-warning"></i> {{ __('messages.Recurrence') }}</h6>
                <div class="d-flex flex-wrap gap-2">
                    @php
                        $recurrences = [
                            '1st' => __('messages.1st'),
                            'monthly' => __('messages.monthly'),
                        ];
                        $selectedRecurrence = old('recurrence', ['1st', 'monthly']);
                    @endphp
                    @foreach($recurrences as $val => $label)
                        <div class="form-check form-check-inline glass-card p-2 rounded-3 m-0 d-flex align-items-center" style="border: 1px solid var(--glass-border); background: rgba(255,255,255,0.4);">
                            <input class="form-check-input mt-0" type="checkbox" name="recurrence[]" id="recurrence-{{ $val }}" value="{{ $val }}" {{ in_array($val, $selectedRecurrence) ? 'checked' : '' }} style="margin-inline-end: 0.5rem; width: 1.2em; height: 1.2em;">
                            <label class="form-check-label text-secondary fw-medium mb-0" for="recurrence-{{ $val }}" style="font-size: 0.9rem;">
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <x-forms.input name="location" label="{{ __('messages.Location')}}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>