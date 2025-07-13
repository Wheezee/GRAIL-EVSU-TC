<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassSection;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class ClassSectionController extends Controller
{
    // Store a new class section
    public function store(Request $request, $subjectId)
    {
        $user = Auth::user();
        if (!$user->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }
        $subject = $user->subjects()->findOrFail($subjectId);
        $validated = $request->validate([
            'section' => 'required|string|max:50',
            'schedule' => 'required|string|max:100',
            'classroom' => 'nullable|string|max:100',
        ]);
        $validated['subject_id'] = $subject->id;
        $validated['teacher_id'] = $user->id;
        $validated['student_count'] = 0; // Will be calculated automatically when students are implemented
        ClassSection::create($validated);
        return redirect()->route('subjects.classes', $subject->id)->with('success', 'Class added successfully!');
    }

    // Update an existing class section
    public function update(Request $request, $subjectId, $classSectionId)
    {
        $user = Auth::user();
        if (!$user->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }
        $subject = $user->subjects()->findOrFail($subjectId);
        $classSection = ClassSection::where('id', $classSectionId)
            ->where('subject_id', $subject->id)
            ->where('teacher_id', $user->id)
            ->firstOrFail();
        $validated = $request->validate([
            'section' => 'required|string|max:50',
            'schedule' => 'required|string|max:100',
            'classroom' => 'nullable|string|max:100',
        ]);
        $classSection->update($validated);
        return redirect()->route('subjects.classes', $subject->id)->with('success', 'Class updated successfully!');
    }

    // Delete a class section
    public function destroy($subjectId, $classSectionId)
    {
        $user = Auth::user();
        if (!$user->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }
        $subject = $user->subjects()->findOrFail($subjectId);
        $classSection = ClassSection::where('id', $classSectionId)
            ->where('subject_id', $subject->id)
            ->where('teacher_id', $user->id)
            ->firstOrFail();
        $classSection->delete();
        return redirect()->route('subjects.classes', $subject->id)->with('success', 'Class deleted successfully!');
    }
}
