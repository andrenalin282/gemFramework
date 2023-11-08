<?php

namespace GemFramework\Traits\Table;

/**
 * this trait will insert object into database
 * @method insert()
 */
trait InsertTrait
{
    /**
     * @insert current instance into Database
     *
     * @will return lastInsertedId
     *
     * @you can call affectedRows() and it shall be 1
     *
     * @error: $this->getError();
     */
    public function insert(): int|null
    {
        $table = $this->setTable();
        if(!$table)
        {
            $this->setError('table is not setted in function setTable');
            return null;
        }
        $columns = '';
        $params = '';
        $arrayBind = [];
        $query = "INSERT INTO {$table} ";
        // @phpstan-ignore-next-line
        foreach ($this as $key => $value) {
            $columns .= $key . ',';
            $params .= ':' . $key . ',';
            $arrayBind[':' . $key] = $value;
        }
        $columns = rtrim($columns, ',');
        $params = rtrim($params, ',');

        $query .= " ({$columns}) values ({$params})";
        return $this->insertQuery($query, $arrayBind);
    }
}
