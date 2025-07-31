<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gradebook - {{ $data['subject']->code }} - {{ $data['classSection']->section }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 10px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .student-name {
            text-align: left;
            font-weight: bold;
        }
        .grade-cell {
            font-weight: bold;
        }
        .section-header {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }
        .weights-section {
            margin-top: 30px;
            border: 1px solid #ddd;
            padding: 15px;
        }
        .weights-section h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .weight-item {
            margin: 5px 0;
        }
        .legend-section {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 15px;
        }
        .legend-section h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .legend-item {
            margin: 5px 0;
            display: inline-block;
            margin-right: 20px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Gradebook Report</h1>
        <p><strong>Subject:</strong> {{ $data['subject']->code }} - {{ $data['subject']->title }}</p>
        <p><strong>Section:</strong> {{ $data['classSection']->section }}</p>
        <p><strong>Teacher:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Generated:</strong> {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p><strong>Grading Mode:</strong> {{ ucfirst(str_replace('_', ' ', $data['gradingMode'])) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="student-name">Student Name</th>
                @if(isset($data['headers']) && is_array($data['headers']))
                    {{-- Frontend data format with enhanced headers --}}
                    @foreach($data['headers'] as $header)
                        @if($header['type'] === 'assessment')
                            <th>{{ $header['fullText'] ?? $header['text'] }}</th>
                        @endif
                    @endforeach
                    <th>Midterm Grade</th>
                    <th>Finals Grade</th>
                    <th>Overall Grade</th>
                @else
                    {{-- Database format (fallback) --}}
                    <th rowspan="3" class="student-name">Student Name</th>
                    @foreach(['Activities', 'Quizzes', 'Exams', 'Recitation', 'Projects'] as $label)
                        @php
                            $type = strtolower($label);
                            if ($type === 'recitation') $type = 'recitations';
                            $midtermCount = $data['assessments'][$type]['midterms']->count();
                            $finalsCount = $data['assessments'][$type]['finals']->count();
                            $colspan = max($midtermCount + $finalsCount, 1);
                        @endphp
                        <th colspan="{{ $colspan }}" class="section-header">{{ $label }}</th>
                    @endforeach
                    <th rowspan="3">Midterm Grade</th>
                    <th rowspan="3">Finals Grade</th>
                    <th rowspan="3">Overall Grade</th>
                @endif
            </tr>
            @if(!isset($data['headers']))
                {{-- Database format headers (continued) --}}
                <tr>
                    @foreach(['activities', 'quizzes', 'exams', 'recitations', 'projects'] as $type)
                        @php
                            $midtermCount = $data['assessments'][$type]['midterms']->count();
                            $finalsCount = $data['assessments'][$type]['finals']->count();
                        @endphp
                        @if ($midtermCount > 0)
                            <th colspan="{{ $midtermCount }}">Midterm</th>
                        @endif
                        @if ($finalsCount > 0)
                            <th colspan="{{ $finalsCount }}">Finals</th>
                        @endif
                        @if ($midtermCount === 0 && $finalsCount === 0)
                            <th>No Assessments</th>
                        @endif
                    @endforeach
                </tr>
                <tr>
                    @foreach(['activities', 'quizzes', 'exams', 'recitations', 'projects'] as $type)
                        @foreach ($data['assessments'][$type]['midterms'] as $item)
                            <th>{{ $item->name }}</th>
                        @endforeach
                        @foreach ($data['assessments'][$type]['finals'] as $item)
                            <th>{{ $item->name }}</th>
                        @endforeach
                        @if ($data['assessments'][$type]['midterms']->count() === 0 && $data['assessments'][$type]['finals']->count() === 0)
                            <th>--</th>
                        @endif
                    @endforeach
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($data['students'] as $student)
                <tr>
                    @if(isset($student->name))
                        {{-- Frontend data format --}}
                        <td class="student-name">{{ $student->name }}</td>
                        @if(isset($student->scores) && is_array($student->scores))
                            @foreach($student->scores as $score)
                                <td>{{ $score }}</td>
                            @endforeach
                        @endif
                    @else
                        {{-- Database format --}}
                        <td class="student-name">{{ $student->last_name }}, {{ $student->first_name }}</td>
                        
                        @foreach(['activities', 'quizzes', 'exams', 'recitations', 'projects'] as $type)
                            @foreach ($data['assessments'][$type]['midterms'] as $assessment)
                                @php
                                    $modelMap = [
                                        'activities' => 'ActivityScore',
                                        'quizzes' => 'QuizScore',
                                        'exams' => 'ExamScore',
                                        'recitations' => 'RecitationScore',
                                        'projects' => 'ProjectScore'
                                    ];
                                    $scoreModel = "App\\Models\\" . $modelMap[$type];
                                    $score = $scoreModel::where('student_id', $student->id)
                                        ->where($type . '_id', $assessment->id)
                                        ->where('term', 'midterms')
                                        ->first();
                                @endphp
                                <td>{{ $score ? $score->score : '' }}</td>
                            @endforeach
                            @foreach ($data['assessments'][$type]['finals'] as $assessment)
                                @php
                                    $modelMap = [
                                        'activities' => 'ActivityScore',
                                        'quizzes' => 'QuizScore',
                                        'exams' => 'ExamScore',
                                        'recitations' => 'RecitationScore',
                                        'projects' => 'ProjectScore'
                                    ];
                                    $scoreModel = "App\\Models\\" . $modelMap[$type];
                                    $score = $scoreModel::where('student_id', $student->id)
                                        ->where($type . '_id', $assessment->id)
                                        ->where('term', 'finals')
                                        ->first();
                                @endphp
                                <td>{{ $score ? $score->score : '' }}</td>
                            @endforeach
                            @if ($data['assessments'][$type]['midterms']->count() === 0 && $data['assessments'][$type]['finals']->count() === 0)
                                <td>--</td>
                            @endif
                        @endforeach
                        
                        <td class="grade-cell">{{ $student->midterms_grade_display ?: '--' }}</td>
                        <td class="grade-cell">{{ $student->finals_grade_display ?: '--' }}</td>
                        <td class="grade-cell">{{ $student->overall_grade_display ?: '--' }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($includeWeights && $data['gradingWeight'])
        <div class="weights-section">
            <h3>Grading Weights</h3>
            <div class="weight-item"><strong>Activities:</strong> {{ $data['gradingWeight']->activities }}%</div>
            <div class="weight-item"><strong>Quizzes:</strong> {{ $data['gradingWeight']->quizzes }}%</div>
            <div class="weight-item"><strong>Exams:</strong> {{ $data['gradingWeight']->exams }}%</div>
            <div class="weight-item"><strong>Recitation:</strong> {{ $data['gradingWeight']->recitation }}%</div>
            <div class="weight-item"><strong>Projects:</strong> {{ $data['gradingWeight']->projects }}%</div>
        </div>
    @endif

    @if ($includeLegend && $data['gradingMode'] !== 'percentage')
        <div class="legend-section">
            <h3>Grade Legend</h3>
            @if ($data['gradingMode'] === 'rule_based')
                <div class="legend-item"><strong>1.00:</strong> 97-100% (Excellent)</div>
                <div class="legend-item"><strong>1.25:</strong> 94-96% (Very Good)</div>
                <div class="legend-item"><strong>1.50:</strong> 91-93% (Good)</div>
                <div class="legend-item"><strong>1.75:</strong> 88-90% (Satisfactory)</div>
                <div class="legend-item"><strong>2.00:</strong> 85-87% (Fair)</div>
                <div class="legend-item"><strong>2.25:</strong> 82-84% (Fair)</div>
                <div class="legend-item"><strong>2.50:</strong> 79-81% (Fair)</div>
                <div class="legend-item"><strong>2.75:</strong> 76-78% (Passing)</div>
                <div class="legend-item"><strong>3.00:</strong> 75% (Lowest Passing)</div>
                <div class="legend-item"><strong>5.00:</strong> Below 75% (Failed)</div>
            @elseif ($data['gradingMode'] === 'computed')
                <div class="legend-item"><strong>1.0:</strong> 100% (Excellent)</div>
                <div class="legend-item"><strong>2.0:</strong> 80% (Good)</div>
                <div class="legend-item"><strong>3.0:</strong> 60% (Passing)</div>
                <div class="legend-item"><strong>5.0:</strong> Below 60% (Failed)</div>
            @endif
        </div>
    @endif
</body>
</html> 