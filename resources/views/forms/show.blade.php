<x-frontend.layout>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-lg border-0 rounded-4 overflow-hidden mb-4" style="background: white;">
                    <div class="p-4 p-md-5 text-white" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                        <h2 class="fw-bold mb-2">{{ $form->title }}</h2>
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 text-uppercase small">
                            {{ $form->type === 'survey' ? __('messages.Survey') ?? 'Survey' : __('messages.Event Registration') ?? 'Event Registration' }}
                        </span>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('forms.submit.public', $form->slug) }}" method="POST">
                            @csrf

                            @foreach ($form->fields as $field)
                                <div class="mb-4">
                                    <label class="form-label fw-bold" style="color: #1a202c;">
                                        {{ $field->label }}
                                        @if ($field->required)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>

                                    @if ($field->type === 'text')
                                        <input type="text" name="field_{{ $field->id }}" class="form-control form-control-lg bg-light border-0 rounded-3" value="{{ old('field_' . $field->id) }}" {{ $field->required ? 'required' : '' }}>

                                    @elseif ($field->type === 'textarea')
                                        <textarea name="field_{{ $field->id }}" rows="4" class="form-control form-control-lg bg-light border-0 rounded-3" {{ $field->required ? 'required' : '' }}>{{ old('field_' . $field->id) }}</textarea>

                                    @elseif ($field->type === 'number')
                                        <input type="number" name="field_{{ $field->id }}" class="form-control form-control-lg bg-light border-0 rounded-3" value="{{ old('field_' . $field->id) }}" {{ $field->required ? 'required' : '' }}>

                                    @elseif ($field->type === 'email')
                                        <input type="email" name="field_{{ $field->id }}" class="form-control form-control-lg bg-light border-0 rounded-3" value="{{ old('field_' . $field->id) }}" {{ $field->required ? 'required' : '' }}>

                                    @elseif ($field->type === 'date')
                                        <input type="date" name="field_{{ $field->id }}" class="form-control form-control-lg bg-light border-0 rounded-3" value="{{ old('field_' . $field->id) }}" {{ $field->required ? 'required' : '' }}>

                                    @elseif ($field->type === 'select')
                                        <select name="field_{{ $field->id }}" class="form-select form-select-lg bg-light border-0 rounded-3" {{ $field->required ? 'required' : '' }}>
                                            <option value="">Choose an option...</option>
                                            @if (is_array($field->options))
                                                @foreach ($field->options as $option)
                                                    <option value="{{ $option }}" {{ old('field_' . $field->id) === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            @endif
                                        </select>

                                    @elseif ($field->type === 'checkbox')
                                        <div class="d-flex flex-column gap-2">
                                            @if (is_array($field->options))
                                                @foreach ($field->options as $option)
                                                    <div class="form-check">
                                                        <input type="checkbox" name="field_{{ $field->id }}[]" value="{{ $option }}" class="form-check-input" id="check_{{ $field->id }}_{{ $loop->index }}" {{ is_array(old('field_' . $field->id)) && in_array($option, old('field_' . $field->id)) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="check_{{ $field->id }}_{{ $loop->index }}">{{ $option }}</label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>

                                    @elseif ($field->type === 'groups')
                                        <select name="field_{{ $field->id }}" class="form-select form-select-lg bg-light border-0 rounded-3" {{ $field->required ? 'required' : '' }}>
                                            <option value="">Select Group...</option>
                                            @foreach ($entities['groups'] ?? [] as $entity)
                                                @php
                                                    $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                @endphp
                                                <option value="{{ $name }}" {{ old('field_' . $field->id) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>

                                    @elseif ($field->type === 'cities')
                                        <select name="field_{{ $field->id }}" class="form-select form-select-lg bg-light border-0 rounded-3" {{ $field->required ? 'required' : '' }}>
                                            <option value="">Select City...</option>
                                            @foreach ($entities['cities'] ?? [] as $entity)
                                                @php
                                                    $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                @endphp
                                                <option value="{{ $name }}" {{ old('field_' . $field->id) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>

                                    @elseif ($field->type === 'neighborhoods')
                                        <select name="field_{{ $field->id }}" class="form-select form-select-lg bg-light border-0 rounded-3" {{ $field->required ? 'required' : '' }}>
                                            <option value="">Select Neighborhood...</option>
                                            @foreach ($entities['neighborhoods'] ?? [] as $entity)
                                                @php
                                                    $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                @endphp
                                                <option value="{{ $name }}" {{ old('field_' . $field->id) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>

                                    @elseif ($field->type === 'committees')
                                        <select name="field_{{ $field->id }}" class="form-select form-select-lg bg-light border-0 rounded-3" {{ $field->required ? 'required' : '' }}>
                                            <option value="">Select Committee...</option>
                                            @foreach ($entities['committees'] ?? [] as $entity)
                                                @php
                                                    $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                @endphp
                                                <option value="{{ $name }}" {{ old('field_' . $field->id) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>

                                    @elseif ($field->type === 'servicebodies')
                                        <select name="field_{{ $field->id }}" class="form-select form-select-lg bg-light border-0 rounded-3" {{ $field->required ? 'required' : '' }}>
                                            <option value="">Select Service Body...</option>
                                            @foreach ($entities['servicebodies'] ?? [] as $entity)
                                                @php
                                                    $name = app()->getLocale() === 'ar' ? ($entity->ar_name ?? $entity->en_name) : ($entity->en_name ?? $entity->ar_name);
                                                @endphp
                                                <option value="{{ $name }}" {{ old('field_' . $field->id) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            @endforeach

                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill py-3 fw-bold shadow-sm" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">
                                    Submit Form
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-frontend.layout>
