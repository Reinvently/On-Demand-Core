<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */


namespace reinvently\ondemand\core\modules\stats\log;


class MongoDbTarget extends \yii\mongodb\log\MongoDbTarget
{
    /**
     * Stores log messages to MongoDB collection.
     */
    public function export()
    {
        $collection = $this->db->getCollection($this->logCollection);
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
//            todo
//            if (!is_string($text)) {
//                $text = VarDumper::export($text);
//            }
            if (!is_string($text)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($text instanceof \Exception) {
                    $text = (string) $text;
                } else {
                    $text = VarDumper::export($text);
                }
            }
            $collection->insert([
                'level' => $level,
                'category' => $category,
                'log_time' => $timestamp,
                'prefix' => $this->getMessagePrefix($message),
                'message' => $text,
            ]);
        }
    }

} 