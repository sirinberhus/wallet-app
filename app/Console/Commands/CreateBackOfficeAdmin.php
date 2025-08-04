<?php

namespace App\Console\Commands;

use App\Models\BackofficeAgent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateBackOfficeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new backoffice admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Creating a new Backoffice Admin User');

        //Ask for user details
        $name = $this->ask('Enter the admin\'s full name');
        $email = $this->ask('Enter the admin\'s email address');
        $password = $this->secret('Enter a password (min 8 characters)');

        $passwordConfirm = $this->secret('Confirm the password');

        if ($password !== $passwordConfirm) {
            $this->error('Passwords do not match');
            return;
        }

        if (strlen($password) < 8) {
            $this->error('Password must be at least 8 characters!');
            return;
        }

        if (BackofficeAgent::where('email', $email)->exists()) {
            $this->error('This email is already registered!');
            return;
        }

        //create admin user
        $admin = BackofficeAgent::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
        ]);

        //show success message
        $this->info('Admin user created successfully!');
        $this->line('Name: ' . $admin->name);
        $this->line('Email: ' . $admin->email);
        $this->warn('Remember this password: ' . $password);
    }
}
