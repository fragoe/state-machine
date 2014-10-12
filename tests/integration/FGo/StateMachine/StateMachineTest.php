<?php
/**
 * This file is part of the state-machine project.
 *
 * (c) Frank Göldner <f-go@gmx.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace FGo\StateMachine;

use FGo\StateMachine\Configuration\ArrayLoader;
use FGo\StateMachine\State\IState;

/**
 * This class …
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 19:53
 */
class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IStateMachine|null
     */
    protected $stateMachine = null;

    /**
     * @var IStatefulObject|null
     */
    protected $statefulObject = null;

    /**
     * @var AnyService|null
     */
    protected $anService = null;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->stateMachine = new StateMachine(new ArrayLoader());
        $this->statefulObject = new StatefulObject();
        $this->anService = new AnyService();
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->stateMachine);
        unset($this->statefulObject);
        unset($this->anService);

        parent::tearDown();
    }

    public function testUsage()
    {
        $config = [
            'states' => [
                'draft' => ['type' => 'INITIAL'],
                'proposed',
                'accepted',
                'published' => ['type' => 'FINAL'],
                'rejected' => ['type' => 'FINAL']
            ],
            'transitions' => [
                'propose' => [
                    'from' => 'draft',
                    'to' => 'proposed',
                    'action' => [
                        'object' => $this->anService,
                        'method' => 'methodX',
                        'arguments' => [42, 0]
                    ]
                ],
                'accept' => [
                    'from' => 'proposed',
                    'to' => 'accepted'
                ],
                'publish' => [
                    'from' => 'accepted',
                    'to' => 'published'
                ],
                'reject' => [
                    'from' => ['proposed', 'accepted'],
                    'to' => 'rejected'
                ]
            ]
        ];
        $this->assertSame($this->stateMachine, $this->stateMachine->configure($config));
        $this->assertTrue($this->stateMachine->initialize($this->statefulObject));

        $this->assertTrue($this->stateMachine->can('propose', $this->statefulObject));
        $this->assertFalse($this->stateMachine->can('accept', $this->statefulObject));
        $this->assertFalse($this->stateMachine->can('reject', $this->statefulObject));
        $this->assertFalse($this->stateMachine->can('publish', $this->statefulObject));

        $this->assertTrue($this->stateMachine->apply('propose', $this->statefulObject));

        $this->assertFalse($this->stateMachine->can('propose', $this->statefulObject));
        $this->assertTrue($this->stateMachine->can('accept', $this->statefulObject));
        $this->assertTrue($this->stateMachine->can('reject', $this->statefulObject));
        $this->assertFalse($this->stateMachine->can('publish', $this->statefulObject));

        $this->assertTrue($this->stateMachine->apply('accept', $this->statefulObject));

        $this->assertFalse($this->stateMachine->can('propose', $this->statefulObject));
        $this->assertFalse($this->stateMachine->can('accept', $this->statefulObject));
        $this->assertTrue($this->stateMachine->can('reject', $this->statefulObject));
        $this->assertTrue($this->stateMachine->can('publish', $this->statefulObject));

        $this->assertTrue($this->stateMachine->apply('reject', $this->statefulObject));

//        $this->assertEmpty('rejected', $this->statefulObject->getState()->getName());
//        $this->assertEmpty(StateTypes::TYPE_FINAL, $this->statefulObject->getState()->getType());
    }
}

class StatefulObject implements IStatefulObject
{
    /** @var IState|null */
    protected $state = null;

    public function getState() { return $this->state; }
    public function setState(IState $state) { $this->state = $state; return $this; }
}

class AnyService
{
    public function methodX($a, $b)
    {
        return $a * $b;
    }
}