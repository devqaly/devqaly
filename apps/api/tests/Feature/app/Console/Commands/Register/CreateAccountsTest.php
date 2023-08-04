<?php

namespace Tests\Feature\app\Console\Commands\Register;

use App\Models\Company\Company;
use App\Models\Company\CompanyMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateAccountsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_console_command_to_create_new_accounts(): void
    {
        $companyName = $this->faker->company;
        $email = $this->faker->email;
        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $timezone = $this->faker->timezone;

        $this
            ->artisan('app:create:accounts:for:company')
            ->expectsQuestion('Company name', $companyName)
            ->expectsQuestion('Company owner email', $email)
            ->expectsQuestion('Company owner first name', $firstName)
            ->expectsQuestion('Company owner last name', $lastName)
            ->expectsQuestion('Company owner timezone', $timezone)
            ->expectsQuestion('CSV name with employees. Remember, the file should be in `storage/app/users-csv` folder. Leave empty if no employees', '')
            ->assertExitCode(0);

        $this->assertDatabaseHas((new User())->getTable(), [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'timezone' => $timezone,
            'email' => $email,
        ]);

        $this->assertDatabaseCount((new User())->getTable(), 1);

        $user = User::query()->firstOrFail();

        $this->assertDatabaseHas((new Company())->getTable(), [
            'name' => $companyName,
            'created_by_id' => $user->id,
        ]);

        $company = Company::query()->firstOrFail();

        $this->assertDatabaseHas((new CompanyMember())->getTable(), [
            'company_id' => $company->id,
            'member_id' => $user->id,
        ]);
    }
}
