<?php

namespace App\Models;
use Core\Model as Model;

class Users extends Model {

    /**
     * @var array $fields Array of database fields.
     * Key is field title.
     * Values: [
     *   @string type – field type [int/float/string/date];
     *   @boolean nullable – if field value can be null;
     *   @boolean autoincrement – if field is auto incrementing;
     * ]
     */
    protected $fields;
    /**
     * @var string $table_name Contains database table name.
     */
    protected $table_name;

    public function __construct() {
        parent::__construct();

        $this->fields = [
            "id" => [
                "type" => "int",
                "nullable" => false,
                "autoincrement" => true,
            ],
            "username" => [
                "type" => "string",
                "nullable" => true,
                "autoincrement" => false,
            ],
            "password" => [
                "type" => "string",
                "nullable" => true,
                "autoincrement" => false,
            ],
            "fullname" => [
                "type" => "string",
                "nullable" => true,
                "autoincrement" => false,
            ],
            "dob" => [
                "type" => "date",
                "nullable" => true,
                "autoincrement" => false,
            ],
            "image_upload_id" => [
                "type" => "int",
                "nullable" => true,
                "autoincrement" => false,
            ],
        ];
        $this->table_name = "users";
    }

    /**
     * @param array $filters Array of filter values (field title as array keys).
     * @return array|bool
     */
    public function getUser($filters = []) {
        return parent::select($this->table_name, $this->fields, $filters);
    }

    /**
     * @param array $valuesArray Array of values to be inserted (field title as array keys).
     * @return array|bool
     */
    public function addUser($valuesArray = []) {
        return parent::insert($this->table_name, $this->fields, $valuesArray);
    }

    /**
     * @param int $id Updating element id (comparing with table field id).
     * @param array $valuesArray Array of values to be inserted (field title as array keys).
     * @return array|bool
     */
    public function updateUser($id, $valuesArray) {
        return parent::update($this->table_name, $this->fields, $id, $valuesArray);
    }


}