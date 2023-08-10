<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\YearConfig;
use Carbon\Carbon;
use App\Models\Report as rr;
use Intervention\Image\ImageManagerStatic as Image;
use Barryvdh\DomPDF\PDF;

class GenerateYearlyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:yfees';

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
}
