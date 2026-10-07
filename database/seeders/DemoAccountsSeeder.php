<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;

/**
 * Two ready-made logins for demos and testing, one per role:
 *     norazlina@vvsb.com / 123   Senior Finance Manager
 *     exec@vvsb.com      / 123   Finance Executive (managed by the account above)
 *
 * DatabaseSeeder does not call this. Run it on purpose:
 *     php artisan db:seed --class=DemoAccountsSeeder
 * Running it again resets both accounts to the values below.
 *
 * The password is public and far weaker than the Sign Up page allows, so change it
 * (or do not run this) on a site other people can reach.
 */
class DemoAccountsSeeder extends Seeder
{
    private const PASSWORD = '123';

    public function run(): void
    {
        $manager = $this->account('norazlina@vvsb.com', 'Norazlina', 'Senior Finance Manager', '0120000001', null);
        $this->account('exec@vvsb.com', 'Demo Executive', 'Finance Executive', '0120000002', $manager->staff_id);

        $this->command?->info('Demo logins ready: norazlina@vvsb.com and exec@vvsb.com, password '.self::PASSWORD);
    }

    private function account(string $email, string $name, string $role, string $phone, ?int $managerId): Staff
    {
        return Staff::updateOrCreate(['staff_email' => $email], [
            'staff_id_prefix' => Staff::prefixForRole($role),
            'staff_name' => $name,
            'staff_phone' => $phone,
            'staff_address' => 'Johor Bahru, Johor',
            'password' => self::PASSWORD, // hashed by the Staff model
            'staff_role' => $role,
            'staff_status' => 'Active',
            'reason' => null,
            'manager_id' => $managerId,
        ]);
    }
}
