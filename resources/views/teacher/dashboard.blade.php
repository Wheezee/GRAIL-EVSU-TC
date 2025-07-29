@extends('layouts.app')

@section('content')
<div class="mb-8">
  <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Dashboard</h2>
  <p class="text-gray-600 dark:text-gray-400 mb-6">Welcome back!</p>

  <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-2">
    <!-- Subjects Count -->
    <div class="bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">📚</div>
      <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalSubjects ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Subjects</div>
    </div>
    <!-- Student Count -->
    <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">👨‍🎓</div>
      <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $totalStudents ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Enrolled Students</div>
    </div>
  </div>
</div>
<!-- Latest Created Items Section -->
<div class="mt-10">
  <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Latest Created Items</h3>
  <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
    <!-- Latest Activities -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
      <div class="font-bold text-blue-600 dark:text-blue-400 mb-3">📝 Activities</div>
      @forelse($latestActivities ?? [] as $activity)
        <div class="mb-2 pb-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
          <button onclick="showClassSelector('activity', {{ $activity->id }}, {{ $activity->subject->id }}, '{{ $activity->term }}', '{{ $activity->name }}')" class="w-full text-left hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2 -m-2 transition-colors">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $activity->name }} ({{ $activity->subject->title }})</div>
            <div class="text-xs text-gray-500 mt-1">Created: {{ $activity->created_at ? $activity->created_at->format('M d, Y H:i') : '--' }}</div>
          </button>
        </div>
      @empty
        <div class="text-gray-400 text-sm">No activities yet.</div>
      @endforelse
    </div>
    
    <!-- Latest Quizzes -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
      <div class="font-bold text-blue-600 dark:text-blue-400 mb-3">📊 Quizzes</div>
      @forelse($latestQuizzes ?? [] as $quiz)
        <div class="mb-2 pb-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
          <button onclick="showClassSelector('quiz', {{ $quiz->id }}, {{ $quiz->subject->id }}, '{{ $quiz->term }}', '{{ $quiz->name }}')" class="w-full text-left hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2 -m-2 transition-colors">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $quiz->name }} ({{ $quiz->subject->title }})</div>
            <div class="text-xs text-gray-500 mt-1">Created: {{ $quiz->created_at ? $quiz->created_at->format('M d, Y H:i') : '--' }}</div>
          </button>
        </div>
      @empty
        <div class="text-gray-400 text-sm">No quizzes yet.</div>
      @endforelse
    </div>
    
    <!-- Latest Exams -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
      <div class="font-bold text-blue-600 dark:text-blue-400 mb-3">🧪 Exams</div>
      @forelse($latestExams ?? [] as $exam)
        <div class="mb-2 pb-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
          <button onclick="showClassSelector('exam', {{ $exam->id }}, {{ $exam->subject->id }}, '{{ $exam->term }}', '{{ $exam->name }}')" class="w-full text-left hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2 -m-2 transition-colors">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $exam->name }} ({{ $exam->subject->title }})</div>
            <div class="text-xs text-gray-500 mt-1">Created: {{ $exam->created_at ? $exam->created_at->format('M d, Y H:i') : '--' }}</div>
          </button>
        </div>
      @empty
        <div class="text-gray-400 text-sm">No exams yet.</div>
      @endforelse
    </div>
    
    <!-- Latest Recitations -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
      <div class="font-bold text-blue-600 dark:text-blue-400 mb-3">🎤 Recitations</div>
      @forelse($latestRecitations ?? [] as $recitation)
        <div class="mb-2 pb-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
          <button onclick="showClassSelector('recitation', {{ $recitation->id }}, {{ $recitation->subject->id }}, '{{ $recitation->term }}', '{{ $recitation->name }}')" class="w-full text-left hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2 -m-2 transition-colors">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $recitation->name }} ({{ $recitation->subject->title }})</div>
            <div class="text-xs text-gray-500 mt-1">Created: {{ $recitation->created_at ? $recitation->created_at->format('M d, Y H:i') : '--' }}</div>
          </button>
        </div>
      @empty
        <div class="text-gray-400 text-sm">No recitations yet.</div>
      @endforelse
    </div>
    
    <!-- Latest Projects -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
      <div class="font-bold text-blue-600 dark:text-blue-400 mb-3">📋 Projects</div>
      @forelse($latestProjects ?? [] as $project)
        <div class="mb-2 pb-2 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-700' : '' }}">
          <button onclick="showClassSelector('project', {{ $project->id }}, {{ $project->subject->id }}, '{{ $project->term }}', '{{ $project->name }}')" class="w-full text-left hover:bg-gray-50 dark:hover:bg-gray-700 rounded p-2 -m-2 transition-colors">
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $project->name }} ({{ $project->subject->title }})</div>
            <div class="text-xs text-gray-500 mt-1">Created: {{ $project->created_at ? $project->created_at->format('M d, Y H:i') : '--' }}</div>
          </button>
        </div>
      @empty
        <div class="text-gray-400 text-sm">No projects yet.</div>
      @endforelse
    </div>
  </div>
</div>

<!-- Class Selector Modal -->
<div id="classSelectorModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
  <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
    <div class="mt-3">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100" id="modalTitle">Select Class Section</h3>
        <button onclick="closeClassSelector()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      <div id="classList" class="space-y-2">
        <!-- Class options will be populated here -->
      </div>
    </div>
  </div>
</div>

<script>
let currentItemData = {};

function showClassSelector(type, itemId, subjectId, term, itemName) {
  currentItemData = { type, itemId, subjectId, term, itemName };
  
  // Fetch class sections for this subject
  fetch(`/api/subjects/${subjectId}/classes`)
    .then(response => response.json())
    .then(classes => {
      const modal = document.getElementById('classSelectorModal');
      const classList = document.getElementById('classList');
      const modalTitle = document.getElementById('modalTitle');
      
      modalTitle.textContent = `Select Class for ${itemName}`;
      
      classList.innerHTML = '';
      
      classes.forEach(classSection => {
        const button = document.createElement('button');
        button.className = 'w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors';
        button.innerHTML = `
          <div class="font-medium text-gray-900 dark:text-gray-100">${classSection.section}</div>
          <div class="text-sm text-gray-500">${classSection.schedule} • ${classSection.student_count} students</div>
        `;
        button.onclick = () => navigateToItem(type, itemId, subjectId, classSection.id, term);
        classList.appendChild(button);
      });
      
      modal.classList.remove('hidden');
    })
    .catch(error => {
      console.error('Error fetching classes:', error);
      alert('Error loading class sections. Please try again.');
    });
}

function closeClassSelector() {
  document.getElementById('classSelectorModal').classList.add('hidden');
}

function navigateToItem(type, itemId, subjectId, classSectionId, term) {
  const routes = {
    'activity': 'activities.show',
    'quiz': 'quizzes.show',
    'exam': 'exams.show',
    'recitation': 'recitations.show',
    'project': 'projects.show'
  };
  
  const routeName = routes[type];
  const url = `{{ url('/') }}/subjects/${subjectId}/classes/${classSectionId}/${term}/${type === 'activity' ? 'activities' : type + 's'}/${itemId}`;
  
  window.location.href = url;
  closeClassSelector();
}

// Close modal when clicking outside
document.getElementById('classSelectorModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeClassSelector();
  }
});
</script>
@endsection
