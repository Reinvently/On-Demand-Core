<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 23.03.2018
 * Time: 14:40
 */

namespace reinvently\ondemand\core\modules\order\strategies\orderinvoice\models;

use reinvently\ondemand\core\modules\order\strategies\orderinvoice\traits\OrderInvoice;

abstract class Order extends \reinvently\ondemand\core\modules\order\models\Order
{
    use OrderInvoice;

}