@extends('layouts/layoutMaster')

@section('title', 'Add New Grade')

@section('content')
<h4 class="mb-1">Add New Grade</h4>
<p class="mb-6">Create a new grade for the Quiz Engine.</p>

<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.grades.store') }}" method="POST">
      @csrf
      
      <div class="mb-4">
        <label class="form-label" for="name">Grade Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Grade 1" required />
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label" for="order">Display Order</label>
        <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" />
        <small class="form-text text-muted">Used for sorting the grades. Lower numbers appear first.</small>
        @error('order')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary me-2">Save Grade</button>
        <a href="{{ route('admin.grades.index') }}" class="btn btn-label-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
