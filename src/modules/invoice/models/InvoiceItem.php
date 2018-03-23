<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 19.03.2018
 * Time: 16:49
 */
namespace reinvently\ondemand\core\modules\invoice\models;

use reinvently\ondemand\core\components\model\CoreModel;

/**
 * This is the model class for table "invoice_item".
 *
 * @property integer $id
 * @property integer $invoiceId
 * @property string $title
 * @property string $description
 * @property integer $price
 * @property integer $count
 * @property integer $createAt
 * @property integer $updateAt
 * @property integer $v
 */
class InvoiceItem extends CoreModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_item';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceId', 'price', 'count', 'createAt', 'updateAt', 'v'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoiceId' => 'Invoice ID',
            'title' => 'Title',
            'description' => 'Description',
            'price' => 'Price',
            'count' => 'Count',
            'createAt' => 'Create At',
            'updateAt' => 'Update At',
            'v' => 'V',
        ];
    }

    /**
     * @inheritDoc
     */
    public function optimisticLock()
    {
        return 'v';
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createAt = time();
        }
        $this->updateAt = time();

        return parent::beforeSave($insert);
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->price * $this->count;
    }
}
