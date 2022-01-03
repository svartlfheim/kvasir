<?php

declare(strict_types=1);

namespace <namespace>;

use App\Common\Doctrine\AbstractPlatformAwareMigration;
use Doctrine\DBAL\Schema\Schema;

final class <className> extends AbstractPlatformAwareMigration
{
    public function getDescription(): string
    {
        return 'The description of my awesome migration!';
    }

    public function doUp(Schema $schema): void
    {
        // Add the up migration steps here
    }

    public function doDown(Schema $schema): void
    {
        // Add the down migration steps here
    }
}