<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffMembers = [
            [
                'name' => 'Raj Kumar',
                'email' => 'raj.kumar@company.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'Priya Sharma',
                'email' => 'priya.sharma@company.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'Amit Patel',
                'email' => 'amit.patel@company.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'Sneha Singh',
                'email' => 'sneha.singh@company.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'Rahul Verma',
                'email' => 'rahul.verma@company.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'Anjali Gupta',
                'email' => 'anjali.gupta@company.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'Vikram Reddy',
                'email' => 'vikram.reddy@company.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'Pooja Rao',
                'email' => 'pooja.rao@company.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'Arjun Nair',
                'email' => 'arjun.nair@company.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
            [
                'name' => 'Neha Iyer',
                'email' => 'neha.iyer@company.com',
                'password' => Hash::make('password123'),
                'role' => 'staff',
            ],
        ];

        foreach ($staffMembers as $staff) {
            $user = User::firstOrCreate(
                ['email' => $staff['email']],
                $staff
            );
            if (!$user->hasRole('staff')) {
                $user->assignRole('staff');
            }
        }
    }
}
