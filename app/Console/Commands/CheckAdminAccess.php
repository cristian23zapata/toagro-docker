<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CheckAdminAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:admin-access {email=admin@local.test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Login as admin user and check access to key routes (internal dispatch)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        // Log in the user for this process
        Auth::login($user);

        $this->info("Acting as: {$user->email} (role_id={$user->role_id})\n");

        $routes = [
            '/',
            '/dashboard',
            '/drivers',
            '/vehicles',
            '/routes',
            '/trips',
            '/deliveries',
            '/incidents',
            '/driver/trips',
            '/client/trips',
            '/profile',
        ];

        $this->table(['Route', 'HTTP Code'], array_map(function ($r) {
            // Create and dispatch an internal request
            $request = HttpRequest::create($r, 'GET');
            $response = app()->handle($request);
            return [$r, $response->getStatusCode()];
        }, $routes));

        return 0;
    }
}
