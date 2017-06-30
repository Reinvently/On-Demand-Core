<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 28.09.2015
 * Time: 16:05
 */

namespace reinvently\ondemand\core\components\transport;


interface ApiInterface
{
    /**
     * @return array
     */
    public function getItemForApi();

    /**
     * @return array
     */
    public function getItemShortForApi();

    /**
     * @return array
     */
    public function getItemIdForApi();

    /**
     * @return array
     */
    /** @return array */
    public function getItemForApiList();

    /**
     * @return array
     */
    public function getItemShortForApiList();

    /**
     * @return array
     */
    public function getItemIdForApiList();

//    public function getScalar();
}