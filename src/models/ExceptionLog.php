<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 28.01.2018
 * Time: 20:50
 */

namespace reinvently\ondemand\core\models;


use reinvently\ondemand\core\components\model\CoreModel;
use yii\base\InvalidParamException;
use yii\helpers\Json;
use yii\web\Application;
use yii\web\Request;

/**
 * This is the model class for table "apiException".
 *
 * @property integer $id
 * @property integer $datetime
 * @property string $route
 * @property integer $userId
 * @property string $message
 * @property string $fileName
 * @property integer $lineFile
 * @property string $stackTrace
 * @property bool $isFailed
 * @property int $ip
 * @property string $request
 */
class ExceptionLog extends CoreModel
{
    const DB_TYPE_TEXT_LENGTH = 0xffff;

    static public function saveException(\Exception $e, $isFailed = false)
    {
        try {
            $exceptionLog = new static();
            $exceptionLog->route = \Yii::$app->requestedRoute;
            if(\Yii::$app instanceof Application && $user = \Yii::$app->getUser()) {
                $exceptionLog->userId = $user->id;
            }
            $exceptionLog->message = $e->getMessage();
            $exceptionLog->fileName = $e->getFile();
            $exceptionLog->lineFile = $e->getLine();
            $exceptionLog->stackTrace = $e->getTraceAsString();
            $exceptionLog->isFailed = $isFailed;
            if (\Yii::$app->request instanceof Request) {
                $exceptionLog->ip = ip2long(\Yii::$app->request->userIP);
                $exceptionLog->request .= ' <b>Method: ' . \Yii::$app->request->method . '</b><br/>';
                $exceptionLog->generateHtmlRequestHeaders(\Yii::$app->request->getHeaders()->toArray());
                $bodyParams = \Yii::$app->request->getBodyParams();
                $queryParams = \Yii::$app->request->getQueryParams();
                $exceptionLog->generateHtmlRequestParams(array_merge(
                    !empty($bodyParams) ? $bodyParams : [],
                    !empty($queryParams) ? $queryParams : []
                ));
            }
            $exceptionLog->save();
        } catch (\Exception $e) {}

    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'exception_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datetime', 'userId', 'lineFile', 'ip'], 'integer'],
            [['message', 'stackTrace'], 'string'],
            [['route', 'fileName'], 'string', 'max' => 255],
            [['request'], 'string', 'max' => static::DB_TYPE_TEXT_LENGTH],
            [['request'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'datetime' => 'Datetime',
            'route' => 'Route',
            'userId' => 'User ID',
            'message' => 'Message',
            'fileName' => 'File Name',
            'lineFile' => 'Line File',
            'stackTrace' => 'Stack Trace',
        ];
    }

    public function beforeSave($insert)
    {
        $this->datetime = time();
        return parent::beforeSave($insert);
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
        } catch (InvalidParamException $e) {
            $request = '<pre>';
            $request .= $e->getMessage();
            $request .= "\n";
            $request .= var_export($params, true);
            $request .= '</pre>';
        }

        $this->request .= $request;

    }

}