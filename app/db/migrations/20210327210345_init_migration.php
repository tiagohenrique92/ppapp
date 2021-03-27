<?php
declare (strict_types = 1);
namespace PPApp;

use Illuminate\Database\Schema\Blueprint;
use \PPApp\Migration\Migration;

final class InitMigration extends Migration
{
    public function up()
    {
        $this->createTableUsers();
        $this->createTableBusinessUsers();
        $this->createTablePersonUsers();
        $this->createTableWallets();
        $this->createTableTransactions();
    }

    public function down()
    {
        $this->schema->drop('transactions');
        $this->schema->drop('wallets');
        $this->schema->drop('person_users');
        $this->schema->drop('business_users');
        $this->schema->drop('users');
    }

    private function createTableUsers()
    {
        $this->schema->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 36)->unique();
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            $table->tinyInteger('type', false, true);
        });
    }

    private function createTableBusinessUsers()
    {
        $this->schema->create('business_users', function (Blueprint $table) {
            $table->unsignedInteger('id_user', false)->unique();
            $table->string('cnpj', 14)->unique();
        });

        $table = $this->table('business_users');
        $table->addForeignKey('id_user', 'users', 'id', array(
            'delete' => 'CASCADE',
            'update' => 'NO_ACTION',
        ))->save();
    }

    private function createTablePersonUsers()
    {
        $this->schema->create('person_users', function (Blueprint $table) {
            $table->unsignedInteger('id_user', false)->unique();
            $table->string('cpf', 11)->unique();
        });

        $table = $this->table('person_users');
        $table->addForeignKey('id_user', 'users', 'id', array(
            'delete' => 'CASCADE',
            'update' => 'RESTRICT',
        ))->save();
    }

    private function createTableWallets()
    {
        $this->schema->create('wallets', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 36)->unique();
            $table->unsignedInteger('id_user', false);
            $table->decimal('balance', 16, 2);
        });

        $table = $this->table('wallets');
        $table->addForeignKey('id_user', 'users', 'id', array(
            'delete' => 'CASCADE',
            'update' => 'RESTRICT',
        ))->save();
    }

    private function createTableTransactions()
    {
        $this->schema->create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->char('uuid', 36)->unique();
            $table->dateTime("created_at", 0);
            $table->decimal('amount', 16, 2);
            $table->unsignedInteger('id_payer', false);
            $table->unsignedInteger('id_payee', false);
        });

        $table = $this->table('transactions');
        $table->addForeignKey('id_payer', 'users', 'id', array(
            'delete' => 'CASCADE',
            'update' => 'RESTRICT',
        ));
        $table->addForeignKey('id_payee', 'users', 'id', array(
            'delete' => 'CASCADE',
            'update' => 'RESTRICT',
        ));
        $table->save();
    }

}
