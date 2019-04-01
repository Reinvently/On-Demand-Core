<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\components\loggers\models;

use reinvently\ondemand\core\components\model\CoreModel;
use yii\base\InvalidArgumentException;
use yii\console\Request;
use yii\console\Response;
use yii\helpers\Json;

/**
 * @property integer $id
 * @property integer $startedAt
 * @property integer $finishedAt
 * @property string $route
 * @property string $request
 * @property string $response
 *
 */
class ConsoleLog extends CoreModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'console_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['startedAt', 'finishedAt'], 'integer'],
            [['route', 'request', 'response'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
//            [['route'], 'string', 'max' => 255],
//            [['request', 'response'], 'string', 'max' => 0xfffe],
        ];
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

        $this->response .= ' <b>Exit Status:</b> ' . $response->exitStatus;

        $this->response .= '<br/>';
    }

    public function afterValidate()
    {
        $this->request = mb_substr($this->request, 0, 0xfffe, 'ASCII');
        $this->response = mb_substr($this->response, 0, 0xfffe, 'ASCII');
        $this->route = mb_substr($this->route, 0, 255, 'ASCII');
        return parent::afterValidate();
    }


}
