@extends('layouts/layoutMaster')

@section('title', 'Grade List')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<h4 class="mb-1">Grade List</h4>
<p class="mb-6">Manage all grades from here.</p>

<div class="card">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-4">
    <h5 class="mb-0">Grades</h5>
    <div class="d-flex gap-3 align-items-center">
      <a href="{{ route('admin.grades.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-2"></i> Add New Grade
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>Order</th>
          <th>Name</th>
          <th>Total Questions</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($grades as $grade)
        <tr>
          <td><span class="badge bg-label-secondary">{{ $grade->order }}</span></td>
          <td><span class="fw-medium">{{ $grade->name }}</span></td>
          <td><span class="badge bg-label-primary">{{ $grade->questions_count ?? 0 }}</span></td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.grades.edit', $grade->id) }}" class="btn btn-icon btn-text-primary btn-sm">
                <i class="icon-base ti tabler-edit icon-md"></i>
              </a>
              <form action="{{ route('admin.grades.destroy', $grade->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-icon btn-text-danger btn-sm" onclick="return confirm('Are you sure you want to delete this grade?')">
                  <i class="icon-base ti tabler-trash icon-md"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="4" class="text-center py-4 text-muted">No grades found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($grades->hasPages())
  <div class="card-footer d-flex justify-content-center">
    {{ $grades->links() }}
  </div>
  @endif
</div>
@endsection
