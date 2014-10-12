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

use FGo\StateMachine\Action\IAction;
use FGo\StateMachine\State\IState;

/**
 * This class …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 13:49
 */
class Transition implements ITransition
{
    /**
     * Name of this transition.
     *
     * @var string
     */
    protected $name = '';

    /**
     * List of possible input states.
     *
     * @var IState[]
     */
    protected $inputStates = [];

    /**
     * List of possible output states.
     *
     * @var IState[]
     */
    protected $outputStates = [];

    /**
     * Condition by which a decision is made about the output status.
     *
     * @var int
     */
    protected $condition = 0;

    /**
     * An optional action which is executed to make the decision about the output status.
     *
     * @var IAction|null
     */
    protected $action = null;


    /**
     * Initializes the new instance of this class.
     *
     * @param string $name The name of this transition.
     */
    public function __construct($name)
    {
        $this->setName($name);
    }
    /**
     * @inheritdoc
     */
    public function apply(IState $inputInputState)
    {
        if (!$this->hasInputState($inputInputState)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Could not apply transition "%s" for input state "%s".',
                    $this->getName(),
                    $inputInputState->getName()
                )
            );
        }

        if ($this->hasAction()) {
            $returnCode = $this->getAction()->execute();
            $this->setCondition($returnCode);
        }

        if (!$this->hasOutputState($this->getCondition())) {
            throw new \Exception(
                sprintf(
                    'No suitable output state found for the condition %d within the transition %s.',
                    $this->getCondition(),
                    $this->getName()
                )
            );
        }

        return $this->getOutputState($this->getCondition());
    }

    /**
     * @inheritdoc
     */
    public function can(IState $state)
    {
        return $this->hasInputState($state);
    }

    /**
     * Get the input state.
     *
     * @return IState[] Returns the list of input states.
     */
    protected function getInputStates()
    {
        return $this->inputStates;
    }

    /**
     * Check whether the given state is one of the possible input states.
     *
     * @param  IState $state The state to check.
     *
     * @return bool Returns <em>true</em> if an associated input state corresponds
     *              to the given one or <em>false</em> if not.
     */
    protected function hasInputState(IState $state)
    {
        foreach ($this->getInputStates() as $inputState) {
            if ($inputState->getName() === $state->getName()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of this transition.
     *
     * <strong>Note:</strong><br/>
     * All whitespaces (or other characters) from the beginning or end of the name
     * will be stripped ({@see trim}).
     *
     * @param string $name The name to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function setName($name)
    {
        $this->name = trim((string)$name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Check whether an action is associated.
     *
     * @return bool Returns <em>true</em> if an action is associated or <em>false</em> if not.
     */
    protected function hasAction()
    {
        return ($this->getAction() !== null);
    }

    /**
     * Get the associated action.
     *
     * @return IAction|null Returns the associated action or <em>null</em> if none is associated.
     */
    protected function getAction()
    {
        return $this->action;
    }

    /**
     * Set the condition.
     *
     * @param  int $condition The condition to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    protected function setCondition($condition)
    {
        $this->condition = (int)$condition;

        return $this;
    }

    /**
     * Get the current condition.
     *
     * @return int Returns the currently set condition.
     */
    protected function getCondition()
    {
        return $this->condition;
    }

    /**
     * Check whether a suitable output state is available for the given condition.
     *
     * @param  int $condition The condition for which the appropriate output status is sought.
     *
     * @return bool Returns <em>true</em> if an appropriated output status is available
     *              or <em>false</em> if not.
     */
    protected function hasOutputState($condition)
    {
        return isset($this->outputStates[(int)$condition]);
    }

    /**
     * Get appropriated output status for the given condition.
     *
     * @param  int $condition The condition for which the appropriate output status is sought.
     *
     * @return IState|null Returns the appropriated output status or <em>null</em> if none was found.
     */
    protected function getOutputState($condition)
    {
        if (!$this->hasOutputState($condition)) {
            return null;
        }

        return $this->outputStates[(int)$condition];
    }

    /**
     * Add a new state to the list of input states.
     *
     * @param IState $state The state to add.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function addInputState(IState $state)
    {
        $this->inputStates[] = $state;

        return $this;
    }

    /**
     * Add a new state to the list of output states.
     *
     * @param IState $state The state to add.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function addOutputState(IState $state)
    {
        $this->outputStates[] = $state;

        return $this;
    }

    /**
     * Associate an action to this transition.
     *
     * @param  IAction $action The action to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function setAction(IAction $action)
    {
        $this->action = $action;

        return $this;
    }
}
