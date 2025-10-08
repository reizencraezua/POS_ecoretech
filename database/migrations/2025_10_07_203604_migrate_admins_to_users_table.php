<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate data from admins table to users table
        if (Schema::hasTable('admins')) {
            $admins = DB::table('admins')->get();
            
            foreach ($admins as $admin) {
                DB::table('users')->insert([
                    'name' => $admin->name,
                    'email' => $admin->email,
                    'email_verified_at' => $admin->email_verified_at,
                    'password' => $admin->password,
                    'role' => $admin->role,
                    'is_active' => true,
                    'employee_id' => null,
                    'remember_token' => $admin->remember_token,
                    'created_at' => $admin->created_at,
                    'updated_at' => $admin->updated_at,
                ]);
            }
            
            // Drop the admins table
            Schema::dropIfExists('admins');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate admins table
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'super_admin'])->default('admin');
            $table->rememberToken();
            $table->timestamps();
        });
        
        // Migrate data back from users table to admins table
        $users = DB::table('users')->whereIn('role', ['admin', 'super_admin'])->get();
        
        foreach ($users as $user) {
            DB::table('admins')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'password' => $user->password,
                'role' => $user->role,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
        }
    }
};