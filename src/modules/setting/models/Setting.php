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
 * Time: 13:49
 */
namespace reinvently\ondemand\core\modules\setting\models;


use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;

/**
 * Class Setting
 * @package app\models
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 */
class Setting extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    const INVOICE_TAX = 1;
    const INSTALLATION_FEE = 2;
    const ONE_TIME_PLAN_DISCOUNT = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'value'], 'required'],
            [['id'], 'integer'],
            [['name', 'value'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'value' => 'Value',
        ];
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
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