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

use FGo\StateMachine\State\IState;
use FGo\StateMachine\Transition\ITransition;

/**
 * This interface …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 15:40
 */
interface IConfigurator
{
    /**
     * Load the given config.
     *
     * @param mixed $config The configuration to load. The data type depends on the used configurator type.
     */
    public function load($config);

    /**
     * Get a list of all states.
     *
     * @return IState[] Returns an indexed array of all defined states.
     */
    public function getStateList();

    /**
     * Get a list of all transitions.
     *
     * @return ITransition[] Returns an indexed array of all defined transitions.
     */
    public function getTransitionList();

    /**
     * Get the initial state.
     *
     * @return IState|null Returns the initial state or <em>null</em> if none was defined.
     */
    public function getInitialState();
}
