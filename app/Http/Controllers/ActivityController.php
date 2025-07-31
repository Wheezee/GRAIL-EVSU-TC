<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityScore;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    public function index($subjectId, $classSectionId, $term)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $activities = $classSection->subject->activities()->where('term', $term)->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedActivity = $activities->first();

        return view('teacher.activities', compact('classSection', 'activities', 'students', 'selectedActivity', 'term'));
    }

    public function show($subjectId, $classSectionId, $term, $activityId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $activities = $classSection->subject->activities()->where('term', $term)->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedActivity = $activities->where('id', $activityId)->first();

        if (!$selectedActivity) {
            return redirect()->route('activities.index', [
                'subject' => $subjectId,
                'classSection' => $classSectionId,
                'term' => $term
            ]);
        }

        return view('teacher.activities', compact('classSection', 'activities', 'students', 'selectedActivity', 'term'));
    }

    public function store(Request $request, $subjectId, $classSectionId, $term)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0.01|max:999.99',
            'due_date' => 'nullable|date|after_or_equal:today',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $activity = $classSection->subject->activities()->create([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'due_date' => $request->due_date,
            'description' => $request->description,
            'order' => $classSection->subject->activities()->where('term', $term)->count() + 1,
            'term' => $term,
        ]);

        return back()->with('success', 'Activity created successfully!');
    }

    public function saveScores(Request $request, $subjectId, $classSectionId, $term, $activityId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $activity = $classSection->subject->activities()->where('term', $term)->findOrFail($activityId);
        $students = $classSection->students;

        $validator = Validator::make($request->all(), [
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:' . $activity->max_score,
            'late_submissions' => 'array',
            'late_submissions.*' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        foreach ($students as $student) {
            $score = $request->scores[$student->id] ?? null;
            $isLate = $request->has('late_submissions') && 
                     isset($request->late_submissions[$student->id]) && 
                     $request->late_submissions[$student->id];

            // Find existing score for this activity and student
            $existingScore = ActivityScore::where('activity_id', $activity->id)
                ->where('student_id', $student->id)
                ->first();

            if ($existingScore) {
                // Update existing record
                $existingScore->update([
                    'term' => $term,
                    'score' => $score,
                    'is_late' => $isLate,
                    'submitted_at' => $score ? now() : null,
                ]);
            } else {
                // Create new record
                ActivityScore::create([
                    'activity_id' => $activity->id,
                    'student_id' => $student->id,
                    'term' => $term,
                    'score' => $score,
                    'is_late' => $isLate,
                    'submitted_at' => $score ? now() : null,
                ]);
            }
        }

        return back()->with('success', 'Scores saved successfully!');
    }

    public function update(Request $request, $subjectId, $classSectionId, $term, $activityId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0.01|max:999.99',
            'due_date' => 'nullable|date|after_or_equal:today',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $activity = $classSection->subject->activities()->where('term', $term)->findOrFail($activityId);

        $activity->update([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'due_date' => $request->due_date,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Activity updated successfully!');
    }

    public function destroy($subjectId, $classSectionId, $term, $activityId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $activity = $classSection->subject->activities()->where('term', $term)->findOrFail($activityId);
        $activity->delete();
        return redirect()->route('activities.index', [
            'subject' => $subjectId,
            'classSection' => $classSectionId,
            'term' => $term
        ])->with('success', 'Activity deleted successfully!');
    }
}
