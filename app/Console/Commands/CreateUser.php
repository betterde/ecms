<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an administrator account';

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
     * Date: 2020/5/15
     * @author George
     */
    public function handle()
    {
        $name = $this->ask('Please enter your name');
        REENTER_EMAIL:
        $email = $this->ask('Please enter your email');
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $this->line('The email address you entered is incorrect!');
            goto REENTER_EMAIL;
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            $this->line('The email address already exists!');
            goto REENTER_EMAIL;
        }

        REENTER_PASS:
        $password = $this->secret('Please enter your password');
        $confirm = $this->secret('Please enter your password again');
        if ($password !== $confirm) {
            $this->line('The password entered does not match, Please enter again!');
            goto REENTER_PASS;
        }
        if ($this->confirm('Do you wish to continue?')) {
            $user = User::create([
                'id' => Str::uuid(),
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password)
            ]);
            $this->line('User created successfully!');
            $this->table(['ID', 'Name', 'Email', 'Created at'], [
                [$user->id, $user->name, $user->email, $user->created_at]
            ]);
        }
    }
}
