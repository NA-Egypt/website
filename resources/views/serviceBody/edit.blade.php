<x-layout>

    <x-backhead>{{__('messages.Edit') . ' ' . __('messages.Service Body')}}</x-backhead>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('serviceBody.update', $serviceBody->id) }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf
            @method('PUT')
            <x-forms.input name="ar_name" label="{{ __('messages.Service Body Arabic Name')}}" value="{{ $serviceBody->ar_name }}"/>
            <x-forms.input name="en_name" label="{{ __('messages.Service Body English Name')}}" value="{{ $serviceBody->en_name }}"/>
            <x-forms.textarea name="description" label="{{ __('messages.Description')}}" value="{{ $serviceBody->description }}"/>
            <x-forms.select :$days name="day_id" label="{{ __('messages.Day')}}" value="{{ $serviceBody->day_id }}"/>
            <div class="row align-items-end">
                <div class="col-md-6">
                    <x-forms.input name="start_time" label="{{ __('messages.From')}}" type="time" value="{{ $serviceBody->start_time }}"/>
                </div>
                <div class="col-md-6">
                    <x-forms.input name="end_time" label="{{ __('messages.To')}}" type="time" value="{{ $serviceBody->end_time }}"/>
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
                        $selectedRecurrence = old('recurrence', $serviceBody->recurrence ?? ['1st', 'monthly']);
                    @endphp
                    @foreach($recurrences as $val => $label)
                        <div class="form-check form-check-inline glass-card p-2 rounded-3 m-0 d-flex align-items-center" style="border: 1px solid var(--glass-border); background: rgba(255,255,255,0.4);">
                            <input class="form-check-input mt-0" type="checkbox" name="recurrence[]" id="recurrence-{{ $val }}" value="{{ $val }}" {{ is_array($selectedRecurrence) && in_array($val, $selectedRecurrence) ? 'checked' : '' }} style="margin-inline-end: 0.5rem; width: 1.2em; height: 1.2em;">
                            <label class="form-check-label text-secondary fw-medium mb-0" for="recurrence-{{ $val }}" style="font-size: 0.9rem;">
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
            <x-forms.input name="location" label="{{ __('messages.Location')}}" value="{{ $serviceBody->location }}"/>
            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Update') }}" />

        </form>
    </div>

</x-layout>