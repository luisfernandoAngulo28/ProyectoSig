<?php

use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder {
    public function run() {
        $role = \Solunes\Master\App\Role::where('name','admin')->first();
        if(!$role) {
            $role = \Solunes\Master\App\Role::create(['name'=>'admin', 'display_name'=>'Admin']);
        }
        $user = \App\User::firstOrNew(['email'=>'admin@taxisapp.com']);
        $user->name = 'Admin';
        $user->password = bcrypt('admin123');
        $user->role_id = $role->id;
        $user->save();
        echo "Admin user created: admin@taxisapp.com / admin123\n";
    }
}
