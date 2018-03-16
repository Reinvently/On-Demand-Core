<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 16.03.2018
 * Time: 12:59
 */

namespace app\exceptions;

use reinvently\ondemand\core\exceptions\UserException;
use Throwable;

class PaymentDuplicateException extends UserException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        $message = 'You already have this card saved on your account. Please try adding a different card',
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

}