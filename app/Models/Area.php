<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Area extends Model
{
    protected $fillable=['warehouse_id','name','code','description','min_temperature','max_temperature','min_humidity','max_humidity','is_active'];
    protected function casts(): array { return ['min_temperature'=>'decimal:2','max_temperature'=>'decimal:2','min_humidity'=>'decimal:2','max_humidity'=>'decimal:2','is_active'=>'boolean']; }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
    public function devices(): HasMany { return $this->hasMany(Device::class); }
}
