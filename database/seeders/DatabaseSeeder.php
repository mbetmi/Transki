<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(10)->create();
        $this->call(UserTableSeeder::class);
        $this->call(MessageTableSeeder::class);
        $this->call(HistoryTableSeeder::class);
        $this->call(FileTableSeeder::class);
        // $this->call(EmployeeTableSeeder::class);

    }
}
