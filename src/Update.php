<?php

namespace Mosamirzz\BulkQuery;

use RuntimeException;
use Mosamirzz\BulkQuery\Bulk;
use Illuminate\Support\Facades\DB;

class Update extends Bulk
{
    /**
     * The key we use to update the record.
     *
     * @var string
     */
    private string $key = "id";

    /**
     * The values of the key in each record.
     *
     * @var array
     */
    private array $keyValues;

    /**
     * Change the key that we use to update the record.
     *
     * @param  string $key
     * @return void
     */
    public function useKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * @inheritDoc
     */
    public function prepare(array $records)
    {
        if (empty($records)) {
            throw new RuntimeException('$records can not be empty');
        }

        $this->keyValues = array_keys($records);
        $table = $this->getTable();

        // The columns that we need to update.
        // column1 = (
        //   CASE id
        //     WHEN 1 THEN value1
        //     WHEN 2 THEN value2
        //     WHEN 3 THEN value3
        //   END
        // )
        foreach ($this->columns as $column) {
            $case = $column . " = (\n";
            $case .= "\tCASE `{$this->key}`\n";

            // The column value that we use to update the record like id
            // The $key is the value of column id this value can be changed
            // if the $this->key is changed to another column in the table.
            // The default of $this->key is `id`
            foreach ($this->keyValues as $key) {
                if (array_key_exists($column, $records[$key])) {
                    $value = "?";
                    $this->bindings[] = $records[$key][$column];
                } else {
                    $value = "`{$table}`.`{$column}`";
                }

                $case .= "\t\tWHEN " . $this->formatColumn($key) . " THEN " . $value . "\n";
            }

            $case .= "\tEND\n)";
            $this->values[] = $case;
        }
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $table = $this->getTable();
        $values = implode(",", $this->values);
        $where = $this->getwhere();

        $query = "UPDATE {$table} SET {$values} WHERE {$this->key} IN(";
        foreach ($this->keyValues as $val) {
            $query .= "?,";
            $this->bindings[] = $val;
        }
        $query = rtrim($query, ",");
        $query .= ") {$where}";

        return DB::update($query, $this->bindings);
    }
}
