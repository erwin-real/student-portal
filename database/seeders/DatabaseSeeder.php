<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $member = Member::factory()->create([
            'rfid' => '0',
            'first_name' => 'Erwin',
            'last_name' => 'Capati',
            'gender' => 'male'
        ]);

        User::factory()->create([
            'name' => 'Dev User',
            'username' => 'devuser',
            'member_id' => $member->id,
            'password' => bcrypt(value: '123123123')
        ]);
    }
}
