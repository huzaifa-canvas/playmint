@extends('layouts/layoutMaster')

@section('title', 'Subject List')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<h4 class="mb-1">Subject List</h4>
<p class="mb-6">Manage all subjects from here.</p>

<div class="card">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-4">
    <h5 class="mb-0">Subjects</h5>
    <div class="d-flex gap-3 align-items-center">
      <a href="{{ route('admin.subjects.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-2"></i> Add New Subject
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Icon</th>
          <th>Total Questions</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($subjects as $index => $subject)
        <tr>
          <td>{{ $subjects->firstItem() + $index }}</td>
          <td><span class="fw-medium">{{ $subject->name }}</span></td>
          <td>
            @if($subject->icon)
              <img src="{{ asset($subject->icon) }}" alt="Icon" class="rounded" style="max-height: 40px;">
            @else
              -
            @endif
          </td>
          <td><span class="badge bg-label-primary">{{ $subject->questions_count ?? 0 }}</span></td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-icon btn-text-primary btn-sm">
                <i class="icon-base ti tabler-edit icon-md"></i>
              </a>
              <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-icon btn-text-danger btn-sm" onclick="return confirm('Are you sure you want to delete this subject?')">
                  <i class="icon-base ti tabler-trash icon-md"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-4 text-muted">No subjects found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($subjects->hasPages())
  <div class="card-footer d-flex justify-content-center">
    {{ $subjects->links() }}
  </div>
  @endif
</div>
@endsection
