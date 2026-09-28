<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260928120443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Cree la table round (Speed Color)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE round (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, correct_color VARCHAR(7) NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME DEFAULT NULL, points INT DEFAULT NULL, INDEX IDX_C5EEEA34A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34A76ED395');
        $this->addSql('DROP TABLE round');
    }
}