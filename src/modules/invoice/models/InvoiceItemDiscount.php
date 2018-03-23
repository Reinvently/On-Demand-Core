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
 * Time: 16:52
 */
namespace reinvently\ondemand\core\modules\invoice\models;

/**
 * This is the model class for table "invoice_item_discount".
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
class InvoiceItemDiscount extends InvoiceItem
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_item_discount';
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
}
