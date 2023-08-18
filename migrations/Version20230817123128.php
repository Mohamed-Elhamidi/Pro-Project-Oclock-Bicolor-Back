<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230817123128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE scores (id INT AUTO_INCREMENT NOT NULL, id_user_id INT DEFAULT NULL, id_game_id INT DEFAULT NULL, nb_points INT NOT NULL, start_game DATETIME DEFAULT NULL, end_game DATETIME DEFAULT NULL, INDEX IDX_750375E79F37AE5 (id_user_id), INDEX IDX_750375E3A127075 (id_game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE scores ADD CONSTRAINT FK_750375E79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE scores ADD CONSTRAINT FK_750375E3A127075 FOREIGN KEY (id_game_id) REFERENCES game (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scores DROP FOREIGN KEY FK_750375E79F37AE5');
        $this->addSql('ALTER TABLE scores DROP FOREIGN KEY FK_750375E3A127075');
        $this->addSql('DROP TABLE scores');
    }
}
