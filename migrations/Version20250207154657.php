<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250207154657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure ADD main_image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE figure ADD CONSTRAINT FK_2F57B37AE4873418 FOREIGN KEY (main_image_id) REFERENCES image (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F57B37AE4873418 ON figure (main_image_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figure DROP FOREIGN KEY FK_2F57B37AE4873418');
        $this->addSql('DROP INDEX UNIQ_2F57B37AE4873418 ON figure');
        $this->addSql('ALTER TABLE figure DROP main_image_id');
    }
}
