<?php

namespace Core;

class Response {

    /**
     * @var int 0 - error occurred; 1 - everything is fine; 2 - warning.
     */
    private $status;
    /**
     * @var string description of response
     */
    private $message;
    /**
     * @var int detailed code of warning or error; must be not equal 0
     */
    private $code;

    /**
     * Response constructor.
     * @param int 0 - error occurred; 1 - everything is fine; 2 - warning.
     * @param string $message description of response
     * @param int $code detailed code of warning or error; must be not equal 0
     */
    public function __construct($status, $message = "", $code = 0) {
        $this->status = $status;
        if ($message !== "") {
            $this->message = $message;
        } else {
            $this->message = false;
        }
        if ($code !== 0) {
            $this->code = $code;
        } else {
            $this->code = false;
        }
    }

    /**
     * @return int
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @return array
     */
    public function getArray() {
        $array = [
            "status" => $this->status,
            "message" => $this->message,
            "code" => $this->code,
        ];

        return $array;
    }

    /**
     * @return false|string
     */
    public function getJSON() {
        return json_encode($this->getArray());
    }
}