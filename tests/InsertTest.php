<?php

namespace Mosamirzz\BulkQuery\Tests;

use Illuminate\Support\Facades\DB;
use Mosamirzz\BulkQuery\Insert;

class InsertTest extends TestCase
{
    public function test_can_perform_insert_query()
    {
        $insert = new Insert("users");
        $insert->useColumns(["name", "email", "password"]);
        $insert->prepare([
            [
                "name" => "user1",
                "email" => "user1@test.com",
                "password" => "123456"
            ],
        ]);
        $insert->execute();

        $users = DB::table('users')->pluck("email")->toArray();

        $this->assertContains("user1@test.com", $users);
    }

    public function test_throw_exception_when_send_empty_records()
    {
        $insert = new Insert("users");
        $insert->useColumns(["name", "email", "password"]);
        $insert->prepare([]);

        $this->expectExceptionMessage('$records can not be empty');
        $insert->execute();
    }

    public function test_it_throw_exception_when_records_has_duplicate_key()
    {
        $insert = new Insert("users");
        $insert->useColumns(["name", "email", "password"]);
        $insert->prepare([
            [
                "name" => "user1",
                "email" => "gm.mohamedsamir@gmail.com",
                "password" => "123456"
            ],
        ]);

        $this->expectExceptionMessage("SQLSTATE[23000]: Integrity constraint violation");
        $insert->execute();
    }
}
