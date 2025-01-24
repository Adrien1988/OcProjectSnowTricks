<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Consolidated Migration: Rebuilds the entire database schema from scratch.
 */
final class Version20250124000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Consolidated migration to rebuild the database schema from scratch.';
    }

    public function up(Schema $schema): void
    {
        // Create tables
        $this->addSql('CREATE TABLE comment (
            id INT AUTO_INCREMENT NOT NULL, 
            author_id INT NOT NULL, 
            figure_id INT NOT NULL, 
            content VARCHAR(255) NOT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            INDEX IDX_9474526CF675F31B (author_id), 
            INDEX IDX_9474526C5C011B5 (figure_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE figure (
            id INT AUTO_INCREMENT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            description LONGTEXT NOT NULL, 
            slug VARCHAR(255) NOT NULL, 
            figure_group VARCHAR(255) NOT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            updated_at DATETIME NOT NULL, 
            UNIQUE INDEX UNIQ_2F57B37A5E237E06 (name), 
            UNIQUE INDEX UNIQ_2F57B37A989D9B62 (slug), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE image (
            id INT AUTO_INCREMENT NOT NULL, 
            figure_id INT NOT NULL, 
            url VARCHAR(255) DEFAULT NULL, 
            alt_text VARCHAR(255) DEFAULT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            INDEX IDX_C53D045F5C011B5 (figure_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE user (
            id INT AUTO_INCREMENT NOT NULL, 
            username VARCHAR(255) NOT NULL, 
            email VARCHAR(255) NOT NULL, 
            password VARCHAR(255) NOT NULL, 
            avatar_url VARCHAR(255) DEFAULT NULL, 
            is_active TINYINT(1) DEFAULT 0 NOT NULL, 
            activation_token VARCHAR(64) DEFAULT NULL, 
            reset_token VARCHAR(64) DEFAULT NULL, 
            reset_token_expires_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), 
            UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        $this->addSql('CREATE TABLE video (
            id INT AUTO_INCREMENT NOT NULL, 
            figure_id INT NOT NULL, 
            embed_code VARCHAR(1000) NOT NULL, 
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
            INDEX IDX_7CC7DA2C5C011B5 (figure_id), 
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign keys
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C5C011B5 FOREIGN KEY (figure_id) REFERENCES figure (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop foreign keys
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5C011B5');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F5C011B5');
        $this->addSql('ALTER TABLE video DROP FOREIGN KEY FK_7CC7DA2C5C011B5');

        // Drop tables
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE figure');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE video');
    }
}
