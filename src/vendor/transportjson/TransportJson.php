<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 24.09.2015
 * Time: 13:25
 */

namespace reinvently\ondemand\core\vendor\transportjson;


use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\TransportInterface;
use reinvently\ondemand\core\exceptions\LogicException;

class TransportJson implements TransportInterface
{

    /**
     * @param ApiInterface $model
     * @return ResponseObject
     */
    public function responseItem(ApiInterface $model)
    {
        return $this->convert(true, $model->getItemForApi(), ResponseObject::TYPE_ITEM);
    }

    /**
     * @param ApiInterface $model
     * @return ResponseObject
     */
    public function responseItemShort(ApiInterface $model)
    {
        return $this->convert(true, $model->getItemShortForApi(), ResponseObject::TYPE_ITEM);
    }

    /**
     * @param ApiInterface $model
     * @return ResponseObject
     */
    public function responseItemId(ApiInterface $model)
    {
        return $this->convert(true, $model->getItemIdForApi(), ResponseObject::TYPE_ITEM);
    }

    /**
     * @param ApiInterface[] $models
     * @return ResponseObject
     */
    public function responseList(array $models)
    {
        $list = array();
        foreach ($models as $model) {
            $list[] = $model->getItemForApiList();
        }
        if (!$list) {
            $list = null;
        }

        return $this->convert(true, $list, ResponseObject::TYPE_LIST);

    }

    /**
     * @param ApiInterface[] $models
     * @return ResponseObject
     */
    public function responseListShort(array $models)
    {
        $list = array();
        foreach ($models as $model) {
            $list[] = $model->getItemShortForApiList();
        }
        if (!$list) {
            $list = null;
        }

        return $this->convert(true, $list, ResponseObject::TYPE_LIST);

    }

    /**
     * @param ApiInterface[] $models
     * @return ResponseObject
     */
    public function responseListIds(array $models)
    {
        $list = array();
        foreach ($models as $model) {
            $list[] = $model->getItemIdForApiList();
        }
        if (!$list) {
            $list = null;
        }

        return $this->convert(true, $list, ResponseObject::TYPE_LIST);
    }

    /**
     * @param int|string|float|bool|null $scalar
     * @return ResponseObject
     */
    public function responseScalar($scalar = null)
    {
        return $this->convert(true, $scalar);
    }

    /**
     * @param $object
     * @return ResponseObject|mixed
     */
    public function responseObject($object)
    {
        return $this->convert(true, $object);
    }

    /**
     * @param array $errors
     * @return ResponseObject
     */
    public function responseErrors($errors)
    {
        return $this->convert(false, null, null, $errors);
    }

    /**
     * @param string $message
     * @param bool $success
     * @return ResponseObject
     */
    public function responseMessage($message, $success = false)
    {
        return $this->convert($success, null, null, null, $message);
    }

    /**
     * @param bool $success
     * @return ResponseObject
     */
    public function responseSuccess($success = true)
    {
        return $this->convert($success);
    }

    /**
     * @param $exceptionData
     * @return ResponseObject
     */
    public function responseException($exceptionData)
    {
        return $this->convert(false, null, null, null, $exceptionData['message'], $exceptionData);
    }

    /**
     * @param bool $success
     * @param mixed $data
     * @param int $type
     * @param object[] $errors
     * @param string $message
     * @param object $debug
     * @return ResponseObject
     * @throws LogicException
     */
    protected function convert(
        $success = true,
        $data = null,
        $type = null,
        $errors = null,
        $message = null,
        $debug = null
    )
    {
        if (!$success and (\Yii::$app->getResponse()->getStatusCode() == 200)) {
            \Yii::$app->getResponse()->setStatusCode(400);
        } elseif ($success and !in_array(\Yii::$app->getResponse()->getStatusCode(), [200, 201, 204])) {
            throw new LogicException();
        }

        $response = new ResponseObject();
        $response->success = $success;
        $response->data = $data;
        $response->type = $type;
        $response->errors = $errors;
        $response->message = $message;
        if (YII_DEBUG) {
            $response->debug = $debug;
        }

        $response = $this->cleanResponseRecurse($response);
        return $response;
    }

    protected function cleanResponseRecurse($item)
    {
        foreach ($item as $k => $v) {
            if (!empty($v) && (is_array($v) || is_object($v))) {
                if (is_array($item)) {
                    $item[$k] = $v = $this->cleanResponseRecurse($v);
                } elseif (is_object($item)) {
                    $item->$k = $v = $this->cleanResponseRecurse($v);
                }
            }

            if (empty($v) && (is_array($v) || $v === '' || $v === null)) {
                if (is_array($item)) {
                    unset($item[$k]);
                } elseif (is_object($item)) {
                    unset($item->$k);
                }
            }

        }
        return $item;
    }

}