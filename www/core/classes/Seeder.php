<?php

namespace Core;

use mysql_xdevapi\Exception;

class Seeder extends DatabaseTable {
    /**
     * @var array of data arrays where key is database table field title. Example: [0 => ["id" => 1]]
     */
    protected $dataset;

    public function __construct() {
        parent::__construct();

        $this->dataset = [];
    }

    protected function add($values) {
        $this->dataset[] = $values;

        return true;
    }

    public function table($table_name) {
        parent::set($table_name);
    }

    protected function seed() {
        $statuses = [];
        foreach ($this->dataset as $i => $data) {
            try {
                if (parent::insert($data)) {
                    $statuses[$i] = 1;
                }
            } catch (Exception $e) {
                $statuses[$i] = 0;
                echo $e->getMessage();
            }
        }
    }
}