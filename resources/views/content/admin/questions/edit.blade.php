@extends('layouts/layoutMaster')

@section('title', 'Edit Question')

@section('content')
<h4 class="mb-1">Edit Question</h4>
<p class="mb-6">Update the question details.</p>

<div class="card">
  <div class="card-body">
    <form action="{{ route('admin.questions.update', $question->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      
      <div class="row">
        <div class="col-md-4 mb-4">
          <label class="form-label" for="subject_id">Subject <span class="text-danger">*</span></label>
          <select id="subject_id" name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
            <option value="">Select Subject</option>
            @foreach($subjects as $subject)
              <option value="{{ $subject->id }}" {{ old('subject_id', $question->subject_id) == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
            @endforeach
          </select>
          @error('subject_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4 mb-4">
          <label class="form-label" for="grade_ids">Grades <span class="text-danger">*</span></label>
          <select id="grade_ids" name="grade_ids[]" class="form-select select2 @error('grade_ids') is-invalid @enderror" multiple required>
            @php
              $selectedGrades = old('grade_ids', $question->grades->pluck('id')->toArray());
            @endphp
            @foreach($grades as $grade)
              <option value="{{ $grade->id }}" {{ in_array($grade->id, $selectedGrades) ? 'selected' : '' }}>{{ $grade->name }}</option>
            @endforeach
          </select>
          @error('grade_ids')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-4 mb-4">
          <label class="form-label" for="difficulty">Difficulty <span class="text-danger">*</span></label>
          <select id="difficulty" name="difficulty" class="form-select @error('difficulty') is-invalid @enderror" required>
            <option value="">Select Difficulty</option>
            <option value="easy" {{ old('difficulty', $question->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
            <option value="medium" {{ old('difficulty', $question->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="hard" {{ old('difficulty', $question->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
          </select>
          @error('difficulty')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label" for="question_text">Question <span class="text-danger">*</span></label>
        <textarea class="form-control @error('question_text') is-invalid @enderror" id="question_text" name="question_text" rows="3" required>{{ old('question_text', $question->question_text) }}</textarea>
        @error('question_text')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-4">
        <label class="form-label" for="image">Question Image <span class="text-muted">(Optional)</span></label>
        @if($question->image)
          <div class="mb-2 d-flex align-items-center gap-3">
            <img src="{{ asset($question->image) }}" alt="Question Image" class="rounded" style="max-height: 100px;">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
              <label class="form-check-label text-danger" for="remove_image">Remove current image</label>
            </div>
          </div>
        @endif
        <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" />
        @error('image')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="row">
        <div class="col-md-6 mb-4">
          <label class="form-label" for="option_a">Option A <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('option_a') is-invalid @enderror" id="option_a" name="option_a" value="{{ old('option_a', $question->option_a) }}" required />
          @error('option_a')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6 mb-4">
          <label class="form-label" for="option_b">Option B <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('option_b') is-invalid @enderror" id="option_b" name="option_b" value="{{ old('option_b', $question->option_b) }}" required />
          @error('option_b')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6 mb-4">
          <label class="form-label" for="option_c">Option C <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('option_c') is-invalid @enderror" id="option_c" name="option_c" value="{{ old('option_c', $question->option_c) }}" required />
          @error('option_c')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6 mb-4">
          <label class="form-label" for="option_d">Option D <span class="text-danger">*</span></label>
          <input type="text" class="form-control @error('option_d') is-invalid @enderror" id="option_d" name="option_d" value="{{ old('option_d', $question->option_d) }}" required />
          @error('option_d')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label" for="correct_option">Correct Option <span class="text-danger">*</span></label>
        <select id="correct_option" name="correct_option" class="form-select @error('correct_option') is-invalid @enderror" required>
          <option value="">Select Correct Option</option>
          <option value="a" {{ old('correct_option', $question->correct_option) == 'a' ? 'selected' : '' }}>Option A</option>
          <option value="b" {{ old('correct_option', $question->correct_option) == 'b' ? 'selected' : '' }}>Option B</option>
          <option value="c" {{ old('correct_option', $question->correct_option) == 'c' ? 'selected' : '' }}>Option C</option>
          <option value="d" {{ old('correct_option', $question->correct_option) == 'd' ? 'selected' : '' }}>Option D</option>
        </select>
        @error('correct_option')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary me-2">Update Question</button>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-label-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection

@section('vendor-style')
@vite(['resources/assets/vendor/libs/select2/select2.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/select2/select2.js'])
@endsection

@section('page-script')
<script type="module">
  $(document).ready(function() {
    $('.select2').select2({
      placeholder: "Select Grades"
    });
  });
</script>
@endsection
