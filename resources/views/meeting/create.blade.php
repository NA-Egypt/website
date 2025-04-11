<x-layout>
    
    <x-section-head>{{__('messages.Add') . ' ' . __('messages.Meeting')}}</x-section-head>

    <div class="container d-flex justify-content-center align-items-center">
        <form action="{{ route('meeting.store') }}" method="post" class="row g-2 col-md-12 col-lg-8 mt-1">
            @csrf

            <x-forms.select :$groups name="group_id" label="{{ __('messages.Group Name')}}"/>
            <x-forms.select :$topics name="topic_id" label="{{ __('messages.Meeting Topic')}}"/>
            <div class="row align-items-end">
                <div class="col-md-4">
                    <x-forms.select :$days name="day_id" label="{{ __('messages.Day')}}"/>
                </div>
                <div class="col-md-4">
                    <x-forms.input name="start_time" label="{{ __('messages.From')}}" type="time" />
                </div>
                <div class="col-md-4">
                    <x-forms.input name="end_time" label="{{ __('messages.To')}}" type="time" />
                </div>
            </div>
            <x-forms.textarea name="description" label="{{ __('messages.Description')}}"/>
            <div class="m-2 p-2" >
                <div class="row align-items-end mx-1">
                    <div class="form-check form-switch col-md-2">
                        <input type="hidden" name="type" value="close">
                        <input 
                            name="type" 
                            class="form-check-input" 
                            type="checkbox" 
                            id="meeting-type" 
                            value="open"
                            {{ old('type', $meeting->type ?? 'close') === 'open' ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="meeting-type" id="switchLabel">
                            {{ old('type', $meeting->type ?? 'close') === 'open' ? 'Open' : 'Close' }}
                        </label>
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
                                    id="option-{{ $option->id }}">
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
                </div>
            </div>

            <x-forms.normal-button color='outline-dark' name="{{ __('messages.Save') }}" />

        </form>
    </div>

</x-layout>

