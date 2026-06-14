<x-layout>
    <x-backhead>{{ __('messages.Edit User') ?? 'Edit User' }}: {{ $user->name }}</x-backhead>

    <div class="container mt-4">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="display_name" class="form-label">{{ __('messages.Display Name') ?? 'Display Name' }}</label>
                <input type="text" name="display_name" id="display_name" class="form-control @error('display_name') is-invalid @enderror" value="{{ old('display_name', $user->display_name) }}" required>
                @error('display_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">{{ __('messages.Email') ?? 'Email' }}</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.Roles') ?? 'Roles' }}</label>
                <div>
                    @foreach ($roles as $role)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-3">
                <label for="service_body_id" class="form-label">{{ __('messages.Service Body') ?? 'Service Body' }}</label>
                <select name="service_body_id" id="service_body_id" class="form-control @error('service_body_id') is-invalid @enderror">
                    <option value="">-- {{ __('messages.None') ?? 'None' }} --</option>
                    @foreach ($serviceBodies as $sb)
                        <option value="{{ $sb->id }}" {{ old('service_body_id', $user->service_body_id) == $sb->id ? 'selected' : '' }}>
                            {{ $sb->en_name }} ({{ $sb->ar_name }})
                        </option>
                    @endforeach
                </select>
                @error('service_body_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.Save') ?? 'Save' }}</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">{{ __('messages.Cancel') ?? 'Cancel' }}</a>
        </form>
    </div>
</x-layout>