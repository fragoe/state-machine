<?php
/**
 * This file is part of the state-machine project.
 *
 * (c) Frank Göldner <f-go@gmx.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FGo\StateMachine;

use FGo\StateMachine\Config\IConfigurator;
use FGo\StateMachine\Config\InvalidConfigurationException;
use FGo\StateMachine\Event\StateEvent;
use FGo\StateMachine\Event\StateMachineEvent;
use FGo\StateMachine\Event\TransitionEvent;
use FGo\StateMachine\State\IState;
use FGo\StateMachine\State\StateTypes;
use FGo\StateMachine\Transition\ITransition;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This class …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 14:31
 */
class StateMachine implements IStateMachine
{
    /**
     * List of all defined states.
     *
     * @var IState[]
     */
    protected $stateList = [];

    /**
     * List of all defined transitions (associative).
     *
     * @var ITransition[]
     */
    protected $transitionList = [];

    /**
     * Configurator instance.
     *
     * @var IConfigurator
     */
    protected $configLoader = null;

    /**
     * Event dispatcher instance.
     *
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher = null;



    /**
     * Initializes the new instance of this class.
     *
     * @param IConfigurator            $configLoader    A config loader.
     * @param EventDispatcherInterface $eventDispatcher An event dispatcher.
     *
     * @return StateMachine Returns the new instance of this class.
     */
    public function __construct(IConfigurator $configLoader, EventDispatcherInterface $eventDispatcher = null)
    {
        $this->configLoader = $configLoader;
        $this->eventDispatcher = $eventDispatcher ?: new EventDispatcher();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function configure($config)
    {
        $this->configLoader->load($config);

        $this->stateList = $this->configLoader->getStateList();
        $this->transitionList = $this->configLoader->getTransitionList();

        return $this;
    }

    /**
     * Get the initial state from the list of statuses.
     *
     * @return IState Returns the initial state.
     *
     * @throws InvalidConfigurationException This exception is thrown when there was no initial state defined.
     */
    protected function getInitialState()
    {
        $state = $this->configLoader->getInitialState();
        if ($state === null) {
            throw new InvalidConfigurationException('No state is defined as the initial state.');
        }

        return $state;
    }

    /**
     * @inheritdoc
     */
    public function initialize(IStatefulObject $object)
    {
        if ($object->getState() === null) {
            $this->eventDispatcher->dispatch('onBeforeInitialize', new StateMachineEvent($this, $object));

            $initialState = $this->getInitialState();
            $object->setState($initialState);

            $this->eventDispatcher->dispatch('onAfterInitialize', new StateMachineEvent($this, $object));

            return true;
        }

        return false;
    }

    /**
     * Get a transition by its name.
     *
     * @param  string $name The name of the transition.
     *
     * @return ITransition Returns the transition found.
     *
     * @throws InvalidConfigurationException This exception is thrown if the requested transition is not defined.
     */
    protected function getTransitionByName($name)
    {
        $name = trim((string)$name);
        if (!isset($this->transitionList[$name])) {
            throw new InvalidConfigurationException(sprintf('Transition "%s" is not defined.'), $name);
        }

        return $this->transitionList[$name];
    }

    /**
     * @inheritdoc
     */
    public function can($transition, IStatefulObject $object)
    {
        $transition = $this->getTransitionByName($transition);
        return $transition->can($object->getState());
    }

    /**
     * @inheritdoc
     */
    public function apply($transition, IStatefulObject $object)
    {
        // Check whether the transition is known
        $transition = $this->getTransitionByName($transition);
        if (!$transition->can($object->getState())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Could not apply transition "%s" to object in state "%s".',
                    $transition->getName(),
                    $object->getState()->getName()
                )
            );
        }

        // Check if the object is in a final state
        if ($object->getState()->getType() === StateTypes::TYPE_FINAL) {
            throw new \Exception('No further transitions possible because the object is in a final state.');
        }

        $fromState = $object->getState();
        $this->eventDispatcher->dispatch(
            'onBeforeApplyTransition', new TransitionEvent($this, $object, $fromState, $fromState, $transition)
        );

        $toState = $transition->apply($object->getState());
        $stateChanged = ($toState !== $fromState);
        if ($stateChanged) {
            $this->eventDispatcher->dispatch(
                'onBeforeStateChange', new StateEvent($this, $object, $fromState, $toState)
            );
            $object->setState($toState);
            $this->eventDispatcher->dispatch(
                'onAfterStateChange', new StateEvent($this, $object, $fromState, $toState)
            );
        }

        $this->eventDispatcher->dispatch(
            'onAfterApplyTransition', new TransitionEvent($this, $object, $fromState, $toState, $transition)
        );

        return $stateChanged;
    }
}
