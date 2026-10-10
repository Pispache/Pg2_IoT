<?php
namespace Tests\Feature;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class AuthTest extends TestCase
{
    use RefreshDatabase;
    public function test_editors_use_dialogs_with_labelled_fields(): void
    {
        $this->seed();
        $this->actingAs(User::where('email','admin@iot.test')->firstOrFail());
        foreach (['devices','alert-rules','users','areas','warehouses'] as $module) {
            $response=$this->get(route($module.'.index'))->assertOk();
            $response->assertSee('data-modal-open=',false)
                ->assertSee('<dialog',false)
                ->assertSee('aria-labelledby=',false)
                ->assertSee('name="_edit_modal"',false)
                ->assertSee('name="_method" value="PUT"',false)
                ->assertSee('Guardar cambios')
                ->assertSee('Cancelar')
                ->assertDontSee('<details',false);
            $document=new \DOMDocument();
            @$document->loadHTML($response->getContent());
            $xpath=new \DOMXPath($document);
            foreach ($xpath->query('//dialog//input[not(@type="hidden")]|//dialog//select|//dialog//textarea') as $field) {
                $id=$field->getAttribute('id');
                $this->assertNotSame('',$id);
                $this->assertSame(1,$xpath->query('//label[@for="'.$id.'"]')->length);
            }
        }
    }

    public function test_invalid_edit_reopens_only_its_modal_and_preserves_input(): void
    {
        $this->seed();
        $this->actingAs(User::where('email','admin@iot.test')->firstOrFail());
        $device=\App\Models\Device::firstOrFail();
        $id='edit-device-'.$device->id;
        $this->from(route('devices.index'))->put(route('devices.update',$device),[
            '_edit_modal'=>$id,'name'=>'Nombre corregido','serial'=>$device->serial,
            'area_id'=>$device->area_id,'status'=>'active','reading_interval_minutes'=>0,
        ])->assertSessionHasErrors('reading_interval_minutes')->assertRedirect(route('devices.index'));
        $response=$this->get(route('devices.index'))->assertOk();
        $response->assertSee('data-open-on-load',false)
            ->assertSee('value="Nombre corregido"',false)
            ->assertSee('aria-invalid="true"',false);
        $this->assertSame(1,substr_count($response->getContent(),'data-open-on-load'));
        $this->assertNotSame('Nombre corregido',$device->fresh()->name);
    }

    public function test_modal_markers_do_not_change_update_behavior(): void
    {
        $this->seed();
        $this->actingAs(User::where('email','admin@iot.test')->firstOrFail());
        $device=\App\Models\Device::firstOrFail();
        $this->from(route('devices.index'))->put(route('devices.update',$device),[
            '_edit_modal'=>'edit-device-'.$device->id,'name'=>'Sensor actualizado',
            'serial'=>$device->serial,'area_id'=>$device->area_id,'status'=>'maintenance',
            'reading_interval_minutes'=>30,'firmware_version'=>'1.0.0','notes'=>'Actualizado desde modal',
        ])->assertSessionHasNoErrors()->assertRedirect(route('devices.index'));
        $this->assertSame('Sensor actualizado',$device->fresh()->name);
        $this->get(route('devices.index'))->assertDontSee('data-open-on-load',false);
    }

    public function test_other_modal_forms_keep_their_update_fields(): void
    {
        $this->seed();
        $this->actingAs(User::where('email','admin@iot.test')->firstOrFail());
        $area=\App\Models\Area::firstOrFail();
        $warehouse=\App\Models\Warehouse::firstOrFail();
        $rule=\App\Models\AlertRule::firstOrFail();
        $user=User::where('email','auditor@iot.test')->firstOrFail();
        $passwordHash=$user->password;
        $cases=[
            ['areas','edit-area-',$area,['warehouse_id'=>$warehouse->id,'code'=>$area->code,'description'=>'Descripción actualizada','min_temperature'=>16,'max_temperature'=>29,'min_humidity'=>35,'max_humidity'=>65]],
            ['warehouses','edit-warehouse-',$warehouse,['code'=>$warehouse->code,'address'=>'Dirección actualizada','description'=>'Descripción actualizada']],
            ['alert-rules','edit-rule-',$rule,['device_id'=>$rule->device_id,'area_id'=>null,'variable'=>'temperature','min_value'=>16,'max_value'=>29,'severity'=>'low']],
            ['users','edit-user-',$user,['email'=>$user->email,'role_id'=>$user->role_id,'password'=>'','password_confirmation'=>'']],
        ];
        foreach ($cases as [$module,$prefix,$model,$fields]) {
            $fields+=['name'=>'Registro actualizado','is_active'=>0,'_edit_modal'=>$prefix.$model->id];
            $this->from(route($module.'.index'))->put(route($module.'.update',$model),$fields)
                ->assertRedirect(route($module.'.index'))->assertSessionHasNoErrors();
            $this->assertSame('Registro actualizado',$model->fresh()->name);
            $this->assertFalse($model->fresh()->is_active);
            $this->get(route($module.'.index'))->assertOk()->assertDontSee('data-open-on-load',false);
        }
        $this->assertSame($passwordHash,$user->fresh()->password);
    }

    public function test_user_modal_never_repopulates_passwords_after_validation_errors(): void
    {
        $this->seed();
        $this->actingAs(User::where('email','admin@iot.test')->firstOrFail());
        $user=User::where('email','auditor@iot.test')->firstOrFail();
        $this->from(route('users.index'))->put(route('users.update',$user),[
            '_edit_modal'=>'edit-user-'.$user->id,'name'=>$user->name,'email'=>$user->email,
            'role_id'=>$user->role_id,'is_active'=>0,'password'=>'RegressionPassword123',
            'password_confirmation'=>'DoesNotMatch123',
        ])->assertSessionHasErrors('password');
        $response=$this->get(route('users.index'))->assertOk();
        $response->assertSee('data-open-on-load',false)->assertDontSee('RegressionPassword123')->assertDontSee('DoesNotMatch123');
        $document=new \DOMDocument();
        @$document->loadHTML($response->getContent());
        $xpath=new \DOMXPath($document);
        $this->assertSame(0,$xpath->query('//input[@id="edit-user-'.$user->id.'-is_active"]/@checked')->length);
        $this->assertTrue($user->fresh()->is_active);
    }

    public function test_active_user_can_login(): void
    {
        $role=Role::create(['name'=>'admin','display_name'=>'Administrador']);
        $user=User::create(['role_id'=>$role->id,'name'=>'Admin','email'=>'admin@example.com','password'=>'secret123','is_active'=>true]);
        $this->post('/login',['email'=>$user->email,'password'=>'secret123'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }
}
