<?php

namespace Tests\Feature;

use App\Enums\EquipmentStatus;
use App\Enums\EquipmentType;
use App\Models\Equipment;
use App\Models\MaintenanceLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipmentAndMaintenanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_equipment_and_maintenance_log_creation()
    {
        $equipment = Equipment::create([
            'name' => 'F-150 Truck',
            'type' => EquipmentType::Vehicle,
            'status' => EquipmentStatus::Active,
            'purchase_date' => '2023-01-01',
        ]);

        $this->assertEquals(EquipmentType::Vehicle, $equipment->type);
        $this->assertEquals('F-150 Truck', $equipment->name);

        $log = MaintenanceLog::create([
            'equipment_id' => $equipment->id,
            'service_date' => '2023-06-01',
            'cost' => 150.00,
            'description' => 'Oil Change',
        ]);

        $this->assertCount(1, $equipment->maintenanceLogs);
        $this->assertEquals(150.00, $equipment->maintenanceLogs->first()->cost);
    }
}
