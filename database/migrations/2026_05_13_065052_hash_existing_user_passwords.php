<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $users = DB::table('users')->get();

        foreach ($users as $user) {

            // Skip already hashed passwords
            if (!str_starts_with($user->userpass, '$2y$')) {

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'userpass' => Hash::make($user->userpass)
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};