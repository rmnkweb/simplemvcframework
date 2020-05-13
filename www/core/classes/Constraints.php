<?php


namespace Core;


class Constraints {

    // TODO: make detailed return
    public function check($constraints, $value) {
        $noError = true;
        if ((array_key_exists("length", $constraints)) AND ($noError)) {
            $noError = $this->checkLength($constraints["length"], $value);
        }
        if ((array_key_exists("match", $constraints)) AND ($noError)) {
            $noError = $this->checkMatch($constraints["match"], $value);
        }

        return $noError;
    }

    private function checkLength($constraint, $value) {
        if ((array_key_exists("from", $constraint)) AND (array_key_exists("to", $constraint))) {
            $valueLength = strlen($value);
            if (($constraint["from"] < $constraint["to"]) AND ($constraint["from"] <= $valueLength) AND ($constraint["to"] >= $valueLength)) {
                return true;
            }
        }

        return false;
    }
    private function checkMatch($constraint, $value) {
        if (preg_match($constraint, $value)) {
            return true;
        } else {
            return false;
        }
    }
}