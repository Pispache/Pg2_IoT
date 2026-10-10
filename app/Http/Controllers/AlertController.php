<?php
namespace App\Http\Controllers;
use App\Models\Alert; use App\Support\Audit; use Illuminate\Http\Request;
class AlertController extends Controller
{
    public function index(Request $r){$q=Alert::with(['device.area.warehouse','rule'])->latest('opened_at');if($r->filled('status'))$q->where('status',$r->status);if($r->filled('severity'))$q->where('severity',$r->severity);return view('alerts.index',['alerts'=>$q->paginate(50)->withQueryString()]);}
    public function acknowledge(Request $r,Alert $alert){if($alert->status==='resolved')return back()->with('warning','La alerta ya fue resuelta.');$alert->update(['status'=>'acknowledged','acknowledged_at'=>now(),'acknowledged_by'=>auth()->id(),'notes'=>$r->input('notes',$alert->notes)]);Audit::record('acknowledge','Alert',$alert->id,'Alerta reconocida');return back()->with('success','Alerta reconocida.');}
    public function resolve(Request $r,Alert $alert){$alert->update(['status'=>'resolved','resolved_at'=>now(),'resolved_by'=>auth()->id(),'notes'=>$r->input('notes',$alert->notes)]);Audit::record('resolve','Alert',$alert->id,'Alerta resuelta');return back()->with('success','Alerta resuelta.');}
}
