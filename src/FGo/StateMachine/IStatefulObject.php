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

use FGo\StateMachine\State\IState;

/**
 * This interface …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 12:40
 */
interface IStatefulObject
{
    /**
     * Get the current state of this object.
     *
     * @return IState Returns the current state.
     */
    public function getState();

    /**
     * Set the current state of this object.
     *
     * @param  IState $state The state to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function setState(IState $state);
}
 