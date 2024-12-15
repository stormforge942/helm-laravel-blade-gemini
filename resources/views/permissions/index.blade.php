@extends('layouts.app')

@section('content')
    <h1>Permissions List</h1>
    <table>
        <thead>
            <tr>
                <th>Permission</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td>
                        <a href="{{ route('permissions.edit', $permission->id) }}">Edit</a> |
                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
