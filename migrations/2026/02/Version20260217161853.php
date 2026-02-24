<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260217161853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscription_plan_prices (id UUID NOT NULL, stripe_price_id VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, interval_value INT NOT NULL, interval_unit VARCHAR(255) NOT NULL, price_value INT NOT NULL, price_currency VARCHAR(255) NOT NULL, plan_id UUID DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_6D8905BCE899029B ON subscription_plan_prices (plan_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PLAN_PRICE_ID ON subscription_plan_prices (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PLAN_PRICE_STRIPE_PRICE_ID ON subscription_plan_prices (stripe_price_id)');
        $this->addSql('CREATE TABLE subscription_plans (id UUID NOT NULL, tier VARCHAR(255) NOT NULL, is_active BOOLEAN NOT NULL, gyms_limit INT NOT NULL, staff_limit INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_PLAN_ID ON subscription_plans (id)');
        $this->addSql('CREATE TABLE subscriptions (cancel_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id UUID NOT NULL, stripe_subscription_id VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, owner_id UUID DEFAULT NULL, price_id UUID DEFAULT NULL, next_price_id UUID DEFAULT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_4778A017E3C61F9 ON subscriptions (owner_id)');
        $this->addSql('CREATE INDEX IDX_4778A01D614C7E7 ON subscriptions (price_id)');
        $this->addSql('CREATE INDEX IDX_4778A01EE0E8B09 ON subscriptions (next_price_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_SUBSCRIPTION_ID ON subscriptions (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_SUBSCRIPTION_STRIPE_SUBSCRIPTION_ID ON subscriptions (stripe_subscription_id)');
        $this->addSql('ALTER TABLE subscription_plan_prices ADD CONSTRAINT FK_6D8905BCE899029B FOREIGN KEY (plan_id) REFERENCES subscription_plans (id)');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A017E3C61F9 FOREIGN KEY (owner_id) REFERENCES owners (id)');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A01D614C7E7 FOREIGN KEY (price_id) REFERENCES subscription_plan_prices (id)');
        $this->addSql('ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A01EE0E8B09 FOREIGN KEY (next_price_id) REFERENCES subscription_plan_prices (id)');
        $this->addSql('ALTER TABLE owners ADD stripe_customer_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_OWNER_STRIPE_CUSTOMER_ID ON owners (stripe_customer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_plan_prices DROP CONSTRAINT FK_6D8905BCE899029B');
        $this->addSql('ALTER TABLE subscriptions DROP CONSTRAINT FK_4778A017E3C61F9');
        $this->addSql('ALTER TABLE subscriptions DROP CONSTRAINT FK_4778A01D614C7E7');
        $this->addSql('ALTER TABLE subscriptions DROP CONSTRAINT FK_4778A01EE0E8B09');
        $this->addSql('DROP TABLE subscription_plan_prices');
        $this->addSql('DROP TABLE subscription_plans');
        $this->addSql('DROP TABLE subscriptions');
        $this->addSql('DROP INDEX UNIQ_OWNER_STRIPE_CUSTOMER_ID');
        $this->addSql('ALTER TABLE owners DROP stripe_customer_id');
    }
}
