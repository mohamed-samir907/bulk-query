<?php

namespace Mosamirzz\BulkQuery;

use RuntimeException;
use Mosamirzz\BulkQuery\Bulk;
use Illuminate\Support\Facades\DB;

class Insert extends Bulk
{
    /**
     * @inheritDoc
     */
    public function prepare($records)
    {
        foreach ($records as $record) {
            // (?,?,?)
            $val = "(";
            foreach ($this->columns as $column) {
                // add the value to the bindings
                if (isset($record[$column])) {
                    $this->bindings[] = $record[$column];
                } else {
                    $this->bindings[] = null;
                }

                // set placeholder instead of the value
                $val .= "?,";
            }

            $this->values[] = rtrim($val, ",") . ")";
        }
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        if (empty($this->values)) {
            throw new RuntimeException('$records can not be empty');
        }

        $table = $this->getTable();
        $columns = implode(",", $this->columns);
        $values = implode(",", $this->values);

        $query = "INSERT INTO {$table} ($columns) VALUES {$values}";
        return DB::insert($query, $this->bindings);
    }
}
