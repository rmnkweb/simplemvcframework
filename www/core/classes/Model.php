<?php

namespace Core;
use PDO;
use PDOException;
use Exception;
use Core\Constraints as Constraints;


class Model extends DatabaseTable {

    public function checkConstraints($values) {
        $constraints = new Constraints;
        $noError = true;
        foreach ($this->fields as $fieldName => $field) {
            if ((array_key_exists($fieldName, $values)) AND ($noError)) {
                if (array_key_exists("constraints", $field)) {
                    if ($constraints->check($field["constraints"], $values[$fieldName]) === false) {
                        $noError = false;
                    }
                }
            }
        }

        return $noError;
    }

}