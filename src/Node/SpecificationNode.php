<?php

namespace WouterJ\PhpSpec\ClosureExtension\Node;

use PhpSpec\Loader\Suite;
use PhpSpec\Loader\Node\SpecificationNode as PhpSpecSpecificationNode;
use PhpSpec\Locator\ResourceInterface;

class SpecificationNode extends PhpSpecSpecificationNode
{
    /** @var SpecificationInterface */
    private $instance;

    public function __construct($title, $instance, ResourceInterface $resource)
    {
        $this->instance = $instance;

        parent::__construct($title, new \ReflectionClass($instance), $resource);
    }

    public function getInstance()
    {
        return $this->instance;
    }
}
