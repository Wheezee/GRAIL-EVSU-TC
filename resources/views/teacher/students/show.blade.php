@extends('layouts.app')

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
      <a href="{{ route('students.index') }}" class="hover:text-evsu dark:hover:text-evsu transition-colors whitespace-nowrap">
        Students
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <span class="text-gray-900 dark:text-gray-100 font-medium whitespace-nowrap">{{ $student->full_name }}</span>
    </li>
  </ol>
</nav>

<!-- Header Section -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
  <div class="flex items-center gap-4">
    <div class="w-16 h-16 bg-evsu rounded-full flex items-center justify-center text-white text-xl font-bold">
      {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
    </div>
    <div>
      <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $student->full_name }}</h1>
      <p class="text-gray-600 dark:text-gray-400">{{ $student->student_id }}</p>
    </div>
  </div>
  <div class="flex gap-2">
    <a href="{{ route('students.index') }}" 
       class="inline-flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
      <i data-lucide="arrow-left" class="w-4 h-4"></i>
      Back to Students
    </a>
    <button onclick="openEditStudentModal()" 
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
      <i data-lucide="edit" class="w-4 h-4"></i>
      Edit Student
    </button>
  </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <!-- Left Column: Demographics -->
  <div class="lg:col-span-1">
    <!-- Demographics Card -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6">
      <div class="flex items-center gap-3 mb-4">
        <i data-lucide="user" class="w-5 h-5 text-evsu"></i>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Demographics</h2>
      </div>
      
      <div class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Full Name</label>
          <p class="text-gray-900 dark:text-gray-100">{{ $student->full_name }}</p>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Student ID</label>
          <p class="text-gray-900 dark:text-gray-100 font-mono">{{ $student->student_id }}</p>
        </div>
        
        @if($student->middle_name)
        <div>
          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Middle Name</label>
          <p class="text-gray-900 dark:text-gray-100">{{ $student->middle_name }}</p>
        </div>
        @endif
        
        @if($student->email)
        <div>
          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Email</label>
          <p class="text-gray-900 dark:text-gray-100">{{ $student->email }}</p>
        </div>
        @endif
        
        @if($student->birth_date)
        <div>
          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Birth Date</label>
          <p class="text-gray-900 dark:text-gray-100">{{ $student->birth_date->format('F j, Y') }} @if($age)({{ $age }} years old)@endif</p>
        </div>
        @endif
        
        @if($student->gender)
        <div>
          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Gender</label>
          <p class="text-gray-900 dark:text-gray-100 capitalize">{{ $student->gender }}</p>
        </div>
        @endif
        
        @if($student->contact_number)
        <div>
          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Contact Number</label>
          <p class="text-gray-900 dark:text-gray-100">{{ $student->contact_number }}</p>
        </div>
        @endif
        
        @if($student->address)
        <div>
          <label class="block text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Address</label>
          <p class="text-gray-900 dark:text-gray-100">{{ $student->address }}</p>
        </div>
        @endif
      </div>
    </div>

    <!-- Enrollment Status Card -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
      <div class="flex items-center gap-3 mb-4">
        <i data-lucide="graduation-cap" class="w-5 h-5 text-evsu"></i>
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Enrollment Status</h2>
      </div>
      
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <span class="text-gray-600 dark:text-gray-400">Total Enrolled Classes</span>
          <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $enrolledClasses->count() }}</span>
        </div>
        
        <div class="flex items-center justify-between">
          <span class="text-gray-600 dark:text-gray-400">Enrollment Date</span>
          <span class="font-semibold text-gray-900 dark:text-gray-100">
            @if($enrolledClasses->count() > 0 && $enrolledClasses->first()->pivot->enrollment_date)
              {{ $enrolledClasses->first()->pivot->enrollment_date->format('M j, Y') }}
            @else
              Not enrolled
            @endif
          </span>
        </div>
        
        <div class="flex items-center justify-between">
          <span class="text-gray-600 dark:text-gray-400">Status</span>
          <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                     @if($enrolledClasses->count() > 0) bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300 @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300 @endif">
            @if($enrolledClasses->count() > 0)
              <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
              Enrolled
            @else
              <i data-lucide="user-x" class="w-3 h-3 mr-1"></i>
              Not Enrolled
            @endif
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Column: Enrolled Subjects and Academic Performance -->
  <div class="lg:col-span-2">
    @if($enrolledClasses->count() > 0)
      <!-- Enrolled Subjects -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6">
        <div class="flex items-center gap-3 mb-6">
          <i data-lucide="book-open" class="w-5 h-5 text-evsu"></i>
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Enrolled Subjects</h2>
        </div>
        
        <div class="space-y-4">
          @foreach($enrolledClasses as $classSection)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
              <div class="flex items-start justify-between mb-3">
                <div>
                  <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $classSection->subject->code }} - {{ $classSection->subject->name }}</h3>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Section {{ $classSection->section }} • {{ $classSection->schedule }}</p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">Teacher: {{ $classSection->teacher->name }}</p>
                </div>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-evsu/10 text-evsu">
                  {{ $classSection->pivot->status }}
                </span>
              </div>
              
              <!-- Academic Performance Summary -->
              @if(isset($academicData[$classSection->id]))
                @php $data = $academicData[$classSection->id]; @endphp
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                  <div class="text-center">
                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                      {{ $data['activities']['average'] ? number_format($data['activities']['average'], 1) : 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Activities ({{ $data['activities']['count'] }})</div>
                  </div>
                  <div class="text-center">
                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                      {{ $data['quizzes']['average'] ? number_format($data['quizzes']['average'], 1) : 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Quizzes ({{ $data['quizzes']['count'] }})</div>
                  </div>
                  <div class="text-center">
                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                      {{ $data['exams']['average'] ? number_format($data['exams']['average'], 1) : 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Exams ({{ $data['exams']['count'] }})</div>
                  </div>
                  <div class="text-center">
                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                      {{ $data['projects']['average'] ? number_format($data['projects']['average'], 1) : 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Projects ({{ $data['projects']['count'] }})</div>
                  </div>
                  <div class="text-center">
                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                      {{ $data['recitations']['average'] ? number_format($data['recitations']['average'], 1) : 'N/A' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Recitations ({{ $data['recitations']['count'] }})</div>
                  </div>
                </div>
              @endif
            </div>
          @endforeach
        </div>
      </div>

      <!-- Detailed Academic Performance -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6">
        <div class="flex items-center gap-3 mb-6">
          <i data-lucide="bar-chart-3" class="w-5 h-5 text-evsu"></i>
          <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Detailed Academic Performance</h2>
        </div>
        
        <div class="space-y-6">
          @foreach($enrolledClasses as $classSection)
            @if(isset($academicData[$classSection->id]))
              @php $data = $academicData[$classSection->id]; @endphp
              <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $classSection->subject->code }} - {{ $classSection->subject->name }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <!-- Activities -->
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex items-center gap-2 mb-2">
                      <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-600"></i>
                      <span class="font-medium text-gray-900 dark:text-gray-100">Activities</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                      {{ $data['activities']['average'] ? number_format($data['activities']['average'], 1) : 'N/A' }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ $data['activities']['scores']->count() }} of {{ $data['activities']['count'] }} completed
                    </div>
                  </div>
                  
                  <!-- Quizzes -->
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex items-center gap-2 mb-2">
                      <i data-lucide="help-circle" class="w-4 h-4 text-green-600"></i>
                      <span class="font-medium text-gray-900 dark:text-gray-100">Quizzes</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                      {{ $data['quizzes']['average'] ? number_format($data['quizzes']['average'], 1) : 'N/A' }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ $data['quizzes']['scores']->count() }} of {{ $data['quizzes']['count'] }} completed
                    </div>
                  </div>
                  
                  <!-- Exams -->
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex items-center gap-2 mb-2">
                      <i data-lucide="file-text" class="w-4 h-4 text-red-600"></i>
                      <span class="font-medium text-gray-900 dark:text-gray-100">Exams</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                      {{ $data['exams']['average'] ? number_format($data['exams']['average'], 1) : 'N/A' }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ $data['exams']['scores']->count() }} of {{ $data['exams']['count'] }} completed
                    </div>
                  </div>
                  
                  <!-- Projects -->
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex items-center gap-2 mb-2">
                      <i data-lucide="folder-open" class="w-4 h-4 text-purple-600"></i>
                      <span class="font-medium text-gray-900 dark:text-gray-100">Projects</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                      {{ $data['projects']['average'] ? number_format($data['projects']['average'], 1) : 'N/A' }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ $data['projects']['scores']->count() }} of {{ $data['projects']['count'] }} completed
                    </div>
                  </div>
                  
                  <!-- Recitations -->
                  <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <div class="flex items-center gap-2 mb-2">
                      <i data-lucide="mic" class="w-4 h-4 text-orange-600"></i>
                      <span class="font-medium text-gray-900 dark:text-gray-100">Recitations</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">
                      {{ $data['recitations']['average'] ? number_format($data['recitations']['average'], 1) : 'N/A' }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ $data['recitations']['scores']->count() }} of {{ $data['recitations']['count'] }} completed
                    </div>
                  </div>
                </div>
              </div>
            @endif
          @endforeach
        </div>
      </div>
    @else
      <!-- No Enrollments -->
      <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-8 text-center">
        <div class="flex flex-col items-center">
          <i data-lucide="graduation-cap" class="w-12 h-12 text-gray-400 mb-4"></i>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No Enrolled Subjects</h3>
          <p class="text-gray-600 dark:text-gray-400 mb-4">This student is not currently enrolled in any subjects.</p>
          <a href="{{ route('students.index') }}" 
             class="inline-flex items-center gap-2 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Enroll in Subjects
          </a>
        </div>
      </div>
    @endif
  </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl mx-4 transform transition-all">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3">
        <i data-lucide="user-edit" class="w-6 h-6 text-evsu"></i>
        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Edit Student</h3>
      </div>
      <button onclick="closeEditStudentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <form id="editStudentForm" method="POST" class="p-6">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Student ID -->
        <div>
          <label for="edit_student_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Student ID</label>
          <input type="text" id="edit_student_id" name="student_id" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., 2025-0007"
                 value="{{ $student->student_id }}">
        </div>

        <!-- First Name -->
        <div>
          <label for="edit_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
          <input type="text" id="edit_first_name" name="first_name" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Juan"
                 value="{{ $student->first_name }}">
        </div>

        <!-- Last Name -->
        <div>
          <label for="edit_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
          <input type="text" id="edit_last_name" name="last_name" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Dela Cruz"
                 value="{{ $student->last_name }}">
        </div>

        <!-- Middle Name -->
        <div>
          <label for="edit_middle_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name (Optional)</label>
          <input type="text" id="edit_middle_name" name="middle_name" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Santos"
                 value="{{ $student->middle_name }}">
        </div>

        <!-- Email -->
        <div>
          <label for="edit_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email (Optional)</label>
          <input type="email" id="edit_email" name="email" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., juan.delacruz@email.com"
                 value="{{ $student->email }}">
        </div>

        <!-- Birth Date -->
        <div>
          <label for="edit_birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Birth Date (Optional)</label>
          <input type="date" id="edit_birth_date" name="birth_date" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 value="{{ $student->birth_date ? $student->birth_date->format('Y-m-d') : '' }}">
        </div>

        <!-- Gender -->
        <div>
          <label for="edit_gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender (Optional)</label>
          <select id="edit_gender" name="gender" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white">
            <option value="">Select Gender</option>
            <option value="male" {{ $student->gender == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ $student->gender == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ $student->gender == 'other' ? 'selected' : '' }}>Other</option>
          </select>
        </div>

        <!-- Contact Number -->
        <div>
          <label for="edit_contact_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Number (Optional)</label>
          <input type="text" id="edit_contact_number" name="contact_number" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., +63 912 345 6789"
                 value="{{ $student->contact_number }}">
        </div>

        <!-- Address -->
        <div class="md:col-span-2">
          <label for="edit_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address (Optional)</label>
          <textarea id="edit_address" name="address" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                    placeholder="Enter complete address">{{ $student->address }}</textarea>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button" onclick="closeEditStudentModal()" 
                class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
          Cancel
        </button>
        <button type="submit" 
                class="flex-1 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">
          Update Student
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditStudentModal() {
  document.getElementById('editStudentModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
  
  // Set form action
  document.getElementById('editStudentForm').action = "{{ route('students.update', $student->id) }}";
}

function closeEditStudentModal() {
  document.getElementById('editStudentModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
}
</script>
@endsection 