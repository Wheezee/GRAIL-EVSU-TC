@extends('layouts.app')

@section('content')
<!-- Breadcrumbs -->
<nav class="mb-6" aria-label="Breadcrumb">
  <ol class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
    <li>
      <a href="{{ route('dashboard') }}" class="hover:text-evsu dark:hover:text-evsu transition-colors">
        Home
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-4 h-4 mx-2"></i>
      <a href="{{ route('subjects.index') }}" class="hover:text-evsu dark:hover:text-evsu transition-colors">
        Subjects
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-4 h-4 mx-2"></i>
      <a href="{{ route('subjects.classes', $classSection->subject->id) }}" class="hover:text-evsu dark:hover:text-evsu transition-colors">
        {{ $classSection->subject->code }} - {{ $classSection->subject->title }}
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-4 h-4 mx-2"></i>
      <a href="{{ route('grading.system', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id]) }}" class="hover:text-evsu dark:hover:text-evsu transition-colors">
        {{ $classSection->section }}
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-4 h-4 mx-2"></i>
      <span class="text-gray-900 dark:text-gray-100 font-medium">Gradebook</span>
    </li>
  </ol>
</nav>

<!-- Header Section -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4">
  <div>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Gradebook - {{ $classSection->section }}</h2>
    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $classSection->subject->code }} - {{ $classSection->subject->title }}</p>
  </div>
  <div class="flex gap-2">
    <button class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
      <i data-lucide="download" class="w-4 h-4"></i>
      Export
    </button>
    <button class="inline-flex items-center gap-2 px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">
      <i data-lucide="plus" class="w-4 h-4"></i>
      Add Assessment
    </button>
  </div>
</div>

<!-- Gradebook Table -->
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-700 z-10">Student</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-blue-50 dark:bg-blue-900/20">Activities</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-green-50 dark:bg-green-900/20">Quizzes</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-yellow-50 dark:bg-yellow-900/20">Exams</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-purple-50 dark:bg-purple-900/20">Recitation</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-indigo-50 dark:bg-indigo-900/20">Projects</th>
          <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-100 dark:bg-gray-600">Final Grade</th>
        </tr>
        <tr>
          <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider sticky left-0 bg-gray-50 dark:bg-gray-700 z-10">Name</th>
          <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-blue-50 dark:bg-blue-900/20">A1 | A2 | A3</th>
          <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-green-50 dark:bg-green-900/20">Q1 | Q2 | Q3</th>
          <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-yellow-50 dark:bg-yellow-900/20">Mid | Final</th>
          <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-purple-50 dark:bg-purple-900/20">R1 | R2 | R3</th>
          <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-indigo-50 dark:bg-indigo-900/20">P1 | P2</th>
          <th class="px-6 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-100 dark:bg-gray-600">Grade</th>
        </tr>
      </thead>
      <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        <!-- Placeholder data - will be dynamic -->
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 sticky left-0 bg-white dark:bg-gray-800 z-10">
            <div>
              <div class="font-medium">Juan Dela Cruz</div>
              <div class="text-gray-500 dark:text-gray-400">2025-0001</div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-blue-50 dark:bg-blue-900/20">
            <div class="grid grid-cols-3 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-green-50 dark:bg-green-900/20">
            <div class="grid grid-cols-3 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-yellow-50 dark:bg-yellow-900/20">
            <div class="grid grid-cols-2 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-purple-50 dark:bg-purple-900/20">
            <div class="grid grid-cols-3 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-indigo-50 dark:bg-indigo-900/20">
            <div class="grid grid-cols-2 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-gray-100 dark:bg-gray-600">
            <div class="font-semibold text-gray-900 dark:text-gray-100">--</div>
          </td>
        </tr>
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 sticky left-0 bg-white dark:bg-gray-800 z-10">
            <div>
              <div class="font-medium">Maria S. Reyes</div>
              <div class="text-gray-500 dark:text-gray-400">2025-0002</div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-blue-50 dark:bg-blue-900/20">
            <div class="grid grid-cols-3 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-green-50 dark:bg-green-900/20">
            <div class="grid grid-cols-3 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-yellow-50 dark:bg-yellow-900/20">
            <div class="grid grid-cols-2 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-purple-50 dark:bg-purple-900/20">
            <div class="grid grid-cols-3 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-indigo-50 dark:bg-indigo-900/20">
            <div class="grid grid-cols-2 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-gray-100 dark:bg-gray-600">
            <div class="font-semibold text-gray-900 dark:text-gray-100">--</div>
          </td>
        </tr>
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 sticky left-0 bg-white dark:bg-gray-800 z-10">
            <div>
              <div class="font-medium">Pedro Santos</div>
              <div class="text-gray-500 dark:text-gray-400">2025-0003</div>
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-blue-50 dark:bg-blue-900/20">
            <div class="grid grid-cols-3 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-green-50 dark:bg-green-900/20">
            <div class="grid grid-cols-3 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-yellow-50 dark:bg-yellow-900/20">
            <div class="grid grid-cols-2 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-purple-50 dark:bg-purple-900/20">
            <div class="grid grid-cols-3 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-indigo-50 dark:bg-indigo-900/20">
            <div class="grid grid-cols-2 gap-1">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
              <input type="number" class="w-12 px-2 py-1 text-center border border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700" placeholder="-" min="0" max="100">
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-center bg-gray-100 dark:bg-gray-600">
            <div class="font-semibold text-gray-900 dark:text-gray-100">--</div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Legend -->
<div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
  <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Grade Categories:</h4>
  <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-xs">
    <div class="flex items-center gap-2">
      <div class="w-4 h-4 bg-blue-100 dark:bg-blue-900/20 rounded"></div>
      <span class="text-gray-700 dark:text-gray-300">Activities (30%)</span>
    </div>
    <div class="flex items-center gap-2">
      <div class="w-4 h-4 bg-green-100 dark:bg-green-900/20 rounded"></div>
      <span class="text-gray-700 dark:text-gray-300">Quizzes (20%)</span>
    </div>
    <div class="flex items-center gap-2">
      <div class="w-4 h-4 bg-yellow-100 dark:bg-yellow-900/20 rounded"></div>
      <span class="text-gray-700 dark:text-gray-300">Exams (30%)</span>
    </div>
    <div class="flex items-center gap-2">
      <div class="w-4 h-4 bg-purple-100 dark:bg-purple-900/20 rounded"></div>
      <span class="text-gray-700 dark:text-gray-300">Recitation (10%)</span>
    </div>
    <div class="flex items-center gap-2">
      <div class="w-4 h-4 bg-indigo-100 dark:bg-indigo-900/20 rounded"></div>
      <span class="text-gray-700 dark:text-gray-300">Projects (10%)</span>
    </div>
  </div>
</div>

<script>
// Auto-save functionality (placeholder)
document.querySelectorAll('input[type="number"]').forEach(input => {
  input.addEventListener('change', function() {
    // Placeholder for auto-save functionality
    console.log('Grade changed:', this.value);
  });
});

// Calculate final grades (placeholder)
function calculateFinalGrades() {
  // Placeholder for grade calculation logic
  console.log('Calculating final grades...');
}
</script>
@endsection 