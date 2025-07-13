@extends('layouts.app')

<style>
.success-checkmark {
  width: 24px;
  height: 24px;
  position: relative;
  display: inline-block;
  vertical-align: top;
}

.success-checkmark .check-icon {
  width: 24px;
  height: 24px;
  position: relative;
  border-radius: 50%;
  border: 2px solid #4ade80;
  background: white;
  animation: scale 0.3s ease-in-out 0.9s both;
}

.success-checkmark .check-icon::before {
  content: '';
  position: absolute;
  top: 3px;
  left: 7px;
  width: 6px;
  height: 10px;
  border: solid #4ade80;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
  animation: check 0.6s ease-in-out 0.9s forwards;
  opacity: 0;
}

@keyframes scale {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

@keyframes check {
  0% {
    opacity: 0;
    transform: rotate(45deg) scale(0.8);
  }
  50% {
    opacity: 1;
    transform: rotate(45deg) scale(1.2);
  }
  100% {
    opacity: 1;
    transform: rotate(45deg) scale(1);
  }
}

/* Dark mode support */
.dark .success-checkmark .check-icon {
  border-color: #22c55e;
  background: #1f2937;
}

.dark .success-checkmark .check-icon::before {
  border-color: #22c55e;
}
</style>

@section('content')
<!-- Breadcrumbs -->
<nav class="mb-6" aria-label="Breadcrumb">
  <ol class="flex flex-wrap items-center gap-1 sm:gap-2 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
    <li class="flex items-center">
      <a href="{{ route('dashboard') }}" class="hover:text-evsu dark:hover:text-evsu transition-colors whitespace-nowrap">
        Home
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <a href="{{ route('subjects.index') }}" class="hover:text-evsu dark:hover:text-evsu transition-colors whitespace-nowrap">
        Subjects
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <a href="{{ route('subjects.classes', $classSection->subject->id) }}" class="hover:text-evsu dark:hover:text-evsu transition-colors max-w-[120px] sm:max-w-none truncate">
        {{ $classSection->subject->code }} - {{ $classSection->subject->title }}
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <span class="text-gray-900 dark:text-gray-100 font-medium whitespace-nowrap">{{ $classSection->section }}</span>
    </li>
  </ol>
</nav>

@if (session('success'))
  <div id="successMessage" class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg transform transition-all duration-500 ease-out">
    <div class="flex items-center gap-3">
      <div class="success-checkmark">
        <div class="check-icon">
          <span class="icon-line line-tip"></span>
          <span class="icon-line line-long"></span>
          <div class="icon-circle"></div>
          <div class="icon-fix"></div>
        </div>
      </div>
      <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
      <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
        <i data-lucide="x" class="w-4 h-4"></i>
      </button>
    </div>
  </div>
@endif

@if (session('error'))
  <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
    <div class="flex items-center gap-3">
      <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
      <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
    </div>
  </div>
@endif

@if ($errors->any())
  <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
    <div class="flex items-start gap-3">
      <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5"></i>
      <div>
        <p class="text-red-800 dark:text-red-200 font-medium mb-1">Please fix the following errors:</p>
        <ul class="text-red-700 dark:text-red-300 text-sm space-y-1">
          @foreach ($errors->all() as $error)
            <li>• {{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
@endif

<!-- Header Section -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
  <div>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $classSection->section }}</h2>
    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $classSection->subject->code }} - {{ $classSection->subject->title }}</p>
  </div>
  <a href="{{ route('gradebook.all', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-evsu hover:bg-evsuDark text-white font-semibold rounded-lg shadow transition-transform transform hover:scale-105 focus:outline-none">
    <i data-lucide="clipboard-list" class="w-5 h-5"></i>
    Gradebook (All)
  </a>
</div>

<!-- Grading Categories Section -->
<div class="mb-8">
  <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Grading Categories</h3>
  <div class="grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
    <!-- Activities Card -->
    <div class="group cursor-pointer" onclick="window.location.href='{{ route('activities.index', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}'">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg hover:scale-105 transition-all duration-200">
        <div class="text-center">
          <div class="text-3xl mb-3">📝</div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Activities</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">No. of Activities: {{ $classSection->subject->activities()->count() }}</p>
        </div>
      </div>
    </div>

    <!-- Quizzes Card -->
    <div class="group cursor-pointer" onclick="window.location.href='{{ route('quizzes.index', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}'">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg hover:scale-105 transition-all duration-200">
        <div class="text-center">
          <div class="text-3xl mb-3">📊</div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Quizzes</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">No. of Quizzes: {{ $classSection->subject->quizzes()->count() }}</p>
        </div>
      </div>
    </div>

    <!-- Exams Card -->
    <div class="group cursor-pointer" onclick="window.location.href='{{ route('exams.index', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}'">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg hover:scale-105 transition-all duration-200">
        <div class="text-center">
          <div class="text-3xl mb-3">🧪</div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Exams</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">No. of Exams: {{ $classSection->subject->exams()->count() }}</p>
        </div>
      </div>
    </div>

    <!-- Recitation Card -->
    <div class="group cursor-pointer" onclick="window.location.href='{{ route('recitations.index', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}'">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg hover:scale-105 transition-all duration-200">
        <div class="text-center">
          <div class="text-3xl mb-3">🎤</div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Recitations</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">No. of Recitations: {{ $classSection->subject->recitations()->count() }}</p>
        </div>
      </div>
    </div>

    <!-- Project Card -->
    <div class="group cursor-pointer" onclick="window.location.href='{{ route('projects.index', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}'">
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg hover:scale-105 transition-all duration-200">
        <div class="text-center">
          <div class="text-3xl mb-3">📋</div>
          <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Projects</h4>
          <p class="text-sm text-gray-600 dark:text-gray-400">No. of Projects: {{ $classSection->subject->projects()->count() }}</p>
        </div>
      </div>
    </div>


  </div>
</div>

<!-- Enrolled Students Section -->
<div class="mb-8">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Enrolled Students</h3>
    <button onclick="openEnrollStudentModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg shadow transition-transform transform hover:scale-105 focus:outline-none">
      <i data-lucide="plus" class="w-4 h-4"></i>
      Enroll Student
    </button>
  </div>

  <!-- Students Table -->
  <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Risk Predictions</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          @forelse($enrolledStudents as $student)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                <div class="flex items-center gap-2">
                  {{ $student->student_id }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $student->full_name }}</td>
              <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 text-center">
                @php
                  $risks = $student->risk_predictions;

                  if (count($risks) === 1 && $risks[0] === 'Not At Risk') {
                      $riskLevel = 'Not At Risk';
                  } elseif (in_array('At Risk', $risks)) {
                      $riskLevel = 'High Risk';
                  } else {
                      $riskLevel = 'Low Risk';
                  }

                  // Filter out general categories for separate display
                  $riskCauses = array_filter($risks, function($r) use ($riskLevel) {
                      if ($r === 'Not At Risk') return false;
                      if ($r === 'At Risk') return false;
                      return true;
                  });
                @endphp

                <div class="flex flex-wrap gap-1 justify-center">
                  <!-- Summary Label -->
                  <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                    {{ $riskLevel === 'Not At Risk' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' :
                       ($riskLevel === 'Low Risk' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' :
                       'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300') }}">
                    @if($riskLevel === 'Not At Risk')
                      <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                    @elseif($riskLevel === 'Low Risk')
                      <i data-lucide="alert-circle" class="w-3 h-3 mr-1"></i>
                    @else
                      <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                    @endif
                    {{ $riskLevel }}
                  </span>

                  <!-- Detailed Labels -->
                  @foreach($riskCauses as $cause)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300">
                      {{ $cause }}
                    </span>
                  @endforeach
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                <div class="flex items-center gap-x-2 justify-center">
                  <button
                    onclick="toggleDetails('{{ $student->id }}')"
                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-colors"
                    type="button"
                  >
                    Details
                  </button>
                  <button
                    onclick="showDebugInfo('{{ $student->full_name }}', {{ json_encode($student->risk_predictions) }}, {{ $student->late_submission_pct }}, {{ $student->missed_submission_pct }})"
                    class="px-3 py-1.5 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 transition-colors"
                    type="button"
                  >
                    Debug
                  </button>
                  <button
                    onclick="if(confirm('Are you sure you want to unenroll this student from the class?')) { document.getElementById('unenroll-form-{{ $student->id }}').submit(); }"
                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-colors"
                    type="button"
                  >
                    Unenroll
                  </button>
                  <form id="unenroll-form-{{ $student->id }}"
                        method="POST"
                        action="{{ route('students.remove', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'student' => $student->id]) }}"
                        style="display: none;">
                    @csrf
                    @method('DELETE')
                  </form>
                </div>
              </td>
            </tr>
            <!-- Detailed Scores Row -->
            <tr id="details-{{ $student->id }}" class="hidden bg-gray-50 dark:bg-gray-700/50">
              <td colspan="4" class="px-6 py-4">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                  <div class="text-center">
                    <div class="font-medium text-gray-700 dark:text-gray-300">Activity Avg</div>
                    <div class="text-lg font-bold {{ $student->activity_avg_pct >= 75 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                      {{ number_format($student->activity_avg_pct, 1) }}%
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="font-medium text-gray-700 dark:text-gray-300">Quiz Avg</div>
                    <div class="text-lg font-bold {{ $student->quiz_avg_pct >= 75 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                      {{ number_format($student->quiz_avg_pct, 1) }}%
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="font-medium text-gray-700 dark:text-gray-300">Exam Score</div>
                    <div class="text-lg font-bold {{ $student->exam_score_pct >= 70 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                      {{ number_format($student->exam_score_pct, 1) }}%
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="font-medium text-gray-700 dark:text-gray-300">Project Score</div>
                    <div class="text-lg font-bold {{ $student->project_score_pct >= 70 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                      {{ number_format($student->project_score_pct, 1) }}%
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="font-medium text-gray-700 dark:text-gray-300">Recitation</div>
                    <div class="text-lg font-bold {{ $student->recitation_score_pct >= 70 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                      {{ number_format($student->recitation_score_pct, 1) }}%
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="font-medium text-gray-700 dark:text-gray-300">Late Sub</div>
                    <div class="text-lg font-bold {{ $student->late_submission_pct <= 30 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                      {{ number_format($student->late_submission_pct, 1) }}%
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="font-medium text-gray-700 dark:text-gray-300">Missed Sub</div>
                    <div class="text-lg font-bold {{ $student->missed_submission_pct <= 20 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                      {{ number_format($student->missed_submission_pct, 1) }}%
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="font-medium text-gray-700 dark:text-gray-300">Resubmission</div>
                    <div class="text-lg font-bold {{ $student->resubmission_pct <= 25 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                      {{ number_format($student->resubmission_pct, 1) }}%
                    </div>
                  </div>
                  <div class="text-center">
                    <div class="font-medium text-gray-700 dark:text-gray-300">Variation</div>
                    <div class="text-lg font-bold text-gray-600 dark:text-gray-400">
                      {{ number_format($student->variation_score_pct, 1) }}%
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                <div class="flex flex-col items-center">
                  <i data-lucide="user-plus" class="w-8 h-8 mb-2"></i>
                  <p>No students enrolled yet.</p>
                  <p class="text-sm">Click "Enroll Student" to add students to this class.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Enroll Student Modal -->
<div id="enrollStudentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3">
        <i data-lucide="user-check" class="w-6 h-6 text-evsu"></i>
        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Enroll Student</h3>
      </div>
      <button onclick="closeEnrollStudentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <form method="POST" action="{{ route('students.enroll', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}" class="p-6">
      @csrf
      <div class="space-y-4">
        <!-- Multiple Students Selection -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Students to Enroll</label>
          <div class="relative">
            <input type="text" id="student_search" placeholder="Search students..." 
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white mb-2">
          </div>
          
          <!-- Students List with Checkboxes -->
          <div class="border border-gray-300 dark:border-gray-600 rounded-lg max-h-48 overflow-y-auto bg-white dark:bg-gray-700">
            @foreach(\App\Models\Student::whereNotIn('id', $enrolledStudents->pluck('id'))->orderBy('last_name')->orderBy('first_name')->get() as $student)
              <div class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-600 last:border-b-0">
                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" id="student_{{ $student->id }}" 
                       class="w-4 h-4 text-evsu bg-gray-100 border-gray-300 rounded focus:ring-evsu dark:focus:ring-evsu dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                <label for="student_{{ $student->id }}" class="flex-1 cursor-pointer">
                  <div class="flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-evsu"></i>
                    <span class="text-gray-900 dark:text-gray-100">{{ $student->full_name_with_id }}</span>
                  </div>
                </label>
              </div>
            @endforeach
          </div>
          
          <!-- Select All / Deselect All -->
          <div class="flex items-center justify-between mt-2">
            <div class="flex gap-2">
              <button type="button" onclick="selectAllStudents()" class="text-sm text-evsu hover:text-evsuDark font-medium">
                Select All
              </button>
              <button type="button" onclick="deselectAllStudents()" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium">
                Deselect All
              </button>
            </div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
              <span id="selected-count">0</span> selected
            </div>
          </div>
        </div>

        <!-- Or Divider -->
        <div class="relative">
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
          </div>
          <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">or</span>
          </div>
        </div>

        <!-- Create New Student Button -->
        <button type="button" onclick="openCreateStudentModal()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
          <i data-lucide="user-plus" class="w-4 h-4"></i>
          Create New Student
        </button>
      </div>

              <!-- Modal Footer -->
        <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
          <button type="button" onclick="closeEnrollStudentModal()" 
                  class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
            Cancel
          </button>
          <button type="submit" 
                  class="flex-1 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">
            Enroll Student
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Create New Student Modal -->
<div id="createStudentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3">
        <i data-lucide="user-plus" class="w-6 h-6 text-evsu"></i>
        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Create New Student</h3>
      </div>
      <button onclick="closeCreateStudentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <form id="createStudentForm" method="POST" action="{{ route('students.store') }}" class="p-6">
      @csrf
      <div class="space-y-4">
        <!-- Student ID -->
        <div>
          <label for="student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student ID</label>
          <input type="text" id="student_id" name="student_id" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., 2025-0007"
                 value="{{ old('student_id') }}">
        </div>

        <!-- First Name -->
        <div>
          <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
          <input type="text" id="first_name" name="first_name" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Juan"
                 value="{{ old('first_name') }}">
        </div>

        <!-- Last Name -->
        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
          <input type="text" id="last_name" name="last_name" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Dela Cruz"
                 value="{{ old('last_name') }}">
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
          <input type="email" id="email" name="email" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., juan.delacruz@email.com"
                 value="{{ old('email') }}">
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button" onclick="closeCreateStudentModal()" 
                class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
          Cancel
        </button>
        <button type="submit" 
                class="flex-1 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">
          Create Student
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function navigateToCategory(category) {
  // Placeholder function - will navigate to specific category management page
  console.log('Navigating to category:', category);
  // Example: window.location.href = `/grading/${category}`;
}

function openEnrollStudentModal() {
  document.getElementById('enrollStudentModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeEnrollStudentModal() {
  document.getElementById('enrollStudentModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
}

function openCreateStudentModal() {
  closeEnrollStudentModal();
  document.getElementById('createStudentModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeCreateStudentModal() {
  document.getElementById('createStudentModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
  document.getElementById('createStudentForm').reset();
}

// Student search functionality for checkboxes
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('student_search');
  const studentCheckboxes = document.querySelectorAll('input[name="student_ids[]"]');
  
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      const query = this.value.toLowerCase();
      
      studentCheckboxes.forEach(checkbox => {
        const label = checkbox.closest('div').querySelector('label span');
        const studentText = label.textContent.toLowerCase();
        
        if (query.length === 0 || studentText.includes(query)) {
          checkbox.closest('div').style.display = 'flex';
        } else {
          checkbox.closest('div').style.display = 'none';
        }
      });
    });
  }
});

// Select All / Deselect All functions
function selectAllStudents() {
  const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
  checkboxes.forEach(checkbox => {
    if (checkbox.closest('div').style.display !== 'none') {
      checkbox.checked = true;
    }
  });
  updateSelectedCount();
}

function deselectAllStudents() {
  const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
  checkboxes.forEach(checkbox => {
    checkbox.checked = false;
  });
  updateSelectedCount();
}

function updateSelectedCount() {
  const checkedStudents = document.querySelectorAll('input[name="student_ids[]"]:checked');
  const countElement = document.getElementById('selected-count');
  if (countElement) {
    countElement.textContent = checkedStudents.length;
  }
}

// Add event listeners to checkboxes for counter
document.addEventListener('DOMContentLoaded', function() {
  const checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
  });
  updateSelectedCount(); // Initialize counter
});

// Form validation for enroll student
document.addEventListener('DOMContentLoaded', function() {
  const enrollForm = document.querySelector('form[action*="enroll"]');
  if (enrollForm) {
    enrollForm.addEventListener('submit', function(e) {
      const checkedStudents = document.querySelectorAll('input[name="student_ids[]"]:checked');
      if (checkedStudents.length === 0) {
        e.preventDefault();
        alert('Please select at least one student to enroll.');
        return false;
      }
    });
  }
});

// Auto-hide success message after 5 seconds
setTimeout(function() {
  const successMessage = document.getElementById('successMessage');
  if (successMessage) {
    successMessage.style.transform = 'translateY(-100%)';
    successMessage.style.opacity = '0';
    setTimeout(function() {
      successMessage.remove();
    }, 500);
  }
}, 5000);

// Toggle student details
function toggleDetails(studentId) {
  const detailsRow = document.getElementById('details-' + studentId);
  const chevron = document.getElementById('chevron-' + studentId);
  
  if (detailsRow.classList.contains('hidden')) {
    detailsRow.classList.remove('hidden');
    chevron.style.transform = 'rotate(180deg)';
  } else {
    detailsRow.classList.add('hidden');
    chevron.style.transform = 'rotate(0deg)';
  }
}

// Show debug information
function showDebugInfo(studentName, riskPredictions, lateSubmissionPct, missedSubmissionPct) {
  const predictionsText = riskPredictions.length > 0 
    ? riskPredictions.join(', ') 
    : 'No risk predictions';
  
  alert(`Debug Info for ${studentName}:\n\nRaw API Risk Predictions:\n${predictionsText}\n\nLate Submission: ${lateSubmissionPct}%\nMissed Submission: ${missedSubmissionPct}%`);
}
</script>
@endsection 