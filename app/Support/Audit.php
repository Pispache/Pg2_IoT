<?php
namespace App\Support;
use App\Models\AuditLog;
class Audit
{
    public static function record(string $action, string $entityType, ?int $entityId, string $description): void
    {
        AuditLog::create([
            'user_id'=>auth()->id(), 'action'=>$action, 'entity_type'=>$entityType, 'entity_id'=>$entityId,
            'description'=>$description, 'ip_address'=>request()?->ip(), 'created_at'=>now(),
        ]);
    }
}
