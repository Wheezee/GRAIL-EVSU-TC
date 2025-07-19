<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\GradingWeight;

class GradingWeightController extends Controller
{
    public function update(Request $request, $subjectId)
    {
        $user = auth()->user();
        $subject = $user->subjects()->findOrFail($subjectId);
        $validated = $request->validate([
            'activities' => 'required|integer|min:0|max:100',
            'quizzes' => 'required|integer|min:0|max:100',
            'exams' => 'required|integer|min:0|max:100',
            'recitation' => 'required|integer|min:0|max:100',
            'projects' => 'required|integer|min:0|max:100',
        ]);
        $total = $validated['activities'] + $validated['quizzes'] + $validated['exams'] + $validated['recitation'] + $validated['projects'];
        if ($total !== 100) {
            return back()->withErrors(['weights' => 'Total must be 100%.'])->withInput();
        }
        $gradingWeight = GradingWeight::updateOrCreate(
            ['subject_id' => $subject->id],
            $validated
        );
        return back()->with('success', 'Assessment weights updated!');
    }
} 