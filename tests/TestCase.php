<?php

namespace Mosamirzz\BulkQuery\Tests;

use Mosamirzz\BulkQuery\Insert;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createTables();
        $this->seedData();
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }

    private function createTables()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
        });
    }

    private function seedData()
    {
        $insert = new Insert("users");
        $insert->useColumns(["name", "email", "password"]);
        $insert->prepare([
            [
                "name" => "mohamed",
                "email" => "gm.mohamedsamir@gmail.com",
                "password" => "password"
            ],
            [
                "name" => "ahmed",
                "email" => "ahmed@gmail.com",
                "password" => "password"
            ],
            [
                "name" => "ali",
                "email" => "ali@gmail.com",
                "password" => "password"
            ],
        ]);
        $insert->execute();
    }
}
