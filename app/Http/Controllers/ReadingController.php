<?php
namespace App\Http\Controllers;
use App\Models\Device; use App\Models\Reading; use Illuminate\Http\Request;
class ReadingController extends Controller
{
    public function index(Request $r){$q=Reading::with('device.area.warehouse')->latest('recorded_at');if($r->filled('device_id'))$q->where('device_id',$r->integer('device_id'));if($r->filled('from'))$q->whereDate('recorded_at','>=',$r->date('from'));if($r->filled('to'))$q->whereDate('recorded_at','<=',$r->date('to'));return view('readings.index',['readings'=>$q->paginate(50)->withQueryString(),'devices'=>Device::orderBy('name')->get()]);}
}
