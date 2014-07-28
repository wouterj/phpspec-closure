<?php

namespace WouterJ\PhpSpec\ClosureExtension\Node;

use PhpSpec\Loader\Node\ExampleNode as BaseExampleNode;

class ExampleNode extends BaseExampleNode
{
    /** @var \Closure */
    private $function;

    public function __construct($title, \Closure $function)
    {
        $this->function = $function;

        parent::__construct($title, new \ReflectionFunction($function));
    }

    public function getFunction()
    {
        return $this->function;
    }
}

