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
 * This class …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 18:38
 */
class Action implements IAction
{
    /**
     * Object that provides the method to invoke.
     *
     * @var null
     */
    protected $object = null;

    /**
     * The method to be invoked.
     *
     * @var string
     */
    protected $method = '';

    /**
     * List of arguments which are passed to the method.
     *
     * @var array
     */
    protected $arguments = [];


    /**
     * Initialize a new instance of this class.
     *
     * @param object $object    The object that provides the method to be invoked.
     * @param string $method    The method to be invoked.
     * @param array  $arguments (optional; default: []) A list of arguments which are passed to the method.
     */
    public function __construct($object, $method, array $arguments = [])
    {
        $this->setObject($object)->setMethod($method)->setArguments($arguments);
    }
    /**
     * Get the associated object that provides the method to be invoked.
     *
     * @return object|null Returns the associated object or <em>null</em> if none was set.
     */
    protected function getObject()
    {
        return $this->object;
    }

    /**
     * Set the associated object that provides the method to be invoked.
     *
     * @param  object $object The object to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    protected function setObject($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException(
                'The thing you tried to set as action object is not type of object.'
            );
        }
        $this->object = $object;

        return $this;
    }

    /**
     * Get the name of the method which would be invoked.
     *
     * @return string Returns the method name.
     */
    protected function getMethod()
    {
        return $this->method;
    }

    /**
     * Set the name of the method which would be invoked.
     *
     * @param  string $method The method name to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    protected function setMethod($method)
    {
        $this->method = trim((string)$method);

        return $this;
    }

    /**
     * Get the list of arguments which are passed to the method.
     *
     * @return array Returns the list of argments.
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Set the list of arguments which are passed to the method.
     *
     * @param array $arguments The list of arguments to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Add an arguments to the list of arguments which are passed to the method.
     *
     * @param mixed $argument The argument to add.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;

        return $this;
    }

    /**
     * Execute this action.
     *
     * @return int Returns a return code.
     *
     * @throws \Exception This exception is thrown when something went wrong during the execution.
     */
    public function execute()
    {
        return call_user_func_array([$this->getObject(), $this->getMethod()], $this->getArguments());
    }
}
