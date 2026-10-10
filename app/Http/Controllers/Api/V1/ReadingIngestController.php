<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller; use App\Http\Requests\IngestReadingRequest; use App\Models\Device; use App\Models\Reading; use App\Services\AlertService; use App\Services\DeviceTokenService; use Illuminate\Http\Request;
class ReadingIngestController extends Controller
{
    public function store(IngestReadingRequest $r,DeviceTokenService $tokens,AlertService $alerts)
    {
        $device=Device::where('serial',$r->string('serial'))->first();
        if(!$device || $device->status!=='active') return response()->json(['ok'=>false,'message'=>'Dispositivo no registrado o inactivo.'],404);
        $token=$r->header('X-Device-Token') ?: $r->input('api_token');
        if(!$tokens->validate($device,$token)) return response()->json(['ok'=>false,'message'=>'Token de dispositivo inválido.'],401);
        $reading=Reading::create(['device_id'=>$device->id,'temperature'=>$r->input('temperature'),'humidity'=>$r->input('humidity'),'battery'=>$r->input('battery'),'wifi_signal'=>$r->input('wifi_signal'),'recorded_at'=>$r->input('recorded_at') ?: now(),'received_at'=>now(),'raw_payload'=>$r->except('api_token')]);
        $device->update(['last_seen_at'=>now(),'firmware_version'=>$r->input('firmware_version',$device->firmware_version)]);
        $reading->load('device'); $alerts->evaluate($reading);
        return response()->json(['ok'=>true,'message'=>'Lectura registrada correctamente.','reading_id'=>$reading->id,'device'=>$device->serial,'server_time'=>now()->toIso8601String()],201);
    }
    public function config(Request $r,DeviceTokenService $tokens)
    {
        $data=$r->validate(['serial'=>['required','string','max:120'],'api_token'=>['nullable','string','max:200']]);
        $device=Device::with('area')->where('serial',$data['serial'])->first();
        if(!$device || !$tokens->validate($device,$r->header('X-Device-Token') ?: $r->input('api_token'))) return response()->json(['ok'=>false,'message'=>'No autorizado.'],401);
        return response()->json(['ok'=>true,'serial'=>$device->serial,'interval_minutes'=>$device->reading_interval_minutes,'limits'=>['temperature'=>['min'=>$device->area?->min_temperature,'max'=>$device->area?->max_temperature],'humidity'=>['min'=>$device->area?->min_humidity,'max'=>$device->area?->max_humidity]]]);
    }
}
