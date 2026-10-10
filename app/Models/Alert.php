<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Alert extends Model
{
    protected $fillable=['device_id','alert_rule_id','reading_id','variable','value','message','severity','status','opened_at','acknowledged_at','acknowledged_by','resolved_at','resolved_by','notes'];
    protected function casts(): array { return ['value'=>'decimal:2','opened_at'=>'datetime','acknowledged_at'=>'datetime','resolved_at'=>'datetime']; }
    public function device(): BelongsTo { return $this->belongsTo(Device::class); }
    public function rule(): BelongsTo { return $this->belongsTo(AlertRule::class,'alert_rule_id'); }
    public function reading(): BelongsTo { return $this->belongsTo(Reading::class); }
    public function acknowledgedBy(): BelongsTo { return $this->belongsTo(User::class,'acknowledged_by'); }
    public function resolvedBy(): BelongsTo { return $this->belongsTo(User::class,'resolved_by'); }
}
