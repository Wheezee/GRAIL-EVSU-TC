<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\ClassSection;
use App\Http\Controllers\GradingWeightController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Login page
Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('welcome');
})->name('login')->middleware('guest');

// Login POST
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    return back()->with('error', 'Invalid credentials.');
})->middleware('guest');

// Register page
Route::get('/register', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('register');
})->name('register')->middleware('guest');

// Register POST
Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);
    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'user_type' => 'teacher',
    ]);
    Auth::login($user);
    return redirect('/dashboard');
})->middleware('guest');

// Dashboard (protected)
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin()) {
        return view('admin.dashboard');
    }
    if ($user->isTeacher()) {
        // Get all subjects for this teacher
        $subjects = $user->subjects()->with(['classes.students'])->get();
        $subjectIds = $subjects->pluck('id');
        $classSections = \App\Models\ClassSection::whereIn('subject_id', $subjectIds)->get();
        $classSectionIds = $classSections->pluck('id');
        // Count unique students across all class sections
        $studentIds = \App\Models\Student::whereHas('classSections', function($q) use ($classSectionIds) {
            $q->whereIn('class_section_id', $classSectionIds);
        })->pluck('id')->unique();
        $totalStudents = $studentIds->count();
        $totalSubjects = $subjects->count();
        $totalClassSections = $classSections->count();
        $totalActivities = \App\Models\Activity::whereIn('subject_id', $subjectIds)->count();
        $totalQuizzes = \App\Models\Quiz::whereIn('subject_id', $subjectIds)->count();
        $totalExams = \App\Models\Exam::whereIn('subject_id', $subjectIds)->count();
        $totalProjects = \App\Models\Project::whereIn('subject_id', $subjectIds)->count();
        $totalRecitations = \App\Models\Recitation::whereIn('subject_id', $subjectIds)->count();
        
        // Latest created items
        $latestActivities = \App\Models\Activity::whereIn('subject_id', $subjectIds)->with('subject')->orderByDesc('created_at')->limit(3)->get();
        $latestQuizzes = \App\Models\Quiz::whereIn('subject_id', $subjectIds)->with('subject')->orderByDesc('created_at')->limit(3)->get();
        $latestExams = \App\Models\Exam::whereIn('subject_id', $subjectIds)->with('subject')->orderByDesc('created_at')->limit(3)->get();
        $latestRecitations = \App\Models\Recitation::whereIn('subject_id', $subjectIds)->with('subject')->orderByDesc('created_at')->limit(3)->get();
        $latestProjects = \App\Models\Project::whereIn('subject_id', $subjectIds)->with('subject')->orderByDesc('created_at')->limit(3)->get();

        // Upcoming items (due_date in future, any type)
        $now = now();
        $upcomingActivities = \App\Models\Activity::whereIn('subject_id', $subjectIds)->where('due_date', '>', $now)->count();
        $upcomingQuizzes = \App\Models\Quiz::whereIn('subject_id', $subjectIds)->where('due_date', '>', $now)->count();
        $upcomingExams = \App\Models\Exam::whereIn('subject_id', $subjectIds)->where('due_date', '>', $now)->count();
        $upcomingProjects = \App\Models\Project::whereIn('subject_id', $subjectIds)->where('due_date', '>', $now)->count();
        $upcomingRecitations = \App\Models\Recitation::whereIn('subject_id', $subjectIds)->where('due_date', '>', $now)->count();
        $totalUpcoming = $upcomingActivities + $upcomingQuizzes + $upcomingExams + $upcomingProjects + $upcomingRecitations;

        // Total graded submissions (all types)
        $gradedActivities = \App\Models\ActivityScore::whereHas('activity', function($q) use ($subjectIds) { $q->whereIn('subject_id', $subjectIds); })->count();
        $gradedQuizzes = \App\Models\QuizScore::whereHas('quiz', function($q) use ($subjectIds) { $q->whereIn('subject_id', $subjectIds); })->count();
        $gradedExams = \App\Models\ExamScore::whereHas('exam', function($q) use ($subjectIds) { $q->whereIn('subject_id', $subjectIds); })->count();
        $gradedProjects = \App\Models\ProjectScore::whereHas('project', function($q) use ($subjectIds) { $q->whereIn('subject_id', $subjectIds); })->count();
        $gradedRecitations = \App\Models\RecitationScore::whereHas('recitation', function($q) use ($subjectIds) { $q->whereIn('subject_id', $subjectIds); })->count();
        $totalGraded = $gradedActivities + $gradedQuizzes + $gradedExams + $gradedProjects + $gradedRecitations;

        // Pending grading: items with at least one student but no score yet
        $pendingActivities = \App\Models\Activity::whereIn('subject_id', $subjectIds)
            ->whereDoesntHave('scores')->count();
        $pendingQuizzes = \App\Models\Quiz::whereIn('subject_id', $subjectIds)
            ->whereDoesntHave('scores')->count();
        $pendingExams = \App\Models\Exam::whereIn('subject_id', $subjectIds)
            ->whereDoesntHave('scores')->count();
        $pendingProjects = \App\Models\Project::whereIn('subject_id', $subjectIds)
            ->whereDoesntHave('scores')->count();
        $pendingRecitations = \App\Models\Recitation::whereIn('subject_id', $subjectIds)
            ->whereDoesntHave('scores')->count();
        $totalPending = $pendingActivities + $pendingQuizzes + $pendingExams + $pendingProjects + $pendingRecitations;

        return view('teacher.dashboard', compact(
            'totalStudents',
            'totalSubjects',
            'totalClassSections',
            'totalActivities',
            'totalQuizzes',
            'totalExams',
            'totalProjects',
            'totalRecitations',
            'latestActivities',
            'latestQuizzes',
            'latestExams',
            'latestRecitations',
            'latestProjects',
            'totalUpcoming',
            'totalGraded',
            'totalPending',
        ));
    }
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->middleware('auth');

// Subjects routes for teacher (only teachers can access)
Route::get('/subjects', function () {
    if (!auth()->user()->isTeacher()) {
        abort(403, 'Access denied. Teachers only.');
    }
    $subjects = auth()->user()->subjects()->orderBy('code')->get();
    return view('teacher.subjects', compact('subjects'));
})->name('subjects.index')->middleware('auth');

Route::post('/subjects', function (Request $request) {
    if (!auth()->user()->isTeacher()) {
        abort(403, 'Access denied. Teachers only.');
    }
    
    $validated = $request->validate([
        'code' => [
            'required',
            'string',
            'max:20',
            function ($attribute, $value, $fail) {
                $exists = \App\Models\Subject::where('code', $value)
                    ->where('teacher_id', auth()->id())
                    ->exists();
                if ($exists) {
                    $fail('You already have a subject with this code.');
                }
            }
        ],
        'title' => 'required|string|max:255',
        'units' => 'required|numeric|min:0.5|max:6.0',
    ]);
    
    $subject = auth()->user()->subjects()->create([
        'code' => $validated['code'],
        'title' => $validated['title'],
        'units' => $validated['units'],
        'teacher_id' => auth()->id(),
    ]);
    
    return redirect()->route('subjects.index')->with('success', 'Subject added successfully!');
})->name('subjects.store')->middleware('auth');

Route::put('/subjects/{id}', function (Request $request, $id) {
    if (!auth()->user()->isTeacher()) {
        abort(403, 'Access denied. Teachers only.');
    }
    
    $subject = auth()->user()->subjects()->findOrFail($id);
    
    $validated = $request->validate([
        'code' => [
            'required',
            'string',
            'max:20',
            function ($attribute, $value, $fail) use ($id) {
                $exists = \App\Models\Subject::where('code', $value)
                    ->where('teacher_id', auth()->id())
                    ->where('id', '!=', $id)
                    ->exists();
                if ($exists) {
                    $fail('You already have a subject with this code.');
                }
            }
        ],
        'title' => 'required|string|max:255',
        'units' => 'required|numeric|min:0.5|max:6.0',
    ]);
    
    $subject->update($validated);
    
    return redirect()->route('subjects.index')->with('success', 'Subject updated successfully!');
})->name('subjects.update')->middleware('auth');

Route::delete('/subjects/{id}', function ($id) {
    if (!auth()->user()->isTeacher()) {
        abort(403, 'Access denied. Teachers only.');
    }
    
    $subject = auth()->user()->subjects()->findOrFail($id);
    $subject->delete();
    
    return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully!');
})->name('subjects.destroy')->middleware('auth');

Route::get('/subjects/{id}', function ($id) {
    if (!auth()->user()->isTeacher()) {
        abort(403, 'Access denied. Teachers only.');
    }
    return "Show Subject #$id (placeholder)";
})->name('subjects.show')->middleware('auth');

Route::get('/subjects/{subject}/classes', function ($subjectId) {
    if (!auth()->user()->isTeacher()) {
        abort(403, 'Access denied. Teachers only.');
    }
    $subject = auth()->user()->subjects()->findOrFail($subjectId);
    $classes = ClassSection::where('subject_id', $subject->id)
        ->where('teacher_id', auth()->id())
        ->orderBy('section')
        ->get();
    return view('teacher.subject-classes', compact('subject', 'classes'));
})->name('subjects.classes')->middleware('auth');

// Class Sections CRUD routes (teacher only)
Route::post('/subjects/{subject}/classes', [\App\Http\Controllers\ClassSectionController::class, 'store'])
    ->name('classes.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}', [\App\Http\Controllers\ClassSectionController::class, 'update'])
    ->name('classes.update')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}', [\App\Http\Controllers\ClassSectionController::class, 'destroy'])
    ->name('classes.destroy')->middleware('auth');

// Add a pattern for the {term} parameter
Route::pattern('term', 'midterms|finals');

// Grading System routes
Route::get('/subjects/{subject}/classes/{classSection}/{term}/grading', function ($subjectId, $classSectionId, $term) {
    if (!auth()->user()->isTeacher()) {
        abort(403, 'Access denied. Teachers only.');
    }
    $subject = auth()->user()->subjects()->findOrFail($subjectId);
    $classSection = ClassSection::where('id', $classSectionId)
        ->where('subject_id', $subject->id)
        ->where('teacher_id', auth()->id())
        ->firstOrFail();
    
    // Load enrolled students with calculated percentages
    $enrolledStudents = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
    
    // Calculate percentages for each student
    foreach ($enrolledStudents as $student) {
        // Activity Average Percentage
        $activityScores = \App\Models\ActivityScore::where('student_id', $student->id)
            ->whereHas('activity', function($query) use ($subject) {
                $query->where('subject_id', $subject->id);
            })->get();
        
        $activityAvgPct = 0;
        if ($activityScores->count() > 0) {
            $totalScore = $activityScores->sum('score');
            $totalMaxScore = $activityScores->sum(function($score) {
                return $score->activity->max_score;
            });
            $activityAvgPct = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;
        }
        
        // Activity Score Variance and Stddev (raw scores)
        $activityScoreValues = $activityScores->pluck('score')->toArray();
        $activityScoreVariance = 0;
        $activityScoreStddev = 0;
        if (count($activityScoreValues) > 1) {
            $mean = array_sum($activityScoreValues) / count($activityScoreValues);
            $variance = array_sum(array_map(function($score) use ($mean) {
                return pow($score - $mean, 2);
            }, $activityScoreValues)) / count($activityScoreValues);
            $activityScoreVariance = $variance;
            $activityScoreStddev = sqrt($variance);
        }
        $student->activity_score_variance = round($activityScoreVariance, 2);
        $student->activity_score_stddev = round($activityScoreStddev, 2);
        
        // Combined Activity + Quiz Score Variance and Stddev (raw scores)
        $quizScores = \App\Models\QuizScore::where('student_id', $student->id)
            ->whereHas('quiz', function($query) use ($subject) {
                $query->where('subject_id', $subject->id);
            })->get();
        
        $quizScoreValues = $quizScores->pluck('score')->toArray();
        $combinedScores = array_merge($activityScoreValues, $quizScoreValues);
        $activityQuizScoreVariance = 0;
        $activityQuizScoreStddev = 0;
        if (count($combinedScores) > 1) {
            $mean = array_sum($combinedScores) / count($combinedScores);
            $variance = array_sum(array_map(function($score) use ($mean) {
                return pow($score - $mean, 2);
            }, $combinedScores)) / count($combinedScores);
            $activityQuizScoreVariance = $variance;
            $activityQuizScoreStddev = sqrt($variance);
        }
        $student->activity_quiz_score_variance = round($activityQuizScoreVariance, 2);
        $student->activity_quiz_score_stddev = round($activityQuizScoreStddev, 2);
        
        // Quiz Average Percentage
        $quizAvgPct = 0;
        if ($quizScores->count() > 0) {
            $totalScore = $quizScores->sum('score');
            $totalMaxScore = $quizScores->sum(function($score) {
                return $score->quiz->max_score;
            });
            $quizAvgPct = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;
        }
        
        // Exam Score Percentage
        $examScores = \App\Models\ExamScore::where('student_id', $student->id)
            ->whereHas('exam', function($query) use ($subject) {
                $query->where('subject_id', $subject->id);
            })->get();
        
        $examScorePct = 0;
        if ($examScores->count() > 0) {
            $totalScore = $examScores->sum('score');
            $totalMaxScore = $examScores->sum(function($score) {
                return $score->exam->max_score;
            });
            $examScorePct = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;
        }
        
        // Late Submission Percentage (for projects)
        $projectScores = \App\Models\ProjectScore::where('student_id', $student->id)
            ->whereHas('project', function($query) use ($subject) {
                $query->where('subject_id', $subject->id);
            })->get();
        
        $lateSubmissionPct = 0;
        $missedSubmissionPct = 0;
        $resubmissionPct = 0;
        $projectScorePct = 0;
        
        // Projects
        $allProjects = \App\Models\Project::where('subject_id', $subject->id)->get();
        // Resubmission Percentage (projects only, count each project once if resubmitted)
        $resubmittedProjects = 0;
        foreach ($allProjects as $project) {
            $score = $projectScores->where('project_id', $project->id)->first();
            if ($score && $score->resubmission_count > 0) {
                $resubmittedProjects++;
            }
        }
        $resubmissionPct = $allProjects->count() > 0 ? ($resubmittedProjects / $allProjects->count()) * 100 : 0;
        
        // Project Score Percentage
        if ($projectScores->count() > 0) {
            $totalScore = $projectScores->sum('score');
            $totalMaxScore = $projectScores->sum(function($score) {
                return $score->project->max_score;
            });
            $projectScorePct = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;
        }
        
        // Recitation Score Percentage
        $recitationScores = \App\Models\RecitationScore::where('student_id', $student->id)
            ->whereHas('recitation', function($query) use ($subject) {
                $query->where('subject_id', $subject->id);
            })->get();
        
        $recitationScorePct = 0;
        if ($recitationScores->count() > 0) {
            $totalScore = $recitationScores->sum('score');
            $totalMaxScore = $recitationScores->sum(function($score) {
                return $score->recitation->max_score;
            });
            $recitationScorePct = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;
        }
        
        // Missed and Late Submission Percentage (all types)
        $totalItems = 0;
        $missedCount = 0;
        $lateCount = 0;

        // Activities
        $allActivities = \App\Models\Activity::where('subject_id', $subject->id)->get();
        foreach ($allActivities as $activity) {
            $score = $activityScores->where('activity_id', $activity->id)->first();
            $totalItems++;
            if (!$score || $score->score === null || $score->score == 0) {
                $missedCount++;
            }
            if ($score && isset($score->is_late) && $score->is_late) {
                $lateCount++;
            }
        }

        // Quizzes
        $allQuizzes = \App\Models\Quiz::where('subject_id', $subject->id)->get();
        foreach ($allQuizzes as $quiz) {
            $score = $quizScores->where('quiz_id', $quiz->id)->first();
            $totalItems++;
            if (!$score || $score->score === null || $score->score == 0) {
                $missedCount++;
            }
            if ($score && isset($score->is_late) && $score->is_late) {
                $lateCount++;
            }
        }

        // Exams
        $allExams = \App\Models\Exam::where('subject_id', $subject->id)->get();
        foreach ($allExams as $exam) {
            $score = $examScores->where('exam_id', $exam->id)->first();
            $totalItems++;
            if (!$score || $score->score === null || $score->score == 0) {
                $missedCount++;
            }
            if ($score && isset($score->is_late) && $score->is_late) {
                $lateCount++;
            }
        }

        // Projects
        $allProjects = \App\Models\Project::where('subject_id', $subject->id)->get();
        foreach ($allProjects as $project) {
            $score = $projectScores->where('project_id', $project->id)->first();
            $totalItems++;
            if (!$score || $score->score === null || $score->score == 0) {
                $missedCount++;
            }
            if ($score && isset($score->is_late) && $score->is_late) {
                $lateCount++;
            }
        }

        // Calculate Missed Submission Percentage
        $missedSubmissionPct = $totalItems > 0 ? ($missedCount / $totalItems) * 100 : 0;

        // Late Submission Percentage (only for items that support late submission)
        $lateCount = 0;
        $totalLateEligibleItems = 0;

        // Activities (support late submission)
        foreach ($allActivities as $activity) {
            $score = $activityScores->where('activity_id', $activity->id)->first();
            $totalLateEligibleItems++;
            if ($score && $score->is_late === true) {
                $lateCount++;
            }
        }

        // Projects (support late submission)
        foreach ($allProjects as $project) {
            $score = $projectScores->where('project_id', $project->id)->first();
            $totalLateEligibleItems++;
            if ($score && $score->is_late === true) {
                $lateCount++;
            }
        }

        // Note: Quizzes and Exams don't have is_late field, so they're not included

        $lateSubmissionPct = $totalLateEligibleItems > 0 ? ($lateCount / $totalLateEligibleItems) * 100 : 0;
        
        // Variation Score Percentage - Calculate based on variance in performance
        $variationScorePct = 0;
        $scores = [$examScorePct, $activityAvgPct, $quizAvgPct, $projectScorePct, $recitationScorePct];
        $validScores = array_filter($scores, function($score) { return $score > 0; }); // Only consider scores > 0
        
        if (count($validScores) > 1) {
            $mean = array_sum($validScores) / count($validScores);
            $variance = array_sum(array_map(function($score) use ($mean) { 
                return pow($score - $mean, 2); 
            }, $validScores)) / count($validScores);
            $standardDeviation = sqrt($variance);
            
            // Convert to percentage (higher variation = higher percentage)
            // Normalize: 0% = no variation, 100% = very high variation
            $variationScorePct = min(100, ($standardDeviation / 20) * 100); // Scale factor of 20 for reasonable range
        }
        
        // Add calculated percentages to student object
        $student->activity_avg_pct = round($activityAvgPct, 1);
        $student->quiz_avg_pct = round($quizAvgPct, 1);
        $student->exam_score_pct = round($examScorePct, 1);
        $student->late_submission_pct = round($lateSubmissionPct, 1);
        $student->missed_submission_pct = round($missedSubmissionPct, 1);
        $student->resubmission_pct = round($resubmissionPct, 1);
        $student->recitation_score_pct = round($recitationScorePct, 1);
        $student->project_score_pct = round($projectScorePct, 1);
        $student->variation_score_pct = round($variationScorePct, 1);
        
        // Get risk predictions from GRAIL API
        $riskPredictions = [];
        try {
            // Try localhost first, short timeout (1s)
            $response = null;
            try {
                $response = Http::timeout(0.3)->post('http://127.0.0.1:5000/api/predict', [
                    'exam_score_pct' => $student->exam_score_pct,
                    'missed_submission_pct' => $student->missed_submission_pct,
                    'late_submission_pct' => $student->late_submission_pct,
                    'resubmission_pct' => $student->resubmission_pct,
                    'variation_score_pct' => $student->variation_score_pct,
                    'activity_avg_pct' => $student->activity_avg_pct,
                    'quiz_avg_pct' => $student->quiz_avg_pct,
                    'project_score_pct' => $student->project_score_pct,
                    'recitation_score_pct' => $student->recitation_score_pct,
                ]);
            } catch (\Exception $e) {
                // If localhost fails, try remote with longer timeout (10s)
                $response = Http::timeout(10)->post('https://buratizer127.pythonanywhere.com/api/predict', [
                    'exam_score_pct' => $student->exam_score_pct,
                    'missed_submission_pct' => $student->missed_submission_pct,
                    'late_submission_pct' => $student->late_submission_pct,
                    'resubmission_pct' => $student->resubmission_pct,
                    'variation_score_pct' => $student->variation_score_pct,
                    'activity_avg_pct' => $student->activity_avg_pct,
                    'quiz_avg_pct' => $student->quiz_avg_pct,
                    'project_score_pct' => $student->project_score_pct,
                    'recitation_score_pct' => $student->recitation_score_pct,
                ]);
            }
            
            if ($response->successful()) {
                $data = $response->json();
                if ($data['success']) {
                    $riskPredictions = $data['data']['risk_categories'] ?? [];
                }
            }
        } catch (\Exception $e) {
            // If API is not available, use fallback risk assessment
            $riskPredictions = ["Machine Learning is asleep rn dud"];
        }
        
        $student->risk_predictions = $riskPredictions;
    }
    
    return view('teacher.grading-system', compact('classSection', 'enrolledStudents', 'term'));
})->name('grading.system')->middleware('auth');

Route::get('/subjects/{subject}/classes/{classSection}/{term}/gradebook', function ($subjectId, $classSectionId, $term) {
    if (!auth()->user()->isTeacher()) {
        abort(403, 'Access denied. Teachers only.');
    }
    $subject = auth()->user()->subjects()->findOrFail($subjectId);
    $classSection = ClassSection::where('id', $classSectionId)
        ->where('subject_id', $subject->id)
        ->where('teacher_id', auth()->id())
        ->firstOrFail();
    $gradingWeight = $subject->gradingWeight;

    // Fetch all assessment items per type and term
    $assessments = [
        'activities' => [
            'midterms' => $subject->activities()->where('term', 'midterms')->orderBy('order')->get(),
            'finals' => $subject->activities()->where('term', 'finals')->orderBy('order')->get(),
        ],
        'quizzes' => [
            'midterms' => $subject->quizzes()->where('term', 'midterms')->orderBy('order')->get(),
            'finals' => $subject->quizzes()->where('term', 'finals')->orderBy('order')->get(),
        ],
        'exams' => [
            'midterms' => $subject->exams()->where('term', 'midterms')->orderBy('order')->get(),
            'finals' => $subject->exams()->where('term', 'finals')->orderBy('order')->get(),
        ],
        'recitations' => [
            'midterms' => $subject->recitations()->where('term', 'midterms')->orderBy('order')->get(),
            'finals' => $subject->recitations()->where('term', 'finals')->orderBy('order')->get(),
        ],
        'projects' => [
            'midterms' => $subject->projects()->where('term', 'midterms')->orderBy('order')->get(),
            'finals' => $subject->projects()->where('term', 'finals')->orderBy('order')->get(),
        ],
    ];

    // Fetch students
    $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();

    // (Keep previous grade calculations for summary columns)
    foreach ($students as $student) {
        foreach (['midterms', 'finals'] as $t) {
            // Activities
            $activityScores = \App\Models\ActivityScore::where('student_id', $student->id)
                ->where('term', $t)
                ->whereHas('activity', function($q) use ($subject) { $q->where('subject_id', $subject->id); })
                ->get();
            $activityAvg = $activityScores->count() > 0 ?
                ($activityScores->sum('score') / $activityScores->sum(function($s){ return $s->activity->max_score; }) * 100) : null;
            // Quizzes
            $quizScores = \App\Models\QuizScore::where('student_id', $student->id)
                ->where('term', $t)
                ->whereHas('quiz', function($q) use ($subject) { $q->where('subject_id', $subject->id); })
                ->get();
            $quizAvg = $quizScores->count() > 0 ?
                ($quizScores->sum('score') / $quizScores->sum(function($s){ return $s->quiz->max_score; }) * 100) : null;
            // Exams
            $examScores = \App\Models\ExamScore::where('student_id', $student->id)
                ->where('term', $t)
                ->whereHas('exam', function($q) use ($subject) { $q->where('subject_id', $subject->id); })
                ->get();
            $examAvg = $examScores->count() > 0 ?
                ($examScores->sum('score') / $examScores->sum(function($s){ return $s->exam->max_score; }) * 100) : null;
            // Recitation
            $recitationScores = \App\Models\RecitationScore::where('student_id', $student->id)
                ->where('term', $t)
                ->whereHas('recitation', function($q) use ($subject) { $q->where('subject_id', $subject->id); })
                ->get();
            $recitationAvg = $recitationScores->count() > 0 ?
                ($recitationScores->sum('score') / $recitationScores->sum(function($s){ return $s->recitation->max_score; }) * 100) : null;
            // Projects
            $projectScores = \App\Models\ProjectScore::where('student_id', $student->id)
                ->where('term', $t)
                ->whereHas('project', function($q) use ($subject) { $q->where('subject_id', $subject->id); })
                ->get();
            $projectAvg = $projectScores->count() > 0 ?
                ($projectScores->sum('score') / $projectScores->sum(function($s){ return $s->project->max_score; }) * 100) : null;

            // Save to student object
            $student->{$t.'_activity'} = $activityAvg !== null ? round($activityAvg,1) : null;
            $student->{$t.'_quiz'} = $quizAvg !== null ? round($quizAvg,1) : null;
            $student->{$t.'_exam'} = $examAvg !== null ? round($examAvg,1) : null;
            $student->{$t.'_recitation'} = $recitationAvg !== null ? round($recitationAvg,1) : null;
            $student->{$t.'_project'} = $projectAvg !== null ? round($projectAvg,1) : null;

            // Calculate term grade using weights
            if ($gradingWeight) {
                $grade = 0;
                $totalWeight = 0;
                if ($activityAvg !== null) { $grade += $activityAvg * ($gradingWeight->activities/100); $totalWeight += $gradingWeight->activities; }
                if ($quizAvg !== null) { $grade += $quizAvg * ($gradingWeight->quizzes/100); $totalWeight += $gradingWeight->quizzes; }
                if ($examAvg !== null) { $grade += $examAvg * ($gradingWeight->exams/100); $totalWeight += $gradingWeight->exams; }
                if ($recitationAvg !== null) { $grade += $recitationAvg * ($gradingWeight->recitation/100); $totalWeight += $gradingWeight->recitation; }
                if ($projectAvg !== null) { $grade += $projectAvg * ($gradingWeight->projects/100); $totalWeight += $gradingWeight->projects; }
                $student->{$t.'_grade'} = $totalWeight > 0 ? round($grade,1) : null;
            } else {
                $student->{$t.'_grade'} = null;
            }
        }
        // Overall grade: average of midterm and finals if both exist
        if ($student->midterms_grade !== null && $student->finals_grade !== null) {
            $student->overall_grade = round((($student->midterms_grade + $student->finals_grade)/2),1);
        } else {
            $student->overall_grade = null;
        }
    }

    return view('teacher.gradebook', compact('classSection', 'term', 'gradingWeight', 'students', 'assessments'));
})->name('gradebook.all')->middleware('auth');

// Student management routes
Route::get('/students', [\App\Http\Controllers\StudentController::class, 'index'])->name('students.index')->middleware('auth');
Route::get('/students/{student}', [\App\Http\Controllers\StudentController::class, 'show'])->name('students.show')->middleware('auth');
Route::post('/students', [\App\Http\Controllers\StudentController::class, 'store'])->name('students.store')->middleware('auth');
Route::put('/students/{student}', [\App\Http\Controllers\StudentController::class, 'update'])->name('students.update')->middleware('auth');
Route::delete('/students/{student}', [\App\Http\Controllers\StudentController::class, 'destroy'])->name('students.destroy')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/students/{student}/analysis', [App\Http\Controllers\StudentController::class, 'analysis'])->name('students.analysis');
Route::post('/subjects/{subject}/classes/{classSection}/enroll', [\App\Http\Controllers\StudentController::class, 'enroll'])->name('students.enroll')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/students/{student}', [\App\Http\Controllers\StudentController::class, 'remove'])->name('students.remove')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/available-students', [\App\Http\Controllers\StudentController::class, 'getAvailableStudents'])->name('students.available')->middleware('auth');

// Activity routes
Route::get('/subjects/{subject}/classes/{classSection}/{term}/activities', [\App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/{term}/activities/{activity}', [\App\Http\Controllers\ActivityController::class, 'show'])->name('activities.show')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/{term}/activities', [\App\Http\Controllers\ActivityController::class, 'store'])->name('activities.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}/{term}/activities/{activity}', [\App\Http\Controllers\ActivityController::class, 'update'])->name('activities.update')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/{term}/activities/{activity}/scores', [\App\Http\Controllers\ActivityController::class, 'saveScores'])->name('activities.scores.save')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/{term}/activities/{activity}', [\App\Http\Controllers\ActivityController::class, 'destroy'])->name('activities.destroy')->middleware('auth');

// Quiz routes
Route::get('/subjects/{subject}/classes/{classSection}/{term}/quizzes', [\App\Http\Controllers\QuizController::class, 'index'])->name('quizzes.index')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/{term}/quizzes/{quiz}', [\App\Http\Controllers\QuizController::class, 'show'])->name('quizzes.show')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/{term}/quizzes', [\App\Http\Controllers\QuizController::class, 'store'])->name('quizzes.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}/{term}/quizzes/{quiz}', [\App\Http\Controllers\QuizController::class, 'update'])->name('quizzes.update')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/{term}/quizzes/{quiz}/scores', [\App\Http\Controllers\QuizController::class, 'saveScores'])->name('quizzes.scores.save')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/{term}/quizzes/{quiz}', [\App\Http\Controllers\QuizController::class, 'destroy'])->name('quizzes.destroy')->middleware('auth');

// Exam routes
Route::get('/subjects/{subject}/classes/{classSection}/{term}/exams', [\App\Http\Controllers\ExamController::class, 'index'])->name('exams.index')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/{term}/exams/{exam}', [\App\Http\Controllers\ExamController::class, 'show'])->name('exams.show')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/{term}/exams', [\App\Http\Controllers\ExamController::class, 'store'])->name('exams.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}/{term}/exams/{exam}', [\App\Http\Controllers\ExamController::class, 'update'])->name('exams.update')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/{term}/exams/{exam}/scores', [\App\Http\Controllers\ExamController::class, 'saveScores'])->name('exams.scores.save')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/{term}/exams/{exam}', [\App\Http\Controllers\ExamController::class, 'destroy'])->name('exams.destroy')->middleware('auth');

// Recitation routes
Route::get('/subjects/{subject}/classes/{classSection}/{term}/recitations', [\App\Http\Controllers\RecitationController::class, 'index'])->name('recitations.index')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/{term}/recitations/{recitation}', [\App\Http\Controllers\RecitationController::class, 'show'])->name('recitations.show')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/{term}/recitations', [\App\Http\Controllers\RecitationController::class, 'store'])->name('recitations.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}/{term}/recitations/{recitation}', [\App\Http\Controllers\RecitationController::class, 'update'])->name('recitations.update')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/{term}/recitations/{recitation}/scores', [\App\Http\Controllers\RecitationController::class, 'saveScores'])->name('recitations.scores.save')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/{term}/recitations/{recitation}', [\App\Http\Controllers\RecitationController::class, 'destroy'])->name('recitations.destroy')->middleware('auth');

// Project routes
Route::get('/subjects/{subject}/classes/{classSection}/{term}/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/{term}/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/{term}/projects', [\App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}/{term}/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/{term}/projects/{project}/scores', [\App\Http\Controllers\ProjectController::class, 'saveScores'])->name('projects.scores.save')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/{term}/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('auth');

// API route for class selector
Route::get('/api/subjects/{subjectId}/classes', function ($subjectId) {
    if (!auth()->user()->isTeacher()) {
        abort(403, 'Access denied. Teachers only.');
    }
    
    $subject = auth()->user()->subjects()->findOrFail($subjectId);
    $classes = $subject->classes()->select('id', 'section', 'schedule', 'student_count')->get();
    
    return response()->json($classes);
})->middleware('auth');

// Grading weights update
Route::post('/subjects/{subject}/grading-weights', [GradingWeightController::class, 'update'])->name('grading.weights.update')->middleware('auth');

// Fallback risk calculation function
function calculateFallbackRisk($student) {
    $risks = [];
    
    // Academic Performance Risks
    if ($student->exam_score_pct < 70) {
        $risks[] = 'Low Exam Performance';
    }
    if ($student->activity_avg_pct < 75) {
        $risks[] = 'Poor Activity Performance';
    }
    if ($student->quiz_avg_pct < 75) {
        $risks[] = 'Poor Quiz Performance';
    }
    if ($student->project_score_pct < 70) {
        $risks[] = 'Low Project Scores';
    }
    if ($student->recitation_score_pct < 70) {
        $risks[] = 'Poor Recitation Performance';
    }
    
    // Submission Behavior Risks
    if ($student->missed_submission_pct > 20) {
        $risks[] = 'High Missed Submissions';
    }
    if ($student->late_submission_pct > 30) {
        $risks[] = 'Frequent Late Submissions';
    }
    if ($student->resubmission_pct > 25) {
        $risks[] = 'Multiple Resubmissions';
    }
    
    // Overall Performance Risk
    $overallAvg = ($student->exam_score_pct + $student->activity_avg_pct + $student->quiz_avg_pct + $student->project_score_pct + $student->recitation_score_pct) / 5;
    if ($overallAvg < 75) {
        $risks[] = 'Overall Academic Risk';
    }
    
    // Consistency Risk
    if ($student->variation_score_pct > 50) {
        $risks[] = 'Inconsistent Performance';
    }
    
    return $risks;
}
