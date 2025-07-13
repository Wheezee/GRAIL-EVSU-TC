<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityScore;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    public function index($subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $activities = $classSection->subject->activities()->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        
        $selectedActivity = $activities->first();

        return view('teacher.activities', compact('classSection', 'activities', 'students', 'selectedActivity'));
    }

    public function show($subjectId, $classSectionId, $activityId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $activities = $classSection->subject->activities()->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedActivity = $activities->where('id', $activityId)->firstOrFail();

        return view('teacher.activities', compact('classSection', 'activities', 'students', 'selectedActivity'));
    }

    public function store(Request $request, $subjectId, $classSectionId)
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
            'order' => $classSection->subject->activities()->count() + 1,
        ]);

        return back()->with('success', 'Activity created successfully!');
    }

    public function saveScores(Request $request, $subjectId, $classSectionId, $activityId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $activity = $classSection->subject->activities()->findOrFail($activityId);
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

            ActivityScore::updateOrCreate(
                [
                    'activity_id' => $activity->id,
                    'student_id' => $student->id,
                ],
                [
                    'score' => $score,
                    'is_late' => $isLate,
                    'submitted_at' => $score ? now() : null,
                ]
            );
        }

        return back()->with('success', 'Scores saved successfully!');
    }

    public function update(Request $request, $subjectId, $classSectionId, $activityId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $activity = $classSection->subject->activities()->findOrFail($activityId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0.01|max:999.99',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $activity->update([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'due_date' => $request->due_date,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Activity updated successfully!');
    }

    public function destroy($subjectId, $classSectionId, $activityId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $activity = Activity::findOrFail($activityId);
        $activity->delete();
        return redirect()->route('activities.index', [
            'subject' => $subjectId,
            'classSection' => $classSectionId
        ])->with('success', 'Activity deleted successfully!');
    }
}
