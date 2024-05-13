<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240513195557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id_categorie INT AUTO_INCREMENT NOT NULL, nom_categorie VARCHAR(255) NOT NULL, type_categorie VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id_categorie)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dietary_programs_copy (id INT AUTO_INCREMENT NOT NULL, coach_id INT DEFAULT NULL, subscriber_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, calorie_goal INT DEFAULT NULL, macro_ratio_carbs DOUBLE PRECISION DEFAULT NULL, macro_ratio_protein DOUBLE PRECISION DEFAULT NULL, macro_ratio_fat DOUBLE PRECISION DEFAULT NULL, meal_types VARCHAR(255) DEFAULT NULL, notes TEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX subscriber_id (subscriber_id), INDEX coach_id (coach_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE equipement (id INT AUTO_INCREMENT NOT NULL, rate INT NOT NULL, date_equip VARCHAR(255) NOT NULL, type_equip VARCHAR(255) NOT NULL, reclamation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, date VARCHAR(255) NOT NULL, winner VARCHAR(255) NOT NULL, categorie VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, cin VARCHAR(255) NOT NULL, nomparticipant VARCHAR(255) NOT NULL, age INT NOT NULL, email VARCHAR(255) NOT NULL, evenement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE planning (iduser INT DEFAULT NULL, idPlanning INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, nbSeance INT NOT NULL, prix DOUBLE PRECISION NOT NULL, INDEX IDX_D499BFF65E5C27E9 (iduser), PRIMARY KEY(idPlanning)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id_produit INT AUTO_INCREMENT NOT NULL, id_categorie INT DEFAULT NULL, libelle VARCHAR(20) DEFAULT NULL, quantite INT DEFAULT NULL, prix INT DEFAULT NULL, photo_produit VARCHAR(255) DEFAULT NULL, INDEX IDX_29A5EC27C9486A13 (id_categorie), PRIMARY KEY(id_produit)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, id_equip_id INT DEFAULT NULL, etat TINYINT(1) NOT NULL, INDEX IDX_42C84955DA1F734C (id_equip_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seance (id INT AUTO_INCREMENT NOT NULL, iduser INT DEFAULT NULL, heuredebut VARCHAR(255) NOT NULL, heurefin VARCHAR(255) NOT NULL, date VARCHAR(255) NOT NULL, prix DOUBLE PRECISION NOT NULL, typeSeance VARCHAR(255) NOT NULL, idPlanning INT DEFAULT NULL, INDEX IDX_DF7DFD0E873A95AD (idPlanning), INDEX IDX_DF7DFD0E5E5C27E9 (iduser), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscriber_info (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, height DOUBLE PRECISION DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, age INT DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, goals TEXT DEFAULT NULL, activity_level VARCHAR(255) DEFAULT NULL, dietary_restrictions TEXT DEFAULT NULL, food_preferences TEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_52B1E77A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, numero_de_telephone VARCHAR(12) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dietary_programs_copy ADD CONSTRAINT FK_2C7263073C105691 FOREIGN KEY (coach_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dietary_programs_copy ADD CONSTRAINT FK_2C7263077808B1AD FOREIGN KEY (subscriber_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE planning ADD CONSTRAINT FK_D499BFF65E5C27E9 FOREIGN KEY (iduser) REFERENCES user (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27C9486A13 FOREIGN KEY (id_categorie) REFERENCES categorie (id_categorie)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955DA1F734C FOREIGN KEY (id_equip_id) REFERENCES equipement (id)');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0E873A95AD FOREIGN KEY (idPlanning) REFERENCES planning (idPlanning)');
        $this->addSql('ALTER TABLE seance ADD CONSTRAINT FK_DF7DFD0E5E5C27E9 FOREIGN KEY (iduser) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subscriber_info ADD CONSTRAINT FK_52B1E77A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dietary_programs_copy DROP FOREIGN KEY FK_2C7263073C105691');
        $this->addSql('ALTER TABLE dietary_programs_copy DROP FOREIGN KEY FK_2C7263077808B1AD');
        $this->addSql('ALTER TABLE planning DROP FOREIGN KEY FK_D499BFF65E5C27E9');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27C9486A13');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955DA1F734C');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0E873A95AD');
        $this->addSql('ALTER TABLE seance DROP FOREIGN KEY FK_DF7DFD0E5E5C27E9');
        $this->addSql('ALTER TABLE subscriber_info DROP FOREIGN KEY FK_52B1E77A76ED395');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE dietary_programs_copy');
        $this->addSql('DROP TABLE equipement');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE participation');
        $this->addSql('DROP TABLE planning');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE seance');
        $this->addSql('DROP TABLE subscriber_info');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
