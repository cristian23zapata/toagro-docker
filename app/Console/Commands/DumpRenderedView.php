<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DumpRenderedView extends Command
{
    protected $signature = 'dump:rendered-dashboard {email}';
    protected $description = 'Render dashboard as given user email and print HTML';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error('User not found: ' . $email);
            return 1;
        }

        auth()->login($user);

        $html = view('dashboard')->render();

        // Print a short snippet around the 'Ver Viajes' button
        if (preg_match('/<a[^>]*>(\s*Ver Viajes\s*)<\/a>/i', $html, $m, PREG_OFFSET_CAPTURE)) {
            $offset = $m[0][1];
            $snippet = substr($html, max(0, $offset - 200), 400);
            $this->line($snippet);
        } else {
            $this->line('Could not find Ver Viajes anchor in rendered HTML.');
        }

        auth()->logout();

        return 0;
    }
}
