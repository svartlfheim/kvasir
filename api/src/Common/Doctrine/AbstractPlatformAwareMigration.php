<?php

declare(strict_types=1);

namespace App\Common\Doctrine;

use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

abstract class AbstractPlatformAwareMigration extends AbstractMigration
{
    protected function guardPlatformVersion(): void
    {
        $platformClass = get_class($this->platform);

        $this->abortIf(! $this->isSupportedPlatform(), "This database platform '$platformClass' is not supported.");
    }

    protected function isPostgres(): bool
    {
        return $this->platform instanceof PostgreSQLPlatform;
    }

    protected function isMySQL8(): bool
    {
        return $this->platform instanceof MySQL80Platform;
    }

    protected function isSupportedPlatform(): bool
    {
        return $this->isPostgres()
            || $this->isMySQL8();
    }

    public function up(Schema $schema): void
    {
        $this->guardPlatformVersion();

        $this->doUp($schema);
    }

    public function down(Schema $schema): void
    {
        $this->guardPlatformVersion();

        $this->doDown($schema);
    }

    abstract public function doUp(Schema $schema): void;

    abstract public function doDown(Schema $schema): void;
}
