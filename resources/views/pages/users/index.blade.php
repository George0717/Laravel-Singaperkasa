@extends('layouts.app')

@section('title', 'User')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Users List</h1>

        <a href="{{ route('users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md mb-4 inline-block">Create New User</a>

        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-6 py-3 text-sm font-medium text-gray-600">Name</th>
                        <th class="px-6 py-3 text-sm font-medium text-gray-600">Email</th>
                        <th class="px-6 py-3 text-sm font-medium text-gray-600">Role</th>
                        <th class="px-6 py-3 text-sm font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">{{ $user->role }}</td> <!-- Akses kolom role langsung -->
                            <td class="px-6 py-4 flex space-x-2">
                                <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
