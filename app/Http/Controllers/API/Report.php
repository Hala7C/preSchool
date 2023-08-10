<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Subject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Matcher\Subset;
use SebastianBergmann\CodeCoverage\Util\Percentage;
use App\Models\Employee;
use App\Models\StudentFees;
use App\Models\YearConfig;
use App\Models\Report as rr;
use Illuminate\Support\Facades\File;

// use Barryvdh\DomPDF\Facade\Pdf;
use Intervention\Image\ImageManagerStatic as Image;
use Barryvdh\DomPDF\PDF;
use Dompdf\Dompdf;

use Illuminate\Support\Facades\View;

ini_set('max_execution_time', 180);

class Report extends Controller
{
    public function TeacherPerformance(Request $request)
    {
        $validaor = Validator::make($request->all(), [
            'subject_id' => ['required', 'exists:subject,id'],
            'term' => ['required', 'in:s1,s2'],
            'type' => ['required', 'in:first,second,final']
        ]);
        if ($validaor->fails()) {
            return response()->json($validaor->errors(), 400);
        }
        $subjectID = $request->subject_id;
        $term = $request->term;
        $type = $request->type;
        $exams = Exam::where('subject_id', $subjectID)->where('term', $term)->where('type', $type)->get();
        $classes = DB::table('class')
            ->join('teacher_class_subject', 'class.id', '=', 'teacher_class_subject.class_id')
            ->where('teacher_class_subject.subject_id', '=', $subjectID)
            ->where('teacher_class_subject.teacher_id', '=', $exams[0]->teacher_id)
            ->distinct()
            ->get(['class.*']);
        /**
         * get all marks for those exams
         */
        $marks = $exams->marks()->get();
        $data = collect();
        foreach ($classes as $class) {
            foreach ($marks as $mark) {
                $std = $mark->student()->get();
                $cid = $std->classs()->get();
                $markSum = 0;
                $count = 0;
                if ($class->id == $cid) {
                    $markSum += $mark;
                    $count++;
                }
                $persentage = $markSum / $count;
                $data->push([
                    'class_id' => $class->id,
                    'class_name' => $class->name,
                    'teacher_id' => $exams->teacher_id,
                    'teacher_name' => Employee::find($exams->teacher_id)->fullname,
                    'Percentage' => $persentage,
                    'subject_id' => $subjectID,
                    'subject_name' => Subject::find($subjectID)->name,
                    'term' => $term,
                    'type' => $type
                ]);
            }
        }

        return $data;
    }

    public function feesYearlyReport()
    {
        $cuurentYear = Carbon::now()->year;
        $years = YearConfig::all();
        $data_years = [];
        $data_study=[];
        $data_bus=[];
        foreach ($years as $year) {
            array_push($data_years,$year->year);
            array_push($data_study,$year->study_fees);
            array_push($data_bus,$year->bus_fees);
        }
        $imagePath = public_path('charts/year_study.png');
        $imagePath2 = public_path('charts/year_bus.png');
        $labels = $data_years;
        $values = $data_study;
        $this->generateBarChartImage($labels, $values, $imagePath);
        $this->generateBarChartImage($labels, $data_bus, $imagePath2);
        $pdf = app(PDF::class);
                $pdf->loadView('yearlyReport');
                $fileName='yearly_fees_report-'.$cuurentYear.'.pdf';
                $file= $pdf->download($fileName);
                $filePath = public_path('uploads/'.$fileName); // Get the full file path in the public folder

                $pdf->save($filePath);
                rr::create([
                    'fileName'=>$fileName,
                    'url'=>'uploads/'.$fileName,
                    'type'=>'yearly-fees'
                ]);
                             return $file;

    }
    //all year that have reports
    public function feesMonthlylyReport()
    {

        $cuurentYear = Carbon::now()->year;
        $cuurentmonth=Carbon::now()->month;
        $data = (new StudentFeesController)->allStudentInfo();
        $paided = StudentFees::sum('amount');
        $fees = YearConfig::where('year', '=', $cuurentYear)->get();
        $full = ($fees[0]->study_fees + $fees[0]->bus_fees) * count($data['data']);
        $unpaided = $full - $paided;
        $imagePath = public_path('charts/d.png');
        $labels = ['paided', 'unpaided'];
        $values = [$paided, $unpaided];
        $this->generateChartImage($labels, $values, $imagePath);
        $pdf = app(PDF::class);
                $pdf->loadView('monthlyReport',['data'=>$data,'paided'=>$paided,'full'=>$full,'unpaided'=>$unpaided]);
                $fileName='without_chart'.$cuurentmonth.'.pdf';
                $file= $pdf->download($fileName);
                $filePath = public_path('uploads/'.$fileName); // Get the full file path in the public folder

                $pdf->save($filePath);
                rr::create([
                    'fileName'=>$fileName,
                    'url'=>'uploads/'.$fileName,
                    'type'=>'monthly-fees'
                ]);
                             return $file;

    }
    public function generateChartImage($labels, $sizes, $imagePath)
    {
        $full = $sizes[0] + $sizes[1];
        $paidPercentage = sprintf("%.2f", $sizes[0] / $full * 100);
        $unpaidPercentage = sprintf("%.2f", 100 - $paidPercentage);
        // Create a new image canvas
        $image = Image::canvas(400, 400, '#ffffff');

        // Define chart dimensions and position
        $chartSize = 200;
        $chartX = ($image->width() - $chartSize) / 2;
        $chartY = ($image->height() - $chartSize) / 2;

        // Calculate total size
        $totalSize = array_sum($sizes);
        $chartColors = ['#55E6C1', '#FD7272'];
        // Draw chart slices
        $startAngle = 0;
        foreach ($sizes as $index => $size) {
            $endAngle = $startAngle + ($size / $totalSize) * 360;
            $color = $chartColors[$index % count($chartColors)];

            // Draw pie slice as a filled polygon
            $numSides = 100; // Number of sides to approximate the arc
            $points = [$chartX + $chartSize / 2, $chartY + $chartSize / 2];
            for ($i = 0; $i <= $numSides; $i++) {
                $angle = $startAngle + ($i / $numSides) * ($endAngle - $startAngle);
                $x = $chartX + $chartSize / 2 + cos(deg2rad($angle)) * $chartSize / 2;
                $y = $chartY + $chartSize / 2 + sin(deg2rad($angle)) * $chartSize / 2;
                $points[] = $x;
                $points[] = $y;
            }

            $image->polygon($points, function ($draw) use ($color) {
                $draw->background($color);
            });

            $startAngle = $endAngle;
        }

        // Add chart labels
        $labelRadius = $chartSize / 2 + 50;
        $labelAngle = 0;
        foreach ($labels as $index => $label) {
            $x = $chartX + $chartSize / 2 + cos(deg2rad($labelAngle + (360 / count($labels)) / 2)) * $labelRadius;
            $y = $chartY + $chartSize / 2 + sin(deg2rad($labelAngle + (360 / count($labels)) / 2)) * $labelRadius;
            if ($label == 'paided') {
                $image->text($label . ' - ' . $paidPercentage . ' %', $x, $y, function ($font) {
                    $font->file('fonts/arial.ttf');
                    $font->size(14);
                    $font->color('#55E6C1');
                    $font->align('center');
                    $font->valign('middle');
                });
            } else {
                $image->text($label . ' - ' . $unpaidPercentage . ' %', $x, $y, function ($font) {
                    $font->file('fonts/arial.ttf');
                    $font->size(14);
                    $font->color('#FD7272');
                    $font->align('center');
                    $font->valign('middle');
                });
            }

            $labelAngle += (360 / count($labels));
        }
        // Save the chart image
        $image->save($imagePath);
    }
    function generateBarChartImage($labels, $data, $imagePath)
{
// Create a new canvas with specified dimensions
$width = 800;
$height = 400;
$image = Image::canvas($width, $height);

// Set background color
$backgroundColor = '#FFFF';
$image->rectangle(0, 0, $width, $height, function ($draw) use ($backgroundColor) {
    $draw->background($backgroundColor);
});

// Calculate chart dimensions
$chartAreaWidth = 0.8 * $width;
$chartAreaHeight = 0.7 * $height;
$barWidth = $chartAreaWidth / count($labels);
$maxDataValue = max($data);
$scalingFactor = $chartAreaHeight / $maxDataValue;

// Draw chart bars
$barColor = '#337AB7';
foreach ($data as $index => $value) {
    $x = ($index + 0.1) * $barWidth;
    $y = $chartAreaHeight - ($value * $scalingFactor);
    $barHeight = $value * $scalingFactor;
    $image->rectangle($x, $y, $x + 0.8 * $barWidth, $chartAreaHeight, function ($draw) use ($barColor) {
        $draw->background($barColor);
    });
}

// Draw horizontal axis
$axisColor = '#333333';
$image->line(0, $chartAreaHeight, $chartAreaWidth, $chartAreaHeight, function ($draw) use ($axisColor) {
    $draw->color($axisColor);
});

// Draw vertical axis
$image->line($chartAreaWidth, 0, $chartAreaWidth, $chartAreaHeight, function ($draw) use ($axisColor) {
    $draw->color($axisColor);
});

// Draw labels and sizes on axes
$labelColor = '#333333';
$fontSize = 12;
$labelY = $chartAreaHeight + ($fontSize + 5);
$labelSpacing = $chartAreaWidth / count($labels);
foreach ($labels as $index => $label) {
    $x = ($index + 0.5) * $labelSpacing;
    $image->text($label, $x, $labelY, function ($font) use ($labelColor, $fontSize) {
        $font->file(public_path('fonts/arial.ttf'));
        $font->size($fontSize);
        $font->color($labelColor);
        $font->align('center');
        $font->valign('middle');
    });
}

$sizeInterval = $maxDataValue / 5;
$sizeY = $chartAreaHeight;
$sizeStep = $chartAreaHeight / 5;
for ($i = 0; $i <= 5; $i++) {
    $image->text($sizeInterval * $i, $chartAreaWidth + 5, $sizeY, function ($font) use ($labelColor, $fontSize) {
        $font->file(public_path('fonts/arial.ttf'));
        $font->size($fontSize);
        $font->color($labelColor);
        $font->align('left');
        $font->valign('middle');
    });
    $image->line($chartAreaWidth - 5, $sizeY, $chartAreaWidth, $sizeY, function ($draw) use ($axisColor) {
        $draw->color($axisColor);
    });
    $sizeY -= $sizeStep;
}

// Save the chart image
$image->save($imagePath);
}

    public function getMonthlyFeesReport(){
        $reports=rr::where('type','=','monthly-fees')->get();
        $data=collect();
        foreach($reports as $repo){
            if(!$data->contains('fileName','=',$repo->fileName)){
                $data->push($repo);
            }
        }
        return['data'=>$data,'status'=>210];
    }

    public function getYearlyFeesReport(){
        $reports=rr::where('type','=','yearly-fees')->get();
        $data=collect();
        foreach($reports as $repo){
            if(!$data->contains('fileName','=',$repo->fileName)){
                $data->push($repo);
            }
        }
        return['data'=>$data,'status'=>210];
    }

    public function getAllReport(){
        $reports=rr::all();
        $data=collect();
        foreach($reports as $repo){
            if(!$data->contains('fileName','=',$repo->fileName)){
                $data->push($repo);
            }
        }
        return['data'=>$data,'status'=>210];
    }
    ///last api but with all subjects togather

}
