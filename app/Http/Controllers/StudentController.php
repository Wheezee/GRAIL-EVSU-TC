<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $students = Student::orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15);

        return view('teacher.students.index', compact('students'));
    }

    public function show(Student $student)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        // Get enrolled class sections with subjects
        $enrolledClasses = $student->classSections()
            ->with(['subject', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate age if birth date is available
        $age = null;
        if ($student->birth_date) {
            $age = $student->birth_date->diffInYears(now());
        }

        // Get academic performance data for each enrolled class
        $academicData = [];
        foreach ($enrolledClasses as $classSection) {
            $subject = $classSection->subject;
            
            // Get all assessment types for this subject
            $activities = $subject->activities()->with('scores')->get();
            $quizzes = $subject->quizzes()->with('scores')->get();
            $exams = $subject->exams()->with('scores')->get();
            $projects = $subject->projects()->with('scores')->get();
            $recitations = $subject->recitations()->with('scores')->get();

            // Get student's scores
            $activityScores = $activities->flatMap(function($activity) use ($student) {
                return $activity->scores()->where('student_id', $student->id)->get();
            });
            $quizScores = $quizzes->flatMap(function($quiz) use ($student) {
                return $quiz->scores()->where('student_id', $student->id)->get();
            });
            $examScores = $exams->flatMap(function($exam) use ($student) {
                return $exam->scores()->where('student_id', $student->id)->get();
            });
            $projectScores = $projects->flatMap(function($project) use ($student) {
                return $project->scores()->where('student_id', $student->id)->get();
            });
            $recitationScores = $recitations->flatMap(function($recitation) use ($student) {
                return $recitation->scores()->where('student_id', $student->id)->get();
            });

            // Calculate averages
            $activityAvg = $activityScores->count() > 0 ? $activityScores->avg('score') : null;
            $quizAvg = $quizScores->count() > 0 ? $quizScores->avg('score') : null;
            $examAvg = $examScores->count() > 0 ? $examScores->avg('score') : null;
            $projectAvg = $projectScores->count() > 0 ? $projectScores->avg('score') : null;
            $recitationAvg = $recitationScores->count() > 0 ? $recitationScores->avg('score') : null;

            $academicData[$classSection->id] = [
                'subject' => $subject,
                'classSection' => $classSection,
                'activities' => [
                    'count' => $activities->count(),
                    'scores' => $activityScores,
                    'average' => $activityAvg,
                ],
                'quizzes' => [
                    'count' => $quizzes->count(),
                    'scores' => $quizScores,
                    'average' => $quizAvg,
                ],
                'exams' => [
                    'count' => $exams->count(),
                    'scores' => $examScores,
                    'average' => $examAvg,
                ],
                'projects' => [
                    'count' => $projects->count(),
                    'scores' => $projectScores,
                    'average' => $projectAvg,
                ],
                'recitations' => [
                    'count' => $recitations->count(),
                    'scores' => $recitationScores,
                    'average' => $recitationAvg,
                ],
            ];
        }

        return view('teacher.students.show', compact('student', 'enrolledClasses', 'age', 'academicData'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:20|unique:students,student_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email',
            'middle_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $student = Student::create($validator->validated());

        return back()->with('success', 'Student created successfully!');
    }

    public function update(Request $request, Student $student)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:20|unique:students,student_id,' . $student->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email,' . $student->id,
            'middle_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $student->update($validator->validated());

        return back()->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        // Check if student is enrolled in any classes
        if ($student->classSections()->count() > 0) {
            return back()->with('error', 'Cannot delete student who is enrolled in classes. Please unenroll them first.');
        }

        $student->delete();

        return back()->with('success', 'Student deleted successfully!');
    }

    public function enroll(Request $request, $subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $enrolledCount = 0;
        $alreadyEnrolledCount = 0;

        foreach ($request->student_ids as $studentId) {
            // Check if already enrolled
            if ($classSection->students()->where('students.id', $studentId)->exists()) {
                $alreadyEnrolledCount++;
                continue;
            }

            $classSection->students()->attach($studentId, [
                'enrollment_date' => now(),
                'status' => 'enrolled'
            ]);
            $enrolledCount++;
        }

        // Update student count
        $classSection->update([
            'student_count' => $classSection->students()->count()
        ]);

        $message = '';
        if ($enrolledCount > 0) {
            $message .= $enrolledCount . ' student' . ($enrolledCount > 1 ? 's' : '') . ' enrolled successfully!';
        }
        if ($alreadyEnrolledCount > 0) {
            $message .= ($enrolledCount > 0 ? ' ' : '') . $alreadyEnrolledCount . ' student' . ($alreadyEnrolledCount > 1 ? 's were' : ' was') . ' already enrolled.';
        }

        return back()->with('success', $message);
    }

    public function remove(Request $request, $subjectId, $classSectionId, $studentId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $classSection->students()->detach($studentId);

        // Update student count
        $classSection->update([
            'student_count' => $classSection->students()->count()
        ]);

        return back()->with('success', 'Student removed from class successfully!');
    }

    public function getAvailableStudents($subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        // Get all students not enrolled in this class section
        $enrolledStudentIds = $classSection->students()->pluck('students.id');
        
        $availableStudents = Student::whereNotIn('id', $enrolledStudentIds)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'student_id', 'first_name', 'last_name']);

        return response()->json($availableStudents);
    }

    public function analysis($subjectId, $classSectionId, \App\Models\Student $student)
    {
        $subject = \App\Models\Subject::findOrFail($subjectId);
        $classSection = \App\Models\ClassSection::findOrFail($classSectionId);

        $activities = $subject->activities;
        $quizzes = $subject->quizzes;
        $exams = $subject->exams;
        $projects = $subject->projects;
        $recitations = $subject->recitations;

        $activityScores = \App\Models\ActivityScore::where('student_id', $student->id)
            ->whereIn('activity_id', $activities->pluck('id'))
            ->get()->keyBy('activity_id');
        $quizScores = \App\Models\QuizScore::where('student_id', $student->id)
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()->keyBy('quiz_id');
        $examScores = \App\Models\ExamScore::where('student_id', $student->id)
            ->whereIn('exam_id', $exams->pluck('id'))
            ->get()->keyBy('exam_id');
        $projectScores = \App\Models\ProjectScore::where('student_id', $student->id)
            ->whereIn('project_id', $projects->pluck('id'))
            ->get()->keyBy('project_id');
        $recitationScores = \App\Models\RecitationScore::where('student_id', $student->id)
            ->whereIn('recitation_id', $recitations->pluck('id'))
            ->get()->keyBy('recitation_id');

        // --- Risk assessment logic (copied from web.php grading route) ---
        $activityAvgPct = 0;
        if ($activityScores->count() > 0) {
            $totalScore = $activityScores->sum('score');
            $totalMaxScore = $activityScores->sum(function($score) { return $score->activity->max_score; });
            $activityAvgPct = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;
        }
        $quizAvgPct = 0;
        if ($quizScores->count() > 0) {
            $totalScore = $quizScores->sum('score');
            $totalMaxScore = $quizScores->sum(function($score) { return $score->quiz->max_score; });
            $quizAvgPct = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;
        }
        $examScorePct = 0;
        if ($examScores->count() > 0) {
            $totalScore = $examScores->sum('score');
            $totalMaxScore = $examScores->sum(function($score) { return $score->exam->max_score; });
            $examScorePct = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;
        }
        $projectScorePct = 0;
        if ($projectScores->count() > 0) {
            $totalScore = $projectScores->sum('score');
            $totalMaxScore = $projectScores->sum(function($score) { return $score->project->max_score; });
            $projectScorePct = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;
        }
        $recitationScorePct = 0;
        if ($recitationScores->count() > 0) {
            $totalScore = $recitationScores->sum('score');
            $totalMaxScore = $recitationScores->sum(function($score) { return $score->recitation->max_score; });
            $recitationScorePct = $totalMaxScore > 0 ? ($totalScore / $totalMaxScore) * 100 : 0;
        }
        // Missed and Late Submission Percentage (all types)
        $totalItems = 0;
        $missedCount = 0;
        $lateCount = 0;
        // Activities
        foreach ($activities as $activity) {
            $score = $activityScores->get($activity->id);
            $totalItems++;
            if (!$score || $score->score === null || $score->score == 0) {
                $missedCount++;
            }
            if ($score && isset($score->is_late) && $score->is_late) {
                $lateCount++;
            }
        }
        // Quizzes
        foreach ($quizzes as $quiz) {
            $score = $quizScores->get($quiz->id);
            $totalItems++;
            if (!$score || $score->score === null || $score->score == 0) {
                $missedCount++;
            }
            if ($score && isset($score->is_late) && $score->is_late) {
                $lateCount++;
            }
        }
        // Exams
        foreach ($exams as $exam) {
            $score = $examScores->get($exam->id);
            $totalItems++;
            if (!$score || $score->score === null || $score->score == 0) {
                $missedCount++;
            }
            if ($score && isset($score->is_late) && $score->is_late) {
                $lateCount++;
            }
        }
        // Projects
        foreach ($projects as $project) {
            $score = $projectScores->get($project->id);
            $totalItems++;
            if (!$score || $score->score === null || $score->score == 0) {
                $missedCount++;
            }
            if ($score && isset($score->is_late) && $score->is_late) {
                $lateCount++;
            }
        }
        $missedSubmissionPct = $totalItems > 0 ? ($missedCount / $totalItems) * 100 : 0;
        $lateSubmissionPct = $totalItems > 0 ? ($lateCount / $totalItems) * 100 : 0;
        // Resubmission Percentage (projects only)
        $resubmittedProjects = 0;
        foreach ($projects as $project) {
            $score = $projectScores->get($project->id);
            if ($score && isset($score->resubmission_count) && $score->resubmission_count > 0) {
                $resubmittedProjects++;
            }
        }
        $resubmissionPct = $projects->count() > 0 ? ($resubmittedProjects / $projects->count()) * 100 : 0;
        // Variation Score Percentage
        $variationScorePct = 0;
        $scores = [$examScorePct, $activityAvgPct, $quizAvgPct, $projectScorePct, $recitationScorePct];
        $validScores = array_filter($scores, function($score) { return $score > 0; });
        if (count($validScores) > 1) {
            $mean = array_sum($validScores) / count($validScores);
            $variance = array_sum(array_map(function($score) use ($mean) { return pow($score - $mean, 2); }, $validScores)) / count($validScores);
            $standardDeviation = sqrt($variance);
            $variationScorePct = min(100, ($standardDeviation / 20) * 100);
        }
        // Set on student for API
        $student->activity_avg_pct = round($activityAvgPct, 1);
        $student->quiz_avg_pct = round($quizAvgPct, 1);
        $student->exam_score_pct = round($examScorePct, 1);
        $student->late_submission_pct = round($lateSubmissionPct, 1);
        $student->missed_submission_pct = round($missedSubmissionPct, 1);
        $student->resubmission_pct = round($resubmissionPct, 1);
        $student->recitation_score_pct = round($recitationScorePct, 1);
        $student->project_score_pct = round($projectScorePct, 1);
        $student->variation_score_pct = round($variationScorePct, 1);
        // --- Call GRAIL API or fallback ---
        $riskPredictions = [];
        try {
            $response = null;
            try {
                $response = \Http::timeout(0.3)->post('http://127.0.0.1:5000/api/predict', [
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
                $response = \Http::timeout(10)->post('https://buratizer127.pythonanywhere.com/api/predict', [
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
            if ($response && $response->successful()) {
                $data = $response->json();
                if ($data['success']) {
                    $riskPredictions = $data['data']['risk_categories'] ?? [];
                }
            }
        } catch (\Exception $e) {
            // fallback
            $riskPredictions = [];
        }
        $student->risk_predictions = $riskPredictions;
        $remarks = $student->risk_predictions ?? [];
        // --- end risk assessment logic ---

        // Check if student has any real scores
        $hasAnyScore = false;
        foreach ([$activityScores, $quizScores, $examScores, $projectScores, $recitationScores] as $scoreSet) {
            if ($scoreSet->count() > 0 && $scoreSet->sum('score') > 0) {
                $hasAnyScore = true;
                break;
            }
        }
        if (!$hasAnyScore) {
            $riskLevel = 'No Data';
            $remarks = ['Lacking data to make a general status'];
            return view('teacher.student-analysis', compact(
                'student', 'classSection', 'subject',
                'activities', 'quizzes', 'exams', 'projects', 'recitations', 'remarks',
                'activityScores', 'quizScores', 'examScores', 'projectScores', 'recitationScores', 'riskLevel'
            ));
        }

        return view('teacher.student-analysis', compact(
            'student', 'classSection', 'subject',
            'activities', 'quizzes', 'exams', 'projects', 'recitations', 'remarks',
            'activityScores', 'quizScores', 'examScores', 'projectScores', 'recitationScores'
        ));
    }
}
