<?php

/**
 * Core Query Builder
 * Provides fluent interface for building SQL queries dynamically
 * Follows MVC rule: centralized query building with __toString()
 */
class Core_Query {
    protected $select = [];
    protected $from = '';
    protected $joins = [];
    protected $where = [];
    protected $orderBy = [];
    protected $limit = null;
    protected $offset = null;
    protected $params = [];
    
    /**
     * Set SELECT columns
     * @param array|string $columns
     * @return $this
     */
    public function select($columns = ['*']) {
        if (is_string($columns)) {
            $columns = [$columns];
        }
        $this->select = $columns;
        return $this;
    }
    
    /**
     * Set FROM table
     * @param string $table
     * @return $this
     */
    public function from($table) {
        $this->from = $table;
        return $this;
    }
    
    /**
     * Add JOIN clause
     * @param string $table
     * @param string $condition
     * @param string $type LEFT|INNER|RIGHT
     * @return $this
     */
    public function join($table, $condition, $type = 'LEFT') {
        $this->joins[] = [
            'type' => strtoupper($type),
            'table' => $table,
            'condition' => $condition
        ];
        return $this;
    }
    
    /**
     * Add WHERE condition
     * @param string $condition
     * @param mixed $value Optional value for parameterized query
     * @return $this
     */
    public function where($condition, $value = null) {
        $this->where[] = $condition;
        if ($value !== null) {
            // Auto-generate parameter placeholder
            $paramName = 'param_' . count($this->params);
            $this->params[$paramName] = $value;
        }
        return $this;
    }
    
    /**
     * Add ORDER BY clause
     * @param string $column
     * @param string $direction ASC|DESC
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC') {
        $this->orderBy[] = $column . ' ' . strtoupper($direction);
        return $this;
    }
    
    /**
     * Set LIMIT
     * @param int $limit
     * @return $this
     */
    public function limit($limit) {
        $this->limit = (int)$limit;
        return $this;
    }
    
    /**
     * Set OFFSET
     * @param int $offset
     * @return $this
     */
    public function offset($offset) {
        $this->offset = (int)$offset;
        return $this;
    }
    
    /**
     * Get bound parameters
     * @return array
     */
    public function getParams() {
        return $this->params;
    }
    
    /**
     * Build and return SQL string
     * Uses __toString() magic method
     * @return string
     */
    public function __toString() {
        $sql = 'SELECT ';
        
        // SELECT
        $sql .= empty($this->select) ? '*' : implode(', ', $this->select);
        
        // FROM
        if (empty($this->from)) {
            throw new Exception('FROM table not specified in query');
        }
        $sql .= ' FROM ' . $this->from;
        
        // JOINS
        foreach ($this->joins as $join) {
            $sql .= ' ' . $join['type'] . ' JOIN ' . $join['table'] . ' ON ' . $join['condition'];
        }
        
        // WHERE
        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }
        
        // ORDER BY
        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }
        
        // LIMIT
        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        
        // OFFSET
        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }
        
        return $sql;
    }
    
    /**
     * Execute query and return results
     * @param Core_Database $db
     * @return array
     */
    public function fetchAll(Core_Database $db) {
        return $db->fetchAll((string)$this, $this->params);
    }
    
    /**
     * Execute query and return single row
     * @param Core_Database $db
     * @return array|false
     */
    public function fetchOne(Core_Database $db) {
        return $db->fetchOne((string)$this, $this->params);
    }
}
