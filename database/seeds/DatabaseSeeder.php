<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(CompaniesSeeder::class);
        $this->call(EquipmentCategoriesSeeder::class);
        $this->call(EquipmentModelsSeeder::class);
        $this->call(StatusesSeeder::class);
        $this->call(EquipmentsSeeder::class);
        $this->call(TeamsSeeder::class);
        $this->call(FormsSeeder::class);
        $this->call(AreasSeeder::class);
        $this->call(DefaultFormsDataSeeder::class);
        $this->call(DefaultStatementsSeeder::class);
        $this->call(ProjectStatusSeeder::class);
        $this->call(PsychometricCalculationsSeeder::class);
        $this->call(TicketCategoriesTableSeeder::class);
        $this->call(DewCalculationsSeeder::class);
        $this->call(DefaultScopeSeeder::class);
    }
}
