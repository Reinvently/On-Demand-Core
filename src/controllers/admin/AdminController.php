<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\controllers\admin;

use app\controllers\api\AuthController;
use reinvently\ondemand\core\modules\user\models\Client;
use yii\web\Controller;

/**
 * Class AdminController
 * @package reinvently\ondemand\core\controllers\admin
 */
abstract class AdminController extends Controller
{
    public $navItems = [];
    public $authToken = null;

    public function init()
    {
        parent::init();

        $this->layout = '@app/core/layouts/admin';
        $this->navItems = isset(\Yii::$app->params['admin']['menu']) ? \Yii::$app->params['admin']['menu'] : $this->getDefaultMenu();

        /** @var Client $client */
        $client = Client::find()
            ->where(['userId' => \Yii::$app->user->id])
            ->andWhere(['>=', 'expiredAt', time()])
            ->one();
        if (!$client) {
            /** @var Client $client */
            $client = new Client();
            $client->userId = \Yii::$app->user->id;
            $client->uuid = AuthController::getSessionId();
            $client->token = $client->generateToken();
            $client->ip = \Yii::$app->request->userIP;
            $client->save();
        }
        $this->authToken = $client->token;
    }

    private function getDefaultMenu()
    {
        return [
            [
                'label' => 'Categories',
                'url' => '/admin/category',
            ],
            [
                'label' => 'Products',
                'url' => '/admin/product',
            ],
            [
                'label' => 'Orders',
                'url' => '/admin/order',
            ],
            [
                'label' => 'Users',
                'url' => '/admin/user',
            ],
            [
                'label' => 'Promo codes',
                'url' => '/admin/promo-code',
            ],
            [
                'label' => 'Stats',
                'items' => [
                    ['label' => 'Orders', 'url' => '/admin/stats/order'],
                    ['label' => 'Payment', 'url' => '/admin/payment'],
                    //'<li class="divider"></li>',
                    //'<li class="dropdown-header">Dropdown Header</li>',
                    //['label' => 'Level 1 - Dropdown B', 'url' => '#'],
                ],
            ],
            [
                'label' => 'Map',
                'items' => [
                    ['label' => 'Locations', 'url' => '/admin/map/locations'],
                ],
            ],
            [
                'label' => 'Settings',
                'items' => [
                    ['label' => 'Config', 'url' => '/admin/settings/config'],
                    ['label' => 'Tariff', 'url' => '/admin/tariff'],
                    ['label' => 'Extra fee', 'url' => '/admin/extra-fee'],
                ],
            ],
        ];
    }
}