<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\loggers\models;

use reinvently\ondemand\core\components\model\CoreModel;
use yii\base\InvalidArgumentException;
use yii\helpers\Json;
use yii\web\Request;

/**
 * @property integer $id
 * @property integer $userId
 * @property integer $startedAt
 * @property integer $finishedAt
 * @property string $route
 * @property string $request
 * @property int $ip
 *
 */
class AdminLog extends CoreModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'ip', 'startedAt', 'finishedAt'], 'integer'],
            [['route'], 'string', 'max' => 255],
            [['request'], 'string', 'max' => 0xffff],
            [['route', 'request'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ip' => 'IP Address',
        ];
    }

    /**
     * @param Request $request
     */
    public function generateHtmlRequestMethod($request)
    {
        if (!$request) {
            return;
        }

        if ($request->getMethod()) {
            $this->request .= ' <b>Method:</b> ' . $request->getMethod();
        }

        if ($request->getUrl()) {
            $this->request .= ' ' . $request->getUrl();
        }

        $this->request .= '<br/>';
    }

    /**
     * @param array $headers
     */
    public function generateHtmlRequestHeaders($headers)
    {
        if (!$headers) {
            return;
        }

        $this->request .= ' <b>Headers:</b><br/>';
        foreach ($headers as $header => $values) {
            $this->request .= $header . ': ' . join('; ', $values) . '<br/>';
        }
    }

    /**
     * @param array $params
     */
    public function generateHtmlRequestParams($params)
    {
        if (!$params) {
            return;
        }

        $request = '';
        try {
            $request .= ' <b>Params:</b><br/><pre>';
            $request .= Json::encode($params, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $request .= '</pre>';
        } catch (InvalidArgumentException $e) {
            $request = '<pre>';
            $request .= $e->getMessage();
            $request .= "\n";
            $request .= var_export($params, true);
            $request .= '</pre>';
        }

        $this->request .= $request;

    }

    /**
     * This method is invoked before validation starts.
     * The default implementation raises a `beforeValidate` event.
     * You may override this method to do preliminary checks before validation.
     * Make sure the parent implementation is invoked so that the event can be raised.
     * @return boolean whether the validation should be executed. Defaults to true.
     * If false is returned, the validation will stop and the model is considered invalid.
     */
    public function beforeValidate()
    {
        $this->request = mb_substr($this->request, 0, 0xffff);
        $this->route = mb_substr($this->route, 0, 255);
        return parent::beforeValidate();
    }


}
