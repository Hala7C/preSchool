<?php



namespace App\Http\Controllers\API;

use App\Models\Template;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $template = Template::all();
        return ['data' => $template, 'status' => '210'];
    }
    public function teacherTemplates()
    {
        $template = Template::where("status", "=", "available")->get();
        return ['data' => $template, 'status' => '210'];
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'path' => ['required', 'string', 'unique:templates,path'],
            'name' => ['required', 'string', 'unique:templates,name'],
            'status' => ['in:available,unavailable']
        ], [
            'required' => 'The field (:attribute) is required ',
            'unique' => 'The field (:attribute) must be unique ',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $name = $request->name;
        $path = $request->path;
        $status = $request->status;
        $template =  Template::create([
            'name' => $name,
            'path' => $path,
            'manager_id' => Auth::id(),
            'status' => $status,

        ]);
        return ['data' => $template, 'status' => '210'];
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, $id)
    {
        //

        //
        //$status = $request->status;
        Template::where('id', $id)->update(['status' => $request->status]);
        $template = Template::findOrFail($id);
        return $template;
    }



    public function destroy($id)
    {
        //
        $template = Template::findOrFail($id);
        if ($template) {
            return ['data' => 'Template deleted successfuly :)'];
        }
        return ['data' => 'this template is not found'];
    }
}
