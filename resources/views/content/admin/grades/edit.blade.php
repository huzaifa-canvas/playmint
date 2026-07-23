@extends('layouts/layoutMaster')

@section('title', 'Edit Grade')

@section('content')
<h4 class="mb-1">Edit Grade</h4>
<p class="mb-6">Update the grade details.</p>

<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.grades.update', $grade->id) }}" method="POST">
      @csrf
      @method('PUT')
      
      <div class="mb-4">
        <label class="form-label" for="name">Grade Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $grade->name) }}" required />
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label" for="order">Display Order</label>
        <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $grade->order) }}" />
        <small class="form-text text-muted">Used for sorting the grades. Lower numbers appear first.</small>
        @error('order')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary me-2">Update Grade</button>
        <a href="{{ route('admin.grades.index') }}" class="btn btn-label-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
