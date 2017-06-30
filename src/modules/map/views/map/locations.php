<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

/* @var $this yii\web\View */

\reinvently\ondemand\core\modules\map\assets\MapAsset::register($this);

$this->registerJsFile('https://maps.googleapis.com/maps/api/js', ['async' => 'async', 'defer' => 'defer']);

?>
<h1>Map</h1>
<div id="map"></div>