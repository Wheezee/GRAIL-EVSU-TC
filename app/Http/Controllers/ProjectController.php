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
    public function index($subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $projects = $classSection->subject->projects()->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        
        $selectedProject = $projects->first();

        return view('teacher.projects', compact('classSection', 'projects', 'students', 'selectedProject'));
    }

    public function show($subjectId, $classSectionId, $projectId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $projects = $classSection->subject->projects()->with('scores')->get();
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        $selectedProject = $projects->where('id', $projectId)->first();
        if (!$selectedProject) {
            // If the project is missing, redirect to the projects index for this class section with a message
            return redirect()->route('projects.index', ['subject' => $subjectId, 'classSection' => $classSectionId])
                ->with('error', 'The selected project could not be found.');
        }

        return view('teacher.projects', compact('classSection', 'projects', 'students', 'selectedProject'));
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
            'order' => $classSection->subject->projects()->count() + 1,
        ]);

        return back()->with('success', 'Project created successfully!');
    }

    public function saveScores(Request $request, $subjectId, $classSectionId, $projectId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $project = $classSection->subject->projects()->findOrFail($projectId);
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
            $isLate = isset($request->late_submissions[$student->id]) && $request->late_submissions[$student->id];
            $resubmissionCount = $request->resubmission_counts[$student->id] ?? 0;

            $existingScore = ProjectScore::where('project_id', $project->id)
                ->where('student_id', $student->id)
                ->first();

            $data = [
                'score' => $score,
                'is_late' => $isLate,
                'resubmission_count' => $resubmissionCount,
            ];

            // Set submission dates
            if ($score) {
                if (!$existingScore || !$existingScore->submitted_at) {
                    $data['submitted_at'] = now();
                }
                
                // Set resubmission date if resubmission count increased
                if ($existingScore && $resubmissionCount > $existingScore->resubmission_count) {
                    $data['resubmission_date'] = now();
                }
            }

            ProjectScore::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'student_id' => $student->id,
                ],
                $data
            );
        }

        return back()->with('success', 'Scores saved successfully!');
    }

    public function update(Request $request, $subjectId, $classSectionId, $projectId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $project = $classSection->subject->projects()->findOrFail($projectId);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'max_score' => 'required|numeric|min:0.01|max:999.99',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $project->update([
            'name' => $request->name,
            'max_score' => $request->max_score,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ]);

        return back()->with('success', 'Project updated successfully!');
    }

    public function destroy($subjectId, $classSectionId, $projectId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $project = Project::findOrFail($projectId);
        $project->delete();
        return redirect()->route('projects.index', [
            'subject' => $subjectId,
            'classSection' => $classSectionId
        ])->with('success', 'Project deleted successfully!');
    }
}
