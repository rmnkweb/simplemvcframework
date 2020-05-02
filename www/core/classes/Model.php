<?php

namespace Core;
use PDOException;
use Exception;


class Model {

    /**
     * @var object PDO object connected to database.
     */
    protected $db;
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

    function __construct () {

        if (PHP_SAPI === 'cli') {
            global $cli;

            $this->db = &$cli->db;
        } else {
            global $app;

            $this->db = &$app->db;
        }

    }

    /**
     * @param string $table_name Name of database table.
     * @param array $fields Array of database fields with types (field title as array keys).
     * @param array $filters Array of filter values (field title as array keys).
     * @return array|bool Returns fetched array of selected items on success OR returns false on error.
     */
    protected function select($table_name, $fields = [], $filters = []) {
        if ((is_array($filters)) && (!empty($filters))) {
            $whereStr = " WHERE ";

            foreach ($filters as $filterTitle => $filterValue) {
                if (array_key_exists($filterTitle, $fields)) {
                    if ($fields[$filterTitle]["type"] === "int") {
                        if (is_numeric($filterValue)) {
                            $whereStr .= $filterTitle . " = " . ((int) $filterValue) . " AND ";
                        } else {
                            throw new Exception("DB->select error. Filter field " . $filterTitle . " value (" . $filterValue . ") is not numeric: int expected.", 1);
                        }
                    } elseif ($fields[$filterTitle]["type"] === "float") {
                        if (is_numeric($filterValue)) {
                            $whereStr .= $filterTitle . " = " . ((float) $filterValue) . " AND ";
                        } else {
                            throw new Exception("DB->select error. Filter field " . $filterTitle . " value (" . $filterValue . ") is not numeric: float expected.", 1);
                        }
                    } else {
                        $whereStr .= $filterTitle . " = " .  "\"" . htmlspecialchars($filterValue) . "\" AND ";
                    }
                }
            }

            $whereStr = substr($whereStr, 0, -5);
        } else {
            $whereStr = "";
        }

        $query = "SELECT * FROM `" . htmlspecialchars($table_name) . "`" . $whereStr;

        try {
            $statement = $this->db->prepare($query);
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * @param string $table_name Name of database table.
     * @param array $fields Array of database fields with types (field title as array keys).
     * @param array $values Array of values to be inserted (field title as array keys).
     * @param bool $returnValues If true – inserted item array will be returned (with new ID). Default false.
     * @return array|bool Returns fetched array of inserted items on success OR (bool) true (depends on $returnValues param). Returns false on error.
     * @throws Exception
     */
    protected function insert($table_name, $fields, $values, $returnValues = false) {
        $queryFieldTitlesStr = "";
        $queryFieldValuesStr = "";
        foreach ($fields as $fieldTitle => $field) {

            if ($field["autoincrement"] === false) {
                $queryFieldTitlesStr .= "`" . $fieldTitle . "`, ";

                if (array_key_exists($fieldTitle, $values)) {
                    if ($field["type"] === "int") {
                        if (is_numeric($values[$fieldTitle])) {
                            $queryFieldValuesStr .= ((int) $values[$fieldTitle]) . ", ";
                        } else {
                            throw new Exception("DB->insert error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") is not numeric: int expected.", 1);
                        }
                    } elseif ($field["type"] === "float") {
                        if (is_numeric($values[$fieldTitle])) {
                            $queryFieldValuesStr .= ((float) $values[$fieldTitle]) . ", ";
                        } else {
                            throw new Exception("DB->insert error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") is not numeric: float expected.", 1);
                        }
                    } elseif ($field["type"] === "date") {
                        if ((preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $values[$fieldTitle], $matches)) AND
                            (checkdate($matches[2],$matches[3],$matches[1]))) {
                            $queryFieldValuesStr .= "\"" . $values[$fieldTitle] . "\", ";
                        } else {
                            throw new Exception("DB->insert error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") have wrong format: date expected.", 1);
                        }
                    } elseif ($field["type"] === "datetime") {
                        if ((preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $values[$fieldTitle], $matches)) AND
                            (checkdate($matches[2],$matches[3],$matches[1]))) {
                            $queryFieldValuesStr .= "\"" . $values[$fieldTitle] . "\", ";
                        } else {
                            throw new Exception("DB->insert error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") have wrong format: datetime expected.", 1);
                        }
                    } else {
                        $queryFieldValuesStr .= "\"" . htmlspecialchars($values[$fieldTitle]) . "\", ";
                    }
                } else {
                    if ($field["nullable"]) {
                        $queryFieldValuesStr .= "NULL, ";
                    } else {
                        throw new Exception("DB->insert error. Field " . $fieldTitle . " value not found. Can't be null.");
                    }
                }
            }
        }
        $queryFieldTitlesStr = substr($queryFieldTitlesStr, 0, -2);
        $queryFieldValuesStr = substr($queryFieldValuesStr, 0, -2);

        $query = "INSERT INTO `" . htmlspecialchars($table_name) . "` (" . $queryFieldTitlesStr . ") VALUES (" . $queryFieldValuesStr . ")";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute();
            if ($returnValues === true) {
                if (((isset($fields["id"])) && ($fields["id"]["autoincrement"] !== false)) || (!isset($fields["id"]))) {
                    $values["id"] = $this->db->lastInsertId();
                }
                return $values;
            } else {
                return true;
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * @param string $table_name Name of database table.
     * @param array $fields Array of database fields with types (field title as array keys).
     * @param array $values Array of values to be inserted (field title as array keys).
     * @return bool Returns true on success OR false on error.
     * @throws Exception
     */
    protected function update($table_name, $fields, $id, $values) {

        if (count($values)) {
            $querySetStr = " SET ";

            foreach ($values as $fieldTitle => $value) {

                if (array_key_exists($fieldTitle, $fields)) {
                    if ($fields[$fieldTitle]["autoincrement"] === false) {
                        if ($fields[$fieldTitle]["type"] === "int") {
                            if (is_numeric($values[$fieldTitle])) {
                                $querySetStr .= $fieldTitle . " = " . ((int) $values[$fieldTitle]) . ", ";
                            } else {
                                throw new Exception("DB->update error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") is not numeric: int expected.", 1);
                            }
                        } elseif ($fields[$fieldTitle]["type"] === "float") {
                            if (is_numeric($values[$fieldTitle])) {
                                $querySetStr .= $fieldTitle . " = " . ((float) $values[$fieldTitle]) . ", ";
                            } else {
                                throw new Exception("DB->update error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") is not numeric: float expected.", 1);
                            }
                        } elseif ($fields[$fieldTitle]["type"] === "date") {
                            if ((preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $values[$fieldTitle], $matches)) AND
                                (checkdate($matches[2],$matches[3],$matches[1]))) {
                                $querySetStr .= $fieldTitle . " = " . "\"" . $values[$fieldTitle] . "\", ";
                            } else {
                                throw new Exception("DB->update error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") have wrong format: date expected.", 1);
                            }
                        } elseif ($fields[$fieldTitle]["type"] === "datetime") {
                            if ((preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $values[$fieldTitle], $matches)) AND
                                (checkdate($matches[2],$matches[3],$matches[1]))) {
                                $querySetStr .= $fieldTitle . " = " . "\"" . $values[$fieldTitle] . "\", ";
                            } else {
                                throw new Exception("DB->update error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") have wrong format: datetime expected.", 1);
                            }
                        } else {
                            $querySetStr .= $fieldTitle . " = " . "\"" . htmlspecialchars($values[$fieldTitle]) . "\", ";
                        }
                    }
                } else {
                    throw new Exception("DB->update error. Field with name " . $fieldTitle . " not found.");
                }
            }
            $querySetStr = substr($querySetStr, 0, -2);

            $query = "UPDATE `" . htmlspecialchars($table_name) . "`" . $querySetStr . " WHERE id = $id";

            try {
                $statement = $this->db->prepare($query);
                $statement->execute();
                return true;
            } catch(PDOException $e) {
                echo $e->getMessage();
                return false;
            }

        } else {
            return false;
        }
    }

}