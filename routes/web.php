<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\ClassSection;

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
    if (auth()->user()->isAdmin()) {
        return view('admin.dashboard');
    }
    return view('teacher.dashboard');
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

// Grading System routes
Route::get('/subjects/{subject}/classes/{classSection}/grading', function ($subjectId, $classSectionId) {
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
            $riskPredictions = self::calculateFallbackRisk($student);
        }
        
        $student->risk_predictions = $riskPredictions;
    }
    
    return view('teacher.grading-system', compact('classSection', 'enrolledStudents'));
})->name('grading.system')->middleware('auth');

Route::get('/subjects/{subject}/classes/{classSection}/gradebook', function ($subjectId, $classSectionId) {
    if (!auth()->user()->isTeacher()) {
        abort(403, 'Access denied. Teachers only.');
    }
    $subject = auth()->user()->subjects()->findOrFail($subjectId);
    $classSection = ClassSection::where('id', $classSectionId)
        ->where('subject_id', $subject->id)
        ->where('teacher_id', auth()->id())
        ->firstOrFail();
    return view('teacher.gradebook', compact('classSection'));
})->name('gradebook.all')->middleware('auth');

// Student management routes
Route::get('/students', [\App\Http\Controllers\StudentController::class, 'index'])->name('students.index')->middleware('auth');
Route::post('/students', [\App\Http\Controllers\StudentController::class, 'store'])->name('students.store')->middleware('auth');
Route::put('/students/{student}', [\App\Http\Controllers\StudentController::class, 'update'])->name('students.update')->middleware('auth');
Route::delete('/students/{student}', [\App\Http\Controllers\StudentController::class, 'destroy'])->name('students.destroy')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/enroll', [\App\Http\Controllers\StudentController::class, 'enroll'])->name('students.enroll')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/students/{student}', [\App\Http\Controllers\StudentController::class, 'remove'])->name('students.remove')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/available-students', [\App\Http\Controllers\StudentController::class, 'getAvailableStudents'])->name('students.available')->middleware('auth');

// Activity routes
Route::get('/subjects/{subject}/classes/{classSection}/activities', [\App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/activities/{activity}', [\App\Http\Controllers\ActivityController::class, 'show'])->name('activities.show')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/activities', [\App\Http\Controllers\ActivityController::class, 'store'])->name('activities.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}/activities/{activity}', [\App\Http\Controllers\ActivityController::class, 'update'])->name('activities.update')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/activities/{activity}/scores', [\App\Http\Controllers\ActivityController::class, 'saveScores'])->name('activities.scores.save')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/activities/{activity}', [\App\Http\Controllers\ActivityController::class, 'destroy'])->name('activities.destroy')->middleware('auth');

// Quiz routes
Route::get('/subjects/{subject}/classes/{classSection}/quizzes', [\App\Http\Controllers\QuizController::class, 'index'])->name('quizzes.index')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/quizzes/{quiz}', [\App\Http\Controllers\QuizController::class, 'show'])->name('quizzes.show')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/quizzes', [\App\Http\Controllers\QuizController::class, 'store'])->name('quizzes.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}/quizzes/{quiz}', [\App\Http\Controllers\QuizController::class, 'update'])->name('quizzes.update')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/quizzes/{quiz}/scores', [\App\Http\Controllers\QuizController::class, 'saveScores'])->name('quizzes.scores.save')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/quizzes/{quiz}', [\App\Http\Controllers\QuizController::class, 'destroy'])->name('quizzes.destroy')->middleware('auth');

// Exam routes
Route::get('/subjects/{subject}/classes/{classSection}/exams', [\App\Http\Controllers\ExamController::class, 'index'])->name('exams.index')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/exams/{exam}', [\App\Http\Controllers\ExamController::class, 'show'])->name('exams.show')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/exams', [\App\Http\Controllers\ExamController::class, 'store'])->name('exams.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}/exams/{exam}', [\App\Http\Controllers\ExamController::class, 'update'])->name('exams.update')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/exams/{exam}/scores', [\App\Http\Controllers\ExamController::class, 'saveScores'])->name('exams.scores.save')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/exams/{exam}', [\App\Http\Controllers\ExamController::class, 'destroy'])->name('exams.destroy')->middleware('auth');

// Recitation routes
Route::get('/subjects/{subject}/classes/{classSection}/recitations', [\App\Http\Controllers\RecitationController::class, 'index'])->name('recitations.index')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/recitations/{recitation}', [\App\Http\Controllers\RecitationController::class, 'show'])->name('recitations.show')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/recitations', [\App\Http\Controllers\RecitationController::class, 'store'])->name('recitations.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}/recitations/{recitation}', [\App\Http\Controllers\RecitationController::class, 'update'])->name('recitations.update')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/recitations/{recitation}/scores', [\App\Http\Controllers\RecitationController::class, 'saveScores'])->name('recitations.scores.save')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/recitations/{recitation}', [\App\Http\Controllers\RecitationController::class, 'destroy'])->name('recitations.destroy')->middleware('auth');

// Project routes
Route::get('/subjects/{subject}/classes/{classSection}/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index')->middleware('auth');
Route::get('/subjects/{subject}/classes/{classSection}/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/projects', [\App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store')->middleware('auth');
Route::put('/subjects/{subject}/classes/{classSection}/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update')->middleware('auth');
Route::post('/subjects/{subject}/classes/{classSection}/projects/{project}/scores', [\App\Http\Controllers\ProjectController::class, 'saveScores'])->name('projects.scores.save')->middleware('auth');
Route::delete('/subjects/{subject}/classes/{classSection}/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('auth');

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
