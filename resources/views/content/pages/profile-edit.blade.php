@extends('layouts/layoutMaster')

@section('title', 'My Profile')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Account Settings /</span> Profile
</h4>

<div class="row">
  <div class="col-md-12">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="card mb-4">
      <h5 class="card-header">Profile Details</h5>
      <!-- Account -->
      <div class="card-body">
        <div class="d-flex align-items-start align-items-sm-center gap-4">
          <img src="{{ auth()->user()->avatar_url ?? asset('assets/img/avatars/1.png') }}" alt="user-avatar" class="d-block w-px-100 h-px-100 rounded" id="uploadedAvatar" />
        </div>
      </div>
      <hr class="my-0">
      <div class="card-body">
        <form id="formAccountSettings" method="POST" action="{{ route('admin.profile.update') }}">
          @csrf
          @method('PUT')
          <div class="row">
            <div class="mb-3 col-md-6">
              <label for="name" class="form-label">Name</label>
              <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" autofocus />
              @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3 col-md-6">
              <label for="email" class="form-label">E-mail</label>
              <input class="form-control @error('email') is-invalid @enderror" type="text" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" placeholder="john.doe@example.com" />
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          
          <hr class="my-4">
          <h5 class="mb-3">Change Password</h5>
          
          <div class="row">
            <div class="mb-3 col-md-6 form-password-toggle">
              <label class="form-label" for="current_password">Current Password</label>
              <div class="input-group input-group-merge">
                <input class="form-control @error('current_password') is-invalid @enderror" type="password" name="current_password" id="current_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                <span class="input-group-text cursor-pointer"><i class="ti tabler-eye-off"></i></span>
                @error('current_password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="mb-3 col-md-6 form-password-toggle">
              <label class="form-label" for="new_password">New Password</label>
              <div class="input-group input-group-merge">
                <input class="form-control @error('new_password') is-invalid @enderror" type="password" id="new_password" name="new_password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                <span class="input-group-text cursor-pointer"><i class="ti tabler-eye-off"></i></span>
                @error('new_password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>

            <div class="mb-3 col-md-6 form-password-toggle">
              <label class="form-label" for="new_password_confirmation">Confirm New Password</label>
              <div class="input-group input-group-merge">
                <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" />
                <span class="input-group-text cursor-pointer"><i class="ti tabler-eye-off"></i></span>
              </div>
            </div>
          </div>
          
          <div class="mt-2">
            <button type="submit" class="btn btn-primary me-2">Save changes</button>
            <a href="{{ route('pages-home') }}" class="btn btn-label-secondary">Cancel</a>
          </div>
        </form>
      </div>
      <!-- /Account -->
    </div>
  </div>
</div>
@endsection
