<?php

namespace Core;

class Response {

    public function getArray() {
        $array = [];

        return $array;
    }

    public function getJSON() {
        return json_encode($this->getArray());
    }
}