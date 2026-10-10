<?php
namespace App\Http\Controllers;
use App\Models\Device; use App\Models\Reading; use Dompdf\Dompdf; use Dompdf\Options; use Illuminate\Http\Request;
class ReportController extends Controller
{
    private function query(Request $r){$q=Reading::with('device.area.warehouse')->orderBy('recorded_at');if($r->filled('device_id'))$q->where('device_id',$r->integer('device_id'));if($r->filled('from'))$q->whereDate('recorded_at','>=',$r->date('from'));if($r->filled('to'))$q->whereDate('recorded_at','<=',$r->date('to'));return $q;}
    public function index(Request $r){$rows=$this->query($r)->paginate(100)->withQueryString();return view('reports.index',['readings'=>$rows,'devices'=>Device::orderBy('name')->get()]);}
    public function csv(Request $r){$rows=$this->query($r)->get();$name='reporte_lecturas_'.now()->format('Ymd_His').'.csv';return response()->streamDownload(function() use($rows){$h=fopen('php://output','w');fputcsv($h,['Fecha','Bodega','Área','Dispositivo','Serial','Temperatura °C','Humedad %','Batería %','WiFi dBm']);foreach($rows as $x)fputcsv($h,[$x->recorded_at,$x->device->area->warehouse->name,$x->device->area->name,$x->device->name,$x->device->serial,$x->temperature,$x->humidity,$x->battery,$x->wifi_signal]);fclose($h);},$name,['Content-Type'=>'text/csv']);}
    public function pdf(Request $r){$rows=$this->query($r)->limit(1000)->get();$html=view('reports.pdf',compact('rows'))->render();$o=new Options();$o->set('isRemoteEnabled',false);$pdf=new Dompdf($o);$pdf->loadHtml($html,'UTF-8');$pdf->setPaper('A4','landscape');$pdf->render();return response($pdf->output(),200,['Content-Type'=>'application/pdf','Content-Disposition'=>'inline; filename="reporte_iot.pdf"']);}
}
