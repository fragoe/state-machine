<?php
/**
 * This file is part of the state-machine project.
 *
 * (c) Frank Göldner <f-go@gmx.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FGo\StateMachine\Transition;

use FGo\StateMachine\State\IState;

/**
 * This interface …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 12:23
 */
interface ITransition
{
    /**
     * Get the name of this transition.
     *
     * @return string
     */
    public function getName();

    /**
     * Check whether this transition can be applied to the given input state.
     *
     * @param  IState $state The state to check.
     *
     * @return bool Returns <em>true</em> if this transition can be applied to the given
     *              input state or <em>false> if not.
     */
    public function can(IState $state);

    /**
     * Apply this transition.
     *
     * @param  IState $inputState The input state.
     *
     * @return IState Returns the result state. This also could be the previous state if the transition
     *                could not be applied.
     *
     * @throws \Exception This exception is thrown if there is no suitable to-state.
     */
    public function apply(IState $inputState);

    /**
     * Get the string representation of this transition.
     *
     * @return string
     */
    public function __toString();
}
