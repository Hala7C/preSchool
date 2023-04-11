<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use App\Models\Bus;
use App\Models\School;
use App\Models\Student;
use Symfony\Component\Process\Exception\ProcessFailedException;
class VRPPython extends Controller
{

public function testPythonScript()
{
    $busNumber=Bus::all()->count();
    $buses=Bus::all();
    $capacities=array();
    foreach($buses as $bus){
        $sp=$bus->capacity;
        array_push($capacities,$sp);
    }

    $students=Student::all()->whereNotNull('lat');
    $school=School::all()->first();
    if($school==null){
        return response()->json('please add school location first', 400);
    }
    $locations=array();
    array_push($locations,[$school->lng,$school->lat]);

    foreach($students as $std){
        $loc=array();
        array_push($loc,$std->lng);
        array_push($loc,$std->lat);
        array_push($locations,$loc);
    }
//lng,lat
    $json=array(
        'bus_number'=>$busNumber,
        'vehicle_capacities'=>$capacities,
        'locations' =>$locations
    );
    $res=json_encode($json);
    $path="C:/Users/loloo/AppData/Roaming/Python/Python310/site-packages/";
    $process =new Process(["python",$path."test.py",$res],null,
    ['SYSTEMROOT' => getenv('SYSTEMROOT'), 'PATH' => getenv("PATH")]);
    $process->run();
    if (!$process->isSuccessful()){
        throw new ProcessFailedException($process);
    }

    $res=json_decode($process->getOutput(), true);
    $busList=$res['buses'];
    foreach($busList as $b){
        $cap= $b[0];
        $bus=Bus::where('capacity','=',$cap)->get();
        foreach($b[1] as $std){
            if($std==0){
                continue;
            }
            $student_id=$students[$std]->id;
            $student=Student::findOrFail($student_id);
            $student->bus_id=$bus[0]->id;
            $student->save();

        }
    }
    return "Students distrubited successfully";
}
}
