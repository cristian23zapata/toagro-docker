<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class FindUser extends Command
{
    protected $signature = 'find:user {email}';
    protected $description = 'Buscar usuario por email';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error('Usuario no encontrado');
            return 1;
        }
        $this->info('Usuario encontrado:');
        $this->table(['id','name','email','role_id','email_verified_at'], [[$user->id,$user->name,$user->email,$user->role_id,$user->email_verified_at]]);
        return 0;
    }
}
