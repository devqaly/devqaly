<?php

namespace App\Console\Commands\Register;

use App\Enum\User\UserCurrentPositionEnum;
use App\Models\User;
use App\services\Auth\RegisterTokenService;
use App\services\Resources\CompanyService;
use DateTimeZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateAccounts extends Command
{
    protected $signature = 'app:create:accounts:for:company';

    protected $description = 'This command will create accounts for an organizer';

    public function handle(RegisterTokenService $registerTokenService, CompanyService $companyService): void
    {
        $companyName = $this->ask('Company name');
        $companyOwnerEmail = $this->ask('Company owner email');
        $companyOwnerFirstName = $this->ask('Company owner first name');
        $companyOwnerLastName = $this->ask('Company owner last name');

        $this->info('Choose one of the timezones bellow:');
        $this->info(collect(DateTimeZone::listIdentifiers())->join(', '));

        $companyOwnerTimezone = $this->ask('Company owner timezone');

        $csv = $this->ask('CSV name with employees. Remember, the file should be in `storage/app/users-csv` folder. Leave empty if no employees');

        try {
            DB::beginTransaction();

            $this->info("Creating company owner with email: {$companyOwnerEmail}");

            $registerToken = $registerTokenService->createToken(data: collect(['email' => $companyOwnerEmail]), sendEmail: false);
            $data = $registerTokenService->completeRegistration(collect([
                'firstName' => $companyOwnerFirstName,
                'lastName' => $companyOwnerLastName,
                'timezone' => $companyOwnerTimezone,
                'password' => 'password',
                'currentPosition' => UserCurrentPositionEnum::MANAGER->value,
            ]), $registerToken);

            /** @var User $owner */
            $owner = $data->get('user');
            $company = $companyService->createCompany(
                data: collect(['name' => $companyName]),
                createdBy: $data->get('user'),
                customerOptionsMetadata: [
                    'createdFromInvitationCommand' => true,
                    'environment' => config('app.env')
                ]
            );

            $companyService->addMemberToCompany(
                data: collect(['emails' => $owner->email]),
                invitedBy: $owner,
                company: $company,
                oldRegisterToken: $registerToken,
                sendInvitationEmail: false
            );

            if ($csv) {
                // The file must be in the local disk under `storage/app/users-csv`
                if (($open = fopen(Storage::disk('local')->path("users-csv/{$csv}"), "r")) !== FALSE) {
                    while (($data = fgetcsv($open)) !== FALSE) {
                        $email = $data[0];

                        $this->info('Inviting user: ' . $email);

                        $companyService->addMemberToCompany(
                            data: collect(['emails' => $email]),
                            invitedBy: $owner,
                            company: $company,
                            oldRegisterToken: $registerToken,
                            sendInvitationEmail: true
                        );
                    }

                    fclose($open);
                } else {
                    throw new \Exception('Error opening file');
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('There was an error: ' . $e->getMessage());
        }
    }
}
