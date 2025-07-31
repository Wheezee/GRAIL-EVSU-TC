<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizScore;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function index($subjectId, $classSectionId, $term)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $quizzes = $classSection->subject->quizzes()->where('term', $term)->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedQuiz = $quizzes->first();

        return view('teacher.quizzes', compact('classSection', 'quizzes', 'students', 'selectedQuiz', 'term'));
    }

    public function show($subjectId, $classSectionId, $term, $quizId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $quizzes = $classSection->subject->quizzes()->where('term', $term)->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedQuiz = $quizzes->where('id', $quizId)->first();

        if (!$selectedQuiz) {
            return redirect()->route('quizzes.index', [
                'subject' => $subjectId,
                'classSection' => $classSectionId,
                'term' => $term
            ])->with('error', 'The selected quiz does not exist for this term.');
        }

        return view('teacher.quizzes', compact('classSection', 'quizzes', 'students', 'selectedQuiz', 'term'));
    }

    public function store(Request $request, $subjectId, $classSectionId, $term)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0.01|max:999.99',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $quiz = $classSection->subject->quizzes()->create([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
            'order' => $classSection->subject->quizzes()->where('term', $term)->count() + 1,
            'term' => $term,
        ]);

        return back()->with('success', 'Quiz created successfully!');
    }

    public function saveScores(Request $request, $subjectId, $classSectionId, $term, $quizId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $quiz = $classSection->subject->quizzes()->where('term', $term)->findOrFail($quizId);
        $students = $classSection->students;

        $validator = Validator::make($request->all(), [
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:' . $quiz->max_score,
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        foreach ($students as $student) {
            $score = $request->scores[$student->id] ?? null;

            // Find existing score for this quiz and student
            $existingScore = QuizScore::where('quiz_id', $quiz->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existingScore) {
                // Update existing record
                $existingScore->update([
                    'term' => $term,
                    'score' => $score,
                    'submitted_at' => $score ? now() : null,
                ]);
            } else {
                // Create new record
                QuizScore::create([
                    'quiz_id' => $quiz->id,
                    'student_id' => $student->id,
                    'term' => $term,
                    'score' => $score,
                    'submitted_at' => $score ? now() : null,
                ]);
            }
        }

        return back()->with('success', 'Scores saved successfully!');
    }

    public function update(Request $request, $subjectId, $classSectionId, $term, $quizId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0.01|max:999.99',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $quiz = $classSection->subject->quizzes()->where('term', $term)->findOrFail($quizId);

        $quiz->update([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Quiz updated successfully!');
    }

    public function destroy($subjectId, $classSectionId, $term, $quizId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $quiz = $classSection->subject->quizzes()->where('term', $term)->findOrFail($quizId);
        $quiz->delete();
        return redirect()->route('quizzes.index', [
            'subject' => $subjectId,
            'classSection' => $classSectionId,
            'term' => $term
        ])->with('success', 'Quiz deleted successfully!');
    }
}
