@extends('layouts.app')

@section('content')
    <h1>Edit User</h1>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="name" value="{{ $user->name }}" placeholder="Name" required>
        <input type="email" name="email" value="{{ $user->email }}" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password (optional)">

        <label for="role">Select Role:</label>
        <select name="role" id="role" required>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>

        <button type="submit">Update User</button>
    </form>
@endsection
