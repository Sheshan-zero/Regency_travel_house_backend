<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Staff;

class EnableEmergencyAdmin extends Command
{
    protected $signature = 'enable:emergency-admin';
    protected $description = 'Enable the hidden emergency admin account';

    public function handle()
    {
        $admin = Staff::where('email', 'emergency@aioh.com')->first();

        if (!$admin) {
            $this->error('Emergency admin not found.');
            return;
        }

        $admin->update(['is_enabled' => true]);

        $this->info('Emergency admin account has been enabled.');
    }
}

