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
 * <ul>
 *  <li>INITIAL</li>
 *  <li>NORMAL</li>
 *  <li>BREAKPOINT</li>
 *  <li>FINAL</li>
 * </ul>
 *
 * @author Frank Göldner <f-go@gmx.de>
 * @date   11.10.14 13:08
 */
abstract class StateTypes
{
    /**
     * This type of state indicates a initial state.
     *
     * <strong>Note:<strong><br/>
     * There must be only one state of this type!
     *
     * @var string
     */
    const TYPE_INITIAL = 'INITIAL';

    /**
     * This type of state indicates a normal state.
     *
     * @var string
     */
    const TYPE_NORMAL = 'NORMAL';

    /**
     * This type of state indicates a final state.
     *
     * <strong>Note:</strong>
     * To a state of this type, no further transitions can be applied.
     *
     * @var string
     */
    const TYPE_FINAL = 'FINAL';

    /**
     * This type of state indicates a breakpoint state.
     *
     * @var string
     */
    const TYPE_BREAKPOINT = 'BREAKPOINT';

    /**
     * List of all supported state types.
     *
     * @var string[]
     */
    static protected $supportedTypes = [
        self::TYPE_INITIAL,
        self::TYPE_NORMAL,
        self::TYPE_FINAL,
        self::TYPE_BREAKPOINT
    ];

    /**
     * Get a list of all supported state types.
     *
     * @return string[]
     */
    static public function getSupportedTypes()
    {
        return self::$supportedTypes;
    }

    /**
     * Check if the specified type is supported or not.
     *
     * <strong>Note:</strong><br/>
     * This check is case-insensitive.
     *
     * @param  string $type The type to check.
     *
     * @return bool Returns <em>true</em> if it is a supported state type or <em>false</em> if not.
     */
    static public function isSupportedType($type)
    {
        $type = strtoupper((string)$type);
        foreach (self::$supportedTypes as $supportedType) {
            if ($supportedType === $type) {
                return true;
            }
        }

        return false;
    }
}
