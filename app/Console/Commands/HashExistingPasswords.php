<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;

class HashExistingPasswords extends Command
{
    protected $signature = 'users:hash-passwords';
    protected $description = 'Hash all plaintext user passwords';

    public function handle()
    {
        $users = UserModel::all();

        foreach ($users as $user) {

            // Skip already hashed passwords
            if (strlen($user->userpass) >= 60) {
                continue;
            }

            $user->userpass = Hash::make($user->userpass);
            $user->save();
        }

        $this->info('All passwords hashed successfully.');
    }
}