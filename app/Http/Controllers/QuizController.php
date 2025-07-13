<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizScore;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function index($subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $quizzes = $classSection->subject->quizzes()->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        
        $selectedQuiz = $quizzes->first();

        return view('teacher.quizzes', compact('classSection', 'quizzes', 'students', 'selectedQuiz'));
    }

    public function show($subjectId, $classSectionId, $quizId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $quizzes = $classSection->subject->quizzes()->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedQuiz = $quizzes->where('id', $quizId)->firstOrFail();

        return view('teacher.quizzes', compact('classSection', 'quizzes', 'students', 'selectedQuiz'));
    }

    public function store(Request $request, $subjectId, $classSectionId)
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
            'order' => $classSection->subject->quizzes()->count() + 1,
        ]);

        return back()->with('success', 'Quiz created successfully!');
    }

    public function saveScores(Request $request, $subjectId, $classSectionId, $quizId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $quiz = $classSection->subject->quizzes()->findOrFail($quizId);
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

            QuizScore::updateOrCreate(
                [
                    'quiz_id' => $quiz->id,
                    'student_id' => $student->id,
                ],
                [
                    'score' => $score,
                    'submitted_at' => $score ? now() : null,
                ]
            );
        }

        return back()->with('success', 'Scores saved successfully!');
    }

    public function update(Request $request, $subjectId, $classSectionId, $quizId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $quiz = $classSection->subject->quizzes()->findOrFail($quizId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0.01|max:999.99',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $quiz->update([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Quiz updated successfully!');
    }

    public function destroy($subjectId, $classSectionId, $quizId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $quiz = Quiz::findOrFail($quizId);
        $quiz->delete();
        return redirect()->route('quizzes.index', [
            'subject' => $subjectId,
            'classSection' => $classSectionId
        ])->with('success', 'Quiz deleted successfully!');
    }
}
