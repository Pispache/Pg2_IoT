<?php
namespace App\Services;
use App\Models\Alert;
use App\Models\AlertRule;
use App\Models\Reading;
use Illuminate\Support\Collection;
class AlertService
{
    public function evaluate(Reading $reading): void
    {
        $device=$reading->device()->with('area')->first();
        $rules=AlertRule::query()->where('is_active',true)
            ->where(function($q) use ($device){
                $q->where('device_id',$device->id)
                  ->orWhere(function($q2) use ($device){ $q2->whereNull('device_id')->where('area_id',$device->area_id); });
            })->get();
        if ($rules->isEmpty()) $rules=$this->rulesFromAreaDefaults($device);
        foreach($rules as $rule) $this->evaluateRule($reading,$rule);
    }
    private function rulesFromAreaDefaults($device): Collection
    {
        $a=$device->area;
        if(!$a) return collect();
        $rows=[];
        if($a->min_temperature!==null || $a->max_temperature!==null) $rows[]=new AlertRule(['name'=>'Temperatura del área','variable'=>'temperature','min_value'=>$a->min_temperature,'max_value'=>$a->max_temperature,'severity'=>'high','is_active'=>true]);
        if($a->min_humidity!==null || $a->max_humidity!==null) $rows[]=new AlertRule(['name'=>'Humedad del área','variable'=>'humidity','min_value'=>$a->min_humidity,'max_value'=>$a->max_humidity,'severity'=>'medium','is_active'=>true]);
        return collect($rows);
    }
    private function evaluateRule(Reading $reading, AlertRule $rule): void
    {
        $value=$reading->{$rule->variable};
        if($value===null) return;
        $out=($rule->min_value!==null && $value < $rule->min_value) || ($rule->max_value!==null && $value > $rule->max_value);
        $query=Alert::where('device_id',$reading->device_id)->where('variable',$rule->variable)->whereIn('status',['open','acknowledged']);
        if($rule->exists) $query->where('alert_rule_id',$rule->id); else $query->whereNull('alert_rule_id');
        $open=$query->latest('opened_at')->first();
        if($out && !$open){
            $limits=[]; if($rule->min_value!==null) $limits[]='mín. '.$rule->min_value; if($rule->max_value!==null) $limits[]='máx. '.$rule->max_value;
            Alert::create(['device_id'=>$reading->device_id,'alert_rule_id'=>$rule->exists?$rule->id:null,'reading_id'=>$reading->id,'variable'=>$rule->variable,'value'=>$value,'message'=>"{$rule->name}: valor {$value} fuera de rango (".implode(', ',$limits).')','severity'=>$rule->severity,'status'=>'open','opened_at'=>now()]);
        } elseif(!$out && $open){
            $open->update(['status'=>'resolved','resolved_at'=>now(),'notes'=>trim(($open->notes? $open->notes."\n":'').'Resolución automática: el valor regresó al rango configurado.')]);
        }
    }
}
