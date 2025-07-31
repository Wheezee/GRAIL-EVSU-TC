<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassSection;
use App\Models\Subject;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GradebookExportController extends Controller
{
    public function export(Request $request, $subjectId, $classSectionId)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Access denied. Teachers only.');
        }
        
        $subject = auth()->user()->subjects()->findOrFail($subjectId);
        $classSection = ClassSection::where('id', $classSectionId)
            ->where('subject_id', $subject->id)
            ->where('teacher_id', auth()->id())
            ->firstOrFail();
        
        $format = $request->get('format', 'pdf');
        $gradingMode = $request->get('grading_mode', 'percentage');
        $includeWeights = $request->get('include_weights', false);
        $includeLegend = $request->get('include_legend', false);
        $tableData = $request->get('table_data');
        
        // Use table data from frontend if available, otherwise fall back to database queries
        if ($tableData) {
            $data = $this->processTableData(json_decode($tableData, true), $subject, $classSection, $gradingMode);
        } else {
            $data = $this->getGradebookData($subject, $classSection, $gradingMode);
        }
        
        $filename = "gradebook_{$subject->code}_{$classSection->section}_{$format}";
        
        if ($format === 'pdf') {
            return $this->exportPDF($data, $filename, $includeWeights, $includeLegend);
        } else {
            return $this->exportExcel($data, $filename, $includeWeights, $includeLegend);
        }
    }
    
    private function processTableData($tableData, $subject, $classSection, $gradingMode)
    {
        $gradingWeight = $subject->gradingWeight;
        
        // Convert frontend data to the expected format
        $data = [
            'subject' => $subject,
            'classSection' => $classSection,
            'gradingWeight' => $gradingWeight,
            'gradingMode' => $gradingMode,
            'headers' => $tableData['headers'] ?? [],
            'assessments' => [
                'activities' => ['midterms' => collect(), 'finals' => collect()],
                'quizzes' => ['midterms' => collect(), 'finals' => collect()],
                'exams' => ['midterms' => collect(), 'finals' => collect()],
                'recitations' => ['midterms' => collect(), 'finals' => collect()],
                'projects' => ['midterms' => collect(), 'finals' => collect()],
            ],
            'students' => collect(),
        ];
        
        // Process students from frontend data
        foreach ($tableData['students'] as $studentData) {
            $student = new \stdClass();
            $student->name = $studentData['name'];
            $student->scores = $studentData['scores'];
            
            // Extract grades from the scores array (last 3 values are midterm, finals, overall)
            $scoreCount = count($studentData['scores']);
            if ($scoreCount >= 3) {
                $student->midterms_grade = $studentData['scores'][$scoreCount - 3];
                $student->finals_grade = $studentData['scores'][$scoreCount - 2];
                $student->overall_grade = $studentData['scores'][$scoreCount - 1];
            }
            
            $data['students']->push($student);
        }
        
        return $data;
    }
    
    private function getGradebookData($subject, $classSection, $gradingMode)
    {
        $gradingWeight = $subject->gradingWeight;
        
        // Fetch all assessment items per type and term
        $assessments = [
            'activities' => [
                'midterms' => $subject->activities()->where('term', 'midterms')->orderBy('order')->get(),
                'finals' => $subject->activities()->where('term', 'finals')->orderBy('order')->get(),
            ],
            'quizzes' => [
                'midterms' => $subject->quizzes()->where('term', 'midterms')->orderBy('order')->get(),
                'finals' => $subject->quizzes()->where('term', 'finals')->orderBy('order')->get(),
            ],
            'exams' => [
                'midterms' => $subject->exams()->where('term', 'midterms')->orderBy('order')->get(),
                'finals' => $subject->exams()->where('term', 'finals')->orderBy('order')->get(),
            ],
            'recitations' => [
                'midterms' => $subject->recitations()->where('term', 'midterms')->orderBy('order')->get(),
                'finals' => $subject->recitations()->where('term', 'finals')->orderBy('order')->get(),
            ],
            'projects' => [
                'midterms' => $subject->projects()->where('term', 'midterms')->orderBy('order')->get(),
                'finals' => $subject->projects()->where('term', 'finals')->orderBy('order')->get(),
            ],
        ];
        
        // Fetch students
        $students = $classSection->students()->orderBy('last_name')->orderBy('first_name')->get();
        
        // Calculate grades for each student
        foreach ($students as $student) {
            foreach (['midterms', 'finals'] as $t) {
                // Activities
                $activityScores = \App\Models\ActivityScore::where('student_id', $student->id)
                    ->where('term', $t)
                    ->whereHas('activity', function($q) use ($subject) { $q->where('subject_id', $subject->id); })
                    ->get();
                $activityAvg = $activityScores->count() > 0 ?
                    ($activityScores->sum('score') / $activityScores->sum(function($s){ return $s->activity->max_score; }) * 100) : null;
                
                // Quizzes
                $quizScores = \App\Models\QuizScore::where('student_id', $student->id)
                    ->where('term', $t)
                    ->whereHas('quiz', function($q) use ($subject) { $q->where('subject_id', $subject->id); })
                    ->get();
                $quizAvg = $quizScores->count() > 0 ?
                    ($quizScores->sum('score') / $quizScores->sum(function($s){ return $s->quiz->max_score; }) * 100) : null;
                
                // Exams
                $examScores = \App\Models\ExamScore::where('student_id', $student->id)
                    ->where('term', $t)
                    ->whereHas('exam', function($q) use ($subject) { $q->where('subject_id', $subject->id); })
                    ->get();
                $examAvg = $examScores->count() > 0 ?
                    ($examScores->sum('score') / $examScores->sum(function($s){ return $s->exam->max_score; }) * 100) : null;
                
                // Recitation
                $recitationScores = \App\Models\RecitationScore::where('student_id', $student->id)
                    ->where('term', $t)
                    ->whereHas('recitation', function($q) use ($subject) { $q->where('subject_id', $subject->id); })
                    ->get();
                $recitationAvg = $recitationScores->count() > 0 ?
                    ($recitationScores->sum('score') / $recitationScores->sum(function($s){ return $s->recitation->max_score; }) * 100) : null;
                
                // Projects
                $projectScores = \App\Models\ProjectScore::where('student_id', $student->id)
                    ->where('term', $t)
                    ->whereHas('project', function($q) use ($subject) { $q->where('subject_id', $subject->id); })
                    ->get();
                $projectAvg = $projectScores->count() > 0 ?
                    ($projectScores->sum('score') / $projectScores->sum(function($s){ return $s->project->max_score; }) * 100) : null;
                
                // Save to student object
                $student->{$t.'_activity'} = $activityAvg !== null ? round($activityAvg,1) : null;
                $student->{$t.'_quiz'} = $quizAvg !== null ? round($quizAvg,1) : null;
                $student->{$t.'_exam'} = $examAvg !== null ? round($examAvg,1) : null;
                $student->{$t.'_recitation'} = $recitationAvg !== null ? round($recitationAvg,1) : null;
                $student->{$t.'_project'} = $projectAvg !== null ? round($projectAvg,1) : null;
                
                // Calculate term grade using weights
                if ($gradingWeight) {
                    $grades = [];
                    $weights = [
                        'activities' => $gradingWeight->activities / 100,
                        'quizzes' => $gradingWeight->quizzes / 100,
                        'exams' => $gradingWeight->exams / 100,
                        'recitation' => $gradingWeight->recitation / 100,
                        'projects' => $gradingWeight->projects / 100,
                    ];
                    
                    if ($assessments['activities'][$t]->count() > 0 && $activityScores->count() > 0) {
                        $grades['activities'] = $activityScores->map(function($score) {
                            return ($score->score / $score->activity->max_score) * 100;
                        })->toArray();
                    }
                    if ($assessments['quizzes'][$t]->count() > 0 && $quizScores->count() > 0) {
                        $grades['quizzes'] = $quizScores->map(function($score) {
                            return ($score->score / $score->quiz->max_score) * 100;
                        })->toArray();
                    }
                    if ($assessments['exams'][$t]->count() > 0 && $examScores->count() > 0) {
                        $grades['exams'] = $examScores->map(function($score) {
                            return ($score->score / $score->exam->max_score) * 100;
                        })->toArray();
                    }
                    if ($assessments['recitations'][$t]->count() > 0 && $recitationScores->count() > 0) {
                        $grades['recitation'] = $recitationScores->map(function($score) {
                            return ($score->score / $score->recitation->max_score) * 100;
                        })->toArray();
                    }
                    if ($assessments['projects'][$t]->count() > 0 && $projectScores->count() > 0) {
                        $grades['projects'] = $projectScores->map(function($score) {
                            return ($score->score / $score->project->max_score) * 100;
                        })->toArray();
                    }
                    
                    $computedGrade = $this->computeGrade($grades, $weights);
                    $student->{$t.'_grade'} = $computedGrade;
                } else {
                    $student->{$t.'_grade'} = null;
                }
            }
            
            // Overall grade
            if ($student->midterms_grade !== null && $student->finals_grade !== null && 
                $student->midterms_grade !== 'INC' && $student->finals_grade !== 'INC') {
                $student->overall_grade = round((($student->midterms_grade + $student->finals_grade)/2),1);
            } else {
                $student->overall_grade = null;
            }
            
            // Convert grades for display
            $student->midterms_grade_display = $this->convertGrade($student->midterms_grade, $gradingMode);
            $student->finals_grade_display = $this->convertGrade($student->finals_grade, $gradingMode);
            $student->overall_grade_display = $this->convertGrade($student->overall_grade, $gradingMode);
        }
        
        return [
            'subject' => $subject,
            'classSection' => $classSection,
            'students' => $students,
            'assessments' => $assessments,
            'gradingWeight' => $gradingWeight,
            'gradingMode' => $gradingMode
        ];
    }
    
    private function computeGrade($grades, $weights)
    {
        if (empty($grades)) {
            return 'INC';
        }
        
        $totalWeight = 0;
        $weightedSum = 0;
        
        foreach ($grades as $category => $categoryGrades) {
            if (!empty($categoryGrades) && isset($weights[$category])) {
                $categoryAvg = array_sum($categoryGrades) / count($categoryGrades);
                $weightedSum += $categoryAvg * $weights[$category];
                $totalWeight += $weights[$category];
            }
        }
        
        if ($totalWeight == 0) {
            return 'INC';
        }
        
        return round($weightedSum / $totalWeight, 1);
    }
    
    private function convertGrade($percentage, $mode)
    {
        if ($mode === 'percentage') {
            return $percentage . '%';
        }
        
        if ($mode === 'computed') {
            if ($percentage >= 100) return '1.0';
            if ($percentage >= 60) {
                $grade = 3.0 - (($percentage - 60) / 40) * 2.0;
                return round($grade, 2);
            }
            return '5.0';
        }
        
        if ($mode === 'rule_based') {
            if ($percentage >= 97) return '1.00';
            if ($percentage >= 94) return '1.25';
            if ($percentage >= 91) return '1.50';
            if ($percentage >= 88) return '1.75';
            if ($percentage >= 85) return '2.00';
            if ($percentage >= 82) return '2.25';
            if ($percentage >= 79) return '2.50';
            if ($percentage >= 76) return '2.75';
            if ($percentage >= 75) return '3.00';
            return '5.00';
        }
        
        return $percentage;
    }
    
    private function exportPDF($data, $filename, $includeWeights, $includeLegend)
    {
        $pdf = Pdf::loadView('exports.gradebook-pdf', [
            'data' => $data,
            'includeWeights' => $includeWeights,
            'includeLegend' => $includeLegend
        ]);
        
        // Set landscape orientation
        $pdf->setPaper('a4', 'landscape');
        
        return $pdf->download($filename . '.pdf');
    }
    
    private function exportExcel($data, $filename, $includeWeights, $includeLegend)
    {
        return Excel::download(new GradebookExport($data, $includeWeights, $includeLegend), $filename . '.xlsx');
    }
}

class GradebookExport implements FromArray, WithHeadings, WithStyles
{
    protected $data;
    protected $includeWeights;
    protected $includeLegend;
    
    public function __construct($data, $includeWeights, $includeLegend)
    {
        $this->data = $data;
        $this->includeWeights = $includeWeights;
        $this->includeLegend = $includeLegend;
    }
    
    public function array(): array
    {
        $rows = [];
        
        // Student data only (headers are handled by headings() method)
        foreach ($this->data['students'] as $student) {
            // Handle both frontend data format and database format
            if (isset($student->name)) {
                // Frontend data format
                $row = [$student->name];
                
                // Add scores from the frontend data
                if (isset($student->scores) && is_array($student->scores)) {
                    foreach ($student->scores as $score) {
                        $row[] = $score;
                    }
                }
            } else {
                // Database format
                $row = [$student->last_name . ', ' . $student->first_name];
                
                // Add assessment scores
                foreach (['activities', 'quizzes', 'exams', 'recitations', 'projects'] as $type) {
                    foreach (['midterms', 'finals'] as $term) {
                        if ($this->data['assessments'][$type][$term]->count() > 0) {
                            foreach ($this->data['assessments'][$type][$term] as $assessment) {
                                // Get the score using the same method as the working gradebook route
                                $score = $this->getStudentScore($student, $type, $assessment->id, $term);
                                $row[] = $score;
                            }
                        }
                    }
                }
                
                // Add grades
                $row[] = $this->formatGrade($student->midterms_grade);
                $row[] = $this->formatGrade($student->finals_grade);
                $row[] = $this->formatGrade($student->overall_grade);
            }
            
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    private function getStudentScore($student, $type, $assessmentId, $term)
    {
        // Use the same approach as the working gradebook route
        $modelMap = [
            'activities' => 'ActivityScore',
            'quizzes' => 'QuizScore',
            'exams' => 'ExamScore',
            'recitations' => 'RecitationScore',
            'projects' => 'ProjectScore'
        ];
        
        $scoreModel = "App\\Models\\" . $modelMap[$type];
        
        // Use the same query pattern as the working gradebook route
        $score = $scoreModel::where('student_id', $student->id)
            ->where($type . '_id', $assessmentId)
            ->where('term', $term)
            ->first();
        
        return $score ? $score->score : '';
    }
    
    private function getStudentScoreFromData($student, $type, $assessmentId, $term)
    {
        // Use the same approach as the working gradebook route
        $modelMap = [
            'activities' => 'ActivityScore',
            'quizzes' => 'QuizScore',
            'exams' => 'ExamScore',
            'recitations' => 'RecitationScore',
            'projects' => 'ProjectScore'
        ];
        
        $scoreModel = "App\\Models\\" . $modelMap[$type];
        
        // Use the same query pattern as the working gradebook route
        $score = $scoreModel::where('student_id', $student->id)
            ->where($type . '_id', $assessmentId)
            ->where('term', $term)
            ->first();
        
        return $score ? $score->score : '';
    }
    
    private function formatGrade($grade)
    {
        if ($grade === null || $grade === 'INC') {
            return $grade;
        }
        
        return $this->convertGrade($grade, $this->data['gradingMode']);
    }
    
    private function convertGrade($percentage, $mode)
    {
        if ($mode === 'percentage') {
            return $percentage . '%';
        }
        
        if ($mode === 'computed') {
            if ($percentage >= 100) return '1.0';
            if ($percentage >= 60) {
                $grade = 3.0 - (($percentage - 60) / 40) * 2.0;
                return round($grade, 2);
            }
            return '5.0';
        }
        
        if ($mode === 'rule_based') {
            if ($percentage >= 97) return '1.00';
            if ($percentage >= 94) return '1.25';
            if ($percentage >= 91) return '1.50';
            if ($percentage >= 88) return '1.75';
            if ($percentage >= 85) return '2.00';
            if ($percentage >= 82) return '2.25';
            if ($percentage >= 79) return '2.50';
            if ($percentage >= 76) return '2.75';
            if ($percentage >= 75) return '3.00';
            return '5.00';
        }
        
        return $percentage;
    }
    
    public function headings(): array
    {
        // Check if we have frontend data with headers
        if (isset($this->data['headers']) && is_array($this->data['headers'])) {
            $headers = ['Student'];
            
            // Add assessment headers from frontend data
            foreach ($this->data['headers'] as $header) {
                if ($header['type'] === 'assessment') {
                    $headers[] = $header['fullText'] ?? $header['text'];
                }
            }
            
            // Add grade headers
            $headers[] = 'Midterm Grade';
            $headers[] = 'Finals Grade';
            $headers[] = 'Overall Grade';
            
            return $headers;
        }
        
        // Fallback to default headers for database format
        $headers = ['Student'];
        
        // Add assessment headers for database format
        foreach (['activities', 'quizzes', 'exams', 'recitations', 'projects'] as $type) {
            $label = ucfirst($type);
            if ($type === 'recitations') $label = 'Recitation';
            
            foreach (['midterms', 'finals'] as $term) {
                $termLabel = ucfirst($term);
                if ($this->data['assessments'][$type][$term]->count() > 0) {
                    foreach ($this->data['assessments'][$type][$term] as $assessment) {
                        $headers[] = "{$label} {$termLabel} - {$assessment->name}";
                    }
                }
            }
        }
        
        $headers[] = 'Midterm Grade';
        $headers[] = 'Finals Grade';
        $headers[] = 'Overall Grade';
        
        return $headers;
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
