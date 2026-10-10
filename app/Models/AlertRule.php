<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AlertRule extends Model
{
    protected $fillable=['device_id','area_id','name','variable','min_value','max_value','severity','is_active'];
    protected function casts(): array { return ['min_value'=>'decimal:2','max_value'=>'decimal:2','is_active'=>'boolean']; }
    public function device(): BelongsTo { return $this->belongsTo(Device::class); }
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }
}
