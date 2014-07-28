<?php

namespace WouterJ\PhpSpec\ClosureExtension;

use PhpSpec\Loader\Suite;;
use PhpSpec\Loader\ResourceLoader as PhpSpecResourceLoader;
use PhpSpec\Locator\ResourceManager;

class ResourceLoader extends PhpSpecResourceLoader
{
    /** @var ResourceManager */
    private $manager;
    private $resource;
    private $examples = array();

    public function __construct(ResourceManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param string       $locator
     * @param integer|null $line
     *
     * @return Suite
     */
    public function load($locator, $line = null)
    {
        $suite = new Suite;
        $resources = $this->manager->locateResources($locator);

        foreach ($resources as $resource) {
            $objectBehaviour = new DynamicObjectBehaviour();
            $this->resource = $resource;

            $describe = new Structure\Describe(array($this, 'onResourceChanged'));
            $it = new Structure\It($objectBehaviour, array($this, 'onNewExample'));

            $this->loadFile($resource->getSpecFilename(), array(
                'describe' => $describe,
                'it' => $it,
            ));

            $spec = new Node\SpecificationNode($resource->getSrcClassname(), $objectBehaviour, $this->resource);
            foreach ($this->examples as $example) {
                $spec->addExample($example);
            }

            $suite->addSpecification($spec);

            $this->resetContext();
        }

        return $suite;
    }

    public function onResourceChanged($resource)
    {
        $this->resource = $resource;
    }

    public function onNewExample($example)
    {
        $this->examples[] = $example;
    }

    protected function loadFile($path, $structureFunctions)
    {
        extract($structureFunctions);

        require_once $path;
    }

    protected function resetContext()
    {
        $this->example = array();
        $this->resource = null;
    }
}
