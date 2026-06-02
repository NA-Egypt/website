<x-layout>
    <x-backhead>{{ __('messages.Request IT Change') }}</x-backhead>

    <div class="container mt-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form action="{{ route('change-requests.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="request_type" class="form-label fw-bold">{{ __('messages.Request Type') }}</label>
                        <select name="request_type" id="request_type" class="form-select" required>
                            <option value="meetings_groups" {{ old('request_type') == 'meetings_groups' ? 'selected' : '' }}>{{ __('messages.meetings_groups') }}</option>
                            <option value="committee_info" {{ old('request_type') == 'committee_info' ? 'selected' : '' }}>{{ __('messages.committee_info') }}</option>
                            <option value="general" {{ old('request_type') == 'general' ? 'selected' : '' }}>{{ __('messages.general') }}</option>
                            <option value="other" {{ old('request_type') == 'other' ? 'selected' : '' }}>{{ __('messages.other') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label fw-bold">{{ __('messages.Subject') }}</label>
                        <input type="text" name="subject" id="subject" class="form-control" value="{{ old('subject') }}" required placeholder="{{ __('messages.Subject') }}">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">{{ __('messages.Description') }}</label>
                        <textarea name="description" id="description" class="form-control" rows="6" required placeholder="{{ __('messages.Description') }}">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="attachment" class="form-label fw-bold">{{ __('messages.Attachment') }}</label>
                        <input class="form-control" type="file" id="attachment" name="attachment" accept=".pdf,.png,.jpg,.jpeg,.docx,.xlsx">
                        <div class="form-text text-muted">
                            {{ __('messages.Max size') ?? 'Maximum 5MB per file' }}. <br>
                            {{ __('messages.Allowed types') ?? 'Allowed file types' }}: PDF, PNG, JPG, JPEG, DOCX, XLSX
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg flex-fill">
                            <i class="bi bi-send"></i> {{ __('messages.Submit Request') }}
                        </button>
                        <a href="{{ route('change-requests.index') }}" class="btn btn-outline-secondary btn-lg flex-fill text-center d-flex align-items-center justify-content-center">
                            {{ __('messages.Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout>
