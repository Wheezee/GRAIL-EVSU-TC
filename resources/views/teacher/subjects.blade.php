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
      <span class="text-gray-900 dark:text-gray-100 font-medium whitespace-nowrap">Subjects</span>
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
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
  <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Subjects</h2>
  <button onclick="openAddSubjectModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-evsu hover:bg-evsuDark text-white font-semibold rounded-lg shadow transition-transform transform hover:scale-105 focus:outline-none">
    <i data-lucide="plus" class="w-5 h-5"></i>
    Add Subject
  </button>
</div>



@if (count($subjects) > 0)
  <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach ($subjects as $subject)
      <div class="relative group" data-subject-id="{{ $subject->id }}">
        <a href="{{ route('subjects.classes', $subject->id) }}"
           class="block bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm hover:shadow-lg hover:scale-105 transition-all duration-200 cursor-pointer">
          <div class="mb-2">
            <span class="text-xl font-extrabold text-evsu dark:text-evsu block leading-tight" data-subject-code="{{ $subject->code }}">{{ $subject->code }}</span>
            <span class="text-lg font-semibold text-gray-900 dark:text-gray-100" data-subject-title="{{ $subject->title }}">{{ $subject->title }}</span>
          </div>
          <div class="mt-2 text-sm text-gray-700 dark:text-gray-200">
            <div class="flex items-center gap-2">
              <i data-lucide="book-open" class="w-4 h-4 text-evsu"></i>
              <span data-subject-units="{{ $subject->units }}">{{ $subject->units }} Units</span>
            </div>
          </div>
        </a>
        
        <!-- Triple dot menu - outside the card link -->
        <button type="button" onclick="toggleDropdown('dropdown-{{ $subject->id }}')"
          class="absolute top-3 right-3 z-10 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none bg-white dark:bg-gray-800 shadow-sm">
          <i data-lucide="more-vertical" class="w-5 h-5 text-gray-400"></i>
        </button>
        
        <!-- Dropdown menu - outside the card link -->
        <div id="dropdown-{{ $subject->id }}" class="hidden absolute top-10 right-3 z-20 w-32 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1">
          <button type="button" onclick="openEditSubjectModal({{ $subject->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center gap-2">
            <i data-lucide="edit-3" class="w-4 h-4"></i> Edit
          </button>
          <button type="button" onclick="openDeleteSubjectModal({{ $subject->id }})" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900 flex items-center gap-2">
            <i data-lucide="trash-2" class="w-4 h-4"></i> Delete
          </button>
        </div>
      </div>
    @endforeach
  </div>
@else
  <div class="flex flex-col items-center justify-center py-24">
    <i data-lucide="book" class="w-16 h-16 text-gray-300 mb-4"></i>
    <p class="text-lg text-gray-500 mb-2">No subjects found. Click 'Add Subject' to get started.</p>
    <button onclick="openAddSubjectModal()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-evsu hover:bg-evsuDark text-white font-semibold rounded-lg shadow transition-transform transform hover:scale-105 focus:outline-none">
      <i data-lucide="plus" class="w-5 h-5"></i>
      Add Subject
    </button>
  </div>
@endif

<!-- Add Subject Modal -->
<div id="addSubjectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
      <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Add New Subject</h3>
      <button onclick="closeAddSubjectModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <form id="addSubjectForm" method="POST" action="{{ route('subjects.store') }}" class="p-6">
      @csrf
      
      <div class="space-y-4">
        <!-- Subject Code -->
        <div>
          <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject Code</label>
          <input type="text" id="code" name="code" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., MATH101"
                 value="{{ old('code') }}">
        </div>

        <!-- Subject Title -->
        <div>
          <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject Title</label>
          <input type="text" id="title" name="title" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., College Algebra"
                 value="{{ old('title') }}">
        </div>

        <!-- Units -->
        <div>
          <label for="units" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Units</label>
          <input type="number" id="units" name="units" step="0.1" min="0.5" max="6.0" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="3.0"
                 value="{{ old('units') }}">
        </div>


      </div>

      <!-- Modal Footer -->
      <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button" onclick="closeAddSubjectModal()" 
                class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
          Cancel
        </button>
        <button type="submit" 
                class="flex-1 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">
          Add Subject
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Subject Modal -->
<div id="editSubjectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
      <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Edit Subject</h3>
      <button onclick="closeEditSubjectModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <form id="editSubjectForm" method="POST" class="p-6">
      @csrf
      @method('PUT')
      
      <div class="space-y-4">
        <!-- Subject Code -->
        <div>
          <label for="edit_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject Code</label>
          <input type="text" id="edit_code" name="code" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., MATH101">
        </div>

        <!-- Subject Title -->
        <div>
          <label for="edit_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject Title</label>
          <input type="text" id="edit_title" name="title" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="e.g., College Algebra">
        </div>

        <!-- Units -->
        <div>
          <label for="edit_units" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Units</label>
          <input type="number" id="edit_units" name="units" step="0.1" min="0.5" max="6.0" required 
                 class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-evsu focus:border-transparent dark:bg-gray-700 dark:text-white"
                 placeholder="3.0">
        </div>


      </div>

      <!-- Modal Footer -->
      <div class="flex gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
        <button type="button" onclick="closeEditSubjectModal()" 
                class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
          Cancel
        </button>
        <button type="submit" 
                class="flex-1 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">
          Update Subject
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteSubjectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
      <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Delete Subject</h3>
      <button onclick="closeDeleteSubjectModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>
    </div>

    <!-- Modal Body -->
    <div class="p-6">
      <div class="flex items-center gap-3 mb-4">
        <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
        <div>
          <p class="text-gray-900 dark:text-gray-100 font-medium">Are you sure you want to delete this subject?</p>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">This action cannot be undone.</p>
        </div>
      </div>
      
      <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-700 dark:text-gray-300">
          <span class="font-medium">Subject:</span> <span id="deleteSubjectCode" class="text-evsu"></span> - <span id="deleteSubjectTitle"></span>
        </p>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="flex gap-3 p-6 pt-0">
      <button type="button" onclick="closeDeleteSubjectModal()" 
              class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors">
        Cancel
      </button>
      <form id="deleteSubjectForm" method="POST" class="flex-1">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
          Delete Subject
        </button>
      </form>
    </div>
  </div>
</div>

<script>
function openAddSubjectModal() {
  document.getElementById('addSubjectModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeAddSubjectModal() {
  document.getElementById('addSubjectModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
  // Reset form
  document.getElementById('addSubjectForm').reset();
}

// Close modal when clicking outside
document.getElementById('addSubjectModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeAddSubjectModal();
  }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeAddSubjectModal();
    closeEditSubjectModal();
    closeDeleteSubjectModal();
  }
});

// Close edit modal when clicking outside
document.getElementById('editSubjectModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeEditSubjectModal();
  }
});

// Close delete modal when clicking outside
document.getElementById('deleteSubjectModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeDeleteSubjectModal();
  }
});

lucide.createIcons();

// Auto-open modal if there are validation errors
@if ($errors->any())
  document.addEventListener('DOMContentLoaded', function() {
    openAddSubjectModal();
  });
@endif

// Success message animation and auto-hide
@if (session('success'))
  document.addEventListener('DOMContentLoaded', function() {
    const successMessage = document.getElementById('successMessage');
    if (successMessage) {
      // Slide in animation
      successMessage.style.transform = 'translateY(-20px)';
      successMessage.style.opacity = '0';
      
      setTimeout(() => {
        successMessage.style.transform = 'translateY(0)';
        successMessage.style.opacity = '1';
      }, 100);
      
      // Auto-hide after 5 seconds
      setTimeout(() => {
        successMessage.style.transform = 'translateY(-20px)';
        successMessage.style.opacity = '0';
        setTimeout(() => {
          successMessage.remove();
        }, 300);
      }, 5000);
    }
  });
@endif

function toggleDropdown(id) {
  // Hide all dropdowns first
  document.querySelectorAll('[id^="dropdown-"]').forEach(el => el.classList.add('hidden'));
  // Toggle the selected dropdown
  const el = document.getElementById(id);
  if (el) el.classList.toggle('hidden');
}
// Hide dropdowns when clicking outside
window.addEventListener('click', function(e) {
  document.querySelectorAll('[id^="dropdown-"]').forEach(el => {
    if (!el.contains(e.target) && !el.previousElementSibling.contains(e.target)) {
      el.classList.add('hidden');
    }
  });
});
// Edit and Delete functions
function openEditSubjectModal(id) {
  // Get subject data from the card
  const card = document.querySelector(`[data-subject-id="${id}"]`);
  const code = card.querySelector('[data-subject-code]').textContent;
  const title = card.querySelector('[data-subject-title]').textContent;
  const units = card.querySelector('[data-subject-units]').textContent.replace(' Units', '');
  
  // Fill the edit form
  document.getElementById('edit_code').value = code;
  document.getElementById('edit_title').value = title;
  document.getElementById('edit_units').value = units;
  
  // Set the form action
  document.getElementById('editSubjectForm').action = `/subjects/${id}`;
  
  // Show the modal
  document.getElementById('editSubjectModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeEditSubjectModal() {
  document.getElementById('editSubjectModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
  document.getElementById('editSubjectForm').reset();
}

function openDeleteSubjectModal(id) {
  // Get subject data from the card
  const card = document.querySelector(`[data-subject-id="${id}"]`);
  const code = card.querySelector('[data-subject-code]').textContent;
  const title = card.querySelector('[data-subject-title]').textContent;
  
  // Fill the delete confirmation
  document.getElementById('deleteSubjectCode').textContent = code;
  document.getElementById('deleteSubjectTitle').textContent = title;
  
  // Set the form action
  document.getElementById('deleteSubjectForm').action = `/subjects/${id}`;
  
  // Show the modal
  document.getElementById('deleteSubjectModal').classList.remove('hidden');
  document.body.style.overflow = 'hidden';
}

function closeDeleteSubjectModal() {
  document.getElementById('deleteSubjectModal').classList.add('hidden');
  document.body.style.overflow = 'auto';
}
</script>
@endsection 