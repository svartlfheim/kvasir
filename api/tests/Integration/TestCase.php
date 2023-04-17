<?php

namespace App\Tests\Integration;

use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class TestCase extends KernelTestCase
{
    protected bool $skipMigrations;
    protected ?Application $app = null;
    protected string $runRef;

    public function __construct(bool $skipMigrations = false, ?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->skipMigrations = $skipMigrations;
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel([
            'debug' => false,
        ]);

        $reflect = new ReflectionClass($this);

        $this->runRef = substr(str_shuffle(str_repeat("0123456789abcdefghijklmnopqrstuvwxyz", 5)), 0, 5);

        // This is used by the doctrine test configuration as a db suffix
        // Each test gets it's own database, so we parallelise how we see fit
        $_ENV['TEST_TOKEN'] = '_' . $reflect->getShortName() . '_' . $this->runRef;

        $this->app = new Application(self::$kernel);
        $this->app->setAutoExit(false);

        $this->runCommand('doctrine:database:drop', ['--no-interaction' => true, '--if-exists' => true, '--force' => true]);
        $this->runCommand('doctrine:database:create', ['--no-interaction' => true]);

        if (! $this->skipMigrations) {
            $this->runCommand('doctrine:migrations:migrate', ['--no-interaction' => true]);
        }
    }

    protected function getService(string $class)
    {
        $c = static::getContainer();

        return $c->get($class);
    }


    protected function runCommand(string $command, array $args): string
    {
        $args = array_merge(['command' => $command], $args);

        $input = new ArrayInput($args);

        $output = new BufferedOutput();

        $this->app->run($input, $output);

        return $output->fetch();
    }

    protected function tearDown(): void
    {
        /**
         * If the tests fail it can be handy to keep the database around for debugging.
         * This will remove the database if the test passed, but otherwise it will remain.
         *
         * The setup will remove it when re-running the test anyway. This just may result
         * in quite alot of databases on db the server if lots of failures occur.
         */
        if (! $this->hasFailed()) {
            $this->runCommand('doctrine:database:drop', ['--no-interaction' => true, '--force' => true]);
        } else {
            /*
                This is weird...

                Wanted a way to display the db reference we've used if the test fails.
                This ensures he reference is displayed by changing he test name here.
                It can't be changed earlier as the name is used to actually run the test.

                It could cause some weird behaviour, but seems fine so far.
            */
            $this->setName($this->getName() . " (db ref: $this->runRef)");
        }

        parent::tearDown();
    }
}
