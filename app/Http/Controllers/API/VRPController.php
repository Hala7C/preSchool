<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class VRPController extends Controller
{
    //
    public function vrp()
    {
        $process = new Process(
            ["python", 'C:/Users/ASUS/AppData/Roaming/Python/Python311/site-packages/task.py'],
            null,
            ['SYSTEMROOT' => getenv("SYSTEMROOT"), 'PATH' => getenv("PATH")]
        );
        $process->run();

        // error handling
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        $data = $process->getOutput();
        // $output_data = exec('&C:\Users\ASUS\AppData\Local\Microsoft\WindowsApps\python.exe', 'c:\task.py');
        return ['data' => $data, 'status' => 210];
    }
}
