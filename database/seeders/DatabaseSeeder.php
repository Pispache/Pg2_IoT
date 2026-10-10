<?php
namespace Database\Seeders;
use App\Models\AlertRule;
use App\Models\Area;
use App\Models\Device;
use App\Models\Reading;
use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin=Role::firstOrCreate(['name'=>'admin'],['display_name'=>'Administrador','description'=>'Control total del sistema']);
        $supervisor=Role::firstOrCreate(['name'=>'supervisor'],['display_name'=>'Supervisor','description'=>'Gestiona dispositivos, reglas y alertas']);
        $auditor=Role::firstOrCreate(['name'=>'auditor'],['display_name'=>'Auditor / Consulta','description'=>'Consulta lecturas, alertas y reportes']);

        User::updateOrCreate(['email'=>'admin@iot.test'],['name'=>'Administrador IoT','role_id'=>$admin->id,'password'=>Hash::make('Admin123!'),'is_active'=>true]);
        User::updateOrCreate(['email'=>'supervisor@iot.test'],['name'=>'Supervisor Bodega','role_id'=>$supervisor->id,'password'=>Hash::make('Supervisor123!'),'is_active'=>true]);
        User::updateOrCreate(['email'=>'auditor@iot.test'],['name'=>'Auditor de Calidad','role_id'=>$auditor->id,'password'=>Hash::make('Auditor123!'),'is_active'=>true]);

        $warehouse=Warehouse::firstOrCreate(['code'=>'BOD-001'],['name'=>'Bodega Principal de Medicamentos','address'=>'Guatemala','description'=>'Bodega utilizada para la demostración del sistema IoT.','is_active'=>true]);
        $area=Area::firstOrCreate(['code'=>'AREA-AMB-01'],['warehouse_id'=>$warehouse->id,'name'=>'Almacenamiento General','description'=>'Área de almacenamiento a temperatura ambiente.','min_temperature'=>15,'max_temperature'=>30,'min_humidity'=>30,'max_humidity'=>70,'is_active'=>true]);

        $plain='IOT-DEMO-001-SECRET-2026';
        $device=Device::updateOrCreate(['serial'=>'IOT-DEMO-001'],['area_id'=>$area->id,'name'=>'Sensor Demo 01','token_hash'=>hash('sha256',$plain),'token_last4'=>substr($plain,-4),'status'=>'active','reading_interval_minutes'=>60,'firmware_version'=>'1.0.0','last_seen_at'=>now()->subMinutes(20),'notes'=>'Token demo: IOT-DEMO-001-SECRET-2026']);

        AlertRule::firstOrCreate(['device_id'=>$device->id,'variable'=>'temperature'],['name'=>'Temperatura segura','min_value'=>15,'max_value'=>30,'severity'=>'high','is_active'=>true]);
        AlertRule::firstOrCreate(['device_id'=>$device->id,'variable'=>'humidity'],['name'=>'Humedad segura','min_value'=>30,'max_value'=>70,'severity'=>'medium','is_active'=>true]);
        AlertRule::firstOrCreate(['device_id'=>$device->id,'variable'=>'battery'],['name'=>'Batería baja','min_value'=>20,'max_value'=>null,'severity'=>'medium','is_active'=>true]);

        if(Reading::where('device_id',$device->id)->count()===0){
            for($i=24;$i>=0;$i--){
                Reading::create(['device_id'=>$device->id,'temperature'=>round(23.5+sin($i/3)*2,2),'humidity'=>round(55+cos($i/4)*6,2),'battery'=>round(92-$i*.4,2),'wifi_signal'=>-58-rand(0,8),'recorded_at'=>now()->subHours($i),'received_at'=>now()->subHours($i),'raw_payload'=>['seed'=>true]]);
            }
        }

        $this->command?->info('Usuarios demo creados:');
        $this->command?->line('admin@iot.test / Admin123!');
        $this->command?->line('supervisor@iot.test / Supervisor123!');
        $this->command?->line('auditor@iot.test / Auditor123!');
        $this->command?->line('Dispositivo demo: IOT-DEMO-001 / token IOT-DEMO-001-SECRET-2026');
    }
}
