<?php

namespace Core;

class UnitTest {
    /** @var string Full name of testing class, including namespaces. Example: App\Controllers\MainController. */
    protected $testingClassName;

    /** @var string Full name of testing unit inside of class defined in $testingClassName. Example: getUser. */
    protected $testingUnitName;

    /**
     * $cases = [
     *   "first_case" => [
     *     "arguments" => [                        array of arguments for testing unit; no key required; strict order; all arguments are required in test
     *       @var,
     *       @var,
     *       @var,
     *     ],
     *     "expect" => @var;                       value returned when test succeed
     *   ],
     * ];
     * @var array Array of testing values and expectations (see above). Key is test case name.
     */
    protected $cases;

    public function __construct($testingClassName = "", $testingUnitName = "", $cases = []) {
        $this->testingClassName = $testingClassName;
        $this->testingUnitName = $testingUnitName;
        $this->cases = $cases;
    }

    /**
     * Launching test with provided values.
     * @return array
     */
    public function launch() {
        $results = [];
        foreach ($this->cases as $caseTitle => $case) {
            try {
                $object = new $this->testingClassName();

                $result = call_user_func_array([$object, $this->testingUnitName], $case["arguments"]);
                if ($result === $case["expect"]) {
                    $results[$caseTitle] = [
                        "result" => $result,
                        "status" => 1
                    ];
                } elseif ($result == $case["expect"]) {
                    $results[$caseTitle] = [
                        "result" => $result,
                        "status" => 2,
                        "error" => [
                            "message" => "Type mismatch."
                        ],
                    ];
                } else {
                    $results[$caseTitle] = [
                        "result" => $result,
                        "status" => 0,
                        "error" => [
                            "message" => "Result doesn't match expected value."
                        ],
                    ];
                }
            } catch (\Exception $e) {
                $results[$caseTitle] = [
                    "error" => [
                        "message" => $e->getMessage(),
                        "code" => $e->getCode(),
                    ],
                ];
                if ($case["expect"] === "exception") {
                    $results[$caseTitle]["status"] = 1;
                } else {
                    $results[$caseTitle]["status"] = 0;
                }
            }
        }

        return $results;
    }


    /**
     * @return string
     */
    public function getTestingClassName() {
        return $this->testingClassName;
    }

    /**
     * @param string $testingClassName
     */
    public function setTestingClassName($testingClassName) {
        $this->testingClassName = $testingClassName;
    }

    /**
     * @return string
     */
    public function getTestingUnitName() {
        return $this->testingUnitName;
    }

    /**
     * @param string $testingUnitName
     */
    public function setTestingUnitName($testingUnitName) {
        $this->testingUnitName = $testingUnitName;
    }

    /**
     * @return mixed
     */
    public function getCases() {
        return $this->cases;
    }

    /**
     * @param mixed $cases
     */
    public function setCases($cases) {
        $this->cases = $cases;
    }
}