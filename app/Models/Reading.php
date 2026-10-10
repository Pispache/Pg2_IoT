<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Reading extends Model
{
    public $timestamps=false;
    protected $fillable=['device_id','temperature','humidity','battery','wifi_signal','recorded_at','received_at','raw_payload'];
    protected function casts(): array { return ['temperature'=>'decimal:2','humidity'=>'decimal:2','battery'=>'decimal:2','wifi_signal'=>'integer','recorded_at'=>'datetime','received_at'=>'datetime','raw_payload'=>'array']; }
    public function device(): BelongsTo { return $this->belongsTo(Device::class); }
}
