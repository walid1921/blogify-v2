<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112075526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blog_blog_categories (blog_id INT NOT NULL, blog_categories_id INT NOT NULL, INDEX IDX_6ECC1802DAE07E97 (blog_id), INDEX IDX_6ECC1802D194DEDB (blog_categories_id), PRIMARY KEY(blog_id, blog_categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blog_blog_categories ADD CONSTRAINT FK_6ECC1802DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_blog_categories ADD CONSTRAINT FK_6ECC1802D194DEDB FOREIGN KEY (blog_categories_id) REFERENCES blog_categories (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE blog_blog_categories DROP FOREIGN KEY FK_6ECC1802DAE07E97');
        $this->addSql('ALTER TABLE blog_blog_categories DROP FOREIGN KEY FK_6ECC1802D194DEDB');
        $this->addSql('DROP TABLE blog_blog_categories');
    }
}
