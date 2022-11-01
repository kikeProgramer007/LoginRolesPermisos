<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;//necesario
use Spatie\Permission\Models\Permission;//necesario
use Illuminate\Support\Facades\Hash;//necesario
use App\Models\User;//necesario

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        //Registrando 3 roles en la tabla Role
        $role1 = Role::create(['name' => 'cliente','descripcion' => 'comprar productos']);
        $role2 = Role::create(['name' => 'repartidor','descripcion' => 'entregar productos']);
        $role3 = Role::create(['name' => 'administrador','descripcion' => 'administrar sistema']);

        //Crea un permisos(opcion1)
        $permisos = Permission::create(['name'=> 'p.crear']);
        //syncRoles : Asinga el permiso a un rol(opcion1)
        $permisos->syncRoles([$role1,$role2]);

        //Crea un permisos y Asinga el permiso a un rol(opcion2)
        Permission::create(['name'=> 'p.eliminar'])->syncRoles([$role1,$role2]);

        //Crear un Usuario
        $user1=User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'apellidos'=>'carvajal',
            'edad'=>'21',
            'direccion'=>'montero',
            'telefono'=>'71619345',
            'password' => Hash::make('admin'),
        ]);
        //Asignar un rol a usuario
        $user1->assignRole($role3);
        //crear 1000 registros
        User::factory(1000)->create();
    }
}
