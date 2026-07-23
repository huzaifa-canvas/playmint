@extends('layouts/layoutMaster')

@section('title', 'Edit Subject')

@section('content')
<h4 class="mb-1">Edit Subject</h4>
<p class="mb-6">Update the subject details.</p>

<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.subjects.update', $subject->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      
      <div class="mb-4">
        <label class="form-label" for="name">Subject Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $subject->name) }}" required />
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label" for="icon">Subject Icon</label>
        @if($subject->icon)
          <div class="mb-2">
            <img src="{{ asset($subject->icon) }}" alt="{{ $subject->name }}" class="img-thumbnail" style="max-height: 50px;">
          </div>
        @endif
        <input type="file" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" accept="image/*" />
        <small class="form-text text-muted">Upload a new image to replace the current icon.</small>
        @error('icon')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary me-2">Update Subject</button>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-label-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
