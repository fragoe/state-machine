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
     * List of possible input states (associative).
     *
     * @var IState[]
     */
    protected $inputStatuses = [];

    /**
     * List of possible output states (indexed).
     *
     * @var IState[]
     */
    protected $outputStatuses = [];

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
     * Flag that indicates if this transition is the default one.
     *
     * <strong>Note:</strong><br/>
     * This is required if more than one transition for a state are also possible.
     *
     * @var bool
     */
    protected $default = false;


    /**
     * Initializes the new instance of this class.
     *
     * @param  string $name The name of this transition.
     *
     * @return Transition Returns the new instance of this class.
     */
    public function __construct($name)
    {
        $this->setName($name);

        return $this;
    }
    /**
     * @inheritdoc
     */
    public function apply(IState $state)
    {
        if (!$this->hasInputState($state)) {
            throw new \InvalidArgumentException(
                sprintf('Could not apply transition "%s" for input state "%s".', $this->getName(), $state->getName())
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
     * @return IState[] Returns the list (an associative array) of input states.
     */
    protected function getInputStatuses()
    {
        return $this->inputStatuses;
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
        return isset($this->inputStatuses[$state->getName()]);
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
     * Get the string representation of this transition.
     *
     * Currently this is the name of this transition.
     *
     * @return string Returns the string representation of this class.
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
        return isset($this->outputStatuses[(int)$condition]);
    }

    /**
     * Get appropriated output status for the given condition.
     *
     * @param  int $condition The condition for which the appropriate output status is sought.
     *
     * @return IState|null Returns the appropriated output state or <em>null</em> if none was found.
     */
    protected function getOutputState($condition)
    {
        if (!$this->hasOutputState($condition)) {
            return null;
        }

        return $this->outputStatuses[(int)$condition];
    }

    /**
     * Add a new state to the list of input states.
     *
     * @param  IState $state The state to add.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function addInputState(IState $state)
    {
        $this->inputStatuses[$state->getName()] = $state;

        return $this;
    }

    /**
     * Add a new state to the list of output states.
     *
     * @param  IState $state The state to add.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function addOutputState(IState $state)
    {
        $this->outputStatuses[] = $state;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * Set the flag that indicates if this transition is the default one.
     *
     * <strong>Note:</strong><br/>
     * This is required if more than one transition for a state are also possible.
     *
     * @return bool Returns <em>true</em> if this is the default one or <em>false</em> if not.
     */
    public function setDefault($status = true)
    {
        $this->default = (bool)$status;

        return $this;
    }
}
