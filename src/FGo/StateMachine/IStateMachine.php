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

use FGo\StateMachine\Config\InvalidConfigurationException;

/**
 * This interface …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 12:38
 */
interface IStateMachine
{
    /**
     * Configure this state machine.
     *
     * @param  mixed $config The configuration. The data type depends on the used configurator type.
     *
     * @return $this Returns the instance of this or a derived class.
     *
     * @throws InvalidConfigurationException This exception is thrown when the configuration is faulty.
     */
    public function configure($config);

    /**
     * Initializes the given object.
     *
     * <strong>Note:</strong><br/>
     * If this function returns <em>false</em> this means that the given object is already in state and this state
     * has not been changed.
     *
     * @param  IStatefulObject $object The object to initialize.
     *
     * @return bool Returns <em>true</em> if the object was initialized or <em>false</em> if its state has not
     *              been changed.
     *
     * @throws InvalidConfigurationException This exception is thrown when there was no initial state defined.
     */
    public function initialize(IStatefulObject $object);

    /**
     * Check whether the specified transition can be applied to the given object.
     *
     * @param  string          $transition The name of the transition to apply.
     * @param  IStatefulObject $object     The object to which the transition should be applied.p
     *
     * @return bool Returns <em>true</em> if transition can be applied or <em>false</em> if not.
     *
     * @throws InvalidConfigurationException This exception is thrown if the requested transition is not defined.
     */
    public function can($transition, IStatefulObject $object);

    /**
     * Apply the specified transition to the given object.
     *
     * <strong>Note:</strong><br/>
     * This function also returns <em>false</em> if the result state is equal to the from state.
     *
     * @param  string          $transition The name of the transition to apply.
     * @param  IStatefulObject $object     The object to which the transition is applied.
     *
     * @return bool Returns <em>true</em> if the transition was successfully applied or <em>false</em> if not.
     *
     * @throws InvalidConfigurationException This exception is thrown if the requested transition is not defined.
     * @throws \Exception This exception is thrown when no further transitions are possible because the object
     *                    is in a final state.
     */
    public function apply($transition, IStatefulObject $object);
}
