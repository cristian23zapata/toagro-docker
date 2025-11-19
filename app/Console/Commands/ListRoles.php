<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;

class ListRoles extends Command
{
    protected $signature = 'list:roles';
    protected $description = 'Lista roles en la base de datos';

    public function handle()
    {
        $roles = Role::all(['id','name']);
        $this->table(['id','name'],$roles->toArray());
        return 0;
    }
}
