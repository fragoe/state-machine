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
 * This class …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 12:59
 */
class State implements IState
{
    /**
     * Name of this state.
     *
     * @var string
     */
    protected $name = '';

    /**
     * Type of this state (see {@see StateTypes}).
     *
     * @var string
     */
    protected $type = StateTypes::TYPE_NORMAL;



    /**
     * Initializes the new instance of this class.
     *
     * @param  string $name The name of this state.
     * @param  string $type The type of this state.
     *
     * @return State Returns the new instance of this class.
     */
    public function __construct($name, $type)
    {
        $this->setName($name)->setType($type);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of this state.
     *
     * <strong>Note:</strong><br/>
     * All whitespaces (or other characters) from the beginning or end of the name
     * will be stripped ({@see trim}).
     *
     * @param  string $name The name to set.
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the type of this state.
     *
     * <strong>Note:</strong><br/>
     * This must be one of the pre-defined types (see {@see StateTypes})!
     *
     * @param  string $type The type to set.
     *
     * @return $this Returns the instance of this or a derived class.
     *
     * @throws \InvalidArgumentException This exception is thrown if the state to set is not supported.
     */
    public function setType($type)
    {
        $type = strtoupper(trim((string)$type));
        if (!StateTypes::isSupportedType($type)) {
            throw new \InvalidArgumentException(
                sprintf('Could not set type of state. "%s" is not a supported type.', (string)$type)
            );
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Get the string representation of this state.
     *
     * Currently this is the name of this state.
     *
     * @return string Returns the string representation.
     */
    public function __toString()
    {
        return $this->getName();
    }
}
