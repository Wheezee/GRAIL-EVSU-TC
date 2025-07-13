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
      <span class="text-gray-900 dark:text-gray-100 font-medium whitespace-nowrap">Students</span>
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
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Student Management</h2>
    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage all students in the system</p>
  </div>
  <button onclick="openAddStudentModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-evsu hover:bg-evsuDark text-white font-semibold rounded-lg shadow transition-transform transform hover:scale-105 focus:outline-none">
    <i data-lucide="user-plus" class="w-5 h-5"></i>
    Add Student
  </button>
</div>

<!-- Search and Filter Section -->
<div class="mb-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
  <div class="flex flex-col sm:flex-row gap-4">
    <div class="flex-1">
      <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Students</label>
      <div class="relative">
        <input type="text" id="search" placeholder="Search by name, student ID, or email..." 
               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white">
        <i data-lucide="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400"></i>
      </div>
    </div>
    <div class="sm:w-48">
      <label for="filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filter by</label>
      <select id="filter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white">
        <option value="">All Students</option>
        <option value="enrolled">Enrolled</option>
        <option value="not_enrolled">Not Enrolled</option>
      </select>
    </div>
  </div>
</div>

<!-- Students Table -->
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Enrollment Status</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($students as $student)
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
              {{ $student->student_id }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-evsu rounded-full flex items-center justify-center text-white text-xs font-semibold">
                  {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                </div>
                <div>
                  <div class="font-medium">{{ $student->full_name }}</div>
                  @if($student->middle_name)
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $student->middle_name }}</div>
                  @endif
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
              {{ $student->email }}
            </td>
            <td class="px-6 py-4 text-sm text-center">
              @if($student->classSections()->count() > 0)
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                  <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                  Enrolled ({{ $student->classSections()->count() }})
                </span>
              @else
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300">
                  <i data-lucide="user-x" class="w-3 h-3 mr-1"></i>
                  Not Enrolled
                </span>
              @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
              <div class="flex items-center gap-x-2 justify-center">
                <button
                  onclick="openEditStudentModal('{{ $student->id }}', '{{ $student->student_id }}', '{{ $student->first_name }}', '{{ $student->last_name }}', '{{ $student->email }}', '{{ $student->middle_name ?? '' }}', '{{ $student->birth_date ?? '' }}', '{{ $student->gender ?? '' }}', '{{ $student->contact_number ?? '' }}', '{{ $student->address ?? '' }}')"
                  class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 transition-colors"
                  type="button"
                >
                  Edit
                </button>
                <button
                  onclick="if(confirm('Are you sure you want to delete this student? This action cannot be undone.')) { document.getElementById('delete-form-{{ $student->id }}').submit(); }"
                  class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 transition-colors"
                  type="button"
                >
                  Delete
                </button>
                <form id="delete-form-{{ $student->id }}"
                      method="POST"
                      action="{{ route('students.destroy', $student->id) }}"
                      style="display: none;">
                  @csrf
                  @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
              <div class="flex flex-col items-center">
                <i data-lucide="users" class="w-8 h-8 mb-2"></i>
                <p>No students found.</p>
                <p class="text-sm">Click "Add Student" to create your first student.</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  <!-- Pagination -->
  @if($students->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
      {{ $students->links() }}
    </div>
  @endif
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-2xl mx-4 transform transition-all">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-3">
        <i data-lucide="user-plus" class="w-6 h-6 text-evsu"></i>
        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Add New Student</h3>
      </div>
      <button onclick="closeAddStudentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <form method="POST" action="{{ route('students.store') }}" class="p-6">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

        <!-- Middle Name -->
        <div>
          <label for="middle_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name (Optional)</label>
          <input type="text" id="middle_name" name="middle_name" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Santos"
                 value="{{ old('middle_name') }}">
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email (Optional)</label>
          <input type="email" id="email" name="email" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., juan.delacruz@email.com"
                 value="{{ old('email') }}">
        </div>

        <!-- Birth Date -->
        <div>
          <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Birth Date (Optional)</label>
          <input type="date" id="birth_date" name="birth_date" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 value="{{ old('birth_date') }}">
        </div>

        <!-- Gender -->
        <div>
          <label for="gender" class="block text-sm font-medium text-gray-300 mb-2">Gender (Optional)</label>
          <select id="gender" name="gender" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white">
            <option value="">Select Gender</option>
            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
          </select>
        </div>

        <!-- Contact Number -->
        <div>
          <label for="contact_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Number (Optional)</label>
          <input type="text" id="contact_number" name="contact_number" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., +63 912 345 6789"
                 value="{{ old('contact_number') }}">
        </div>

        <!-- Address -->
        <div class="md:col-span-2">
          <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address (Optional)</label>
          <textarea id="address" name="address" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                    placeholder="Enter complete address">{{ old('address') }}</textarea>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button" onclick="closeAddStudentModal()" 
                class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
          Cancel
        </button>
        <button type="submit" 
                class="flex-1 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">
          Add Student
        </button>
      </div>
    </form>
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
                 placeholder="e.g., 2025-0007">
        </div>

        <!-- First Name -->
        <div>
          <label for="edit_first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name</label>
          <input type="text" id="edit_first_name" name="first_name" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Juan">
        </div>

        <!-- Last Name -->
        <div>
          <label for="edit_last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name</label>
          <input type="text" id="edit_last_name" name="last_name" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Dela Cruz">
        </div>

        <!-- Middle Name -->
        <div>
          <label for="edit_middle_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Middle Name (Optional)</label>
          <input type="text" id="edit_middle_name" name="middle_name" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., Santos">
        </div>

        <!-- Email -->
        <div>
          <label for="edit_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email (Optional)</label>
          <input type="email" id="edit_email" name="email" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., juan.delacruz@email.com">
        </div>

        <!-- Birth Date -->
        <div>
          <label for="edit_birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Birth Date (Optional)</label>
          <input type="date" id="edit_birth_date" name="birth_date" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white">
        </div>

        <!-- Gender -->
        <div>
          <label for="edit_gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender (Optional)</label>
          <select id="edit_gender" name="gender" 
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white">
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>

        <!-- Contact Number -->
        <div>
          <label for="edit_contact_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Number (Optional)</label>
          <input type="text" id="edit_contact_number" name="contact_number" 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., +63 912 345 6789">
        </div>

        <!-- Address -->
        <div class="md:col-span-2">
          <label for="edit_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address (Optional)</label>
          <textarea id="edit_address" name="address" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                    placeholder="Enter complete address"></textarea>
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

<script>
// Modal functions
function openAddStudentModal() {
  document.getElementById('addStudentModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeAddStudentModal() {
  document.getElementById('addStudentModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
  document.querySelector('#addStudentModal form').reset();
}

function openEditStudentModal(id, studentId, firstName, lastName, email, middleName, birthDate, gender, contactNumber, address) {
  document.getElementById('editStudentModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
  
  // Set form action
  document.getElementById('editStudentForm').action = `/students/${id}`;
  
  // Populate form fields
  document.getElementById('edit_student_id').value = studentId;
  document.getElementById('edit_first_name').value = firstName;
  document.getElementById('edit_last_name').value = lastName;
  document.getElementById('edit_email').value = email;
  document.getElementById('edit_middle_name').value = middleName;
  document.getElementById('edit_birth_date').value = birthDate;
  document.getElementById('edit_gender').value = gender;
  document.getElementById('edit_contact_number').value = contactNumber;
  document.getElementById('edit_address').value = address;
}

function closeEditStudentModal() {
  document.getElementById('editStudentModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('search');
  const filterSelect = document.getElementById('filter');
  const tableRows = document.querySelectorAll('tbody tr');
  
  function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const filterValue = filterSelect.value;
    
    tableRows.forEach(row => {
      const studentId = row.cells[0].textContent.toLowerCase();
      const name = row.cells[1].textContent.toLowerCase();
      const email = row.cells[2].textContent.toLowerCase();
      const enrollmentStatus = row.cells[3].textContent.toLowerCase();
      
      const matchesSearch = studentId.includes(searchTerm) || 
                           name.includes(searchTerm) || 
                           email.includes(searchTerm);
      
      let matchesFilter = true;
      if (filterValue === 'enrolled') {
        matchesFilter = enrollmentStatus.includes('enrolled');
      } else if (filterValue === 'not_enrolled') {
        matchesFilter = enrollmentStatus.includes('not enrolled');
      }
      
      if (matchesSearch && matchesFilter) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }
  
  searchInput.addEventListener('input', filterTable);
  filterSelect.addEventListener('change', filterTable);
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