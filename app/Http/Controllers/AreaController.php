<?php
namespace App\Http\Controllers;
use App\Models\Area; use App\Models\Warehouse; use App\Support\Audit; use Illuminate\Http\Request;
class AreaController extends Controller
{
    public function index(){return view('areas.index',['areas'=>Area::with('warehouse')->withCount('devices')->orderBy('name')->get(),'warehouses'=>Warehouse::where('is_active',true)->orderBy('name')->get()]);}
    public function store(Request $r){$d=$this->validateData($r);$d['is_active']=$r->boolean('is_active');$a=Area::create($d);Audit::record('create','Area',$a->id,'Área creada: '.$a->name);return back()->with('success','Área creada.');}
    public function update(Request $r,Area $area){$d=$this->validateData($r,$area->id);$d['is_active']=$r->boolean('is_active');$area->update($d);Audit::record('update','Area',$area->id,'Área actualizada: '.$area->name);return back()->with('success','Área actualizada.');}
    public function destroy(Area $area){abort_if($area->devices()->exists(),422,'No puede eliminar un área con dispositivos asociados.');Audit::record('delete','Area',$area->id,'Área eliminada: '.$area->name);$area->delete();return back()->with('success','Área eliminada.');}
    private function validateData(Request $r,?int $id=null): array{return $r->validate(['warehouse_id'=>'required|exists:warehouses,id','name'=>'required|string|max:120','code'=>'required|string|max:50|unique:areas,code'.($id?','.$id:''),'description'=>'nullable|string|max:2000','min_temperature'=>'nullable|numeric|between:-40,80','max_temperature'=>'nullable|numeric|between:-40,80|gte:min_temperature','min_humidity'=>'nullable|numeric|between:0,100','max_humidity'=>'nullable|numeric|between:0,100|gte:min_humidity','is_active'=>'nullable|boolean']);}
}
