<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 15.09.2015
 * Time: 16:11
 */

namespace reinvently\ondemand\core\components\base;

use reinvently\ondemand\core\components\eventmanager\EventManager;
use reinvently\ondemand\core\components\payment\PaymentInterface;
use reinvently\ondemand\core\components\transport\TransportInterface;
use reinvently\ondemand\core\modules\category\models\Category;
use reinvently\ondemand\core\modules\stats\StatsComponent;
use reinvently\ondemand\core\modules\user\models\User;
use reinvently\ondemand\core\vendor\mapsdirections\google\Directions;
use reinvently\ondemand\core\vendor\paymentbraintree\Braintree;
use Reinvently\Twilio\Twilio;
use yii\mongodb\Connection;

/**
 * @property EventManager $eventManager
 * @property Braintree $braintree
 * @property Directions $mapsDirections
 * @property TransportInterface $transport
 * @property StatsComponent $stats
 * @property User $user
 * @property Category category
 * @property Connection mongodb
 * @property \yii\authclient\Collection authClientCollection
 * @property Twilio twilio
 * @property \understeam\fcm\Client fcm
 */
class Application extends \yii\web\Application
{

    public function init()
    {
        parent::init();
    }


}