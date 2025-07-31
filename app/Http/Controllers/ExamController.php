<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamScore;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    public function index($subjectId, $classSectionId, $term)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $exams = $classSection->subject->exams()->where('term', $term)->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedExam = $exams->first();

        return view('teacher.exams', compact('classSection', 'exams', 'students', 'selectedExam', 'term'));
    }

    public function show($subjectId, $classSectionId, $term, $examId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $exams = $classSection->subject->exams()->where('term', $term)->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedExam = $exams->where('id', $examId)->first();

        if (!$selectedExam) {
            return redirect()->route('exams.index', [
                'subject' => $subjectId,
                'classSection' => $classSectionId,
                'term' => $term
            ]);
        }

        return view('teacher.exams', compact('classSection', 'exams', 'students', 'selectedExam', 'term'));
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

        $exam = $classSection->subject->exams()->create([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
            'order' => $classSection->subject->exams()->where('term', $term)->count() + 1,
            'term' => $term,
        ]);

        return back()->with('success', 'Exam created successfully!');
    }

    public function saveScores(Request $request, $subjectId, $classSectionId, $term, $examId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $exam = $classSection->subject->exams()->where('term', $term)->findOrFail($examId);
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

            // Find existing score for this exam and student
            $existingScore = ExamScore::where('exam_id', $exam->id)
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
                ExamScore::create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'term' => $term,
                    'score' => $score,
                    'submitted_at' => $score ? now() : null,
                ]);
            }
        }

        return back()->with('success', 'Scores saved successfully!');
    }

    public function update(Request $request, $subjectId, $classSectionId, $term, $examId)
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

        $exam = $classSection->subject->exams()->where('term', $term)->findOrFail($examId);

        $exam->update([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Exam updated successfully!');
    }

    public function destroy($subjectId, $classSectionId, $term, $examId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $exam = $classSection->subject->exams()->where('term', $term)->findOrFail($examId);
        $exam->delete();
        return redirect()->route('exams.index', [
            'subject' => $subjectId,
            'classSection' => $classSectionId,
            'term' => $term
        ])->with('success', 'Exam deleted successfully!');
    }
}
