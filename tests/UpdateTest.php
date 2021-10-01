<?php

namespace Mosamirzz\BulkQuery\Tests;

use Illuminate\Support\Facades\DB;
use Mosamirzz\BulkQuery\Update;

class UpdateTest extends TestCase
{
    public function a_test_can_perform_update_query_with_id_as_key()
    {
        $user = DB::table('users')->find(2);
        $this->assertSame($user->password, "password");

        $update = new Update("users");
        $update->useColumns(["password"]);
        $update->prepare([
            2 => [
                "password" => "123456"
            ],
        ]);
        $update->execute();

        $user = DB::table('users')->find(2);
        $this->assertSame($user->password, "123456");
    }

    public function test_can_update_with_different_key()
    {
        $update = new Update("users");
        $update->useColumns(["password"]);
        $update->useKey("email");
        $update->prepare([
            "gm.mohamedsamir@gmail.com" => [
                "password" => "mohamed123456"
            ],
        ]);
        $update->execute();

        $user = DB::table('users')->where("email", "gm.mohamedsamir@gmail.com")->first();
        $this->assertEquals($user->password, "mohamed123456");
    }

    public function test_throw_exception_when_send_empty_records()
    {
        $insert = new Update("users");
        $insert->useColumns(["name", "password"]);

        $this->expectExceptionMessage('$records can not be empty');
        $insert->prepare([]);
        $insert->execute();
    }
}
