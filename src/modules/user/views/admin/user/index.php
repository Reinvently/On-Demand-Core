<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel app\models\UserSearch */
/** @var string $roleId */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php /*
    <ul id="myTabs" class="nav nav-tabs" role="tablist">
        <?php foreach(\reinvently\ondemand\core\modules\role\models\Role::$list as $k => $v): ?>
        <li role="presentation" <?= ($roleId == $k ? 'class="active"' : '')?>>
            <a href="/admin/user/index?roleId=<?= $k ?>" id="home-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="true">
                <?= $v ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
 */ ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'label' => 'Role',
                'attribute' => 'roleId',
                'filter' => \reinvently\ondemand\core\modules\role\models\Role::$list,
                'value' => function ($data) {
                    return \reinvently\ondemand\core\modules\role\models\Role::$list[$data->roleId];
                },
            ],
            'email:email',
            //'password',
            //'roleId',
            'firstName',
            'lastName',
            'phone',
            // 'facebookId',
            [
                'attribute' => 'createdAt',
                'format' => 'date',
                'filter' => false,
            ],
            // 'updatedAt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
