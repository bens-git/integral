<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $created = [];

        for ($i = 0; $i < 30; $i++) {
            // generate a friendly, memorable nickname
            $nickname = ucfirst($faker->unique()->userName);
            // normalize for email
            $local = Str::slug($nickname, '.');
            $email = "demo+{$local}@example.test";

            $user = User::create([
                'name' => $nickname,
                'email' => $email,
                'password' => Hash::make('password'),
            ]);

            // ensure email_verified_at is set even if not fillable
            $user->email_verified_at = now();
            $user->save();

            $created[] = [
                'nickname' => $nickname,
                'email' => $email,
                'password' => 'password',
            ];
        }

        $this->command->info('Created 30 demo users. Credentials:');
        foreach ($created as $c) {
            $this->command->line("- {$c['nickname']}  <{$c['email']}>  password: {$c['password']}");
        }

        $this->command->info("Tip: users can login using their demo email and password 'password'.");
    }
}
