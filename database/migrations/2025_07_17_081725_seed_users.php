<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // создание стандартного пользователя
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@ru-drive.com',
            'phone' => '+79999999999',
            'email_verified_at' => now(),
            'password' => Hash::make('admin@ru-drive.com'),
            'role' => User::ADMIN,
        ]);

        $user->markEmailAsVerified();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
