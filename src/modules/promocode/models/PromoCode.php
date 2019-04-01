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
use reinvently\ondemand\core\modules\promocode\exceptions\InvalidPromoCodeException;
use reinvently\ondemand\core\modules\user\models\User;
use yii\helpers\Json;

/**
 * Class PromoCode
 * @package reinvently\ondemand\core\modules\promocode\models
 *
 * @property int id
 * @property string code
 * @property string label
 * @property string description
 * @property int type
 * @property int userId
 * @property boolean isPercent
 * @property int percent
 * @property int static
 * @property int minPrice
 * @property int usedNumbers
 * @property int maxNumbers
 * @property int startAt
 * @property int expireAt
 * @property int createdAt
 * @property int updatedAt
 *
 */
class PromoCode extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    const TYPE_MANUAL = 1;

    /** @var User */
    public $userModelClass = User::class;

    public static $types = [
        self::TYPE_MANUAL => 'Manual',
    ];

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'promo_code';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['code', 'type', 'userId', 'isPercent', 'percent', 'static', 'minPrice', 'usedNumbers', 'maxNumbers', 'createdAt', 'updatedAt'], 'required'],
            [['description'], 'string'],
            [['type', 'userId', 'isPercent', 'percent', 'static', 'minPrice', 'usedNumbers', 'maxNumbers', 'startAt', 'expireAt', 'createdAt', 'updatedAt'], 'integer'],
            [['code', 'label'], 'string', 'max' => 255],
            [['code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'label' => 'Label',
            'description' => 'Description',
            'type' => 'Type',
            'userId' => 'User ID',
            'isPercent' => 'Is Percent',
            'percent' => 'Percent',
            'static' => 'Static',
            'minPrice' => 'Min Price',
            'usedNumbers' => 'Used Numbers',
            'maxNumbers' => 'Max Numbers',
            'startAt' => 'Start At',
            'expireAt' => 'Expire At',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
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
        }
        if ($this->getDirtyAttributes()) {
            $this->updatedAt = time();
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        /** @var User $class */
        $class = $this->userModelClass;
        return $this->hasOne($class, ['id' => 'userId']);
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'label' => $this->label,
            'description' => $this->description,
            'type' => $this->type,
            'isPercent' => $this->isPercent,
            'percent' => $this->percent,
            'static' => $this->static,
            'minPrice' => $this->minPrice,
            'usedNumbers' => $this->usedNumbers,
            'maxNumbers' => $this->maxNumbers,
            'startAt' => $this->startAt,
            'expireAt' => $this->expireAt,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return $this->getItemForApi();
    }

    /**
     * @param $price
     * @param null $userId
     * @param array $params
     * @return bool
     * @throws InvalidPromoCodeException
     */
    public function check($price, $userId = null, $params = [])
    {
        if ($this->minPrice && $this->minPrice > $price) {
            throw new InvalidPromoCodeException('The Price is less than minimal promo code price');
        }

        if ($this->startAt && $this->startAt > time()) {
            throw new InvalidPromoCodeException('The Start Time has not yet come');
        }

        if ($this->expireAt && $this->expireAt < time()) {
            throw new InvalidPromoCodeException('This Code is expired');
        }

        if ($this->userId && $this->userId != $userId) {
            throw new InvalidPromoCodeException('This Code works only for specified User');
        }

        if ($this->maxNumbers && $this->maxNumbers >= $this->usedNumbers) {
            throw new InvalidPromoCodeException('The promo code has expired by max numbers of usage');
        }

        return true;
    }

    /**
     * @param int $price
     * @param int $userId
     * @param array $params
     * @return int
     */
    public function getPriceAfterPromo($price, $userId = null, $params = [])
    {
        $this->check($price, $userId, $params);

        $price = $this->handle($price, $userId, $params);

        return $price;
    }

    /**
     * @param int $price
     * @param int $userId
     * @param array $params
     * @return int
     */
    protected function handle($price, $userId = null, $params = [])
    {
        if ($this->isPercent) {
            $price = $price - $price * $this->percent / 100;
        } else {
            $price = $price - $this->static;
        }

        return $price;
    }

    /**
     * @return bool
     */
    public function process()
    {
        $this->usedNumbers++;
        return $this->save();
    }
}