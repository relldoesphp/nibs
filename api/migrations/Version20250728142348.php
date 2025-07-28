<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250728142348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds initial feedback data to the feedback table.';
    }

    public function up(Schema $schema): void
    {
        // This up() migration is auto-generated, please modify it to your needs
        $this->addSql(/** @lang text */ 'INSERT INTO feedback (name, email, message, created_at, updated_at) VALUES (`:name`, :email, :message, :created_at, :updated_at)');

        $feedbackData = [
            [
                'name' => 'Red Ranger',
                'email' => 'red.ranger@example.com',
                'message' => 'Tyrell is very smart',
                'created_at' => (new \DateTimeImmutable('-5 days'))->format('Y-m-d H:i:s'),
                'updated_at' => (new \DateTimeImmutable('-5 days'))->format('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Blue Ranger',
                'email' => 'blue.ranger@example.com',
                'message' => 'Tyrell is a great employee.',
                'created_at' => (new \DateTimeImmutable('-4 days'))->format('Y-m-d H:i:s'),
                'updated_at' => (new \DateTimeImmutable('-4 days'))->format('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Green Ranger',
                'email' => 'green.ranger@example.com',
                'message' => 'Tyrell is a great teammate.',
                'created_at' => (new \DateTimeImmutable('-3 days'))->format('Y-m-d H:i:s'),
                'updated_at' => (new \DateTimeImmutable('-3 days'))->format('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Yellow Ranger',
                'email' => 'yellow.ranger@example.com',
                'message' => 'Tyrell taught himself how to code.',
                'created_at' => (new \DateTimeImmutable('-2 days'))->format('Y-m-d H:i:s'),
                'updated_at' => (new \DateTimeImmutable('-2 days'))->format('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Black Ranger',
                'email' => 'balck.ranger@example.com',
                'message' => 'Tyrell is a very hard worker.',
                'created_at' => (new \DateTimeImmutable('-1 day'))->format('Y-m-d H:i:s'),
                'updated_at' => (new \DateTimeImmutable('-1 day'))->format('Y-m-d H:i:s'),
            ],
        ];

        foreach ($feedbackData as $data) {
            $this->addSql(
                'INSERT INTO feedback (name, email, message, created_at, updated_at) VALUES (:name, :email, :message, :created_at, :updated_at)',
                $data
            );
        }
    }

    public function down(Schema $schema): void
    {
        // This down() migration is auto-generated, please modify it to your needs
        // Delete the data added by the up() method.
        $this->addSql('DELETE FROM feedback WHERE email IN (:email1, :email2, :email3, :email4, :email5)', [
            'email1' => 'red.ranger@example.com',
            'email2' => 'blue.ranger@example.com',
            'email3' => 'green.ranger@example.com',
            'email4' => 'yellow.ranger@example.com',
            'email5' => 'black.ranger@example.com',
        ], [
            \Doctrine\DBAL\Connection::PARAM_STR_ARRAY, // Specify parameter type for array
            \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
            \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
            \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
            \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
        ]);
    }
}

