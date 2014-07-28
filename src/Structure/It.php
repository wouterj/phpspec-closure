<?php

namespace WouterJ\PhpSpec\ClosureExtension\Structure;

use WouterJ\PhpSpec\ClosureExtension\Node\ExampleNode;
use WouterJ\PhpSpec\ClosureExtension\DynamicObjectBehaviour;

class It
{
    /** @var DynamicObjectBehavior */
    private $objectBehaviour;
    private $onNewExampleListener;

    public function __construct(DynamicObjectBehaviour $objectBehaviour, $onNewExampleListener)
    {
        $this->objectBehaviour = $objectBehaviour;
        $this->onNewExampleListener = $onNewExampleListener;
    }

    public function __invoke($title, $function)
    {
        $function = $function->bindTo($this->objectBehaviour, $this->objectBehaviour);

        call_user_func($this->onNewExampleListener, new ExampleNode($title, $function));
    }
}
