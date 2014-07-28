Feature: Using Assertions
    In order to verify object behaviour
    As a testing developer
    I need to use PHPspec assertions

    Background:
        Given the closure extension is enabled

    Scenario: Using build-in assertions
        Given the class file "StringCalculator1.php" contains:
            """
            <?php

            class StringCalculator1
            {
                public function add($str)
                {
                    if (!is_string($str)) throw new \InvalidArgumentException('It\'s a **String**Calculator!');

                    return array_reduce(explode(',', $str), function ($acc, $i) {
                        return $acc += (int) $i;
                    }, 0);
                }
            }
            """
        And the spec file "spec/StringCalculator1Spec.php" contains:
            """
            <?php

            $describe('StringCalculator', function () use ($it) {
                
                $it ('adds values seperated by commas', function () {
                    $this->add('1,2,3')->shouldBe(6);
                });

                $it ('does only accept strings', function () {
                    $this->shouldThrow('InvalidArgumentException')->duringAdd([]);
                });

            });
            """
        When I run phpspec
        Then the suite should pass

    Scenario: Using custom matchers
        Given the class file "StringCalculator2.php" contains:
            """
            <?php

            class StringCalculator2
            {
                public function multiply($str)
                {
                    if (!is_string($str)) throw new \InvalidArgumentException('It\'s a **String**Calculator!');

                    return array_reduce(explode(',', $str), function ($acc, $i) {
                        return $acc *= (int) $i;
                    }, 0);
                }
            }
            """
        And the spec file "spec/StringCalculator2Spec.php" contains:
            """
            <?php

            $describe('StringCalculator2', function () use ($it) {
                $registerMatcher('BeTheMultiplyOf', function ($subject, $one, $two) {
                    return $subject === $one * $two;
                });

                $it ('multiplies values seperated by commas', function () {
                    $this->multiply('4,3')->shouldBeTheMultiplyOf(2, 6);
                });

            });
            """
        When I run phpspec
        Then the suite should pass
