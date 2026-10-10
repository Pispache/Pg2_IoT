<?php
namespace App\Http\Controllers;
use App\Models\Warehouse; use App\Support\Audit; use Illuminate\Http\Request;
class WarehouseController extends Controller
{
    public function index(){return view('warehouses.index',['warehouses'=>Warehouse::withCount('areas')->orderBy('name')->get()]);}
    public function store(Request $r){$d=$r->validate(['name'=>'required|string|max:120','code'=>'required|string|max:50|unique:warehouses,code','address'=>'nullable|string|max:255','description'=>'nullable|string|max:2000','is_active'=>'nullable|boolean']);$d['is_active']=$r->boolean('is_active');$w=Warehouse::create($d);Audit::record('create','Warehouse',$w->id,'Bodega creada: '.$w->name);return back()->with('success','Bodega creada.');}
    public function update(Request $r,Warehouse $warehouse){$d=$r->validate(['name'=>'required|string|max:120','code'=>'required|string|max:50|unique:warehouses,code,'.$warehouse->id,'address'=>'nullable|string|max:255','description'=>'nullable|string|max:2000','is_active'=>'nullable|boolean']);$d['is_active']=$r->boolean('is_active');$warehouse->update($d);Audit::record('update','Warehouse',$warehouse->id,'Bodega actualizada: '.$warehouse->name);return back()->with('success','Bodega actualizada.');}
    public function destroy(Warehouse $warehouse){abort_if($warehouse->areas()->exists(),422,'No puede eliminar una bodega con áreas asociadas.');Audit::record('delete','Warehouse',$warehouse->id,'Bodega eliminada: '.$warehouse->name);$warehouse->delete();return back()->with('success','Bodega eliminada.');}
}
