<?php
/**
 * This file is part of the state-machine project.
 *
 * (c) Frank Göldner <f-go@gmx.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FGo\StateMachine\State;

/**
 * This interface …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 12:22
 */
interface IState
{
    /**
     * Get the name of this state.
     *
     * @return string
     */
    public function getName();

    /**
     * Get the type of this state (see {@see StateTypes}).
     *
     * @return string
     */
    public function getType();

    /**
     * Get the string representation of this state.
     *
     * @return string
     */
    public function __toString();
}
