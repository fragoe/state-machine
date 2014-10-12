<?php
/**
 * This file is part of the state-machine project.
 *
 * (c) Frank Göldner <f-go@gmx.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FGo\StateMachine\Event;

use FGo\StateMachine\IStatefulObject;
use FGo\StateMachine\IStateMachine;
use FGo\StateMachine\State\IState;

/**
 * This class …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   12.10.14 09:47
 */
class StateEvent extends StateMachineEvent
{
    /**
     * Object's state before the transition has been applied.
     *
     * @var IState
     */
    protected $fromState = null;

    /**
     * Object's state after the transition has been applied.
     *
     * @var IState
     */
    protected $toState = null;



    /**
     * Initializes a new instance of this class.
     *
     * @param IStateMachine   $stateMachine The state machine that handles the object.
     * @param IStatefulObject $object       The object to which the transition should be or has been applied.
     * @param IState          $fromState    The object's state before the transition has been applied.
     * @param IState          $toState      The object's state after the transition has been applied.
     *
     * @return StateEvent Returns the new instance of this class.
     */
    function __construct(IStateMachine $stateMachine, IStatefulObject $object, IState $fromState, IState $toState)
    {
        parent::__construct($stateMachine, $object);

        $this->setFromState($fromState)->setToState($toState);

        return $this;
    }

    /**
     * Get object's state before the transition has been applied.
     *
     * @return IState Returns a state object.
     */
    public function getFromState()
    {
        return $this->fromState;
    }

    /**
     * Set object's state before the transition has been applied.
     *
     * @param  IState $state The state to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    protected function setFromState(IState $state)
    {
        $this->fromState = $state;

        return $this;
    }

    /**
     * Get object's state after the transition has been applied.
     *
     * @return IState Returns a state object.
     */
    public function getToState()
    {
        return $this->toState;
    }

    /**
     * Set object's state after the transition has been applied.
     *
     * @param  IState $state The state to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    protected function setToState(IState $state)
    {
        $this->toState = $state;

        return $this;
    }
}
