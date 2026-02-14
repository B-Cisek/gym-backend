<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210191517 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gyms (created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id UUID NOT NULL, owner_id UUID NOT NULL, name VARCHAR(255) NOT NULL, address_street VARCHAR(100) DEFAULT NULL, address_city VARCHAR(100) DEFAULT NULL, address_postal_code VARCHAR(6) DEFAULT NULL, address_voivodeship VARCHAR(50) DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX UNIQ_GYM_ID ON gyms (id)');
        $this->addSql('CREATE INDEX UNIQ_GYM_OWNER_ID ON gyms (owner_id)');
        $this->addSql('CREATE TABLE owners (created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id UUID NOT NULL, user_id UUID NOT NULL, company_name VARCHAR(255) DEFAULT NULL, tax_id VARCHAR(10) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, address_street VARCHAR(100) DEFAULT NULL, address_city VARCHAR(100) DEFAULT NULL, address_postal_code VARCHAR(6) DEFAULT NULL, address_voivodeship VARCHAR(50) DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_OWNER_ID ON owners (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_OWNER_USER_ID ON owners (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE gyms');
        $this->addSql('DROP TABLE owners');
    }
}
