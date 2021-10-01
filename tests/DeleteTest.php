<?php

namespace Mosamirzz\BulkQuery\Tests;

use Illuminate\Support\Facades\DB;
use Mosamirzz\BulkQuery\Delete;

class DeleteTest extends TestCase
{
    public function test_can_perform_delete_query_with_id_column()
    {
        $delete = new Delete("users");
        $delete->prepare([1, 2, 3]);
        $delete->execute();

        $count = DB::table('users')->count();

        $this->assertEquals($count, 0);
    }

    public function test_can_perform_delete_query_with_another_column()
    {
        $delete = new Delete("users");
        $delete->useKey("email");
        $delete->prepare(["gm.mohamedsamir@gmail.com"]);
        $delete->execute(); 

        $count = DB::table('users')->count();

        $this->assertEquals($count, 2);
    }

    public function test_throw_exception_when_provided_values_is_empty()
    {
        $delete = new Delete("users");
        $delete->prepare([]);

        $this->expectExceptionMessage('$records can not be empty');
        $delete->execute();
    }
}
