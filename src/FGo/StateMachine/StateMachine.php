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

use FGo\StateMachine\Configuration\IConfigLoader;
use FGo\StateMachine\Configuration\InvalidConfigurationException;
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
     * List of all defined transitions.
     *
     * @var ITransition[]
     */
    protected $transitionList = [];

    /**
     * Configuration loader instance.
     *
     * @var IConfigLoader
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
     * @param IConfigLoader            $configLoader    A config loader.
     * @param EventDispatcherInterface $eventDispatcher An event dispatcher.
     */
    public function __construct(IConfigLoader $configLoader, EventDispatcherInterface $eventDispatcher = null)
    {
        $this->configLoader = $configLoader;
        $this->eventDispatcher = $eventDispatcher ?: new EventDispatcher();
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
     * Get the initial state from the state list.
     *
     * @return IState Returns the initial state.
     *
     * @throws InvalidConfigurationException This exception is thrown when there was no initial state defined.
     */
    protected function getInitialState()
    {
        foreach ($this->stateList as $state) {
            if ($state->getType() === StateTypes::TYPE_INITIAL) {
                return $state;
            }
        }

        throw new InvalidConfigurationException('No state is defined as the initial state.');
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
        foreach ($this->transitionList as $transition) {
            if ($transition->getName() === $name) {
                return $transition;
            }
        }

        throw new InvalidConfigurationException(sprintf('Transition "%s" is not defined.'), $name);
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
     * Check if an event dispatcher instance is set.
     *
     * @return bool Return <em>true</em> if an event dispatcher instance is set or <em>false</em> if not.
     */
    protected function hasEventDispatcher()
    {
        return ($this->eventDispatcher !== null);
    }
    /**
     * @inheritdoc
     */
    public function apply($transition, IStatefulObject $object)
    {
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
