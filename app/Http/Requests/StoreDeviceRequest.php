<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreDeviceRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id=$this->route('device')?->id;
        return [
            'area_id'=>['required','exists:areas,id'], 'name'=>['required','string','max:120'],
            'serial'=>['required','string','max:120','unique:devices,serial'.($id?','.$id:'')],
            'status'=>['required','in:active,inactive,maintenance'], 'reading_interval_minutes'=>['required','integer','min:1','max:1440'],
            'firmware_version'=>['nullable','string','max:50'], 'notes'=>['nullable','string','max:2000'],
        ];
    }
}
