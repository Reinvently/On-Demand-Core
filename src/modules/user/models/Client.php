<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\user\models;

use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use Yii;
use yii\db\BaseActiveRecord;

/**
 * Class Client
 *
 * @property int $id
 * @property int $userId
 * @property string $uuid
 * @property string $token
 * @property string $type
 * @property string $ip
 * @property int $expiredAt
 *
 * @property User $user
 */
class Client extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    /**
     * @return string
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * @param $uuid
     * @param bool $userId
     * @return array|null|\yii\db\ActiveRecord
     */
    public static function findActive($uuid, $userId = false)
    {
        $client = self::find()
            ->where(['uuid' => $uuid])
            ->andWhere(['>=', 'expiredAt', time()]);
        if ($userId) {
            $client->andWhere(['userId' => $userId]);
        }

        return $client->one();
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['userId', 'uuid', 'token'], 'required'],
            ['token', 'unique'],
            [['type', 'ip'], 'safe'],
        ];
    }

    public function generateToken()
    {
        $token = $this->_generateTokenString();
        return self::findOne(['token' => $token]) ? $this->generateToken() : $token;
    }

    private function _generateTokenString()
    {
        return Yii::$app->getSecurity()->generateRandomString(32);
    }

    public function beforeSave($insert)
    {
        $this->expiredAt = time() + (60 * 60 * 24 * 30);
        if (isset(Yii::$app->params['tokenDuration'])) {
            $this->expiredAt = time() + Yii::$app->params['tokenDuration'];
        }
        return parent::beforeSave($insert);
    }

    /** ---------------------- */

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        /** @var CoreModel $class */
        $class = Yii::$app->user->identityClass;
        return $this->hasOne($class::className(), ['id' => 'userId']);
    }

    /** ---------------------- */

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'uuid' => $this->uuid,
            'token' => $this->token,
            'type' => $this->type,
            'ip' => $this->ip,
            'expiredAt' => $this->expiredAt,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return [
            'id' => $this->id,
            'token' => $this->token,
        ];
    }


}