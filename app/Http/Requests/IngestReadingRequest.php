<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class IngestReadingRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'serial'=>['required','string','max:120'],
            'temperature'=>['required','numeric','between:-40,80'],
            'humidity'=>['required','numeric','between:0,100'],
            'battery'=>['nullable','numeric','between:0,100'],
            'wifi_signal'=>['nullable','integer','between:-120,0'],
            'recorded_at'=>['nullable','date'],
            'firmware_version'=>['nullable','string','max:50'],
            'api_token'=>['nullable','string','max:200'],
        ];
    }
}
