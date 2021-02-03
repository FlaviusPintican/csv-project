<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    /**
     * @return string[][]
     */
    public function rules() : array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
            ],
        ];
    }
}
