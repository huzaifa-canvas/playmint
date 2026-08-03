@extends('layouts/layoutMaster')

@section('title', 'Add Milestone Badge')

@section('content')
<h4 class="mb-1">Add Milestone Badge</h4>
<p class="mb-6">Create a new milestone badge by setting the quiz range.</p>

<div class="row">
  <div class="col-md-8 col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Badge Details</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('admin.milestones.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="mb-4">
            <label for="name" class="form-label">Badge Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Super Scholar" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="row mb-4">
            <div class="col-md-6">
              <label for="start_range" class="form-label">Start Range (Quizzes)</label>
              <input type="number" class="form-control @error('start_range') is-invalid @enderror" id="start_range" name="start_range" value="{{ old('start_range') }}" placeholder="e.g. 51" min="1" required>
              @error('start_range')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <small class="form-text text-muted">Must be greater than the previous badge's end range.</small>
            </div>
            
            <div class="col-md-6 mt-4 mt-md-0">
              <label for="end_range" class="form-label">End Range (Quizzes)</label>
              <input type="number" class="form-control @error('end_range') is-invalid @enderror" id="end_range" name="end_range" value="{{ old('end_range') }}" placeholder="e.g. 100" min="1" required>
              @error('end_range')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-4">
            <label for="image" class="form-label">Badge Image</label>
            <input class="form-control @error('image') is-invalid @enderror" type="file" id="image" name="image" required>
            @error('image')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mt-4">
            <button type="submit" class="btn btn-primary me-2">Save Badge</button>
            <a href="{{ route('admin.milestones.index') }}" class="btn btn-label-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
