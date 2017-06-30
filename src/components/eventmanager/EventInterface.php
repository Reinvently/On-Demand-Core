<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 14.09.2015
 * Time: 14:12
 */

namespace reinvently\ondemand\core\components\eventmanager;


interface EventInterface
{

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function requiredTriggeredEvent();

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function optionalTriggeredEvent();

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function requiredOnEvent();

    /**
     * @return void|array Events list for ex. [Test2::RAISE_EVENT_TEST2];
     */
    public static function optionalOnEvent();

} 