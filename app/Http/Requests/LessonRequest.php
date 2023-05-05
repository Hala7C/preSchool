<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title'=>['required','string','unique:lessons,title'],
            'semester'=>['required','in:s1,s2,undefined'],
            'number'=>['nullable','numeric'],
            'subject_id'=>['required','exists:subject,id']
        ];
    }
}
