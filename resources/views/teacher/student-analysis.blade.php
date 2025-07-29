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
      <a href="{{ route('subjects.index') }}" class="hover:text-evsu dark:hover:text-evsu transition-colors whitespace-nowrap">
        Subjects
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <a href="{{ route('subjects.classes', $subject->id) }}" class="hover:text-evsu dark:hover:text-evsu transition-colors max-w-[120px] sm:max-w-none truncate">
        {{ $subject->code }} - {{ $subject->title }}
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <a href="{{ route('grading.system', ['subject' => $subject->id, 'classSection' => $classSection->id, 'term' => 'midterms']) }}" class="hover:text-evsu dark:hover:text-evsu transition-colors whitespace-nowrap">
        {{ $classSection->section }}
      </a>
    </li>
    <li class="flex items-center">
      <i data-lucide="chevron-right" class="w-3 h-3 sm:w-4 sm:h-4 mx-1 sm:mx-2 flex-shrink-0"></i>
      <span class="text-gray-900 dark:text-gray-100 font-medium whitespace-nowrap">{{ $student->full_name }} Analysis</span>
    </li>
  </ol>
</nav>

<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-2">{{ $student->full_name }} <span class="text-gray-500">({{ $student->student_id }})</span></h1>
    <p class="mb-4 text-gray-600">Email: <a href="mailto:{{ $student->email }}" class="underline">{{ $student->email }}</a></p>
    <p class="mb-6 text-gray-700 dark:text-gray-300 font-medium">Subject: <span class="font-semibold">{{ $subject->code }} - {{ $subject->title }}</span></p>

    @php
        $riskExplanations = [
            'Chronic Procrastinator' => 'This student often submits work late, which may affect their learning progress. Encourage time management and offer deadline reminders.',
            'Inconsistent Performance' => 'Scores fluctuate a lot, which may mean the student sometimes struggles or is affected by outside factors. Encourage consistency and check in if needed.',
            'Low Exam Performance' => 'Exam scores are below the expected threshold. The student may need extra help with exam preparation or test-taking strategies.',
            'Poor Activity Performance' => 'Activity scores are lower than expected. Encourage the student to participate more actively or seek clarification on assignments.',
            'Poor Quiz Performance' => 'Quiz scores are low. The student may benefit from more practice or reviewing class materials.',
            'Low Project Scores' => 'Project scores are below average. The student might need support with project planning or understanding requirements.',
            'Poor Recitation Performance' => 'Recitation scores are low. The student may need encouragement to participate or more opportunities to practice.',
            'High Missed Submissions' => 'The student has missed several submissions. Check if there are obstacles preventing timely work, and offer support if needed.',
            'Frequent Late Submissions' => 'Late submissions are common. Consider discussing time management strategies or offering deadline reminders.',
            'Multiple Resubmissions' => 'The student often resubmits work. This may indicate difficulty understanding requirements or concepts—offer clarification or extra help.',
            'Overall Academic Risk' => 'Overall performance is below expectations. Consider a holistic review and supportive intervention.',
        ];
        $risks = $remarks;
        $onlyLacking = (count($risks) === 1 && ($risks[0] === 'Lacking data to make a general status' || $risks[0] === 'There is not enough information to make a risk assessment for this student yet.'));
        if ($onlyLacking) {
            $riskLevel = 'No Data';
        } elseif (count($risks) === 1 && $risks[0] === 'Not At Risk') {
            $riskLevel = 'Not At Risk';
        } elseif (in_array('At Risk', $risks)) {
            $riskLevel = 'High Risk';
        } else {
            $riskLevel = 'Low Risk';
        }
        $riskCauses = array_filter($risks, function($r) use ($riskLevel) {
            if ($r === 'Not At Risk') return false;
            if ($r === 'At Risk') return false;
            if ($r === 'Lacking data to make a general status' || $r === 'There is not enough information to make a risk assessment for this student yet.') return false;
            return true;
        });
    @endphp
    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-8 border border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold mb-4">Risk Remarks & Analysis</h2>
            @if($riskLevel === 'No Data')
                <div class="p-4 bg-gray-100 border border-gray-300 text-gray-700 rounded-lg">
                    <i data-lucide="info" class="w-4 h-4 mr-1 inline-block align-middle"></i>
                    There is not enough information to make a risk assessment for this student yet.
                </div>
            @else
                <div class="flex flex-wrap gap-2 mb-3">
                    @if($riskLevel === 'Not At Risk')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                            <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i> Not At Risk
                        </span>
                    @elseif($riskLevel === 'Low Risk')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300">
                            <i data-lucide="alert-circle" class="w-4 h-4 mr-1"></i> Low Risk
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300">
                            <i data-lucide="alert-triangle" class="w-4 h-4 mr-1"></i> High Risk
                        </span>
                    @endif
                    @foreach($riskCauses as $cause)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-300">
                            {{ $cause }}
                        </span>
                    @endforeach
                </div>
                @if(count($riskCauses))
                    <ul class="list-disc pl-6">
                        @foreach($riskCauses as $cause)
                            <li>
                                <span class="font-semibold">{{ $cause }}</span>
                                <div class="text-gray-600 text-sm mt-1">
                                    {{ $riskExplanations[$cause] ?? 'The student may need extra attention or support.' }}
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
                @if($riskLevel === 'High Risk')
                    <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                        <strong>Suggestion:</strong> This student is <span class="font-bold">at risk</span>. Please consider reaching out for support or intervention.<br>
                        <span class="block mt-2">Contact: <a href="mailto:{{ $student->email }}" class="underline">{{ $student->email }}</a></span>
                    </div>
                @endif
            @endif
        </div>

        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4">Scores Over Time</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold mb-2">Activities</h3>
                    <canvas id="activitiesChart" height="180"></canvas>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold mb-2">Quizzes</h3>
                    <canvas id="quizzesChart" height="180"></canvas>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold mb-2">Exams</h3>
                    <canvas id="examsChart" height="180"></canvas>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold mb-2">Recitations</h3>
                    <canvas id="recitationsChart" height="180"></canvas>
                </div>
            </div>
            <div class="mt-8 flex justify-center">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 border border-gray-200 dark:border-gray-700 w-full md:w-1/2">
                    <h3 class="font-semibold mb-2">Projects</h3>
                    <canvas id="projectsChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-lg font-semibold mb-2">Contact & Outreach</h2>
        <p>If you need to reach out to this student, you can email them at <a href="mailto:{{ $student->email }}" class="underline">{{ $student->email }}</a>.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for Activities (percentages)
    const activitiesLabels = @json($activities->map(fn($a) => $a->name ?? $a->title ?? 'Activity'));
    const activitiesPercents = @json($activities->map(function($a) use ($activityScores) {
        $score = isset($activityScores[$a->id]) ? $activityScores[$a->id]->score : null;
        $max = $a->max_score ?? 0;
        return ($score !== null && $max > 0) ? round(($score / $max) * 100, 1) : null;
    }));
    new Chart(document.getElementById('activitiesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: activitiesLabels,
            datasets: [{
                label: 'Activity (%)',
                data: activitiesPercents,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
            }]
        },
        options: {scales: {y: {beginAtZero: true, max: 100}}}
    });
    // Quizzes
    const quizzesLabels = @json($quizzes->map(fn($q) => $q->name ?? $q->title ?? 'Quiz'));
    const quizzesPercents = @json($quizzes->map(function($q) use ($quizScores) {
        $score = isset($quizScores[$q->id]) ? $quizScores[$q->id]->score : null;
        $max = $q->max_score ?? 0;
        return ($score !== null && $max > 0) ? round(($score / $max) * 100, 1) : null;
    }));
    new Chart(document.getElementById('quizzesChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: quizzesLabels,
            datasets: [{
                label: 'Quiz (%)',
                data: quizzesPercents,
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: 'rgba(255, 206, 86, 1)',
            }]
        },
        options: {scales: {y: {beginAtZero: true, max: 100}}}
    });
    // Exams
    const examsLabels = @json($exams->map(fn($e) => $e->name ?? $e->title ?? 'Exam'));
    const examsPercents = @json($exams->map(function($e) use ($examScores) {
        $score = isset($examScores[$e->id]) ? $examScores[$e->id]->score : null;
        $max = $e->max_score ?? 0;
        return ($score !== null && $max > 0) ? round(($score / $max) * 100, 1) : null;
    }));
    new Chart(document.getElementById('examsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: examsLabels,
            datasets: [{
                label: 'Exam (%)',
                data: examsPercents,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: 'rgba(255, 99, 132, 1)',
            }]
        },
        options: {scales: {y: {beginAtZero: true, max: 100}}}
    });
    // Projects
    const projectsLabels = @json($projects->map(fn($p) => $p->name ?? $p->title ?? 'Project'));
    const projectsPercents = @json($projects->map(function($p) use ($projectScores) {
        $score = isset($projectScores[$p->id]) ? $projectScores[$p->id]->score : null;
        $max = $p->max_score ?? 0;
        return ($score !== null && $max > 0) ? round(($score / $max) * 100, 1) : null;
    }));
    new Chart(document.getElementById('projectsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: projectsLabels,
            datasets: [{
                label: 'Project (%)',
                data: projectsPercents,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: 'rgba(75, 192, 192, 1)',
            }]
        },
        options: {scales: {y: {beginAtZero: true, max: 100}}}
    });
    // Recitations
    const recitationsLabels = @json($recitations->map(fn($r) => $r->name ?? $r->title ?? 'Recitation'));
    const recitationsPercents = @json($recitations->map(function($r) use ($recitationScores) {
        $score = isset($recitationScores[$r->id]) ? $recitationScores[$r->id]->score : null;
        $max = $r->max_score ?? 0;
        return ($score !== null && $max > 0) ? round(($score / $max) * 100, 1) : null;
    }));
    new Chart(document.getElementById('recitationsChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: recitationsLabels,
            datasets: [{
                label: 'Recitation (%)',
                data: recitationsPercents,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: 'rgba(153, 102, 255, 1)',
            }]
        },
        options: {scales: {y: {beginAtZero: true, max: 100}}}
    });
</script>
@endsection 