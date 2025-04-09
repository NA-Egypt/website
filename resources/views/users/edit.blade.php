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
        <button type="submit">Update</button>
    </form>
</x-layout>