<?php


namespace Core;

use Exception;
use PDO;
use PDOException;
use Core\Response as Response;

class DatabaseTable extends Database {
    /**
     * @var string $table_name Contains database table name.
     */
    protected $table_name;
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

    public function __construct($table_name = "") {
        parent::__construct();

        if ($table_name != "") {
            $this->set($table_name);
        }
    }

    /**
     * @param $table_name string name of table in database
     * @return Response
     */
    public function set($table_name) {
        $this->table_name = htmlspecialchars($table_name);
        return $this->collectFields();
    }

    /**
     * Collecting all database field information of stored $table_name to $fields variable
     * @return Response
     */
    protected function collectFields() {
        $query = "DESCRIBE " . $this->table_name;
        try {
            $statement = $this->db->prepare($query);
            $statement->execute();
            if ($rows = $statement->fetchAll(PDO::FETCH_ASSOC)) {
                $this->fields = [];
                foreach($rows as $row) {
                    $this->fields[$row["Field"]] = [];
                    // TODO: foreign key check
                    if (strpos($row["Extra"], "auto_increment") !== false) {
                        $this->fields[$row["Field"]]["autoincrement"] = true;
                    } else {
                        $this->fields[$row["Field"]]["autoincrement"] = false;
                    }
                    if (strpos($row["Null"], "YES") !== false) {
                        $this->fields[$row["Field"]]["nullable"] = true;
                    } else {
                        $this->fields[$row["Field"]]["nullable"] = false;
                    }
                    if (strpos($row["Type"], "int") !== false) {
                        $this->fields[$row["Field"]]["type"] = "int";
                    } elseif (strpos($row["Type"], "float") !== false) {
                        $this->fields[$row["Field"]]["type"] = "float";
                    } elseif (strpos($row["Type"], "datetime") !== false) {
                        $this->fields[$row["Field"]]["type"] = "date";
                    } elseif (strpos($row["Type"], "date") !== false) {
                        $this->fields[$row["Field"]]["type"] = "date";
                    } elseif (strpos($row["Type"], "varchar") !== false) {
                        $this->fields[$row["Field"]]["type"] = "string";
                    }
                }
            }
            return new Response(1);
        } catch(PDOException $e) {
            return new Response(0, $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param array $filters Array of filter values (field title as array keys).
     * @return Response Returns fetched array of inserted items on success inside Response->data
     */
    protected function select($filters = []) {
        if ((is_array($filters)) && (!empty($filters))) {
            $whereStr = " WHERE ";

            foreach ($filters as $filterTitle => $filterValue) {
                if (array_key_exists($filterTitle, $this->fields)) {
                    if ($this->fields[$filterTitle]["type"] === "int") {
                        if (is_numeric($filterValue)) {
                            $whereStr .= $filterTitle . " = " . ((int) $filterValue) . " AND ";
                        } else {
                            return new Response(0, "DB->select error. Filter field " . $filterTitle . " value (" . $filterValue . ") is not numeric: int expected.", 1);
                        }
                    } elseif ($this->fields[$filterTitle]["type"] === "float") {
                        if (is_numeric($filterValue)) {
                            $whereStr .= $filterTitle . " = " . ((float) $filterValue) . " AND ";
                        } else {
                            return new Response(0, "DB->select error. Filter field " . $filterTitle . " value (" . $filterValue . ") is not numeric: float expected.", 1);
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

        $query = "SELECT * FROM `" . htmlspecialchars($this->table_name) . "`" . $whereStr;

        try {
            $statement = $this->db->prepare($query);
            $statement->execute();
            return new Response(1, false, false, $statement->fetchAll(PDO::FETCH_ASSOC));
        } catch(PDOException $e) {
            return new Response(0, $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param array $values Array of values to be inserted (field title as array keys).
     * @param bool $returnValues If true – inserted item array will be returned (with new ID). Default false.
     * @return Response Returns fetched array of inserted items on success inside Response->data (depends on $returnValues param)
     */
    protected function insert($values, $returnValues = false) {
        $queryFieldTitlesStr = "";
        $queryFieldValuesStr = "";
        foreach ($this->fields as $fieldTitle => $field) {

            if ($field["autoincrement"] === false) {
                $queryFieldTitlesStr .= "`" . $fieldTitle . "`, ";

                if (array_key_exists($fieldTitle, $values)) {
                    if ($field["type"] === "int") {
                        if (is_numeric($values[$fieldTitle])) {
                            $queryFieldValuesStr .= ((int) $values[$fieldTitle]) . ", ";
                        } else {
                            return new Response(0, "DB->insert error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") is not numeric: int expected.", 1);
                        }
                    } elseif ($field["type"] === "float") {
                        if (is_numeric($values[$fieldTitle])) {
                            $queryFieldValuesStr .= ((float) $values[$fieldTitle]) . ", ";
                        } else {
                            return new Response(0, "DB->insert error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") is not numeric: float expected.", 1);
                        }
                    } elseif ($field["type"] === "date") {
                        if ((preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $values[$fieldTitle], $matches)) AND
                            (checkdate($matches[2],$matches[3],$matches[1]))) {
                            $queryFieldValuesStr .= "\"" . $values[$fieldTitle] . "\", ";
                        } else {
                            return new Response(0, "DB->insert error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") have wrong format: date expected.", 1);
                        }
                    } elseif ($field["type"] === "datetime") {
                        if ((preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $values[$fieldTitle], $matches)) AND
                            (checkdate($matches[2],$matches[3],$matches[1]))) {
                            $queryFieldValuesStr .= "\"" . $values[$fieldTitle] . "\", ";
                        } else {
                            return new Response(0, "DB->insert error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") have wrong format: datetime expected.", 1);
                        }
                    } else {
                        $queryFieldValuesStr .= "\"" . htmlspecialchars($values[$fieldTitle]) . "\", ";
                    }
                } else {
                    if ($field["nullable"]) {
                        $queryFieldValuesStr .= "NULL, ";
                    } else {
                        return new Response(0, "DB->insert error. Field " . $fieldTitle . " value not found. Can't be null.");
                    }
                }
            }
        }
        $queryFieldTitlesStr = substr($queryFieldTitlesStr, 0, -2);
        $queryFieldValuesStr = substr($queryFieldValuesStr, 0, -2);

        $query = "INSERT INTO `" . htmlspecialchars($this->table_name) . "` (" . $queryFieldTitlesStr . ") VALUES (" . $queryFieldValuesStr . ")";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute();
            if ($returnValues === true) {
                if (((isset($this->fields["id"])) && ($this->fields["id"]["autoincrement"] !== false)) || (!isset($this->fields["id"]))) {
                    $values["id"] = $this->db->lastInsertId();
                }
                return new Response(1, false, false, $values);
            } else {
                return new Response(1);
            }
        } catch(PDOException $e) {
            return new Response(0, $e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param int $id of updating element
     * @param array $values Array of values to be inserted (field title as array keys).
     * @return Response Returns true on success OR false on error.
     */
    protected function update($id, $values) {

        if (count($values)) {
            $querySetStr = " SET ";

            foreach ($values as $fieldTitle => $value) {

                if (array_key_exists($fieldTitle, $this->fields)) {
                    if ($this->fields[$fieldTitle]["autoincrement"] === false) {
                        if ($this->fields[$fieldTitle]["type"] === "int") {
                            if (is_numeric($values[$fieldTitle])) {
                                $querySetStr .= $fieldTitle . " = " . ((int) $values[$fieldTitle]) . ", ";
                            } else {
                                return new Response(0, "DB->update error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") is not numeric: int expected.", 1);
                            }
                        } elseif ($this->fields[$fieldTitle]["type"] === "float") {
                            if (is_numeric($values[$fieldTitle])) {
                                $querySetStr .= $fieldTitle . " = " . ((float) $values[$fieldTitle]) . ", ";
                            } else {
                                return new Response(0, "DB->update error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") is not numeric: float expected.", 1);
                            }
                        } elseif ($this->fields[$fieldTitle]["type"] === "date") {
                            if ((preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $values[$fieldTitle], $matches)) AND
                                (checkdate($matches[2],$matches[3],$matches[1]))) {
                                $querySetStr .= $fieldTitle . " = " . "\"" . $values[$fieldTitle] . "\", ";
                            } else {
                                return new Response(0, "DB->update error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") have wrong format: date expected.", 1);
                            }
                        } elseif ($this->fields[$fieldTitle]["type"] === "datetime") {
                            if ((preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $values[$fieldTitle], $matches)) AND
                                (checkdate($matches[2],$matches[3],$matches[1]))) {
                                $querySetStr .= $fieldTitle . " = " . "\"" . $values[$fieldTitle] . "\", ";
                            } else {
                                return new Response(0, "DB->update error. Field " . $fieldTitle . " value (" . $values[$fieldTitle] . ") have wrong format: datetime expected.", 1);
                            }
                        } else {
                            $querySetStr .= $fieldTitle . " = " . "\"" . htmlspecialchars($values[$fieldTitle]) . "\", ";
                        }
                    }
                } else {
                    return new Response(0, "DB->update error. Field with name " . $fieldTitle . " not found.");
                }
            }
            $querySetStr = substr($querySetStr, 0, -2);

            $query = "UPDATE `" . htmlspecialchars($this->table_name) . "`" . $querySetStr . " WHERE id = $id";

            try {
                $statement = $this->db->prepare($query);
                $statement->execute();
                return new Response(1);
            } catch(PDOException $e) {
                return new Response(0, $e->getMessage(), $e->getCode());
            }

        } else {
            return new Response(2, "Empty values array given.");
        }
    }
}