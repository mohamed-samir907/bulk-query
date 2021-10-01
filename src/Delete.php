<?php

namespace Mosamirzz\BulkQuery;

use RuntimeException;
use Mosamirzz\BulkQuery\Bulk;
use Illuminate\Support\Facades\DB;

class Delete extends Bulk
{
    /**
     * The key used to delete the records.
     *
     * @var string
     */
    private $key = "id";

    /**
     * change the default key used to delete the records.
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
        foreach ($records as $record) {
            $this->values[] = "?";
            $this->bindings[] = $record;
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
        $keyValues = implode(",", $this->values);

        $query = "DELETE FROM {$table} WHERE {$this->key} IN({$keyValues})";
        return DB::delete($query, $this->bindings);
    }
}
