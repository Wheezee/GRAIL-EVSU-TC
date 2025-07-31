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
      <a href="{{ route('grading.system', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, 'term' => isset($term) ? $term : 'midterms']) }}" class="hover:text-evsu dark:hover:text-evsu transition-colors">
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
  <div class="flex items-center gap-4">
    <div class="flex items-center gap-2">
      <label for="grading_mode" class="text-sm font-medium text-gray-700 dark:text-gray-300">Grading Mode:</label>
      <select id="grading_mode" class="px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        <option value="percentage">Percentage-Based</option>
        <option value="computed">Computed (1.0–5.0)</option>
        <option value="rule_based">Rule-Based (1.0–5.0)</option>
      </select>
    </div>
    
    <div class="w-px h-6 bg-gray-300 dark:bg-gray-600"></div>
    
    <button onclick="document.getElementById('export-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors">
      <i data-lucide="download" class="w-4 h-4"></i>
      Export
    </button>
  </div>
</div>

<!-- Term-separated Gradebook Table Placeholder -->
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-x-auto hide-scrollbar">
  <table class="w-full min-w-[1800px]">
    <thead>
      <tr>
        <th rowspan="3" class="px-6 py-3 text-left bg-white dark:bg-gray-800 sticky left-0 top-0 z-20">Students</th>
        @foreach(['activities' => 'Activities', 'quizzes' => 'Quizzes', 'exams' => 'Exams', 'recitations' => 'Recitation', 'projects' => 'Projects'] as $type => $label)
          @php
            $midtermCount = count($assessments[$type]['midterms']);
            $finalsCount = count($assessments[$type]['finals']);
            $colspan = max($midtermCount + $finalsCount, 1); // at least 1 to preserve table structure
          @endphp
          <th colspan="{{ $colspan }}" class="px-6 py-3 text-center bg-white dark:bg-gray-800 sticky top-0 z-10">{{ $label }}</th>
        @endforeach
        <th rowspan="3" class="px-6 py-3 text-center bg-white dark:bg-gray-800 sticky top-0 z-10">Midterm Grade</th>
        <th rowspan="3" class="px-6 py-3 text-center bg-white dark:bg-gray-800 sticky top-0 z-10">Finals Grade</th>
        <th rowspan="3" class="px-6 py-3 text-center bg-white dark:bg-gray-800 sticky top-0 z-10">Overall Grade</th>
      </tr>
      <tr>
        @foreach(['activities', 'quizzes', 'exams', 'recitations', 'projects'] as $type)
          @php
            $midtermCount = count($assessments[$type]['midterms']);
            $finalsCount = count($assessments[$type]['finals']);
          @endphp
          @if ($midtermCount > 0)
            <th colspan="{{ $midtermCount }}" class="px-4 py-2 text-center bg-white dark:bg-gray-800 sticky top-8 z-10">Midterm</th>
          @endif
          @if ($finalsCount > 0)
            <th colspan="{{ $finalsCount }}" class="px-4 py-2 text-center bg-white dark:bg-gray-800 sticky top-8 z-10">Finals</th>
          @endif
          @if ($midtermCount === 0 && $finalsCount === 0)
            <th class="text-center px-4 py-2 bg-white dark:bg-gray-800 sticky top-8 z-10">No Assessments</th>
          @endif
        @endforeach
      </tr>
      <tr>
        @foreach(['activities', 'quizzes', 'exams', 'recitations', 'projects'] as $type)
          @php
            $midtermCount = count($assessments[$type]['midterms']);
            $finalsCount = count($assessments[$type]['finals']);
          @endphp
          @foreach($assessments[$type]['midterms'] as $item)
            <th class="px-4 py-2 text-center bg-white dark:bg-gray-800 sticky top-16 z-10">
              <a href="{{ route($type.'.show', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, $type == 'activities' ? 'activity' : ($type == 'quizzes' ? 'quiz' : ($type == 'exams' ? 'exam' : ($type == 'recitations' ? 'recitation' : 'project')) ) => $item->id, 'term' => 'midterms']) }}"
                 class="text-blue-600 dark:text-blue-400 hover:underline">
                {{ $item->name }}
              </a>
            </th>
          @endforeach
          @foreach($assessments[$type]['finals'] as $item)
            <th class="px-4 py-2 text-center bg-white dark:bg-gray-800 sticky top-16 z-10">
              <a href="{{ route($type.'.show', ['subject' => $classSection->subject->id, 'classSection' => $classSection->id, $type == 'activities' ? 'activity' : ($type == 'quizzes' ? 'quiz' : ($type == 'exams' ? 'exam' : ($type == 'recitations' ? 'recitation' : 'project')) ) => $item->id, 'term' => 'finals']) }}"
                 class="text-blue-600 dark:text-blue-400 hover:underline">
                {{ $item->name }}
              </a>
            </th>
          @endforeach
          @if ($midtermCount === 0 && $finalsCount === 0)
            <th class="text-center px-4 py-2 bg-white dark:bg-gray-800 sticky top-16 z-10">--</th>
          @endif
        @endforeach
      </tr>
    </thead>
    <tbody>
      @foreach ($students as $student)
      <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
        <td class="px-6 py-3 bg-white dark:bg-gray-800 sticky left-0 z-10">{{ $student->last_name }}, {{ $student->first_name }}</td>
        @foreach(['activities', 'quizzes', 'exams', 'recitations', 'projects'] as $type)
          @php
            $midtermCount = count($assessments[$type]['midterms']);
            $finalsCount = count($assessments[$type]['finals']);
          @endphp
          {{-- Midterms --}}
          @if($midtermCount > 0)
            @foreach($assessments[$type]['midterms'] as $item)
              <td class="px-4 py-3 text-center hover:bg-yellow-100 dark:hover:bg-yellow-900 transition-colors">
                <?php $score = $item->scores->where('student_id', $student->id)->first(); ?>
                {{ $score && $score->score !== null ? $score->score : '--' }}
              </td>
            @endforeach
          @endif
          {{-- Finals --}}
          @if($finalsCount > 0)
            @foreach($assessments[$type]['finals'] as $item)
              <td class="px-4 py-3 text-center hover:bg-yellow-100 dark:hover:bg-yellow-900 transition-colors">
                <?php $score = $item->scores->where('student_id', $student->id)->first(); ?>
                {{ $score && $score->score !== null ? $score->score : '--' }}
              </td>
            @endforeach
          @endif
          {{-- Empty state --}}
          @if($midtermCount === 0 && $finalsCount === 0)
            <td class="px-4 py-3 text-center hover:bg-yellow-100 dark:hover:bg-yellow-900 transition-colors">--</td>
          @endif
        @endforeach
        <td class="px-4 py-3 text-center font-semibold">
            @if($student->midterms_grade === 'INC')
                <span class="text-red-600 font-bold">INC</span>
            @elseif($student->midterms_grade !== null)
                <span class="grade-display" data-grade="{{ $student->midterms_grade }}" data-type="percentage">
                    {{ $student->midterms_grade }}%
                </span>
            @else
                --
            @endif
        </td>
        <td class="px-4 py-3 text-center font-semibold">
            @if($student->finals_grade === 'INC')
                <span class="text-red-600 font-bold">INC</span>
            @elseif($student->finals_grade !== null)
                <span class="grade-display" data-grade="{{ $student->finals_grade }}" data-type="percentage">
                    {{ $student->finals_grade }}%
                </span>
            @else
                --
            @endif
        </td>
        <td class="px-4 py-3 text-center font-semibold">
            @if($student->overall_grade !== null)
                <span class="grade-display" data-grade="{{ $student->overall_grade }}" data-type="percentage">
                    {{ $student->overall_grade }}%
                </span>
            @else
                --
            @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- Legend and Weights Display -->
<div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">Grade Categories & Weights:</h4>
      <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-xs">
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-blue-100 dark:bg-blue-900/20 rounded"></div>
          <span class="text-gray-700 dark:text-gray-300">Activities (<span id="weight-activities">{{ $gradingWeight ? $gradingWeight->activities : 20 }}%</span>)</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-green-100 dark:bg-green-900/20 rounded"></div>
          <span class="text-gray-700 dark:text-gray-300">Quizzes (<span id="weight-quizzes">{{ $gradingWeight ? $gradingWeight->quizzes : 20 }}%</span>)</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-yellow-100 dark:bg-yellow-900/20 rounded"></div>
          <span class="text-gray-700 dark:text-gray-300">Exams (<span id="weight-exams">{{ $gradingWeight ? $gradingWeight->exams : 30 }}%</span>)</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-purple-100 dark:bg-purple-900/20 rounded"></div>
          <span class="text-gray-700 dark:text-gray-300">Recitation (<span id="weight-recitation">{{ $gradingWeight ? $gradingWeight->recitation : 15 }}%</span>)</span>
        </div>
        <div class="flex items-center gap-2">
          <div class="w-4 h-4 bg-indigo-100 dark:bg-indigo-900/20 rounded"></div>
          <span class="text-gray-700 dark:text-gray-300">Projects (<span id="weight-projects">{{ $gradingWeight ? $gradingWeight->projects : 15 }}%</span>)</span>
        </div>
      </div>
    </div>
    <div>
      <button type="button" class="px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors" onclick="document.getElementById('weights-modal').classList.remove('hidden')">
        <i data-lucide="settings" class="w-4 h-4 inline"></i> Set Weights
      </button>
    </div>
  </div>
</div>

<!-- Weights Modal -->
<div id="weights-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 relative">
    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700" onclick="document.getElementById('weights-modal').classList.add('hidden')">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>
    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Set Assessment Weights</h3>
    <form id="weights-form" method="POST" action="{{ route('grading.weights.update', $classSection->subject->id) }}">
      @csrf
      <div class="space-y-3">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Activities (%)</label>
          <input type="number" name="activities" min="0" max="100" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-gray-100" value="{{ $gradingWeight ? $gradingWeight->activities : 20 }}" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quizzes (%)</label>
          <input type="number" name="quizzes" min="0" max="100" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-gray-100" value="{{ $gradingWeight ? $gradingWeight->quizzes : 20 }}" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Exams (%)</label>
          <input type="number" name="exams" min="0" max="100" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-gray-100" value="{{ $gradingWeight ? $gradingWeight->exams : 30 }}" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recitation (%)</label>
          <input type="number" name="recitation" min="0" max="100" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-gray-100" value="{{ $gradingWeight ? $gradingWeight->recitation : 15 }}" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Projects (%)</label>
          <input type="number" name="projects" min="0" max="100" class="mt-1 block w-full rounded border-gray-300 dark:bg-gray-700 dark:text-gray-100" value="{{ $gradingWeight ? $gradingWeight->projects : 15 }}" required>
        </div>
      </div>
      <div class="mt-4 flex items-center justify-between">
        <span id="weights-error" class="text-red-600 text-sm hidden">Total must be 100%</span>
        <button type="submit" class="px-4 py-2 bg-evsu hover:bg-evsuDark text-white font-medium rounded-lg transition-colors">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Export Modal -->
<div id="export-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 relative">
    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-700" onclick="document.getElementById('export-modal').classList.add('hidden')">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>
    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Export Gradebook</h3>
    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Export Format:</label>
        <div class="space-y-2">
          <label class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
            <input type="radio" name="export_format" value="pdf" class="text-blue-600 focus:ring-blue-500" checked>
            <div class="flex items-center gap-2">
              <i data-lucide="file-text" class="w-5 h-5 text-red-500"></i>
              <span class="text-gray-900 dark:text-gray-100">PDF Document</span>
            </div>
          </label>
          <label class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
            <input type="radio" name="export_format" value="excel" class="text-blue-600 focus:ring-blue-500">
            <div class="flex items-center gap-2">
              <i data-lucide="file-spreadsheet" class="w-5 h-5 text-green-500"></i>
              <span class="text-gray-900 dark:text-gray-100">Excel Spreadsheet</span>
            </div>
          </label>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
          Exports current table data using lightweight client-side extraction.
        </p>
      </div>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Include:</label>
        <div class="space-y-2">
          <label class="flex items-center gap-2">
            <input type="checkbox" id="include_weights" class="rounded text-blue-600 focus:ring-blue-500" checked>
            <span class="text-sm text-gray-700 dark:text-gray-300">Grading weights</span>
          </label>
          <label class="flex items-center gap-2">
            <input type="checkbox" id="include_legend" class="rounded text-blue-600 focus:ring-blue-500" checked>
            <span class="text-sm text-gray-700 dark:text-gray-300">Grade legend</span>
          </label>
        </div>
      </div>
    </div>
    
    <div class="mt-6 flex justify-end gap-3">
      <button onclick="document.getElementById('export-modal').classList.add('hidden')" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
        Cancel
      </button>
      <button onclick="exportGradebook()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
        <i data-lucide="download" class="w-4 h-4 inline mr-2"></i>
        Export
      </button>
    </div>
  </div>
</div>

<script>
// Grade display functionality with multiple modes
let currentGradingMode = 'percentage'; // Default to percentage

// Grade conversion functions
function convertGrade(percentage, mode) {
  if (mode === 'percentage') {
    return percentage + '%';
  }

  if (mode === 'computed') {
    if (percentage >= 100) return '1.0';
    if (percentage >= 60) {
      const grade = 3.0 - ((percentage - 60) / 40) * 2.0;
      return grade.toFixed(2);
    }
    return '5.0';
  }

  if (mode === 'rule_based') {
    if (percentage >= 97) return '1.00';
    if (percentage >= 94) return '1.25';
    if (percentage >= 91) return '1.50';
    if (percentage >= 88) return '1.75';
    if (percentage >= 85) return '2.00';
    if (percentage >= 82) return '2.25';
    if (percentage >= 79) return '2.50';
    if (percentage >= 76) return '2.75';
    if (percentage >= 75) return '3.00';
    return '5.00';
  }
}

// Color coding for different grading modes
function getGradeColor(grade, mode) {
  if (mode === 'percentage') {
    return ''; // No color for percentage
  }
  
  if (mode === 'computed' || mode === 'rule_based') {
    const numGrade = parseFloat(grade);
    if (numGrade <= 1.0) return 'text-green-600'; // Excellent
    if (numGrade <= 1.5) return 'text-blue-600'; // Very Good to Good
    if (numGrade <= 1.75) return 'text-yellow-600'; // Satisfactory
    if (numGrade <= 2.5) return 'text-orange-600'; // Fair
    if (numGrade <= 2.75) return 'text-orange-600'; // Passing
    if (numGrade <= 3.0) return 'text-red-500'; // Lowest Passing
    return 'text-red-700'; // Failed
  }
  
  return '';
}

function updateGradeDisplay() {
  const gradeDisplays = document.querySelectorAll('.grade-display');
  const gradingMode = document.getElementById('grading_mode').value;
  
  gradeDisplays.forEach(display => {
    const grade = parseFloat(display.dataset.grade);
    if (!isNaN(grade)) {
      const convertedGrade = convertGrade(grade, gradingMode);
      display.textContent = convertedGrade;
      display.dataset.type = gradingMode;
      
      // Apply color coding
      const colorClass = getGradeColor(convertedGrade, gradingMode);
      display.className = 'grade-display';
      if (colorClass) {
        display.classList.add('font-bold', colorClass);
      }
    }
  });
}

// Add event listener to grading mode dropdown
document.addEventListener('DOMContentLoaded', function() {
  const gradingModeSelect = document.getElementById('grading_mode');
  if (gradingModeSelect) {
    gradingModeSelect.addEventListener('change', updateGradeDisplay);
  }
  
  // Reinitialize Lucide icons for modals
  if (window.lucide) {
    lucide.createIcons();
  }
});

// Export functionality
function exportGradebook() {
  const format = document.querySelector('input[name="export_format"]:checked').value;
  const includeWeights = document.getElementById('include_weights').checked;
  const includeLegend = document.getElementById('include_legend').checked;
  const gradingMode = document.getElementById('grading_mode').value;
  
  // Get the gradebook table
  const gradebookTable = document.querySelector('.bg-white.dark\\:bg-gray-800.border.border-gray-200.dark\\:border-gray-700.rounded-xl.shadow-sm.overflow-x-auto.hide-scrollbar table');
  
  if (!gradebookTable) {
    alert('Could not find gradebook table');
    return;
  }
  
  // Extract table data from the current HTML
  const tableData = extractTableData(gradebookTable);
  
  // Get current URL parameters
  const urlParams = new URLSearchParams(window.location.search);
  const subjectId = '{{ $classSection->subject->id }}';
  const classSectionId = '{{ $classSection->id }}';
  
  // Build export URL with the extracted data
  let exportUrl = `/subjects/${subjectId}/classes/${classSectionId}/gradebook/export?format=${format}&grading_mode=${gradingMode}`;
  
  if (includeWeights) {
    exportUrl += '&include_weights=1';
  }
  
  if (includeLegend) {
    exportUrl += '&include_legend=1';
  }
  
  // Add the table data as a parameter
  exportUrl += `&table_data=${encodeURIComponent(JSON.stringify(tableData))}`;
  
  // Show loading state
  const exportButton = document.querySelector('#export-modal button[onclick="exportGradebook()"]');
  const originalText = exportButton.innerHTML;
  exportButton.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 inline mr-2 animate-spin"></i>Exporting...';
  exportButton.disabled = true;
  
  // Trigger file download
  window.location.href = exportUrl;
  
  // Close modal after a short delay
  setTimeout(() => {
    document.getElementById('export-modal').classList.add('hidden');
  }, 1000);
  
  // Reset button
  exportButton.innerHTML = originalText;
  exportButton.disabled = false;
  if (window.lucide) {
    lucide.createIcons();
  }
}

// Function to extract table data from the current HTML
function extractTableData(table) {
  const data = {
    headers: [],
    students: [],
    assessments: {
      activities: { midterms: [], finals: [] },
      quizzes: { midterms: [], finals: [] },
      exams: { midterms: [], finals: [] },
      recitations: { midterms: [], finals: [] },
      projects: { midterms: [], finals: [] }
    }
  };
  
  // Extract headers
  const headerRows = table.querySelectorAll('thead tr');
  if (headerRows.length >= 3) {
    // First row - main categories
    const mainHeaders = headerRows[0].querySelectorAll('th');
    mainHeaders.forEach((th, index) => {
      if (index === 0) {
        data.headers.push({ type: 'student', text: th.textContent.trim() });
      } else {
        const colspan = parseInt(th.getAttribute('colspan')) || 1;
        data.headers.push({ type: 'category', text: th.textContent.trim(), colspan });
      }
    });
    
    // Second row - terms (midterm/finals)
    const termHeaders = headerRows[1].querySelectorAll('th');
    const termMap = [];
    termHeaders.forEach(th => {
      termMap.push(th.textContent.trim());
    });
    
    // Third row - assessment names with term mapping
    const assessmentHeaders = headerRows[2].querySelectorAll('th');
    let termIndex = 0;
    let currentTerm = '';
    
    assessmentHeaders.forEach((th, index) => {
      // Find which term this assessment belongs to
      // We need to map the assessment to its corresponding term
      const link = th.querySelector('a');
      const assessmentText = th.textContent.trim();
      
      // Determine the term based on the link URL or position
      let term = '';
      if (link) {
        const href = link.href;
        if (href.includes('/midterms/')) {
          term = 'Midterm';
        } else if (href.includes('/finals/')) {
          term = 'Finals';
        }
      }
      
      // Create assessment header with term
      const assessmentHeader = {
        type: 'assessment',
        text: assessmentText,
        term: term,
        fullText: term ? `${assessmentText} - ${term}` : assessmentText,
        link: link ? link.href : null
      };
      
      data.headers.push(assessmentHeader);
    });
  }
  
  // Extract student data
  const studentRows = table.querySelectorAll('tbody tr');
  studentRows.forEach(row => {
    const cells = row.querySelectorAll('td');
    const student = {
      name: cells[0].textContent.trim(),
      scores: []
    };
    
    // Extract scores starting from the second cell (skip student name)
    for (let i = 1; i < cells.length; i++) {
      const score = cells[i].textContent.trim();
      student.scores.push(score);
    }
    
    data.students.push(student);
  });
  
  return data;
}

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

// Modal validation for weights
const weightsForm = document.getElementById('weights-form');
if (weightsForm) {
  weightsForm.addEventListener('submit', function(e) {
    const total =
      parseInt(weightsForm.activities.value) +
      parseInt(weightsForm.quizzes.value) +
      parseInt(weightsForm.exams.value) +
      parseInt(weightsForm.recitation.value) +
      parseInt(weightsForm.projects.value);
    if (total !== 100) {
      document.getElementById('weights-error').classList.remove('hidden');
      e.preventDefault();
    } else {
      document.getElementById('weights-error').classList.add('hidden');
    }
  });
}
</script>
@endsection 