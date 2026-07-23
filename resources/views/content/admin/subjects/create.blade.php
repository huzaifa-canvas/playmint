@extends('layouts/layoutMaster')

@section('title', 'Add New Subject')

@section('content')
<h4 class="mb-1">Add New Subject</h4>
<p class="mb-6">Create a new subject for the Quiz Engine.</p>

<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.subjects.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      
      <div class="mb-4">
        <label class="form-label" for="name">Subject Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Mathematics" required />
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label" for="icon">Subject Icon</label>
        <input type="file" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" accept="image/*" />
        <small class="form-text text-muted">Upload an image for the subject icon.</small>
        @error('icon')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary me-2">Save Subject</button>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-label-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
