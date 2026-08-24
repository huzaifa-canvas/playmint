@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Dashboard /</span> Overview
</h4>

<div class="row">
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Welcome to the Dashboard!</h4>
        <p class="card-text">
          This is your central hub for managing users. Use the sidebar to navigate to the user management section.
        </p>
      </div>
    </div>
  </div>
  
  <div class="col-sm-6 col-lg-4 mb-4">
    <div class="card card-border-shadow-primary h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-users"></i></span>
          </div>
          <h4 class="ms-1 mb-0">{{ $totalParents ?? 0 }}</h4>
        </div>
        <p class="mb-1">Total Parents</p>
        <p class="mb-0">
          <span class="fw-medium me-1">Registered parent accounts</span>
        </p>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-4 mb-4">
    <div class="card card-border-shadow-success h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            <span class="avatar-initial rounded bg-label-success"><i class="icon-base ti tabler-user-plus"></i></span>
          </div>
          <h4 class="ms-1 mb-0">{{ $totalChildren ?? 0 }}</h4>
        </div>
        <p class="mb-1">Total Children</p>
        <p class="mb-0">
          <span class="fw-medium me-1">Registered child profiles</span>
        </p>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-lg-4 mb-4">
    <div class="card card-border-shadow-warning h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2 pb-1">
          <div class="avatar me-2">
            <span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-device-gamepad"></i></span>
          </div>
          <h4 class="ms-1 mb-0">{{ $totalQuizzes ?? 0 }}</h4>
        </div>
        <p class="mb-1">Total Quizzes</p>
        <p class="mb-0">
          <span class="fw-medium me-1">Completed quiz attempts</span>
        </p>
      </div>
    </div>
  </div>

  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header border-bottom">
        <h5 class="card-title mb-0">Average Scores by Grade & Subject</h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>Grade</th>
              <th>Subject</th>
              <th>Average Score (%)</th>
            </tr>
          </thead>
          <tbody>
            @if(isset($avgScores) && count($avgScores) > 0)
              @foreach($avgScores as $score)
                <tr>
                  <td><span class="fw-medium">{{ $score->grade_name }}</span></td>
                  <td>{{ $score->subject_name }}</td>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <div class="progress w-100" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ round($score->average_score) }}%" aria-valuenow="{{ round($score->average_score) }}" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                      <span class="fw-medium">{{ round($score->average_score, 1) }}%</span>
                    </div>
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="3" class="text-center py-4 text-muted">No quiz data available yet.</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
