<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\Report;
use Illuminate\Console\Command;
use App\Models\StudentFees;
use App\Models\YearConfig;
use App\Models\Report as rr;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use App\Http\Controllers\API\StudentFeesController;
use App\Models\Exam;
use Illuminate\Support\Facades\Storage;
use App\Models\Subject;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mockery\Matcher\Subset;
use SebastianBergmann\CodeCoverage\Util\Percentage;
use App\Models\Employee;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManagerStatic as Image;
use Dompdf\Dompdf;

use Illuminate\Support\Facades\View;

class GenerateMonthlyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:mfees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cuurentYear = Carbon::now()->year;
        $cuurentmonth = Carbon::now()->month;
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
        $pdf->loadView('monthlyReport', ['data' => $data, 'paided' => $paided, 'full' => $full, 'unpaided' => $unpaided]);
        $fileName = 'without_chart-' . $cuurentmonth . '-' . $cuurentYear . '.pdf';
        $file = $pdf->download($fileName);
        $filePath = public_path('uploads/' . $fileName); // Get the full file path in the public folder

        $pdf->save($filePath);
        rr::create([
            'fileName' => $fileName,
            'url' => 'uploads/' . $fileName,
            'type' => 'monthly-fees'
        ]);
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
        $chartColors = ['#1B9CFC', '#FD7272'];
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
                    $font->file(public_path('fonts/arial.ttf'));
                    $font->size(14);
                    $font->color('#1B9CFC');
                    $font->align('center');
                    $font->valign('middle');
                });
            } else {
                $image->text($label . ' - ' . $unpaidPercentage . ' %', $x, $y, function ($font) {
                    $font->file(public_path('fonts/arial.ttf'));
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
}
