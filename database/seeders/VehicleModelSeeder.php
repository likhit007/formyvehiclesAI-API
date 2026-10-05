<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleModelSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modelsByBrand = [
            'Maruthi Suzuki' => [
                ['name' => 'Swift', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Swift VXI Hatchback', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Baleno', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Dzire', 'vehicle_type' => 'Car', 'vehicle_category' => 'Sedan'],
                ['name' => 'Brezza', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Ertiga', 'vehicle_type' => 'Car', 'vehicle_category' => 'MUV'],
                ['name' => 'Wagon R', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Grand Vitara', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Alto K10', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Fronx', 'vehicle_type' => 'Car', 'vehicle_category' => 'Crossover'],
            ],
            'Maruti Suzuki' => [
                ['name' => 'Swift', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Swift VXI Hatchback', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Baleno', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Dzire', 'vehicle_type' => 'Car', 'vehicle_category' => 'Sedan'],
                ['name' => 'Brezza', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Ertiga', 'vehicle_type' => 'Car', 'vehicle_category' => 'MUV'],
                ['name' => 'Wagon R', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Grand Vitara', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Alto K10', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Fronx', 'vehicle_type' => 'Car', 'vehicle_category' => 'Crossover'],
            ],
            'Hero Honda' => [
                ['name' => 'GLAMOUR 125 Fi', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Splendor Plus', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Passion Pro', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'CBZ Xtreme', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Hunk', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Karizma R', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Pleasure', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
            ],
            'Hero MotoCorp' => [
                ['name' => 'Splendor Plus', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'HF Deluxe', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Glamour XTEC', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Passion Plus', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Xpulse 200 4V', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Xtreme 160R', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Destini 125', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
            ],
            'Hyundai' => [
                ['name' => 'Creta', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Venue', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'i20', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Verna', 'vehicle_type' => 'Car', 'vehicle_category' => 'Sedan'],
                ['name' => 'Grand i10 Nios', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Exter', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Alcazar', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Tucson', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
            ],
            'Tata' => [
                ['name' => 'Nexon', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Punch', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Harrier', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Safari', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Tiago', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Tigor', 'vehicle_type' => 'Car', 'vehicle_category' => 'Sedan'],
                ['name' => 'Altroz', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Curvv', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
            ],
            'Toyota' => [
                ['name' => 'Innova Crysta', 'vehicle_type' => 'Car', 'vehicle_category' => 'MUV'],
                ['name' => 'Innova Hycross', 'vehicle_type' => 'Car', 'vehicle_category' => 'MUV'],
                ['name' => 'Fortuner', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Glanza', 'vehicle_type' => 'Car', 'vehicle_category' => 'Hatchback'],
                ['name' => 'Urban Cruiser Hyryder', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Hilux', 'vehicle_type' => 'Car', 'vehicle_category' => 'Pickup'],
                ['name' => 'Camry', 'vehicle_type' => 'Car', 'vehicle_category' => 'Sedan'],
            ],
            'Mahindra' => [
                ['name' => 'Thar', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Scorpio-N', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Scorpio Classic', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'XUV700', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'XUV 3XO', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Bolero', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Bolero Neo', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
            ],
            'Honda' => [
                ['name' => 'City', 'vehicle_type' => 'Car', 'vehicle_category' => 'Sedan'],
                ['name' => 'Amaze', 'vehicle_type' => 'Car', 'vehicle_category' => 'Sedan'],
                ['name' => 'Elevate', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Activa 6G', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'Activa 125', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'Shine 125', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'SP 125', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Dio', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'Unicorn', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'CB350', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
            ],
            'Kia' => [
                ['name' => 'Seltos', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Sonet', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Carens', 'vehicle_type' => 'Car', 'vehicle_category' => 'MUV'],
                ['name' => 'EV6', 'vehicle_type' => 'Car', 'vehicle_category' => 'Crossover'],
            ],
            'Volkswagen' => [
                ['name' => 'Virtus', 'vehicle_type' => 'Car', 'vehicle_category' => 'Sedan'],
                ['name' => 'Taigun', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
                ['name' => 'Tiguan', 'vehicle_type' => 'Car', 'vehicle_category' => 'SUV'],
            ],
            'Royal Enfield' => [
                ['name' => 'Classic 350', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Hunter 350', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Bullet 350', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Meteor 350', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Himalayan 450', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Interceptor 650', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Continental GT 650', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
            ],
            'Bajaj' => [
                ['name' => 'Pulsar 150', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Pulsar NS200', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Pulsar N160', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Platina 100', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Platina 110', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Avenger Cruise 220', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Dominar 400', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Chetak', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
            ],
            'TVS' => [
                ['name' => 'Apache RTR 160 4V', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Apache RTR 200 4V', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Jupiter 110', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'Jupiter 125', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'Raider 125', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Ntorq 125', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'iQube Electric', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'Ronin', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
            ],
            'Yamaha' => [
                ['name' => 'YZF R15 V4', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'MT-15 V2', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'FZ-S FI', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'RayZR 125 Fi-Hybrid', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'Aerox 155', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
            ],
            'Suzuki Motorcycle' => [
                ['name' => 'Access 125', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'Burgman Street 125', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'Avenis 125', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Scooter'],
                ['name' => 'Gixxer 150', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'Gixxer SF 250', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
                ['name' => 'V-Strom SX', 'vehicle_type' => 'Two Wheeler', 'vehicle_category' => 'Motorcycle'],
            ],
            'John Deere' => [
                ['name' => '5050 D', 'vehicle_type' => 'Tractor', 'vehicle_category' => 'Tractor'],
                ['name' => '5310 GearPro', 'vehicle_type' => 'Tractor', 'vehicle_category' => 'Tractor'],
            ],
            'Swaraj' => [
                ['name' => '744 FE', 'vehicle_type' => 'Tractor', 'vehicle_category' => 'Tractor'],
                ['name' => '855 FE', 'vehicle_type' => 'Tractor', 'vehicle_category' => 'Tractor'],
            ],
            'Sonalika' => [
                ['name' => 'DI 745 III', 'vehicle_type' => 'Tractor', 'vehicle_category' => 'Tractor'],
                ['name' => 'Tiger DI 50', 'vehicle_type' => 'Tractor', 'vehicle_category' => 'Tractor'],
            ],
            'Ashok Leyland' => [
                ['name' => 'Oyster', 'vehicle_type' => 'Bus', 'vehicle_category' => 'Bus'],
                ['name' => 'Dost Plus', 'vehicle_type' => 'Truck', 'vehicle_category' => 'Light Commercial'],
                ['name' => 'AVTR 4220', 'vehicle_type' => 'Truck', 'vehicle_category' => 'Heavy Commercial'],
            ],
            'Eicher' => [
                ['name' => '380 Super DI', 'vehicle_type' => 'Tractor', 'vehicle_category' => 'Tractor'],
                ['name' => 'Skyline Pro', 'vehicle_type' => 'Bus', 'vehicle_category' => 'Bus'],
                ['name' => 'Pro 2049', 'vehicle_type' => 'Truck', 'vehicle_category' => 'Light Commercial'],
            ],
            'BharatBenz' => [
                ['name' => '1624', 'vehicle_type' => 'Bus', 'vehicle_category' => 'Bus'],
                ['name' => '2823R', 'vehicle_type' => 'Truck', 'vehicle_category' => 'Heavy Commercial'],
            ],
        ];

        $vehicleTypes = VehicleType::all()->keyBy('slug');

        foreach ($modelsByBrand as $brandName => $models) {
            $brand = Brand::firstOrCreate(['name' => $brandName]);

            foreach ($models as $modelData) {
                $typeSlug = match (strtolower($modelData['vehicle_type'] ?? '')) {
                    'car' => 'car',
                    'tractor' => 'tractor',
                    'bus' => 'bus',
                    'truck' => 'truck',
                    'auto_rickshaw', 'auto rickshaw' => 'auto_rickshaw',
                    'two wheeler', 'two_wheeler' => ($modelData['vehicle_category'] ?? '') === 'Scooter' ? 'scooter' : 'bike',
                    'bike', 'motorcycle' => 'bike',
                    'scooter' => 'scooter',
                    default => null,
                };
                $vehicleTypeId = $typeSlug && isset($vehicleTypes[$typeSlug]) ? $vehicleTypes[$typeSlug]->id : null;

                VehicleModel::firstOrCreate(
                    [
                        'brand_id' => $brand->id,
                        'name' => $modelData['name'],
                    ],
                    [
                        'vehicle_type_id' => $vehicleTypeId,
                        'vehicle_type' => $modelData['vehicle_type'],
                        'vehicle_category' => $modelData['vehicle_category'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
