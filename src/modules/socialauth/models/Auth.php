<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\socialauth\models;

use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\modules\user\models\User;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "auth".
 *
 * @property string $id
 * @property string $userId
 * @property string $source
 * @property string $sourceId
 *
 * @property User $user
 */
class Auth extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'source', 'sourceId'], 'required'],
            [['userId'], 'integer'],
            [['source', 'sourceId'], 'string', 'max' => 255],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        /** @var CoreModel $userModelClass */
        $userModelClass = \Yii::$app->user->identityClass;
        return $this->hasOne($userModelClass, ['id' => 'userId']);
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'source' => 'Source',
            'sourceId' => 'Source ID',
        ];
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'source' => $this->source,
            'sourceId' => $this->sourceId,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return $this->getItemForApi();
    }
}
