@extends('layouts/layoutMaster')

@section('title', 'Avatar Gallery')

@section('content')
<h4 class="mb-1">Avatar Gallery</h4>
<p class="mb-6">Upload and manage child avatars. Click × to remove an avatar.</p>

@if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <h5 class="mb-0">Avatars <span id="avatar-count" class="badge bg-label-primary ms-1">{{ $avatars->count() }}</span></h5>
    <div class="d-flex gap-2 align-items-center">
      {{-- Hidden real file input --}}
      <input type="file" id="avatarFileInput" name="images[]" multiple accept="image/*" style="display:none">
      <button class="btn btn-primary" id="uploadBtn">
        <i class="icon-base ti tabler-upload me-2"></i> Upload Avatars
      </button>
    </div>
  </div>

  <div class="card-body">
    {{-- Upload Progress --}}
    <div id="uploadProgress" class="mb-4" style="display:none">
      <div class="d-flex align-items-center gap-2 mb-2 text-muted">
        <div class="spinner-border spinner-border-sm" role="status"></div>
        <span id="progressText">Uploading...</span>
      </div>
      <div class="progress" style="height:6px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progressBar" style="width:0%"></div>
      </div>
    </div>

    {{-- Avatar Grid --}}
    <div id="avatarGrid" class="row g-3">
      @forelse($avatars as $avatar)
        <div class="col-6 col-sm-4 col-md-3 col-lg-1 avatar-item" id="avatar-{{ $avatar->id }}">
          <div class="avatar-card position-relative">
            <img src="{{ $avatar->image_url }}" alt="Avatar" class="img-fluid rounded-3 w-100" style="height:120px;object-fit:contain;">
            <button type="button"
              class="btn btn-danger btn-icon btn-xs avatar-delete-btn position-absolute top-0 end-0 m-1 rounded-circle"
              data-id="{{ $avatar->id }}"
              style="width:24px;height:24px;padding:0;font-size:12px;line-height:1;">
              <i class="ti tabler-x"></i>
            </button>
          </div>
        </div>
      @empty
        <div class="col-12" id="emptyState">
          <div class="text-center py-5 text-muted">
            <i class="ti tabler-photo-off" style="font-size:3rem;"></i>
            <p class="mt-2">No avatars yet. Click "Upload Avatars" to add some.</p>
          </div>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const fileInput = document.getElementById('avatarFileInput');
  const uploadBtn = document.getElementById('uploadBtn');
  const avatarGrid = document.getElementById('avatarGrid');
  const uploadProgress = document.getElementById('uploadProgress');
  const progressBar = document.getElementById('progressBar');
  const progressText = document.getElementById('progressText');
  const avatarCount = document.getElementById('avatar-count');
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // Trigger file picker
  uploadBtn.addEventListener('click', () => fileInput.click());

  // On file selection → upload via AJAX
  fileInput.addEventListener('change', function () {
    if (!this.files.length) return;

    const formData = new FormData();
    Array.from(this.files).forEach(f => formData.append('images[]', f));

    uploadProgress.style.display = 'block';
    progressBar.style.width = '0%';
    progressText.textContent = 'Uploading ' + this.files.length + ' file(s)...';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '{{ route('admin.avatars.store') }}', true);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
    xhr.setRequestHeader('Accept', 'application/json');

    xhr.upload.onprogress = function (e) {
      if (e.lengthComputable) {
        const pct = Math.round((e.loaded / e.total) * 100);
        progressBar.style.width = pct + '%';
      }
    };

    xhr.onload = function () {
      uploadProgress.style.display = 'none';
      fileInput.value = '';

      if (xhr.status === 200) {
        const res = JSON.parse(xhr.responseText);
        if (res.success) {
          // Remove empty state if it exists
          const empty = document.getElementById('emptyState');
          if (empty) empty.remove();

          // Append each new avatar card to the grid
          res.avatars.forEach(av => {
            const col = document.createElement('div');
            col.className = 'col-6 col-sm-4 col-md-3 col-lg-1 avatar-item';
            col.id = 'avatar-' + av.id;
            col.innerHTML = `
              <div class="avatar-card position-relative">
                <img src="/${av.image_url}" alt="Avatar" class="img-fluid rounded-3 w-100" style="height:120px;object-fit:contain;">
                <button type="button"
                  class="btn btn-danger btn-icon btn-xs avatar-delete-btn position-absolute top-0 end-0 m-1 rounded-circle"
                  data-id="${av.id}"
                  style="width:24px;height:24px;padding:0;font-size:12px;line-height:1;">
                  <i class="ti tabler-x"></i>
                </button>
              </div>`;
            avatarGrid.appendChild(col);
          });

          // Update count badge
          const count = document.querySelectorAll('.avatar-item').length;
          avatarCount.textContent = count;
        }
      } else {
        alert('Upload failed. Please check file types and sizes.');
      }
    };

    xhr.onerror = function () {
      uploadProgress.style.display = 'none';
      alert('Network error during upload.');
    };

    xhr.send(formData);
  });

  // Delete avatar on × click (event delegation)
  avatarGrid.addEventListener('click', function (e) {
    const btn = e.target.closest('.avatar-delete-btn');
    if (!btn) return;

    const id = btn.dataset.id;
    if (!confirm('Remove this avatar?')) return;

    btn.disabled = true;

    fetch(`/admin/avatars/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const card = document.getElementById('avatar-' + id);
        card.style.transition = 'opacity 0.3s';
        card.style.opacity = '0';
        setTimeout(() => {
          card.remove();
          const count = document.querySelectorAll('.avatar-item').length;
          avatarCount.textContent = count;

          // Show empty state if no avatars left
          if (count === 0) {
            avatarGrid.innerHTML = `
              <div class="col-12" id="emptyState">
                <div class="text-center py-5 text-muted">
                  <i class="ti tabler-photo-off" style="font-size:3rem;"></i>
                  <p class="mt-2">No avatars yet. Click "Upload Avatars" to add some.</p>
                </div>
              </div>`;
          }
        }, 300);
      } else {
        btn.disabled = false;
        alert('Could not delete avatar.');
      }
    })
    .catch(() => {
      btn.disabled = false;
      alert('Network error.');
    });
  });
});
</script>
@endsection
