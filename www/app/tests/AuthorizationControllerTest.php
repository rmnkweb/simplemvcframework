<?php


namespace App\Tests;
use Core\UnitTest as UnitTest;

/**
 * Class AuthorizationControllerTest
 * @package App\Tests
 * @deprecated 
 */
class AuthorizationControllerTest {
    /** @var string Full name of testing class. */
    private $className;

    public function __construct() {
        $this->className = "App\\Controllers\\AuthorizationController";
    }

    private function testAuthorize() {
        $cases = [
            "normal_case" => [
                "arguments" => [
                    "username" => "admin2",
                    "password" => "admin123",
                ],
                "expect" => true,
            ],
        ];
        $unitTest = new UnitTest($this->className, "authorize", $cases);
        $results = $unitTest->launch(true);

        return $results;
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
        $results["testAddUser"] = $this->testAuthorize();

        return $results;
    }
}