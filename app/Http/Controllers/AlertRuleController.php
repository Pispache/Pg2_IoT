<?php
namespace App\Http\Controllers;
use App\Models\AlertRule; use App\Models\Area; use App\Models\Device; use App\Support\Audit; use Illuminate\Http\Request;
class AlertRuleController extends Controller
{
    public function index(){return view('alert_rules.index',['rules'=>AlertRule::with(['device','area'])->orderBy('name')->get(),'devices'=>Device::orderBy('name')->get(),'areas'=>Area::orderBy('name')->get()]);}
    public function store(Request $r){$d=$this->data($r);$d['is_active']=$r->boolean('is_active');$rule=AlertRule::create($d);Audit::record('create','AlertRule',$rule->id,'Regla creada: '.$rule->name);return back()->with('success','Regla de alerta creada.');}
    public function update(Request $r,AlertRule $alertRule){$d=$this->data($r);$d['is_active']=$r->boolean('is_active');$alertRule->update($d);Audit::record('update','AlertRule',$alertRule->id,'Regla actualizada: '.$alertRule->name);return back()->with('success','Regla actualizada.');}
    public function destroy(AlertRule $alertRule){Audit::record('delete','AlertRule',$alertRule->id,'Regla eliminada: '.$alertRule->name);$alertRule->delete();return back()->with('success','Regla eliminada.');}
    private function data(Request $r):array{return $r->validate(['name'=>'required|string|max:120','device_id'=>'nullable|exists:devices,id','area_id'=>'nullable|exists:areas,id','variable'=>'required|in:temperature,humidity,battery','min_value'=>'nullable|numeric','max_value'=>'nullable|numeric|gte:min_value','severity'=>'required|in:low,medium,high,critical','is_active'=>'nullable|boolean']);}
}
