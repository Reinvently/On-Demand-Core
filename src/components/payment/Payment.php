<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\components\payment;


use reinvently\ondemand\core\components\eventmanager\EventInterface;
use yii\base\Component;

abstract class Payment extends Component implements EventInterface
{
    const EVENT_TOKEN = 'Payment_token';
    const EVENT_AUTH = 'Payment_auth';
    const EVENT_SALE = 'Payment_sale';
    const EVENT_CANCEL_AUTH = 'Payment_cancel_auth';
    const EVENT_CANCEL_SALE = 'Payment_cancel_sale';

    const RAISE_EVENT_TOKEN = 'Payment_raise_token';
    const RAISE_EVENT_AUTH = 'Payment_raise_auth';
    const RAISE_EVENT_SALE = 'Payment_raise_sale';
    const RAISE_EVENT_CANCEL_AUTH = 'Payment_raise_cancel_auth';
    const RAISE_EVENT_CANCEL_SALE = 'Payment_raise_cancel_sale';

} 