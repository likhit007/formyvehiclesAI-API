<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\VehicleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleTypes = VehicleType::all()->keyBy('slug');

        $brands = [
            ['name' => 'Maruthi Suzuki', 'vehicle_type' => 'Car', 'types' => ['car']],
            ['name' => 'Maruti Suzuki', 'vehicle_type' => 'Car', 'types' => ['car']],
            ['name' => 'Hyundai', 'vehicle_type' => 'Car', 'types' => ['car']],
            ['name' => 'Tata', 'vehicle_type' => 'Car', 'types' => ['car', 'bus', 'truck']],
            ['name' => 'Mahindra', 'vehicle_type' => 'Car', 'types' => ['car', 'tractor', 'truck']],
            ['name' => 'Toyota', 'vehicle_type' => 'Car', 'types' => ['car']],
            ['name' => 'Honda', 'vehicle_type' => null, 'types' => ['car', 'bike', 'scooter']],
            ['name' => 'Kia', 'vehicle_type' => 'Car', 'types' => ['car']],
            ['name' => 'Volkswagen', 'vehicle_type' => 'Car', 'types' => ['car']],
            ['name' => 'Skoda', 'vehicle_type' => 'Car', 'types' => ['car']],
            ['name' => 'Renault', 'vehicle_type' => 'Car', 'types' => ['car']],
            ['name' => 'Nissan', 'vehicle_type' => 'Car', 'types' => ['car']],
            ['name' => 'MG', 'vehicle_type' => 'Car', 'types' => ['car']],
            ['name' => 'Hero Honda', 'vehicle_type' => 'Two Wheeler', 'types' => ['bike', 'scooter']],
            ['name' => 'Hero MotoCorp', 'vehicle_type' => 'Two Wheeler', 'types' => ['bike', 'scooter']],
            ['name' => 'Bajaj', 'vehicle_type' => 'Two Wheeler', 'types' => ['bike', 'scooter', 'auto_rickshaw']],
            ['name' => 'TVS', 'vehicle_type' => 'Two Wheeler', 'types' => ['bike', 'scooter', 'auto_rickshaw']],
            ['name' => 'Royal Enfield', 'vehicle_type' => 'Two Wheeler', 'types' => ['bike']],
            ['name' => 'Yamaha', 'vehicle_type' => 'Two Wheeler', 'types' => ['bike', 'scooter']],
            ['name' => 'Suzuki Motorcycle', 'vehicle_type' => 'Two Wheeler', 'types' => ['bike', 'scooter']],
            ['name' => 'Ather', 'vehicle_type' => 'Two Wheeler', 'types' => ['scooter']],
            ['name' => 'Ola Electric', 'vehicle_type' => 'Two Wheeler', 'types' => ['scooter']],
            ['name' => 'John Deere', 'vehicle_type' => 'Tractor', 'types' => ['tractor']],
            ['name' => 'Swaraj', 'vehicle_type' => 'Tractor', 'types' => ['tractor']],
            ['name' => 'Sonalika', 'vehicle_type' => 'Tractor', 'types' => ['tractor']],
            ['name' => 'Massey Ferguson', 'vehicle_type' => 'Tractor', 'types' => ['tractor']],
            ['name' => 'Ashok Leyland', 'vehicle_type' => 'Commercial', 'types' => ['bus', 'truck']],
            ['name' => 'Eicher', 'vehicle_type' => 'Commercial', 'types' => ['tractor', 'bus', 'truck']],
            ['name' => 'BharatBenz', 'vehicle_type' => 'Commercial', 'types' => ['bus', 'truck']],
            ['name' => 'Volvo', 'vehicle_type' => 'Commercial', 'types' => ['bus', 'truck']],
        ];

        foreach ($brands as $data) {
            $brand = Brand::firstOrCreate(
                ['name' => $data['name']],
                [
                    'vehicle_type' => $data['vehicle_type'],
                    'is_active' => true,
                ]
            );

            if (! empty($data['types'])) {
                $typeIds = [];
                foreach ($data['types'] as $slug) {
                    if (isset($vehicleTypes[$slug])) {
                        $typeIds[] = $vehicleTypes[$slug]->id;
                    }
                }
                if (! empty($typeIds)) {
                    $brand->vehicleTypes()->syncWithoutDetaching($typeIds);
                }
            }
        }
    }
}
