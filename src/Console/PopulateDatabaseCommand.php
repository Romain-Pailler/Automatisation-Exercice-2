<?php

namespace App\Console;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Office;
use Illuminate\Support\Facades\Schema;
use Slim\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Faker\Factory as FakerFactory;

class PopulateDatabaseCommand extends Command
{
    private App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('db:populate');
        $this->setDescription('Populate database with fake data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Populating database...');
        $faker = FakerFactory::create();

        /** @var \Illuminate\Database\Capsule\Manager $db */
        $db = $this->app->getContainer()->get('db');

        // Disable foreign key checks and truncate tables
        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=0");
        $db->getConnection()->statement("TRUNCATE `employees`");
        $db->getConnection()->statement("TRUNCATE `offices`");
        $db->getConnection()->statement("TRUNCATE `companies`");
        $db->getConnection()->statement("SET FOREIGN_KEY_CHECKS=1");
        $currentDateTime = date('Y-m-d H:i:s');

        // Insert companies
        for ($i = 1; $i <= 5; $i++) {
            $db->table('companies')->insert([
                'id' => $i,
                'name' => $faker->company,
                'phone' => $faker->phoneNumber,
                'email' => $faker->companyEmail,
                'website' => $faker->url,
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Google_office_%284135991953%29.jpg/800px-Google_office_%284135991953%29.jpg?20190722090506',
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ]);
        }

        // Insert offices
        for ($i = 1; $i <= 10; $i++) {
            $db->table('offices')->insert([
                'id' => $i,
                'name' => $faker->company . " Office",
                'address' => $faker->streetAddress,
                'city' => $faker->city,
                'zip_code' => $faker->postcode,
                'country' => $faker->country,
                'email' => $faker->email,
                'company_id' => $faker->numberBetween(1, 5),
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ]);
        }

        // Insert employees
        for ($i = 1; $i <= 50; $i++) {
            $db->table('employees')->insert([
                'id' => $i,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'office_id' => $faker->numberBetween(1, 10),
                'email' => $faker->email,
                'job_title' => $faker->jobTitle,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime,
            ]);
        }

        $output->writeln('Database populated successfully!');
        return 0;
    }
}
