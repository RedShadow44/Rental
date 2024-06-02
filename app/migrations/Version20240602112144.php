<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240602112144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rentals (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, book_id INT NOT NULL, rental_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_35ACDB487E3C61F9 (owner_id), UNIQUE INDEX UNIQ_35ACDB4816A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB487E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE rentals ADD CONSTRAINT FK_35ACDB4816A2B381 FOREIGN KEY (book_id) REFERENCES books (id)');
        $this->addSql('ALTER TABLE books ADD available TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB487E3C61F9');
        $this->addSql('ALTER TABLE rentals DROP FOREIGN KEY FK_35ACDB4816A2B381');
        $this->addSql('DROP TABLE rentals');
        $this->addSql('ALTER TABLE books DROP available');
    }
}
