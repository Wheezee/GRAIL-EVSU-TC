<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamScore;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    public function index($subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $exams = $classSection->subject->exams()->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        
        $selectedExam = $exams->first();

        return view('teacher.exams', compact('classSection', 'exams', 'students', 'selectedExam'));
    }

    public function show($subjectId, $classSectionId, $examId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $exams = $classSection->subject->exams()->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedExam = $exams->where('id', $examId)->first();
        if (!$selectedExam) {
            // If the exam is missing, redirect to the exams index for this class section with a message
            return redirect()->route('exams.index', ['subject' => $subjectId, 'classSection' => $classSectionId])
                ->with('error', 'The selected exam could not be found.');
        }

        return view('teacher.exams', compact('classSection', 'exams', 'students', 'selectedExam'));
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

        $exam = $classSection->subject->exams()->create([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
            'order' => $classSection->subject->exams()->count() + 1,
        ]);

        return back()->with('success', 'Exam created successfully!');
    }

    public function saveScores(Request $request, $subjectId, $classSectionId, $examId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $exam = $classSection->subject->exams()->findOrFail($examId);
        $students = $classSection->students;

        $validator = Validator::make($request->all(), [
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:' . $exam->max_score,
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        foreach ($students as $student) {
            $score = $request->scores[$student->id] ?? null;

            ExamScore::updateOrCreate(
                [
                    'exam_id' => $exam->id,
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

    public function update(Request $request, $subjectId, $classSectionId, $examId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $exam = $classSection->subject->exams()->findOrFail($examId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0.01|max:999.99',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $exam->update([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Exam updated successfully!');
    }

    public function destroy($subjectId, $classSectionId, $examId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $exam = Exam::findOrFail($examId);
        $exam->delete();
        return redirect()->route('exams.index', [
            'subject' => $subjectId,
            'classSection' => $classSectionId
        ])->with('success', 'Exam deleted successfully!');
    }
}
