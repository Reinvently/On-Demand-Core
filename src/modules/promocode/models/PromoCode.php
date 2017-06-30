<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\promocode\models;

use reinvently\ondemand\core\components\helpers\CoreHelper;
use reinvently\ondemand\core\components\helpers\DateHelper;
use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\modules\user\models\User;
use yii\helpers\Json;

/**
 * Class PromoCode
 * @package reinvently\ondemand\core\modules\promocode\models
 *
 * @property int id
 * @property string code
 * @property int type
 * @property int promoType
 * @property int userId
 * @property int amount
 * @property int minAmount
 * @property int usedCount
 * @property string days
 * @property int startAt
 * @property int expireAt
 * @property int createdAt
 * @property int updatedAt
 *
 */
class PromoCode extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    const TYPE_STATIC = 0;
    const TYPE_PERCENT = 1;

    const PROMO_TYPE_MANUAL = 0;
    const PROMO_TYPE_FIRST_REVIEW = 1;
    const PROMO_TYPE_PERSONAL_DISCOUNT = 2;
    const PROMO_TYPE_SUBSCRIPTION = 3;
    const PROMO_TYPE_GIFT = 4;
    const PROMO_TYPE_SUBSCRIPTION_IDENTIFIER = 5;
    const PROMO_TYPE_SHARE_PERSONAL = 6;

    /** @var User */
    public $userModelClass = User::class;

    public static $types = [
        self::TYPE_STATIC => 'Static',
        self::TYPE_PERCENT => 'Percent',
    ];

    public $startAtDate;
    public $expireAtDate;

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%promo_code}}';
    }

    /**
     * Constructor.
     */
    public function init()
    {
        parent::init();
        $this->days = range(0, 6);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['code', 'amount', 'type', 'promoType'], 'required'],
            ['code', 'unique'],
            ['minAmount', 'number'],
            ['amount', 'number', 'min' => 1],
            [['startAtDate', 'expireAtDate', 'days'], 'safe'],
        ];
    }

    /**
     * @param int $length
     * @return string
     */
    public static function generate($length = 7)
    {
        while (true) {
            $code = CoreHelper::randomString($length);
            if (!self::find()->where(['code' => $code])->one()) {
                return $code;
            }
        }
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createdAt = time();
            if (!$this->userId) {
                $this->userId = \Yii::$app->user->id;
            }
        }
        $this->updatedAt = time();

        $this->convertAttributes();

        return parent::beforeSave($insert);
    }

    /**
     *
     */
    private function convertAttributes()
    {
        $this->days = Json::encode($this->days);

        $this->startAt = $this->startAtDate ? strtotime($this->startAtDate) : null;
        $this->expireAt = $this->expireAtDate ? strtotime($this->expireAtDate) : null;
    }

    /**
     * This method is called when the AR object is created and populated with the query result.
     * The default implementation will trigger an [[EVENT_AFTER_FIND]] event.
     * When overriding this method, make sure you call the parent implementation to ensure the
     * event is triggered.
     */
    public function afterFind()
    {
        parent::afterFind();

        $this->days = Json::decode($this->days);
        $this->startAtDate = $this->startAt ? date('Y-m-d', $this->startAt) : null;
        $this->expireAtDate = $this->expireAt ? date('Y-m-d', $this->expireAt) : null;
    }

    /**
     * @return string
     */
    public function getDaysView()
    {
        $weekList = DateHelper::weekList();
        $days = [];
        if ($this->days) {
            foreach ($this->days as $day) {
                $days[] = $weekList[$day];
            }
        }
        return implode(' ', $days);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        /** @var User $class */
        $class = $this->userModelClass;
        return $this->hasOne($class::className(), ['id' => 'userId']);
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        // TODO: Implement getItemForApi() method.
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        // TODO: Implement getItemShortForApi() method.
    }
}