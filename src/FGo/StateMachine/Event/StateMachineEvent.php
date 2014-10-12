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
use Symfony\Component\EventDispatcher\Event;

/**
 * This class …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   12.10.14 09:46
 */
class StateMachineEvent extends Event
{
    /**
     * State machine that handles the object.
     *
     * @var IStateMachine
     */
    protected $stateMachine = null;

    /**
     * Object that is handled by the state machine.
     *
     * @var IStatefulObject
     */
    protected $object = null;



    /**
     * Initializes the new instance of this class.
     *
     * @param IStateMachine   $stateMachine The state machine that handles the object.
     * @param IStatefulObject $object       The object that is handled by the state machine.
     *
     * @return StateMachineEvent Returns the new instance of this class.
     */
    public function __construct(IStateMachine $stateMachine, IStatefulObject $object)
    {
        $this->setStateMachine($stateMachine)->setObject($object);

        return $this;
    }

    /**
     * Get the state machine that handles the object.
     *
     * @return IStateMachine Returns the state machine.
     */
    public function getStateMachine()
    {
        return $this->stateMachine;
    }

    /**
     * Set the state machine that handles the object.
     *
     * @param  IStateMachine $stateMachine A state machine instance.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    protected function setStateMachine(IStateMachine $stateMachine)
    {
        $this->stateMachine = $stateMachine;

        return $this;
    }

    /**
     * Get the object that is handled by the state machine.
     *
     * @return IStatefulObject Returns the object.
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set the object that is handled by the state machine.
     *
     * @param  IStatefulObject $object The object to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    protected function setObject(IStatefulObject $object)
    {
        $this->object = $object;

        return $this;
    }
}
