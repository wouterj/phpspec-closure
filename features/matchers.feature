Feature: Using Assertions
    In order to verify object behaviour
    As a testing developer
    I need to use PHPspec assertions

    Background:
        Given the class file "StringCalculator.php" contains:
            """
            class StringCalculator
            {
                public function add($str)
                {
                    if (!is_string($str)) throw new \InvalidArgumentException('It\'s a **String**Calculator!');

                    return array_reduce(function ($acc, $i) {
                        return $acc += (int) $i;
                    }, explode(',', $str), 0);
                }

                public function multiply($str)
                {
                    if (!is_string($str)) throw new \InvalidArgumentException('It\'s a **String**Calculator!');

                    return array_reduce(function ($acc, $i) {
                        return $acc *= (int) $i;
                    }, explode(',', $str), 0);
                }
            }
            """

    Scenario: Using build-in assertions
        Given the spec file "spec/StringCalculator.php" contains:
            """
            describe('StringCalculator', function () {
                
                it ('adds values seperated by commas', function () {
                    $this->add('1,2,3')->shouldBe(6);
                });

                it ('does only accept strings', function () {
                    $this->shouldThrow('InvalidArgumentException')->duringAdd([]);
                });

            });
            """
        When I run phpspec
        Then the suite should pass

    Scenario: Using custom matchers
        Given the spec file "spec/StringCalculator.php" contains:
            """
            describe('StringCalculator', function () {
                matchers(array(
                    'BeTheMultiplyOf' => function ($subject, $one, $two) {
                        return $subject === $one * $two;
                    },
                ));

                it ('multiplies values seperated by commas', function () {
                    $this->multiply('4,3')->shouldBeTheMultiplyOf(2, 6);
                });

            });
            """
        When I run phpspec
        Then the suite should pass
