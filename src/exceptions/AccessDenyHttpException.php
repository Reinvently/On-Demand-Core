<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\exceptions;


use yii\web\HttpException;

class AccessDenyHttpException extends HttpException
{

    /**
     * Constructor.
     * @param string $message error message
     * @param integer $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = 'Access deny: You don\'t have permissions', $code = 0, \Exception $previous = null)
    {
        parent::__construct(403, $message, $code, $previous);
    }

} 