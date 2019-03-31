<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190331070740 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_5A8A6C8D69CCBE9A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, author_id_id, title, publish_date, content FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL COLLATE BINARY, publish_date DATETIME NOT NULL, content CLOB DEFAULT NULL COLLATE BINARY, CONSTRAINT FK_5A8A6C8DF675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO post (id, author_id, title, publish_date, content) SELECT id, author_id_id, title, publish_date, content FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE INDEX IDX_5A8A6C8DF675F31B ON post (author_id)');
        $this->addSql('DROP INDEX IDX_F7129A80233D34C1');
        $this->addSql('DROP INDEX IDX_F7129A803AD8644E');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_user AS SELECT user_source, user_target FROM user_user');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('CREATE TABLE user_user (user_source INTEGER NOT NULL, user_target INTEGER NOT NULL, PRIMARY KEY(user_source, user_target), CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO user_user (user_source, user_target) SELECT user_source, user_target FROM __temp__user_user');
        $this->addSql('DROP TABLE __temp__user_user');
        $this->addSql('CREATE INDEX IDX_F7129A80233D34C1 ON user_user (user_target)');
        $this->addSql('CREATE INDEX IDX_F7129A803AD8644E ON user_user (user_source)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_5A8A6C8DF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__post AS SELECT id, author_id, title, publish_date, content FROM post');
        $this->addSql('DROP TABLE post');
        $this->addSql('CREATE TABLE post (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, publish_date DATETIME NOT NULL, content CLOB DEFAULT NULL, author_id_id INTEGER NOT NULL)');
        $this->addSql('INSERT INTO post (id, author_id_id, title, publish_date, content) SELECT id, author_id, title, publish_date, content FROM __temp__post');
        $this->addSql('DROP TABLE __temp__post');
        $this->addSql('CREATE INDEX IDX_5A8A6C8D69CCBE9A ON post (author_id_id)');
        $this->addSql('DROP INDEX IDX_F7129A803AD8644E');
        $this->addSql('DROP INDEX IDX_F7129A80233D34C1');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user_user AS SELECT user_source, user_target FROM user_user');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('CREATE TABLE user_user (user_source INTEGER NOT NULL, user_target INTEGER NOT NULL, PRIMARY KEY(user_source, user_target))');
        $this->addSql('INSERT INTO user_user (user_source, user_target) SELECT user_source, user_target FROM __temp__user_user');
        $this->addSql('DROP TABLE __temp__user_user');
        $this->addSql('CREATE INDEX IDX_F7129A803AD8644E ON user_user (user_source)');
        $this->addSql('CREATE INDEX IDX_F7129A80233D34C1 ON user_user (user_target)');
    }
}
