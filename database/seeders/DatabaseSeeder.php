<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\Level;
use App\Models\Member;
use App\Models\Section;
use App\Models\Student;
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
        $member = Member::factory()->create([
            'rfid' => '0',
            'first_name' => 'Erwin',
            'last_name' => 'Capati',
            'gender' => 'male'
        ]);

        User::factory()->create([
            'name' => 'Admin User',
            'username' => 'admin',
            'member_id' => $member->id,
            'password' => bcrypt(value: '123123123')
        ]);


        // Member::factory(27)->create();

        // Faculty::factory(count: 7)->create();

        // User::factory(7)->create([
        //     'password' => bcrypt(value: '123123123')
        // ]);

        // Level::factory(count: 8)->create();

        // Section::factory(count: 9)->create();

        // Student::factory(count: 20)->create();


    }
}
