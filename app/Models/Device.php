<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
class Device extends Model
{
    protected $fillable=['area_id','uuid','name','serial','token_hash','token_last4','status','reading_interval_minutes','firmware_version','last_seen_at','notes'];
    protected $hidden=['token_hash'];
    protected function casts(): array { return ['last_seen_at'=>'datetime','reading_interval_minutes'=>'integer']; }
    protected static function booted(): void { static::creating(function (Device $device) { $device->uuid ??= (string) Str::uuid(); }); }
    public function area(): BelongsTo { return $this->belongsTo(Area::class); }
    public function readings(): HasMany { return $this->hasMany(Reading::class); }
    public function latestReading(): HasOne { return $this->hasOne(Reading::class)->latestOfMany('recorded_at'); }
    public function alertRules(): HasMany { return $this->hasMany(AlertRule::class); }
    public function alerts(): HasMany { return $this->hasMany(Alert::class); }
    public function isOnline(): bool
    {
        if (!$this->last_seen_at || $this->status !== 'active') return false;
        return $this->last_seen_at->gt(now()->subMinutes(max(5, $this->reading_interval_minutes * 2)));
    }
}
