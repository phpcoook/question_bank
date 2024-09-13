<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $user = new User;
        $user->first_name = 'Admin';
        $user->last_name = '';
        $user->email = 'admin@admin.com';
        $user->password = Hash::make(123456789);
        $user->role = 'admin';
        $user->date_of_birth = '2024-09-18';
        $user->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::where('email', 'admin@admin.com')->delete();
    }
};
