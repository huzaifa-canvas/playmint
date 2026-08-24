@extends('layouts/layoutMaster')

@section('title', 'User List - Apps')

@section('content')

{{-- Success/Error Messages --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible mb-4" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<h4 class="mb-1">User List</h4>
<p class="mb-6">Manage all users from here.</p>

{{-- Filters + Table --}}
<div class="card">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-4">
    <h5 class="mb-0">Users</h5>
    <div class="d-flex gap-3 align-items-center">
      {{-- Search Form --}}
      <form action="{{ route('app-user-list') }}" method="GET" class="d-flex gap-2">
        <select name="status" class="form-select">
          <option value="">All Statuses</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
        <input type="text" name="search" class="form-control" placeholder="Search name or email" value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary"><i class="ti tabler-search"></i></button>
        @if(request()->has('search') || request()->has('status'))
          <a href="{{ route('app-user-list') }}" class="btn btn-outline-secondary"><i class="ti tabler-x"></i></a>
        @endif
      </form>
      
      {{-- Add User Button --}}
      <a href="{{ route('app-user-create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-2"></i> Add New User
      </a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Email</th>
          <th>Children</th>
          <th>Status</th>
          <th>Joined</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $index => $user)
        <tr>
          <td>{{ $users->firstItem() + $index }}</td>
          <td>
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm me-3">
                <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle" />
              </div>
              <span class="fw-medium">{{ $user->name }}</span>
            </div>
          </td>
          <td>{{ $user->email }}</td>
          <td>
            @if($user->children && $user->children->count() > 0)
              <div class="d-flex align-items-center avatar-group">
                @foreach($user->children->take(3) as $child)
                  <div class="avatar avatar-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $child->name }}">
                    <img src="{{ $child->avatar_url ?? asset('assets/img/avatars/1.png') }}" alt="Avatar" class="rounded-circle pull-up">
                  </div>
                @endforeach
                @if($user->children->count() > 3)
                  <div class="avatar avatar-sm">
                    <span class="avatar-initial rounded-circle pull-up" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $user->children->count() - 3 }} more">+{{ $user->children->count() - 3 }}</span>
                  </div>
                @endif
              </div>
            @else
              <span class="text-muted">None</span>
            @endif
          </td>
          <td>
            @if($user->is_active)
              <span class="badge bg-label-success">Active</span>
            @else
              <span class="badge bg-label-danger">Suspended</span>
            @endif
          </td>
          <td>{{ $user->created_at->format('d M Y') }}</td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('app-user-edit', $user->id) }}" class="btn btn-icon btn-text-primary btn-sm">
                <i class="icon-base ti tabler-edit icon-md"></i>
              </a>
              @if($user->id !== auth()->id() && !$user->is_admin)
              <form action="{{ route('app-user-toggle-status', $user->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-icon btn-text-{{ $user->is_active ? 'warning' : 'success' }} btn-sm" data-bs-toggle="tooltip" title="{{ $user->is_active ? 'Suspend User' : 'Reactivate User' }}" onclick="return confirm('Are you sure you want to {{ $user->is_active ? 'suspend' : 'reactivate' }} this user?')">
                  <i class="icon-base ti tabler-{{ $user->is_active ? 'user-off' : 'user-check' }} icon-md"></i>
                </button>
              </form>
              <form action="{{ route('app-user-delete', $user->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-icon btn-text-danger btn-sm" data-bs-toggle="tooltip" title="Delete User" onclick="return confirm('Are you sure you want to delete this user? This cannot be undone.')">
                  <i class="icon-base ti tabler-trash icon-md"></i>
                </button>
              </form>
              @endif
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center py-4 text-muted">No users found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if($users->hasPages())
  <div class="card-footer d-flex justify-content-center">
    {{ $users->links() }}
  </div>
  @endif
</div>
@endsection
