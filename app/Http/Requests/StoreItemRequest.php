<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

use Illuminate\Support\Facades\Auth;


class StoreItemRequest extends Request
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
            'song_id' => 'integer|required_without:comment',
            'seq_no'  => 'required|numeric|min:0.1',
            'comment' => 'required_without:song_id',
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
            'song_id.required_without' => 'You must select a song or enter a comment',
            'comment.required_without' => 'You must enter a comment or select a song',
        ];
    }    


}
