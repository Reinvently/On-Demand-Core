<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\stats;



use reinvently\ondemand\core\components\eventmanager\CoverEvent;
use reinvently\ondemand\core\components\eventmanager\EventInterface;
use reinvently\ondemand\core\components\eventmanager\EventManager;
use reinvently\ondemand\core\modules\stats\models\Stats;
use reinvently\ondemand\core\modules\user\models\User;
use Yii;
use yii\base\Component;

class StatsComponent extends Component implements EventInterface
{
//    public function init()
//    {
//        $this->initEventAutoSave();
//        parent::init();
//    }
//
//    public function initEventAutoSave()
//    {
//        Yii::$app->eventManager->on(
//            EventManager::EVENT_COVER,
//            function (CoverEvent $event) {
//
//                $stats = new Stats(); // todo needs di?
//
//                $stats->event = $event->event->name;
//
//                if (($o = $event->event->sender) && $o instanceof StatsInterface) {
//                    /** @var StatsInterface $o */
//                    $stats->class = $o->className();
//                    $stats->object = $o->getStatsObject();
//                }
//
//                $user = Yii::$app->user;
//                $stats->userId = $user->id;
//                /** @var User $userModel */
//                if ($userModel = $user->getIdentity(false)) {
//                    $stats->authKey = $userModel->authKey; // todo change on client id
//                }
//
//                $stats->save();
//            }
//        );
//    }

    public static function onEvent(CoverEvent $event) {

        $stats = new Stats(); // todo needs di?

        $stats->event = $event->event->name;

        if (($o = $event->event->sender) && $o instanceof StatsInterface) {
            /** @var StatsInterface $o */
            $stats->class = $o->className();
            $stats->object = $o->getStatsObject();
        }

        $user = Yii::$app->user;
        $stats->userId = $user->id;
        /** @var User $userModel */
        if ($userModel = $user->getIdentity(false)) {
            $stats->authKey = $userModel->authKey; // todo change on client id
        }

        $stats->save();
    }

    public static function requiredTriggeredEvent()
    {
    }

    public static function optionalTriggeredEvent()
    {
    }

    public static function requiredOnEvent()
    {
    }

    public static function optionalOnEvent()
    {
        return [EventManager::EVENT_COVER];
    }
}