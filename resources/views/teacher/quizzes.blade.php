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

/* Score cell animation */
.score-cell {
  transition: all 0.3s ease;
}

.score-cell.saved {
  background-color: #dcfce7;
  transform: scale(1.05);
}

.dark .score-cell.saved {
  background-color: #166534;
}

/* Validation message styles */
.validation-message {
  animation: slideIn 0.3s ease-out;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Error state for score inputs */
.score-cell.error {
  border-color: #ef4444;
  background-color: #fef2f2;
}

.dark .score-cell.error {
  border-color: #dc2626;
  background-color: #450a0a;
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
      <a href="{{ route('grading.system', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}" class="hover:text-evsu dark:hover:text-evsu transition-colors whitespace-nowrap">
        {{ $classSection->section }}
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <span class="text-gray-900 dark:text-gray-100 font-medium whitespace-nowrap">Quizzes</span>
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
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Quizzes — {{ $classSection->section }}</h2>
    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $classSection->subject->code }} - {{ $classSection->subject->title }}</p>
  </div>
  <button onclick="openAddQuizModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-evsu hover:bg-evsuDark text-white font-semibold rounded-lg shadow transition-transform transform hover:scale-105 focus:outline-none">
    <i data-lucide="plus" class="w-5 h-5"></i>
    Add Quiz
  </button>
</div>

@if($quizzes->count() > 0)
  <!-- Quiz Navigation Tabs -->
  <div class="mb-6">
    <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700">
      @foreach($quizzes as $quiz)
        <div class="px-0 py-0 text-sm font-medium rounded-t-lg transition-all duration-200 flex items-center group {{ $selectedQuiz && $selectedQuiz->id === $quiz->id ? 'bg-evsu text-white' : 'text-gray-600 dark:text-gray-400 hover:text-evsu dark:hover:text-evsu hover:bg-gray-100 dark:hover:bg-gray-700' }}">
          <a href="{{ route('quizzes.show', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'quiz' => $quiz->id]) }}" class="flex-1 px-4 py-2 flex items-center min-w-0">
            <span class="truncate">{{ $quiz->name }}</span>
          </a>
        </div>
      @endforeach
    </div>
  </div>

  @if($selectedQuiz)
    <!-- Quiz Details -->
    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
      <div class="flex flex-wrap items-center justify-between gap-6">
        <div class="flex flex-wrap items-center gap-6 text-sm">
          <div class="flex items-center gap-2">
            <span class="font-medium text-gray-700 dark:text-gray-300">Max Score:</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $selectedQuiz->max_score }}</span>
          </div>
          @if($selectedQuiz->description)
            <div class="flex items-center gap-2">
              <span class="font-medium text-gray-700 dark:text-gray-300">Description:</span>
              <span class="text-gray-900 dark:text-gray-100">{{ $selectedQuiz->description }}</span>
            </div>
          @endif
        </div>
        <div class="flex items-center gap-2 ml-auto">
          <button onclick="openEditQuizModal()" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg shadow transition-transform transform hover:scale-105 focus:outline-none h-10 bg-evsu text-white hover:bg-evsuDark">
            <i data-lucide="edit-3" class="w-4 h-4"></i>
            Edit
          </button>
          <button
            onclick="if(confirm('Delete this quiz? This action cannot be undone.')) { document.getElementById('delete-quiz-form-{{ $selectedQuiz->id }}').submit(); }"
            class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-colors"
            type="button"
          >
            Delete
          </button>
          <form id="delete-quiz-form-{{ $selectedQuiz->id }}"
                method="POST"
                action="{{ route('quizzes.destroy', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'quiz' => $selectedQuiz->id]) }}"
                style="display: none;">
            @csrf
            @method('DELETE')
          </form>
        </div>
      </div>
    </div>

    <!-- Grading Sheet Table -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
      <form method="POST" action="{{ route('quizzes.scores.save', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'quiz' => $selectedQuiz->id]) }}" id="scoresForm">
        @csrf
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student Name</th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Score</th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              @foreach($students as $student)
                @php
                  $studentScore = $selectedQuiz->getStudentScore($student->id);
                  $currentScore = $studentScore ? $studentScore->score : null;
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    <div class="flex items-center gap-2">
                      {{ $student->full_name }}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                    <input type="number" 
                           name="scores[{{ $student->id }}]" 
                           value="{{ $currentScore }}"
                           min="0" 
                           max="{{ $selectedQuiz->max_score }}" 
                           step="0.01"
                           class="score-cell w-20 px-3 py-2 text-center border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                           placeholder="0"
                           oninput="validateScore(this, {{ $selectedQuiz->max_score }})">
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Save Button -->
        <div class="p-6 border-t border-gray-200 dark:border-gray-700">
          <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg shadow transition-transform transform hover:scale-105 focus:outline-none">
            <i data-lucide="save" class="w-5 h-5"></i>
            Save Scores
          </button>
        </div>
      </form>
    </div>
  @endif
@else
  <!-- Empty State -->
  <div class="flex flex-col items-center justify-center py-24">
    <i data-lucide="clipboard-list" class="w-16 h-16 text-gray-300 mb-4"></i>
    <p class="text-lg text-gray-500 mb-2">No quizzes found. Click 'Add Quiz' to get started.</p>
    <button onclick="openAddQuizModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-evsu hover:bg-evsuDark text-white font-semibold rounded-lg shadow transition-transform transform hover:scale-105 focus:outline-none">
      <i data-lucide="plus" class="w-5 h-5"></i>
      Add Quiz
    </button>
  </div>
@endif

<!-- Add Quiz Modal -->
<div id="addQuizModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3">
        <i data-lucide="plus-circle" class="w-6 h-6 text-evsu"></i>
        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Add New Quiz</h3>
      </div>
      <button onclick="closeAddQuizModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <form method="POST" action="{{ route('quizzes.store', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}" class="p-6">
      @csrf
      <div class="space-y-4">
        <!-- Quiz Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quiz Name</label>
          <input type="text" id="name" name="name" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Quiz 1"
                 value="{{ old('name') }}">
        </div>

        <!-- Max Score -->
        <div>
          <label for="max_score" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Score</label>
          <input type="number" id="max_score" name="max_score" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., 20"
                 min="0.01" 
                 max="999.99" 
                 step="0.01"
                 value="{{ old('max_score') }}">
        </div>

        <!-- Description -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description (Optional)</label>
          <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                    placeholder="Quiz description...">{{ old('description') }}</textarea>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button" onclick="closeAddQuizModal()" 
                class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
          Cancel
        </button>
        <button type="submit" 
                class="flex-1 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">
          Add Quiz
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Quiz Modal -->
<div id="editQuizModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3">
        <i data-lucide="edit-3" class="w-6 h-6 text-evsu"></i>
        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Edit Quiz</h3>
      </div>
      <button onclick="closeEditQuizModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <form method="POST" action="{{ $selectedQuiz ? route('quizzes.update', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'quiz' => $selectedQuiz->id]) : '#' }}" class="p-6">
      @csrf
      @method('PUT')
      <div class="space-y-4">
        <!-- Quiz Name -->
        <div>
          <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quiz Name</label>
          <input type="text" id="edit_name" name="name" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Quiz 1"
                 value="{{ $selectedQuiz ? $selectedQuiz->name : '' }}">
        </div>

        <!-- Max Score -->
        <div>
          <label for="edit_max_score" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Max Score</label>
          <input type="number" id="edit_max_score" name="max_score" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., 20"
                 min="0.01" 
                 max="999.99" 
                 step="0.01"
                 value="{{ $selectedQuiz ? $selectedQuiz->max_score : '' }}">
        </div>

        <!-- Description -->
        <div>
          <label for="edit_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description (Optional)</label>
          <textarea id="edit_description" name="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                    placeholder="Quiz description...">{{ $selectedQuiz ? $selectedQuiz->description : '' }}</textarea>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button" onclick="closeEditQuizModal()" 
                class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
          Cancel
        </button>
        <button type="submit" 
                class="flex-1 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">
          Update Quiz
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openAddQuizModal() {
  document.getElementById('addQuizModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeAddQuizModal() {
  document.getElementById('addQuizModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
  document.querySelector('#addQuizModal form').reset();
}

function openEditQuizModal() {
  document.getElementById('editQuizModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeEditQuizModal() {
  document.getElementById('editQuizModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
}

// Score validation function
function validateScore(input, maxScore) {
  let value = parseFloat(input.value);
  
  // Handle empty input
  if (input.value === '' || isNaN(value)) {
    input.value = '';
    clearValidationMessage(input);
    input.classList.remove('error');
    return;
  }
  
  // Prevent negative values
  if (value < 0) {
    input.value = 0;
    input.classList.add('error');
    showValidationMessage(input, 'Scores cannot be negative');
    return;
  }
  
  // Prevent values above max score
  if (value > maxScore) {
    input.value = maxScore;
    input.classList.add('error');
    showValidationMessage(input, `Score cannot exceed ${maxScore}`);
    return;
  }
  
  // Clear any validation messages and error styling
  clearValidationMessage(input);
  input.classList.remove('error');
}

// Show validation message
function showValidationMessage(input, message) {
  // Remove existing message
  clearValidationMessage(input);
  
  // Create message element
  const messageEl = document.createElement('div');
  messageEl.className = 'validation-message text-red-500 text-xs mt-1 absolute z-10 bg-white dark:bg-gray-800 px-2 py-1 rounded border border-red-200 dark:border-red-800';
  messageEl.textContent = message;
  
  // Position the message
  const rect = input.getBoundingClientRect();
  messageEl.style.left = rect.left + 'px';
  messageEl.style.top = (rect.bottom + 5) + 'px';
  
  // Add to document
  document.body.appendChild(messageEl);
  
  // Store reference for later removal
  input.validationMessage = messageEl;
  
  // Auto-remove after 3 seconds
  setTimeout(() => {
    clearValidationMessage(input);
  }, 3000);
}

// Clear validation message
function clearValidationMessage(input) {
  if (input.validationMessage) {
    input.validationMessage.remove();
    input.validationMessage = null;
  }
  // Remove error styling
  input.classList.remove('error');
}

// Score validation
document.addEventListener('DOMContentLoaded', function() {
  const scoreInputs = document.querySelectorAll('input[name^="scores"]');
  const maxScore = {{ $selectedQuiz ? $selectedQuiz->max_score : 0 }};
  
  scoreInputs.forEach(input => {
    // Add relative positioning to parent for validation messages
    input.parentElement.style.position = 'relative';
    
    // Prevent negative input on keydown
    input.addEventListener('keydown', function(e) {
      if (e.key === '-' || e.key === 'e') {
        e.preventDefault();
      }
    });
    
    // Additional validation on blur
    input.addEventListener('blur', function() {
      validateScore(this, maxScore);
    });
  });
  
  // Form submission with animation and validation
  const scoresForm = document.getElementById('scoresForm');
  if (scoresForm) {
    scoresForm.addEventListener('submit', function(e) {
      // Validate all scores before submission
      let hasErrors = false;
      scoreInputs.forEach(input => {
        const value = parseFloat(input.value);
        if (input.value !== '' && (value < 0 || value > maxScore)) {
          validateScore(input, maxScore);
          hasErrors = true;
        }
      });
      
      if (hasErrors) {
        e.preventDefault();
        return false;
      }
      
      // Add animation for valid submission
      const scoreCells = document.querySelectorAll('.score-cell');
      scoreCells.forEach(cell => {
        cell.classList.add('saved');
        setTimeout(() => {
          cell.classList.remove('saved');
        }, 1000);
      });
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
</script>
@endsection 