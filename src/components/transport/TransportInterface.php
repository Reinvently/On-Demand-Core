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
 * Time: 13:27
 */

namespace reinvently\ondemand\core\components\transport;


interface TransportInterface
{
    /**
     * @param ApiInterface $model
     * @return mixed
     */
    public function responseItem(ApiInterface $model);

    /**
     * @param ApiInterface $model
     * @return mixed
     */
    public function responseItemShort(ApiInterface $model);

    /**
     * @param ApiInterface $model
     * @return mixed
     */
    public function responseItemId(ApiInterface $model);

    /**
     * @param ApiInterface[] $models
     * @return mixed
     */
    public function responseList(array $models);

    /**
     * @param ApiInterface[] $models
     * @return mixed
     */
    public function responseListShort(array $models);

    /**
     * @param ApiInterface[] $models
     * @return mixed
     */
    public function responseListIds(array $models);

    /**
     * @param int|string|float|bool|null $scalar
     * @return mixed
     */
    public function responseScalar($scalar = null);

    /**
     * @param $object
     * @return mixed
     */
    public function responseObject($object);

    /**
     * @param array $errors
     * @return mixed
     */
    public function responseErrors($errors);

    /**
     * @param string $message
     * @param bool $success
     * @return mixed
     */
    public function responseMessage($message, $success = false);

    /**
     * @param bool $success
     * @return mixed
     */
    public function responseSuccess($success = true);

    /**
     * @param $exceptionData
     * @return mixed
     */
    public function responseException($exceptionData);

}