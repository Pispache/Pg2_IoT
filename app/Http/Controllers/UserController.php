<?php
namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\User;
use App\Support\Audit;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public function index(){ return view('users.index',['users'=>User::with('role')->orderBy('name')->paginate(20),'roles'=>Role::all()]); }
    public function store(Request $r){ $d=$r->validate(['name'=>'required|string|max:120','email'=>'required|email|max:190|unique:users,email','password'=>'required|string|min:8|confirmed','role_id'=>'required|exists:roles,id','is_active'=>'nullable|boolean']); $d['is_active']=$r->boolean('is_active'); $u=User::create($d); Audit::record('create','User',$u->id,'Usuario creado: '.$u->email); return back()->with('success','Usuario creado correctamente.'); }
    public function update(Request $r,User $user){ $d=$r->validate(['name'=>'required|string|max:120','email'=>'required|email|max:190|unique:users,email,'.$user->id,'password'=>'nullable|string|min:8|confirmed','role_id'=>'required|exists:roles,id','is_active'=>'nullable|boolean']); if(!$d['password']) unset($d['password']); $d['is_active']=$r->boolean('is_active'); $user->update($d); Audit::record('update','User',$user->id,'Usuario actualizado: '.$user->email); return back()->with('success','Usuario actualizado.'); }
    public function destroy(User $user){ abort_if(auth()->id()===$user->id,422,'No puede eliminar su propio usuario.'); Audit::record('delete','User',$user->id,'Usuario eliminado: '.$user->email); $user->delete(); return back()->with('success','Usuario eliminado.'); }
}
