<?php
/**
 * @copyright Reinvently (c) 2019
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\pushnotification\models;


use reinvently\ondemand\core\components\model\CoreModel;

/**
 * Class ClientPushToken
 * @package reinvently\ondemand\core\modules\pushnotification\models
 *
 * @property int id
 * @property int clientId
 * @property int userId
 * @property string token
 * @property int createdAt
 * @property int updatedAt
 * @property string platform
 * @property string application
 * @property string applicationVersion
 * @property string authorizedEntity
 */
class ClientPushToken extends CoreModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client_push_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['token', 'platform', 'application', 'applicationVersion', 'authorizedEntity'], 'string', 'max' => 255],
            [['clientId', 'userId', 'createdAt', 'updatedAt'], 'integer'],
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->getIsNewRecord()) {
            $this->createdAt = time();
        }
        if ($this->getDirtyAttributes()) {
            $this->updatedAt = time();
        }
        return parent::beforeSave($insert);
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        /** @var ClientPushToken[] $clientPushTokens */
        $clientPushTokens = ClientPushToken::find()
            ->andWhere(['and', ['token' => $this->token], ['!=', 'id', $this->id]])
            ->all();
        foreach ($clientPushTokens as $clientPushToken) {
            $clientPushToken->delete();
        }
    }

}