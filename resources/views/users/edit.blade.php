<x-layout>
    <h1>Edit User Roles: {{ $user->name }}</h1>
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        <div>
            <label>Roles:</label>
            @foreach ($roles as $role)
                <div>
                    <input type="checkbox"
                           name="roles[]"
                           value="{{ $role->id }}"
                            {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                    <label>{{ $role->name }}</label>
                </div>
            @endforeach
        </div>
        <div style="margin-top: 15px; margin-bottom: 15px;">
            <label>Service Body:</label>
            <select name="service_body_id">
                <option value="">-- None --</option>
                @foreach ($serviceBodies as $sb)
                    <option value="{{ $sb->id }}" {{ $user->service_body_id == $sb->id ? 'selected' : '' }}>
                        {{ $sb->en_name }} ({{ $sb->ar_name }})
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit">Update</button>
    </form>
</x-layout>