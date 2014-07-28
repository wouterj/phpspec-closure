<?php

namespace WouterJ\PhpSpec\ClosureExtension;

use PhpSpec\ObjectBehavior;

class DynamicObjectBehaviour extends ObjectBehavior
{
    private $matchers = array();

    public function _addMatcher($title, $function)
    {
        $this->matchers[$title] = $function;
    }

    public function getMatchers()
    {
        return $this->matchers;
    }
}
