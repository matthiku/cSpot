<?php

# (C) 2016 Matthias Kuhs, Ireland

namespace App\Http\Requests;

use App\Http\Requests\Request;

use Illuminate\Support\Facades\Auth;


class StoreSongPartRequest extends Request
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
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'name'      => 'required|unique:song_parts,name',
                    'sequence'  => 'required|unique:song_parts,sequence',
                    'code'      => 'required|unique:song_parts,code',
                ];
            }
            case 'PATCH':
            case 'PUT':
            {
                return [                    
                    'name'      => 'required|unique:song_parts,name,'    . $this->route('song_part'),
                    'sequence'  => 'required|unique:song_parts,sequence,'. $this->route('song_part'),
                    'code'      => 'required|unique:song_parts,code,'    . $this->route('song_part'),
                ];
            }
            default:break;
        }  
    }
}
