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
        return $template;
    }
    public function employeeTemplates(Request $request)
    {
        $employee_id = $request->get('employee_id');
        $employeeTemplates = Template::where('employee_id', $employee_id)->get();
        return $employeeTemplates;
    }
    public function store(Request $request)
    {
        $template = $request->file('template');
        $templateName = $template->getClientOriginalName();
        $exist = Template::where('name', '=', $templateName)->first();
        if (!$exist) {
            $template->move(public_path('uploads'), $templateName);
            $template =  Template::create([
                'name' => $templateName,
                'path' => 'uploads/' . $templateName,
                'employee_id' => Auth::id(),

            ]);
            return ['data' => $template, 'status' => '210'];
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
        $newtemplate = $request->file('template');
        $templateName = $newtemplate->getClientOriginalName();
        $updatedTemplate = Template::where('name', '=', $templateName)->first();
        $id = Auth::id();
        if ($updatedTemplate->employee_id == $id) {
            $old_template = $updatedTemplate->path;
            Storage::disk('public')->delete($old_template);
            $newtemplate->move(public_path('uploads'), $templateName);
            $updatedTemplate->update([
                'path' => 'uploads/' . $templateName,
            ]);
            return ['data' => $updatedTemplate, 'status' => '210'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Template  $template
     * @return \Illuminate\Http\Response
     */
    public function destroy(Template $template)
    {
        //
        $id = Auth::id();
        if ($template->employee_id == $id) {
            // $deletedTemplate = $template->path;
            Storage::disk('public')->delete($template->path);
            $template->delete();
            return ['data' => 'Template deleted successfuly :)'];
        }
        return ['data' => 'Delete this template is denied!'];
    }
}
