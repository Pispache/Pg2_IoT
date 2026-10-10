<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreDeviceRequest; use App\Models\Area; use App\Models\Device; use App\Services\DeviceTokenService; use App\Support\Audit;
class DeviceController extends Controller
{
    public function index(){return view('devices.index',['devices'=>Device::with(['area.warehouse','latestReading'])->orderBy('name')->get(),'areas'=>Area::with('warehouse')->where('is_active',true)->orderBy('name')->get()]);}
    public function show(Device $device){$device->load(['area.warehouse','latestReading']);$readings=$device->readings()->latest('recorded_at')->paginate(50);$alerts=$device->alerts()->latest('opened_at')->limit(20)->get();return view('devices.show',compact('device','readings','alerts'));}
    public function store(StoreDeviceRequest $r,DeviceTokenService $tokens){$d=$r->validated();$device=Device::create($d);$plain=$tokens->issue($device);Audit::record('create','Device',$device->id,'Dispositivo creado: '.$device->serial);return back()->with('success','Dispositivo creado. Copie el token ahora; solo se muestra una vez.')->with('device_token',$plain)->with('device_serial',$device->serial);}
    public function update(StoreDeviceRequest $r,Device $device){$device->update($r->validated());Audit::record('update','Device',$device->id,'Dispositivo actualizado: '.$device->serial);return back()->with('success','Dispositivo actualizado.');}
    public function regenerateToken(Device $device,DeviceTokenService $tokens){$plain=$tokens->issue($device);Audit::record('token_regenerated','Device',$device->id,'Token regenerado para: '.$device->serial);return back()->with('success','Token regenerado. Copie el nuevo token ahora.')->with('device_token',$plain)->with('device_serial',$device->serial);}
    public function destroy(Device $device){Audit::record('delete','Device',$device->id,'Dispositivo eliminado: '.$device->serial);$device->delete();return redirect()->route('devices.index')->with('success','Dispositivo eliminado.');}
}
