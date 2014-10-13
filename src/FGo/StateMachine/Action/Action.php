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
     * The service that provides the method to invoke.
     *
     * @var null
     */
    protected $service = null;

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
     * Initializes the new instance of this class.
     *
     * @param object $service   The service that provides the method to be invoked.
     * @param string $method    The method to be invoked.
     * @param array  $arguments (optional; default: []) A list of arguments which are passed to the method.
     *
     * @return Action Returns the new instance of this class.
     */
    public function __construct($service, $method, array $arguments = [])
    {
        $this->setService($service)->setMethod($method)->setArguments($arguments);

        return $this;
    }
    /**
     * Get the associated service that provides the method to be invoked.
     *
     * @return object|null Returns the associated service or <em>null</em> if none was set.
     */
    protected function getService()
    {
        return $this->service;
    }

    /**
     * Set the service that provides the method to be invoked.
     *
     * @param  object $service The object to set.
     *
     * @return $this Returns the instance of this or a derived class.
     */
    protected function setService($service)
    {
        if (!is_object($service)) {
            throw new \InvalidArgumentException('The thing you tried to set as action object is not type of object.');
        }
        $this->service = $service;

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
     * @return array Returns the list of arguments.
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Set the list of arguments which are passed to the method.
     *
     * @param  array $arguments The list of arguments to set.
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
     * @param  mixed $argument The argument to add.
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
        return call_user_func_array([$this->getService(), $this->getMethod()], $this->getArguments());
    }
}
