@props([
    'name', 
    'label', 
    'days'=>'',
    'cities'=>'', 
    'serviceBodies'=>'', 
    'neighborhoods'=>'',
    'topics' => '',
    'groups' => '',
    'value'=>''
])

@php
    $default = [
        
        'data-allow-clear'=>"true",
        'class'=>"select2",
        'name'=>$name,
        'style'=>"width: 100%;",
    ]
@endphp

<div class="my-3">

    <x-forms.label :$name :$label />
    @php
        // Select options data source and placeholder
        $options = null;
        $placeholder = '';
        $field = '';

        if (!empty($days)) {
            $options = $days;
            if(app()->getLocale() === 'ar'){

                $field = 'ar_name';
            }else{

                $field = 'en_name';
            }
            $placeholder = "Choose Day";
        }
        if (!empty($cities)) {
            $options = $cities;
            if(app()->getLocale() === 'ar'){

                $field = 'ar_name';
            }else{

                $field = 'en_name';
            }
            $placeholder = "Choose City";
        }
        if (!empty($serviceBodies)) {
            $options = $serviceBodies;
            if(app()->getLocale() === 'ar'){

                $field = 'ar_name';
            }else{

                $field = 'en_name';
            }
            $placeholder = "Choose Service Body";
        }
        if (!empty($neighborhoods)) {
            $options = $neighborhoods;
            if(app()->getLocale() === 'ar'){

                $field = 'ar_name';
            }else{

                $field = 'en_name';
            }
            $placeholder = "Choose Neighborhood";
        }
        if (!empty($topics)) {
            $options = $topics;
            $field = 'title';
            $placeholder = "Choose Topic";
        }
        if (!empty($groups)) {
            $options = $groups;
            if(app()->getLocale() === 'ar'){

                $field = 'ar_name';
            }else{

                $field = 'en_name';
            }
            $placeholder = "Choose Group";
        }

    @endphp
    
    @if ($options)
        <select {{ $attributes->merge($default) }} data-placeholder={{ $placeholder }}>
            <option></option> 
            @foreach ($options as $option)
                @if ($value=='')      
                    <option value="{{ $option->id }}" {{ old($name) == $option->id ? 'selected' : '' }}>
                        {{ $option->$field}}
                    </option>
                @else    
                    <option value="{{ $option->id }}" {{ $value == $option->id ? 'selected' : '' }}>
                        {{ $option->$field }}
                    </option>
                @endif
                
            @endforeach
        </select>

    @endif
    
    <x-forms.error :error="$errors->first($name)" />
        
</div>

