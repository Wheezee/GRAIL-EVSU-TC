@extends('layouts.app')

@section('content')
<div class="mb-8">
  <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Dashboard</h2>
  <p class="text-gray-600 dark:text-gray-400 mb-6">Welcome back!</p>

  <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Total Enrolled Students -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">👨‍🎓</div>
      <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalStudents ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Enrolled Students</div>
    </div>
    <!-- Total Subjects -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">📚</div>
      <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalSubjects ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Subjects</div>
    </div>
    <!-- Total Class Sections -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">🏫</div>
      <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalClassSections ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Class Sections</div>
    </div>
    <!-- Total Activities -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">📝</div>
      <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalActivities ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Activities</div>
    </div>
    <!-- Total Quizzes -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">📊</div>
      <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalQuizzes ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Quizzes</div>
    </div>
    <!-- Total Exams -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">🧪</div>
      <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalExams ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Exams</div>
    </div>
    <!-- Total Projects -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">📋</div>
      <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalProjects ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Projects</div>
    </div>
    <!-- Total Recitations -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm flex flex-col items-center">
      <div class="text-3xl mb-2">🎤</div>
      <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalRecitations ?? '--' }}</div>
      <div class="text-gray-700 dark:text-gray-300 mt-1 text-center">Recitations</div>
    </div>
  </div>
</div>
@endsection
