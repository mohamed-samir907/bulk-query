<?php

namespace Mosamirzz\BulkQuery;

use Illuminate\Database\Eloquent\Model;

abstract class Bulk
{
    /**
     * The table name.
     *
     * @var string
     */
    protected string $table;

    /**
     * The model we need to use.
     * if provided we will get the table name from the model object.
     *
     * @var Model
     */
    protected ?Model $model = null;

    /**
     * The columns that we will use in insert/update query.
     *
     * @var array
     */
    protected array $columns;

    /**
     * Thr query bindings.
     *
     * @var array
     */
    protected array $bindings = [];

    /**
     * The query values.
     *
     * @var array
     */
    protected array $values = [];

    /**
     * Where clause.
     *
     * @var array[]
     */
    protected array $where = [];

    /**
     * Create new Bulk.
     *
     * @param string $table
     */
    public function __construct(string $table)
    {
        $this->table = $table;
    }

    /**
     * Prepare the query.
     *
     * @param  array $records
     * @return void
     */
    abstract public function prepare(array $records);

    /**
     * Execute the query.
     *
     * @return void
     */
    abstract public function execute();

    /**
     * The model we need to use.
     *
     * @param string $model
     * @return void
     */
    public function useModel(string $model)
    {
        $this->model = new $model;
    }

    /**
     * The columns used in insert/update.
     *
     * @param  array $columns
     * @return void
     */
    public function useColumns(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * Where clause.
     *
     * @param  string $column
     * @param  string $operator
     * @param  mixed $value
     * @return $this
     */
    public function where(string $column, string $operator, $value)
    {
        $this->where[] = [$column, $operator, $value];

        return $this;
    }

    /**
     * Get the table name.
     *
     * @return void
     */
    protected function getTable(): string
    {
        if ($this->model) {
            return $this->model->getTable();
        }

        return $this->table;
    }

    /**
     * Get where as string.
     *
     * @return string
     */
    protected function getwhere(): string
    {
        $val = "";
        foreach ($this->where as $where) {
            $whereValue = end($where);
            $where[2] = "?";

            $val .= "AND " . implode(" ", $where);
            $this->bindings[] = $whereValue;
        }

        return rtrim($val, "AND");
    }

    protected function formatColumn($value)
    {
        return is_string($value) ? "\"" . $value . "\"" : $value;
    }
}
