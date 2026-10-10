<?php
namespace App\Services;
use App\Models\Device;
use Illuminate\Support\Str;
class DeviceTokenService
{
    public function issue(Device $device): string
    {
        $plain = 'iot_'.Str::random(48);
        $device->update(['token_hash'=>hash('sha256',$plain),'token_last4'=>substr($plain,-4)]);
        return $plain;
    }
    public function validate(Device $device, ?string $plain): bool
    {
        return $plain && $device->token_hash && hash_equals($device->token_hash, hash('sha256',$plain));
    }
}
