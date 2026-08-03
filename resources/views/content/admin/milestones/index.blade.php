@extends('layouts/layoutMaster')

@section('title', 'Milestone Badges List')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<h4 class="mb-1">Milestone Badges</h4>
<p class="mb-6">Manage all milestone badges and their ranges here.</p>

<div class="card">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-4">
    <h5 class="mb-0">Badges</h5>
    <div class="d-flex gap-3 align-items-center">
      <a href="{{ route('admin.milestones.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-2"></i> Add New Badge
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Range (Quizzes)</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($milestones as $index => $milestone)
        <tr>
          <td>{{ $milestones->firstItem() + $index }}</td>
          <td><span class="fw-medium">{{ $milestone->name }}</span></td>
          <td><span class="badge bg-label-info">{{ $milestone->start_range }} - {{ $milestone->end_range }}</span></td>
          <td>
            @if($milestone->image)
              <img src="{{ asset($milestone->image) }}" alt="Image" class="rounded" style="max-height: 40px;">
            @else
              -
            @endif
          </td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.milestones.edit', $milestone->id) }}" class="btn btn-icon btn-text-primary btn-sm">
                <i class="icon-base ti tabler-edit icon-md"></i>
              </a>
              <form action="{{ route('admin.milestones.destroy', $milestone->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-icon btn-text-danger btn-sm" onclick="return confirm('Are you sure you want to delete this badge?')">
                  <i class="icon-base ti tabler-trash icon-md"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center">No milestone badges found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($milestones->hasPages())
    <div class="card-footer d-flex justify-content-center pb-0">
      {{ $milestones->links('pagination::bootstrap-5') }}
    </div>
  @endif
</div>
@endsection
