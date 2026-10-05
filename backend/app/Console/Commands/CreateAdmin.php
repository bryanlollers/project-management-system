<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Create an administrator using interactive prompts';

    public function handle(): int
    {
        $data = [
            'name' => $this->ask('Name'),
            'email' => $this->ask('Email'),
            'password' => $this->secret('Password (12-128 characters)'),
            'password_confirmation' => $this->secret('Confirm password'),
        ];
        $validator = Validator::make($data, [
            'name' => 'required|string|max:160',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:12|max:128|confirmed',
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }
        User::create([...$validator->safe()->except('password_confirmation'), 'role' => 'admin']);
        $this->info('Administrator created.');

        return self::SUCCESS;
    }
}
