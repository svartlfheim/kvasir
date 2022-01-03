<?php

namespace App\Tests\Unit\Common\Doctrine;

use App\Common\Doctrine\AbstractPlatformAwareMigration;
use App\Tests\Unit\TestCase;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Exception\AbortMigration;

class AbstractPlatformAwareMigrationTest extends TestCase
{
    protected function buildTestObj(AbstractPlatform $mockPlatform): object
    {
        return new class ($mockPlatform) extends AbstractPlatformAwareMigration {
            public function __construct(AbstractPlatform $mockPlatform)
            {
                $this->platform = $mockPlatform;
            }

            public function doUp(Schema $schema): void
            {
                $schema->createTable('faketable');
            }

            public function doDown(Schema $schema): void
            {
                $schema->dropTable('faketable');
            }
        };
    }

    public function testThatUpAndDownRunForSupportedPlatforms(): void
    {
        $platforms = [
            PostgreSQLPlatform::class,
            PostgreSQL100Platform::class, // extends the PostgreSQLPlatform

            MySQL80Platform::class,
        ];

        foreach ($platforms as $platformClass) {
            $platform = new $platformClass();
            $t = $this->buildTestObj($platform);

            $mockSchema = $this->createMock(Schema::class);
            $mockSchema->expects($this->exactly(1))->method('createTable')->with('faketable');
            $mockSchema->expects($this->exactly(1))->method('dropTable')->with('faketable');

            $t->up($mockSchema);
            $t->down($mockSchema);
        }
    }

    public function testThatUpDoesNotRunForUnSupportedPlatforms(): void
    {
        $platform = new MySQL57Platform();
        $t = $this->buildTestObj($platform);

        $mockSchema = $this->createMock(Schema::class);

        $this->expectExceptionObject(new AbortMigration("This database platform 'Doctrine\DBAL\Platforms\MySQL57Platform' is not supported."));
        $t->up($mockSchema);
    }
}
