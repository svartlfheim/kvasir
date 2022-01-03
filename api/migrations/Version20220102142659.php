<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Common\Doctrine\AbstractPlatformAwareMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20220102142659 extends AbstractPlatformAwareMigration
{
    public function getDescription(): string
    {
        return 'Create the initial structure for the connections table.';
    }

    public function doUp(Schema $schema): void
    {
        $table = $schema->createTable('connections');

        $table->addColumn('id', 'guid', [
            'notnull' => true,
        ]);
        $table->addColumn('name', 'string', [
            'length' => 100,
            'notnull' => true,
            'customSchemaOptions' => [
                'unique' => true,
            ],
        ]);

        $table->addColumn('engine', 'string', [
            'length' => 20,
            'notnull' => true,
        ]);

        $table->setPrimaryKey(['id']);
    }

    public function doDown(Schema $schema): void
    {
        $schema->dropTable('connections');
    }
}
