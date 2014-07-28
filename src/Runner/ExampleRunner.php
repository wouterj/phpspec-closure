<?php

namespace WouterJ\PhpSpec\ClosureExtension\Runner;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\Runner\Maintainer\LetAndLetgoMaintainer;
use PhpSpec\Formatter\Presenter\PresenterInterface;
use PhpSpec\SpecificationInterface;
use PhpSpec\Event\ExampleEvent;
use PhpSpec\Loader\Node\ExampleNode as PhpSpecExampleNode;
use PhpSpec\Runner\ExampleRunner as BaseExampleRunner;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;

use PhpSpec\Exception\Exception as PhpSpecException;
use PhpSpec\Exception\Example as ExampleException;
use Prophecy\Exception as ProphecyException;
use Exception;

class ExampleRunner extends BaseExampleRunner
{
    /** @var EventDispatcherInterface */
    private $dispatcher;
    /** @var PresenterInterface */
    private $presenter;
    /** @var MaintainerInterface[] */
    private $maintainers = array();

    public function __construct(EventDispatcherInterface $dispatcher, PresenterInterface $presenter)
    {
        $this->dispatcher = $dispatcher;
        $this->presenter = $presenter;
    }

    /**
     * @param MaintainerInterface $maintainer
     */
    public function registerMaintainer(MaintainerInterface $maintainer)
    {
        $this->maintainers[] = $maintainer;

        @usort($this->maintainers, function ($maintainer1, $maintainer2) {
            return $maintainer2->getPriority() - $maintainer1->getPriority();
        });
    }

    public function run(PhpSpecExampleNode $example)
    {
        if ($example instanceof ExampleNode) {
            return parent::run($example);
        }

        $startTime = microtime(true);
        $this->dispatcher->dispatch('beforeExample',
            new ExampleEvent($example)
        );

        try {
            $this->executeExample(
                $example->getSpecification()->getInstance(),
                $example
            );

            $status    = ExampleEvent::PASSED;
            $exception = null;
        } catch (ExampleException\PendingException $e) {
            $status    = ExampleEvent::PENDING;
            $exception = $e;
        } catch (ProphecyException\Prediction\PredictionException $e) {
            $status    = ExampleEvent::FAILED;
            $exception = $e;
        } catch (ExampleException\FailureException $e) {
            $status    = ExampleEvent::FAILED;
            $exception = $e;
        } catch (Exception $e) {
            $status    = ExampleEvent::BROKEN;
            $exception = $e;
        }

        if ($exception instanceof PhpSpecException) {
            $exception->setCause($example->getFunctionReflection());
        }

        $runTime = microtime(true) - $startTime;
        $this->dispatcher->dispatch('afterExample',
            $event = new ExampleEvent($example, $runTime, $status, $exception)
        );

        return $event->getResult();
    }

    protected function executeExample(SpecificationInterface $context, PhpSpecExampleNode $example)
    {
        if ($example->isPending()) {
            throw new ExampleException\PendingException;
        }

        $matchers      = new MatcherManager($this->presenter);
        $collaborators = new CollaboratorManager($this->presenter);
        $maintainers   = array_filter($this->maintainers, function ($maintainer) use ($example) {
            return $maintainer->supports($example);
        });

        // run maintainers prepare
        foreach ($maintainers as $maintainer) {
            $maintainer->prepare($example, $context, $matchers, $collaborators);
        }

        // execute example
        $reflection = $example->getFunctionReflection();

        try {
            call_user_func_array($example->getFunction(), $collaborators->getArgumentsFor($reflection));
        } catch (\Exception $e) {
            $this->runMaintainersTeardown(
                $this->searchExceptionMaintainers($maintainers),
                $example,
                $context,
                $matchers,
                $collaborators
            );
            throw $e;
        }

        $this->runMaintainersTeardown($maintainers, $example, $context, $matchers, $collaborators);
    }

    /**
     * @param $maintainers
     * @param $example
     * @param $context
     * @param $matchers
     * @param $collaborators
     */
    private function runMaintainersTeardown($maintainers, $example, $context, $matchers, $collaborators)
    {
        foreach (array_reverse($maintainers) as $maintainer) {
            $maintainer->teardown($example, $context, $matchers, $collaborators);
        }
    }

    /**
     * @param Maintainer\MaintainerInterface[] $maintainers
     * @return Maintainer\MaintainerInterface[]
     */
    private function searchExceptionMaintainers($maintainers)
    {
        return array_filter(
            $maintainers,
            function ($maintainer) {
                return $maintainer instanceof LetAndLetgoMaintainer;
            }
        );
    }
}
