<x-layout>
    <x-backhead>{{ __('messages.Add User') ?? 'Add User' }}</x-backhead>

    <div class="container mt-4">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            
            <div class="mb-3">
                <label for="display_name" class="form-label">{{ __('messages.Display Name') ?? 'Display Name' }}</label>
                <input type="text" name="display_name" id="display_name" class="form-control @error('display_name') is-invalid @enderror" value="{{ old('display_name') }}" required>
                @error('display_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('messages.Email') ?? 'Email' }}</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.Roles') ?? 'Roles' }}</label>
                <div>
                    @foreach ($roles as $role)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}">
                            <label class="form-check-label" for="role_{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.Save') ?? 'Save' }}</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') ?? 'Cancel' }}</a>
        </form>
    </div>
</x-layout>
