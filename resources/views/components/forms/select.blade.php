@props([
    'name', 
    'label', 
    'days'=>'',
    'cities'=>'', 
    'serviceBodies'=>'', 
    'neighborhoods'=>'',
    'topics' => '',
    'groups' => '',
    'users'=>'',
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
            if(app()->getLocale() === 'ar'){

                $field = 'ar_name';
            }else{

                $field = 'en_name';
            }
            $placeholder = "Choose Topic";
        }
        if(!empty($users)){
            $options = $users;
            $field = 'email';
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
        @php
            $baseName = str_replace('[]', '', $name);
            $oldValue = old($baseName);
            $isArrayParam = str_ends_with($name, '[]');
            $currentValue = $value;
            
            if ($value !== '' && $isArrayParam && !is_array($value)) {
                if (is_object($value) && method_exists($value, 'toArray')) {
                    $currentValue = $value->pluck('id')->toArray();
                } else {
                    $currentValue = (array) $value;
                }
            }
        @endphp
        <select {{ $attributes->merge($default) }} data-placeholder="{{ $placeholder }}">
            <option></option>
            @foreach ($options as $option)
                @php
                    $isSelected = false;
                    if ($currentValue === '') {
                        if (is_array($oldValue)) {
                            $isSelected = in_array($option->id, $oldValue);
                        } else {
                            $isSelected = ($oldValue == $option->id);
                        }
                    } else {
                        if (is_array($currentValue)) {
                            $isSelected = in_array($option->id, $currentValue);
                        } else {
                            $isSelected = ($currentValue == $option->id);
                        }
                    }
                @endphp
                <option value="{{ $option->id }}" {{ $isSelected ? 'selected' : '' }}>
                    {{ $option->$field }}
                </option>
            @endforeach
        </select>

    @endif
    
    <x-forms.error :error="$errors->first($name)" />
        
</div>

