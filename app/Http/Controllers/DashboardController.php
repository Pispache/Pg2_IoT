<?php
namespace App\Http\Controllers;
use App\Models\Alert;
use App\Models\Device;
use App\Models\Reading;
use App\Models\Warehouse;
class DashboardController extends Controller
{
    public function index()
    {
        $devices=Device::with(['area.warehouse','latestReading'])->orderBy('name')->get();
        $stats=['warehouses'=>Warehouse::where('is_active',true)->count(),'devices'=>Device::count(),'online'=>$devices->filter->isOnline()->count(),'open_alerts'=>Alert::whereIn('status',['open','acknowledged'])->count()];
        $recentAlerts=Alert::with('device')->latest('opened_at')->limit(8)->get();
        $chartDevice=$devices->first();
        $chartReadings=$chartDevice ? Reading::where('device_id',$chartDevice->id)->where('recorded_at','>=',now()->subDay())->orderBy('recorded_at')->get() : collect();
        return view('dashboard',compact('devices','stats','recentAlerts','chartDevice','chartReadings'));
    }
}
