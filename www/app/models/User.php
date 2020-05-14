<?php

namespace App\Models;
use Core\Model as Model;
use Core\Response as Response;

class User extends Model {

    public function __construct() {
        parent::__construct();

        $this->fields = [
            "id" => [
                "type" => "int",
                "nullable" => false,
                "autoincrement" => true,
            ],
            "name" => [
                "type" => "string",
                "nullable" => true,
                "autoincrement" => false,
                "constraints" => [
                    "length" => [
                        "from" => 4,
                        "to" => 20
                    ],
                    "match" => "/^[a-zA-Z0-9]+$/",
                ]
            ],
            "password" => [
                "type" => "string",
                "nullable" => true,
                "autoincrement" => false,
                "constraints" => [
                    "length" => [
                        "from" => 4,
                        "to" => 20
                    ],
                    "match" => "/^[a-zA-Z0-9]+$/",
                ]
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
     * @return Response
     */
    public function getUsers($filters = []) {
        return parent::select($filters);
    }

    /**
     * @param array $valuesArray Array of values to be inserted (field title as array keys).
     * @return Response
     */
    public function addUser($valuesArray = []) {
        if (array_key_exists("password", $valuesArray)) {
            $valuesArray["password"] = $this->encryptPassword($valuesArray["password"]);
        }
        return parent::insert($valuesArray);
    }

    /**
     * @param int $id Updating element id (comparing with table field id).
     * @param array $valuesArray Array of values to be inserted (field title as array keys).
     * @return Response
     */
    public function updateUser($id, $valuesArray) {
        if (array_key_exists("password", $valuesArray)) {
            $valuesArray["password"] = $this->encryptPassword($valuesArray["password"]);
        }
        return parent::update($id, $valuesArray);
    }

    /**
     * @param string $string password before encryption
     * @return bool|string
     */
    public function encryptPassword($string) {
        return password_hash($string, PASSWORD_BCRYPT, ["cost" => 12]);
    }


}