<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Requests;

use App\Http\Requests\Request;

use Illuminate\Support\Facades\Auth;


class StorePlanRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // validation rules
            'date'        => 'required',
            'type_id'     => 'numeric|required|exists:types,id',
            'leader_id'   => 'numeric|required|min:1',
            'teacher_id'  => 'numeric',
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'type_id.numeric' => 'You must select a valid Service Type',
            'leader_id.numeric' => 'You must select a leader',
            'teacher_id.numeric' => 'You must select a teacher or \'none\'',
        ];
    }    
}
