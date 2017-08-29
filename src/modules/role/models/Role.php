<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\role\models;

use Yii;
use \yii\base\Model;

class Role extends Model
{
    const SYSTEM = 0;
    const ADMIN = 1;
    const USER = 2;
    const GUEST = 3;
    const DRIVER = 4;

    public static $list = [
        self::ADMIN => 'Administrators',
        self::USER => 'Users',
        self::GUEST => 'Guests',
        self::DRIVER => 'Drivers',
    ];

}