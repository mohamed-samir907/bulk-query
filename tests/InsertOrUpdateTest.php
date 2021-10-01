<?php

namespace Mosamirzz\BulkQuery\Tests;

use Illuminate\Support\Facades\DB;
use Mosamirzz\BulkQuery\InsertOrUpdate;

class InsertOrUpdateTest extends TestCase
{
    public function test_can_perfom_insert_or_update_query_without_unique_key_exception()
    {
        $query = new InsertOrUpdate("users");
        $query->useColumns(["name", "email", "password"]);
        $query->updatableColumns(["name", "password"]);
        $query->prepare([
            [
                "name" => "mohamed samir",
                "email" => "gm.mohamedsamir@gmail.com",
                "password" => "mohamed124"
            ],
            [
                "name" => "hello",
                "email" => "hello@user.com",
                "password" => "hello"
            ],
        ]);
        $query->execute();

        $user = DB::table('users')->where("email")->first();
        $this->assertEquals($user->name, "mohamed samir");
        $this->assertEquals($user->password, "mohamed124");
    }

    public function test_throw_exception_when_send_empty_records()
    {
        $insert = new InsertOrUpdate("users");
        $insert->useColumns(["name", "email", "password"]);
        $insert->prepare([]);

        $this->expectExceptionMessage('$records can not be empty');
        $insert->execute();
    }
}
