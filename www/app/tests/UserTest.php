<?php

namespace App\Tests;
use Core\UnitTest as UnitTest;

class UserTest {
    /** @var string Full name of testing class. */
    private $className;

    public function __construct() {
        $this->className = "App\\Models\\User";
    }

    private function testUpdateUser() {
        $cases = [
            "first_case" => [
                "arguments" => [],
                "expect" => true,
            ],
        ];
        $unitTest = new UnitTest($this->className, "updateUser", $cases);
    }

    private function testAddUser() {
        $cases = [
            "empty_case" => [
                "arguments" => [],
                "expect" => true,
            ],
            "normal_case" => [
                "arguments" => [
                    "valuesArray" => [
                        "username" => "Tester",
                        "password" => "verytestfullpassword",
                        "fullname" => "Testerov Test Testerovich",
                        "dob" => "2010-12-02",
                    ]
                ],
                "expect" => true,
            ],
            "wrong_dob" => [
                "arguments" => [
                    "valuesArray" => [
                        "dob" => "12.02.2010",
                    ]
                ],
                "expect" => "exception",
            ],
        ];
        $unitTest = new UnitTest($this->className, "addUser", $cases);
        $results = $unitTest->launch(true);

        return $results;
    }

    private function testGetUser() {
        $cases = [
            "first_case" => [
                "arguments" => [],
                "expect" => true,
            ],
        ];
        $unitTest = new UnitTest($this->className, "getUser", $cases);
    }

    /**
     * $results = [
     *   "test1" => [
     *     "case1" => [
     *       "status"= ...
     *       etc...
     *     ],
     *   ],
     * ];
     * @return array of results which contains array of test results and where key is test name. Full structure above.
     */
    public function test() {
        $results = [];
        $results["testAddUser"] = $this->testAddUser();

        return $results;
    }
}