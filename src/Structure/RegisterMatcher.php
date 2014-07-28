<?php

namespace WouterJ\PhpSpec\ClosureExtension\Structure;

use WouterJ\PhpSpec\ClosureExtension\Node\ExampleNode;
use WouterJ\PhpSpec\ClosureExtension\DynamicObjectBehaviour;

class RegisterMatcher
{
    /** @var DynamicObjectBehavior */
    private $objectBehaviour;

    public function __construct(DynamicObjectBehaviour $objectBehaviour)
    {
        $this->objectBehaviour = $objectBehaviour;
    }

    public function __invoke($title, $function)
    {
        $this->objectBehaviour->_addMatcher($title, $function);
    }
}
