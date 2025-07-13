<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index()
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $students = Student::orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(15);

        return view('teacher.students.index', compact('students'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:20|unique:students,student_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email',
            'middle_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $student = Student::create($validator->validated());

        return back()->with('success', 'Student created successfully!');
    }

    public function update(Request $request, Student $student)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:20|unique:students,student_id,' . $student->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:students,email,' . $student->id,
            'middle_name' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $student->update($validator->validated());

        return back()->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        // Check if student is enrolled in any classes
        if ($student->classSections()->count() > 0) {
            return back()->with('error', 'Cannot delete student who is enrolled in classes. Please unenroll them first.');
        }

        $student->delete();

        return back()->with('success', 'Student deleted successfully!');
    }

    public function enroll(Request $request, $subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $enrolledCount = 0;
        $alreadyEnrolledCount = 0;

        foreach ($request->student_ids as $studentId) {
            // Check if already enrolled
            if ($classSection->students()->where('students.id', $studentId)->exists()) {
                $alreadyEnrolledCount++;
                continue;
            }

            $classSection->students()->attach($studentId, [
                'enrollment_date' => now(),
                'status' => 'enrolled'
            ]);
            $enrolledCount++;
        }

        // Update student count
        $classSection->update([
            'student_count' => $classSection->students()->count()
        ]);

        $message = '';
        if ($enrolledCount > 0) {
            $message .= $enrolledCount . ' student' . ($enrolledCount > 1 ? 's' : '') . ' enrolled successfully!';
        }
        if ($alreadyEnrolledCount > 0) {
            $message .= ($enrolledCount > 0 ? ' ' : '') . $alreadyEnrolledCount . ' student' . ($alreadyEnrolledCount > 1 ? 's were' : ' was') . ' already enrolled.';
        }

        return back()->with('success', $message);
    }

    public function remove(Request $request, $subjectId, $classSectionId, $studentId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $classSection->students()->detach($studentId);

        // Update student count
        $classSection->update([
            'student_count' => $classSection->students()->count()
        ]);

        return back()->with('success', 'Student removed from class successfully!');
    }

    public function getAvailableStudents($subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }

        $classSection = ClassSection::where('id', $classSectionId)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        // Get all students not enrolled in this class section
        $enrolledStudentIds = $classSection->students()->pluck('students.id');
        
        $availableStudents = Student::whereNotIn('id', $enrolledStudentIds)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get(['id', 'student_id', 'first_name', 'last_name']);

        return response()->json($availableStudents);
    }
}
