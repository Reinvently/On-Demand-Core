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
use yii\web\Response;

/**
 * @property integer $id
 * @property integer $userId
 * @property string $token
 * @property integer $startedAt
 * @property integer $finishedAt
 * @property string $route
 * @property string $request
 * @property string $response
 * @property int $ip
 *
 */
class ApiLog extends CoreModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'ip', 'startedAt', 'finishedAt'], 'integer'],
            [['token', 'route', 'request', 'response'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
            [['request', 'response'], 'filter', 'filter' => '\reinvently\ondemand\core\components\loggers\models\LogFilter::process'],
//            [['token', 'route'], 'string', 'max' => 255],
//            [['request', 'response'], 'string', 'max' => 0xfffe],
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
     * @param Response $response
     */
    public function generateHtmlResponseStatusCode($response)
    {
        if (!$response) {
            return;
        }

        if ($response->getStatusCode()) {
            $this->response .= ' <b>Status Code:</b> ' . $response->getStatusCode();
        }

        if ($response->statusText) {
            $this->response .= ' ' . $response->statusText;
        }

        $this->response .= '<br/>';
    }

    /**
     * @param array $headers
     */
    public function generateHtmlResponseHeaders($headers)
    {
        if (!$headers) {
            return;
        }

        $this->response .= ' <b>Headers:</b><br/>';
        foreach ($headers as $header => $values) {
            $this->response .= $header . ': ' . join('; ', $values) . '<br/>';
        }
    }

    /**
     * @param array $params
     */
    public function generateHtmlResponseParams($params)
    {
        if (!$params) {
            return;
        }

        $response = '';
        $response .= '<b>Body:</b><pre>';
        try {
            $response .= Json::encode($params, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        } catch (InvalidArgumentException $e) {
            $response .= $e->getMessage();
            $response .= "\n";
            $response .= var_export($params, true);
        }
        $response .= '</pre>';

        $this->response .= $response;

    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        $this->request = mb_substr($this->request, 0, 0xfffe, 'ASCII');
        $this->response = mb_substr($this->response, 0, 0xfffe, 'ASCII');
        $this->token = mb_substr($this->token, 0, 255, 'ASCII');
        $this->route = mb_substr($this->route, 0, 255, 'ASCII');
        return parent::afterValidate();
    }


}
