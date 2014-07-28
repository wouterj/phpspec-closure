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
        $container->setShared('loader.resource_loader', function ($c) {
            return new ResourceLoader($c->get('locator.resource_manager'));
        });

        $container->setShared('runner.example', function ($c) {
            $runner = new Runner\ExampleRunner(
                $c->get('event_dispatcher'),
                $c->get('formatter.presenter')
            );

            array_map(
                array($runner, 'registerMaintainer'),
                $c->getByPrefix('runner.maintainers')
            );

            return $runner;
        });
    }
}
