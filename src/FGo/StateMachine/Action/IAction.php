<?php
/**
 * This file is part of the state-machine project.
 *
 * (c) Frank Göldner <f-go@gmx.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FGo\StateMachine\Action;

/**
 * This interface …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 12:32
 */
interface IAction
{
    /**
     * Execute this action.
     *
     * @return int Returns a return code.
     *
     * @throws \Exception This exception is thrown when something went wrong during the execution.
     */
    public function execute();
}
