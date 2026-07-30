@extends('layouts/layoutMaster')

@section('title', 'Question List')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<h4 class="mb-1">Question List</h4>
<p class="mb-6">Manage the quiz question bank.</p>

<div class="card mb-4">
  <div class="card-body">
    <form action="{{ route('admin.questions.index') }}" method="GET" class="d-flex flex-wrap gap-3 align-items-end">
      <div>
        <label for="subject_id" class="form-label">Filter by Subject</label>
        <select name="subject_id" id="subject_id" class="form-select">
          <option value="">All Subjects</option>
          @foreach($subjects as $subject)
            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label for="grade_id" class="form-label">Filter by Grade</label>
        <select name="grade_id" id="grade_id" class="form-select">
          <option value="">All Grades</option>
          @foreach($grades as $grade)
            <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>{{ $grade->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label for="difficulty" class="form-label">Filter by Difficulty</label>
        <select name="difficulty" id="difficulty" class="form-select">
          <option value="">All Difficulties</option>
          <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
          <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
          <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
        </select>
      </div>
      <div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-label-secondary">Reset</a>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-4">
    <h5 class="mb-0">Questions</h5>
    <div class="d-flex gap-3 align-items-center">
      <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">
        <i class="icon-base ti tabler-plus me-2"></i> Add New Question
      </a>
    </div>
  </div>
  <div class="table-responsive text-nowrap">
    <table class="table table-hover">
      <thead class="table-light">
        <tr>
          <th>Subject</th>
          <th>Grade</th>
          <th>Difficulty</th>
          <th>Question</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($questions as $question)
        <tr>
          <td><span class="badge bg-label-info">{{ $question->subject->name ?? 'N/A' }}</span></td>
          <td>
            @foreach($question->grades as $grade)
              <span class="badge bg-label-success">{{ $grade->name }}</span>
            @endforeach
          </td>
          <td>
            @php
              $difficultyColors = ['easy' => 'success', 'medium' => 'warning', 'hard' => 'danger'];
            @endphp
            <span class="badge bg-label-{{ $difficultyColors[$question->difficulty] ?? 'secondary' }}">{{ ucfirst($question->difficulty ?? 'N/A') }}</span>
          </td>
          <td>{{ \Illuminate\Support\Str::limit($question->question_text, 50) }}</td>
          <td>
            <div class="d-flex gap-1">
              <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-icon btn-text-primary btn-sm">
                <i class="icon-base ti tabler-edit icon-md"></i>
              </a>
              <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-icon btn-text-danger btn-sm" onclick="return confirm('Are you sure you want to delete this question?')">
                  <i class="icon-base ti tabler-trash icon-md"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center py-4 text-muted">No questions found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($questions->hasPages())
  <div class="card-footer d-flex justify-content-center">
    {{ $questions->links() }}
  </div>
  @endif
</div>
@endsection
