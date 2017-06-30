<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */
/**
 * Created by PhpStorm.
 * User: sglushko
 * Date: 10.11.2015
 * Time: 17:32
 */

namespace reinvently\ondemand\core\components\transport;


/**
 * Class ApiTransportTrait
 * @package reinvently\ondemand\core\components\transport
 *
 * @property int id
 */
trait ApiTransportTrait
{
    /**
     * @return array
     */
    abstract public function getItemForApi();

    /**
     * @return array
     */
    abstract public function getItemShortForApi();

    /**
     * @return array
     */
    public function getItemIdForApi()
    {
        return [
            'id' => $this->id,
        ];
    }

    /** @return array */
    public function getItemForApiList()
    {
        return $this->getItemShortForApi();
    }

    /**
     * @return array
     */
    public function getItemShortForApiList()
    {
        return $this->getItemShortForApi();
    }

    /**
     * @return array
     */
    public function getItemIdForApiList()
    {
        return $this->getItemIdForApi();
    }
} 