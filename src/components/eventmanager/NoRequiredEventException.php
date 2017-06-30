<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\components\eventmanager;


use reinvently\ondemand\core\exceptions\LogicException;
use Exception;

class NoRequiredEventException extends LogicException
{
    /** @var string */
    protected $eventName;

    /**
     * @param string $eventName
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct($eventName, $message = '', $code = 0, Exception $previous = null)
    {
        $this->eventName = $eventName;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'No Required Event ' . $this->eventName;
    }


}