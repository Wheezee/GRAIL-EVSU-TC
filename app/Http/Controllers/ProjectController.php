<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectScore;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ProjectController extends Controller
{
    public function index($subjectId, $classSectionId, $term)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $projects = $classSection->subject->projects()->where('term', $term)->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedProject = $projects->first();

        return view('teacher.projects', compact('classSection', 'projects', 'students', 'selectedProject', 'term'));
    }

    public function show($subjectId, $classSectionId, $term, $projectId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $projects = $classSection->subject->projects()->where('term', $term)->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedProject = $projects->where('id', $projectId)->first();

        if (!$selectedProject) {
            return redirect()->route('projects.index', [
                'subject' => $subjectId,
                'classSection' => $classSectionId,
                'term' => $term
            ]);
        }

        return view('teacher.projects', compact('classSection', 'projects', 'students', 'selectedProject', 'term'));
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
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $project = $classSection->subject->projects()->create([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'order' => $classSection->subject->projects()->where('term', $term)->count() + 1,
            'term' => $term,
        ]);

        return back()->with('success', 'Project created successfully!');
    }

    public function saveScores(Request $request, $subjectId, $classSectionId, $term, $projectId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $project = $classSection->subject->projects()->where('term', $term)->findOrFail($projectId);
        $students = $classSection->students;

        $validator = Validator::make($request->all(), [
            'scores' => 'required|array',
            'scores.*' => 'nullable|numeric|min:0|max:' . $project->max_score,
            'late_submissions' => 'nullable|array',
            'late_submissions.*' => 'boolean',
            'resubmission_counts' => 'nullable|array',
            'resubmission_counts.*' => 'integer|min:0|max:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        foreach ($students as $student) {
            $score = $request->scores[$student->id] ?? null;
            $isLate = $request->has('late_submissions') && 
                     isset($request->late_submissions[$student->id]) && 
                     $request->late_submissions[$student->id];
            $resubmissionCount = $request->resubmission_counts[$student->id] ?? 0;

            ProjectScore::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'student_id' => $student->id,
                    'term' => $term,
                ],
                [
                    'score' => $score,
                    'is_late' => $isLate,
                    'resubmission_count' => $resubmissionCount,
                    'submitted_at' => $score ? now() : null,
                ]
            );
        }

        return back()->with('success', 'Scores saved successfully!');
    }

    public function update(Request $request, $subjectId, $classSectionId, $term, $projectId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0.01|max:999.99',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $project = $classSection->subject->projects()->where('term', $term)->findOrFail($projectId);

        $project->update([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ]);

        return back()->with('success', 'Project updated successfully!');
    }

    public function destroy($subjectId, $classSectionId, $term, $projectId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $project = $classSection->subject->projects()->where('term', $term)->findOrFail($projectId);
        $project->delete();
        return redirect()->route('projects.index', [
            'subject' => $subjectId,
            'classSection' => $classSectionId,
            'term' => $term
        ])->with('success', 'Project deleted successfully!');
    }
}
