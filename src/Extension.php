<?php

namespace WouterJ\PhpSpec\ClosureExtension;

use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ServiceContainer;

/**
 * The ClosureExtension allows you to write better looking spec files with the
 * same functionality as PHPspec.
 *
 * @author Wouter J <wouter@wouterj.nl>
 */
class Extension implements ExtensionInterface
{
    public function load(ServiceContainer $container)
    {
    }
}
