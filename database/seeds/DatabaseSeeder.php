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
        DB::table('admins')->insert([
            'name' => 'Miguel Frazão',
            'email' => 'miguel@cloudoki.pt',
            'password' => Hash::make('cloudoki_pass'),
            'permission_lvl' => 2,
            'updated' => time()
        ]);

		DB::table('users')->insert([
            'name' => 'Miguel User',
            'email' => 'miguel@user.pt',
            'password' => bcrypt('bcrypt'),
            'updated' => time(),
            'joined' => time(),
        ]);

        $json_data = json_decode(file_get_contents(public_path('customers.json')), true);
        foreach ($json_data as $key => $value) {
            DB::table('customers')->insert([
                'name' => $value['name'],
                'since' => strtotime($value['since']),
                'revenue' => $value['revenue'],
            ]);
        }

        $json_data = json_decode(file_get_contents(public_path('products.json')), true);
        foreach ($json_data as $key => $value) {
            DB::table('products')->insert([
                'product-id' => $value['id'],
                'description' => $value['description'],
                'id_category' => $value['category'],
                'price' => $value['price'],
            ]);
        }

        $categories = array('Tools', 'Switches');
        foreach ($categories as $c) {
            DB::table('categories')->insert([
                'name' => $c
            ]);
        }

    }
}
