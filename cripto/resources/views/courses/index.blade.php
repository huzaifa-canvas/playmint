@extends('layouts.app')
@section('content')
<div class="container">
    <a href="{{ route('courses.create') }}" class="btn btn-primary mb-3">Add Course</a>
    <table class="table">
        <tr><th>#</th><th>Name</th><th>Category</th><th>Price</th><th>Status</th><th>Actions</th></tr>
        @foreach($courses as $course)
        <tr>
            <td>{{ $course->id }}</td>
            <td>{{ $course->name }}</td>
            <td>{{ $course->category?->title }}</td>
            <td>{{ $course->price }}</td>
            <td>{{ $course->status }}</td>
            <td>
                <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('courses.destroy', $course) }}" method="post" style="display:inline">
                    @csrf @method('delete')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
    {{ $courses->links() }}
</div>
@endsection
