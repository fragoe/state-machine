<?php
/**
 * This file is part of the state-machine project.
 *
 * (c) Frank Göldner <f-go@gmx.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FGo\StateMachine\Config;

use FGo\StateMachine\Action\Action;
use FGo\StateMachine\State\IState;
use FGo\StateMachine\State\State;
use FGo\StateMachine\State\StateTypes;
use FGo\StateMachine\Transition\ITransition;
use FGo\StateMachine\Transition\Transition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 15:40
 */
class ArrayConfigurator implements IConfigurator
{
    /**
     * Config default values.
     *
     * @var array
     */
    protected $defaultConfig = [
        'states' => [],
        'transitions' => []
    ];

    /**
     * List of all defined statuses (associative array).
     *
     * @var IState[]
     */
    protected $stateList = [];

    /**
     * List of all defined transitions (associative array).
     *
     * @var array
     */
    protected $transitionList = [];

    /**
     * The initial state.
     *
     * @var null|IState
     */
    protected $initialState = null;



    /**
     * Load the given config.
     *
     * @param array $config The config to be loaded.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function load($config)
    {
        if (!is_array($config)) {
            throw new InvalidConfigurationException('The given configuration must be of type array.');
        }
        $config = array_merge($this->defaultConfig, $config);

        $this->stateList = $this->loadStates($config['states']);
        $this->transitionList = $this->loadTransitions($config['transitions']);

        if ($this->getInitialState() === null) {
            throw new InvalidConfigurationException('No state is defined as the initial state.');
        }

        return $this;
    }

    /**
     * Load all defined states from the given config.
     *
     * @param  array $states The states configuration.
     *
     * @return IState[] Returns a list of states.
     */
    protected function loadStates(array $states)
    {
        $this->initialState = null;  // reset the initial state

        $stateResolver = new OptionsResolver();
        $stateResolver
            ->setDefaults(['type' => StateTypes::TYPE_NORMAL])
            ->setAllowedValues(['type' => StateTypes::getSupportedTypes()])
            ->setNormalizers(
                [
                    'type' => function(Options $o, $value) { $o->count(); return strtoupper(trim((string)$value)); }
                ]
            );

        $stateList = [];
        foreach ($states as $name => $config) {
            if (is_string($config)) {
                $name   = (string)$config;
                $config = [];
            }
            $config = $stateResolver->resolve($config);

            $state = new State($name, $config['type']);
            if ($state->getType() === StateTypes::TYPE_INITIAL) {
                if ($this->getInitialState() !== null) {
                    throw new InvalidConfigurationException('Only one state can be defined as the initial state.');
                }
                $this->setInitialState($state);
            }
            $stateList[$state->getName()] = $state;
        }

        return $stateList;
    }

    /**
     * Get state from the list of states by its name.
     *
     * @param  string $name The name of the state.
     *
     * @return IState|null Returns the state or <em>null</em> if none was found for the given name.
     */
    protected function getStateByName($name)
    {
        $name = trim((string)$name);
        if (!isset($this->stateList[$name])) {
            return null;
        }

        return $this->stateList[$name];
    }

    /**
     * Load all defined transitions from the given config.
     *
     * @param  array $transitions The transitions configuration.
     *
     * @return ITransition[] Returns a list of states.
     */
    protected function loadTransitions(array $transitions)
    {
        $transitionResolver = new OptionsResolver();
        $transitionResolver
            ->setRequired(['from', 'to'])
            ->setOptional(['default', 'action'])
            ->setDefaults(['default' => false])
            ->setNormalizers(
                [
                    'from'    => function(Options $o, $value) { $o->count(); return (array)$value; },
                    'to'      => function(Options $o, $value) { $o->count(); return (array)$value; },
                    'default' => function(Options $o, $value) { $o->count(); return (bool)$value; },
                    'action'  => function(Options $o, $value) { $o->count(); return !isset($value) ? null : (array)$value; }
                ]
            );
        $actionResolver = new OptionsResolver();
        $actionResolver
            ->setRequired(['service', 'method'])
            ->setOptional(['arguments'])
            ->setDefaults(['arguments' => []]);

        $transitionList = [];
        foreach ($transitions as $name => $config) {
            $transition = new Transition($name);
            $config = $transitionResolver->resolve($config);
            foreach ($config['from'] as $stateName) {
                $state = $this->getStateByName($stateName);
                if ($state === null) {
                    throw new InvalidConfigurationException(
                        sprintf(
                            'The state "%s" defined as a from-state for transition "%s" is not defined.',
                            trim((string)$stateName),
                            $transition->getName()
                        )
                    );
                }
                $transition->addInputState($state);
            }

            foreach ($config['to'] as $stateName) {
                $state = $this->getStateByName($stateName);
                if ($state === null) {
                    throw new InvalidConfigurationException(
                        sprintf(
                            'The state "%s" defined as a to-state for transition "%s" is not defined.',
                            trim((string)$stateName),
                            $transition->getName()
                        )
                    );
                }
                $transition->addOutputState($state);
            }

            $transition->setDefault($config['default']);

            if (isset($config['action'])) {
                $config = $actionResolver->resolve($config['action']);
                if (!is_object($config['service'])) {
                    throw new InvalidConfigurationException(
                        'The thing you provided as an object is not type of object.'
                    );
                }
                if ((string)$config['method'] === '') {
                    throw new InvalidConfigurationException(
                        'The name of the method to be invoked must not be empty.'
                    );
                }
                $action = new Action($config['service'], $config['method'], $config['arguments']);

                $transition->setAction($action);
            }

            $transitionList[$transition->getName()] = $transition;
        }

        return $transitionList;
    }

    /**
     * @inheritdoc
     */
    public function getStateList()
    {
        return $this->stateList;
    }

    /**
     * @inheritdoc
     */
    public function getTransitionList()
    {
        return $this->transitionList;
    }

    /**
     * @inheritdoc
     */
    public function getInitialState()
    {
        return $this->initialState;
    }

    /**
     * Set the initial state.
     *
     * @param  IState $state The initial state.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    protected function setInitialState(IState $state)
    {
        $this->initialState = $state;

        return $this;
    }
}
