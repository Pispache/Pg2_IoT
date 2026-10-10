<?php
namespace Tests\Feature;
use App\Models\Area;
use App\Models\Device;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ApiReadingTest extends TestCase
{
    use RefreshDatabase;
    public function test_device_can_send_reading_with_valid_token(): void
    {
        $w=Warehouse::create(['name'=>'Bodega','code'=>'B1','is_active'=>true]);
        $a=Area::create(['warehouse_id'=>$w->id,'name'=>'Área','code'=>'A1','min_temperature'=>15,'max_temperature'=>30,'min_humidity'=>30,'max_humidity'=>70,'is_active'=>true]);
        $token='secret-demo';
        Device::create(['area_id'=>$a->id,'name'=>'Sensor','serial'=>'S-001','token_hash'=>hash('sha256',$token),'token_last4'=>'demo','status'=>'active','reading_interval_minutes'=>60]);
        $this->postJson('/api/v1/readings',['serial'=>'S-001','temperature'=>25,'humidity'=>55,'battery'=>90],['X-Device-Token'=>$token])->assertCreated()->assertJson(['ok'=>true]);
        $this->assertDatabaseHas('readings',['temperature'=>25.00,'humidity'=>55.00]);
    }
    public function test_invalid_token_is_rejected(): void
    {
        $w=Warehouse::create(['name'=>'Bodega','code'=>'B1','is_active'=>true]);
        $a=Area::create(['warehouse_id'=>$w->id,'name'=>'Área','code'=>'A1','is_active'=>true]);
        Device::create(['area_id'=>$a->id,'name'=>'Sensor','serial'=>'S-001','token_hash'=>hash('sha256','correct'),'token_last4'=>'rect','status'=>'active','reading_interval_minutes'=>60]);
        $this->postJson('/api/v1/readings',['serial'=>'S-001','temperature'=>25,'humidity'=>55],['X-Device-Token'=>'wrong'])->assertUnauthorized();
    }
}
