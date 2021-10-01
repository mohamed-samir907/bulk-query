<?php

namespace Mosamirzz\BulkQuery;

use RuntimeException;
use Mosamirzz\BulkQuery\Bulk;
use Illuminate\Support\Facades\DB;

/**
 * This query updates the data only when it 
 * find a `unique` or `primary key`.
 */
class InsertOrUpdate extends Bulk
{
    /**
     * The columns that will be updated on duplicate key.
     *
     * @var array
     */
    private array $updatableColumns;

    /**
     * The columns that will be updated on duplicate key.
     *
     * @param  array $columns
     * @return void
     */
    public function updatableColumns(array $columns)
    {
        $this->updatableColumns = $columns;
    }

    /**
     * @inheritDoc
     */
    public function prepare(array $records)
    {
        $placeholder = $this->getColumnsPlaceholder();

        foreach ($records as $record) {
            $this->values[] = $placeholder;

            foreach ($this->columns as $column) {
                $this->bindings[] = $record[$column];
            }
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
        $updatable = $this->getUpdatable();

        $query = "INSERT INTO `{$table}` ({$columns}) VALUES {$values}\tON DUPLICATE KEY UPDATE {$updatable}";
        return DB::insert($query, $this->bindings);
    }

    /**
     * Get the values placeholder.
     * 
     * Return value must be like "(?, ?, ?)"
     * 
     * @return string
     */
    private function getColumnsPlaceholder()
    {
        $placeholder = "(";

        for ($i = 0; $i < count($this->columns); $i++) {
            $placeholder .= "?,";
        }

        return rtrim($placeholder, ",") . ")";
    }

    /**
     * Get the updatable values.
     *
     * It must be like "column1 = VALUES(column1), column2 = VALUES(column2)"
     * 
     * @return string
     */
    private function getUpdatable()
    {
        $updatable = "";

        foreach ($this->updatableColumns as $column) {
            $updatable .= "{$column} = VALUES({$column}),";
        }

        return trim(
            rtrim($updatable, ",")
        );
    }
}
