<?php

namespace App\Http\Controllers;

use App\Models\Recitation;
use App\Models\RecitationScore;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecitationController extends Controller
{
    public function index($subjectId, $classSectionId, $term)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $recitations = $classSection->subject->recitations()->where('term', $term)->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedRecitation = $recitations->first();

        return view('teacher.recitations', compact('classSection', 'recitations', 'students', 'selectedRecitation', 'term'));
    }

    public function show($subjectId, $classSectionId, $term, $recitationId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $recitations = $classSection->subject->recitations()->where('term', $term)->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedRecitation = $recitations->where('id', $recitationId)->first();

        if (!$selectedRecitation) {
            return redirect()->route('recitations.index', [
                'subject' => $subjectId,
                'classSection' => $classSectionId,
                'term' => $term
            ]);
        }

        return view('teacher.recitations', compact('classSection', 'recitations', 'students', 'selectedRecitation', 'term'));
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

        $recitation = $classSection->subject->recitations()->create([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
            'order' => $classSection->subject->recitations()->where('term', $term)->count() + 1,
            'term' => $term,
        ]);

        return back()->with('success', 'Recitation created successfully!');
    }

    public function saveScores(Request $request, $subjectId, $classSectionId, $term, $recitationId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $recitation = $classSection->subject->recitations()->where('term', $term)->findOrFail($recitationId);
        $students = $classSection->students;

        $validator = Validator::make($request->all(), [
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:' . $recitation->max_score,
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        foreach ($students as $student) {
            $score = $request->scores[$student->id] ?? null;

            RecitationScore::updateOrCreate(
                [
                    'recitation_id' => $recitation->id,
                    'student_id' => $student->id,
                    'term' => $term,
                ],
                [
                    'score' => $score,
                    'submitted_at' => $score ? now() : null,
                ]
            );
        }

        return back()->with('success', 'Scores saved successfully!');
    }

    public function update(Request $request, $subjectId, $classSectionId, $term, $recitationId)
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

        $recitation = $classSection->subject->recitations()->where('term', $term)->findOrFail($recitationId);

        $recitation->update([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Recitation updated successfully!');
    }

    public function destroy($subjectId, $classSectionId, $term, $recitationId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $recitation = $classSection->subject->recitations()->where('term', $term)->findOrFail($recitationId);
        $recitation->delete();
        return redirect()->route('recitations.index', [
            'subject' => $subjectId,
            'classSection' => $classSectionId,
            'term' => $term
        ])->with('success', 'Recitation deleted successfully!');
    }
}
