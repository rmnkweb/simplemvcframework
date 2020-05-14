<?php

namespace Core;

use Core\Response as Response;
use Exception;

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
        $results = [];
        foreach ($this->dataset as $i => $data) {
            try {
                if (parent::insert($data)) {
                    $results[$i] = new Response(1);
                }
            } catch (Exception $e) {
                $results[$i] = new Response(0, $e->getMessage(), $e->getCode());
            }
        }
    }
}