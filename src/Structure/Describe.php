<?php

namespace WouterJ\PhpSpec\ClosureExtension\Structure;

class Describe
{
    private $onResourceChangedListener;

    public function __construct($onResourceChangedListener)
    {
        $this->onResourceChangedListener = $onResourceChangedListener;
    }

    public function __invoke($fqcn, $testMethod)
    {
        $testMethod();
    }
}
