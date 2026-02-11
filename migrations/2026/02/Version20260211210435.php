<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20260211210435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove address_voivodeship from gyms and owners';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE gyms DROP address_voivodeship');
        $this->addSql('ALTER TABLE owners DROP address_voivodeship');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE gyms ADD address_voivodeship VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE owners ADD address_voivodeship VARCHAR(50) DEFAULT NULL');
    }
}
