<?php

namespace Core;

/**
 * Class Response using to connect objects/methods between each others. Attributes can only be set during initialization.
 * @package Core
 */
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
     * @var mixed data to be returned from object/method
     */
    private $data;

    /**
     * Response constructor.
     * @param int 0 - error occurred; 1 - everything is fine; 2 - warning.
     * @param string $message description of response
     * @param int $code detailed code of warning or error; must be not equal 0
     * @param mixed $data to be returned from object/method
     */
    public function __construct($status, $message = "", $code = 0, $data = false) {
        $this->status = $status;
        if ($message != false) {
            $this->message = $message;
        } else {
            $this->message = false;
        }
        if ($code != false) {
            $this->code = $code;
        } else {
            $this->code = false;
        }
        if ($data !== false) {
            $this->data = $data;
        } else {
            $this->data = false;
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
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getArray() {
        $array = [
            "status" => $this->status,
            "message" => $this->message,
            "code" => $this->code,
            "data" => $this->data,
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