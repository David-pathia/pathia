<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260104115140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initialisation du schéma complet pour PostgreSQL (Optimisé Upsun)';
    }

    public function up(Schema $schema): void
    {
        // Création des tables de base
        $this->addSql('CREATE TABLE feature (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');

        $this->addSql('CREATE TABLE subscription (id SERIAL NOT NULL, user_id INT DEFAULT NULL, subscriptionplan_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ended_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, etat BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A3C664D3A76ED395 ON subscription (user_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3D85CFD9F ON subscription (subscriptionplan_id)');

        $this->addSql('CREATE TABLE subscription_plan (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description TEXT NOT NULL, enligne BOOLEAN NOT NULL, PRIMARY KEY(id))');

        $this->addSql('CREATE TABLE subscription_plan_feature (subscription_plan_id INT NOT NULL, feature_id INT NOT NULL, PRIMARY KEY(subscription_plan_id, feature_id))');
        $this->addSql('CREATE INDEX IDX_CF3E21F99B8CE200 ON subscription_plan_feature (subscription_plan_id)');
        $this->addSql('CREATE INDEX IDX_CF3E21F960E4B879 ON subscription_plan_feature (feature_id)');

        // Utilisation des guillemets pour "user" car c'est un mot réservé dans PostgreSQL
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_active BOOLEAN DEFAULT true NOT NULL, is_verified BOOLEAN DEFAULT false NOT NULL, verified_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, password_change_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, failed_login_count INT DEFAULT 0 NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, cp VARCHAR(255) DEFAULT NULL, ville VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');

        // Table Messenger et Trigger de notification
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages (queue_name, available_at, delivered_at, id)');

        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');

        // Contraintes de clés étrangères
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3D85CFD9F FOREIGN KEY (subscriptionplan_id) REFERENCES subscription_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_feature ADD CONSTRAINT FK_CF3E21F99B8CE200 FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plan (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_plan_feature ADD CONSTRAINT FK_CF3E21F960E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3A76ED395');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3D85CFD9F');
        $this->addSql('ALTER TABLE subscription_plan_feature DROP CONSTRAINT FK_CF3E21F99B8CE200');
        $this->addSql('ALTER TABLE subscription_plan_feature DROP CONSTRAINT FK_CF3E21F960E4B879');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE subscription_plan');
        $this->addSql('DROP TABLE subscription_plan_feature');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
