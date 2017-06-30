<?php
/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\settings\controllers;

use reinvently\ondemand\core\controllers\admin\AdminController;
use reinvently\ondemand\core\modules\settings\models\Settings;

/**
 * Class AdminSettingsController
 * @package reinvently\ondemand\core\modules\settings\controllers
 */
class AdminSettingsController extends AdminController
{
    public $modelClass = Settings::class;

    /**
     * @var array
     */
    public $fields = [
        [
            'label' => 'Option 1',
            'attribute' => 'option1',
        ],
        [
            'label' => 'Option 2',
            'attribute' => 'option2',
        ],
    ];

    /**
     * @return string
     */
    public function actionConfig()
    {
        /** @var Settings $modelClass */
        $modelClass = $this->modelClass;
        $post = \Yii::$app->request->post();

        if ($post) {
            foreach($post as $key => $value) {
                $modelClass::set($key, $value);
            }
        }

        foreach($this->fields as &$f) {
            $f['value'] = $modelClass::get($f['attribute']);
        }

        $data = [
            'fields' => $this->fields,
        ];
        return $this->render('@app/core/modules/settings/views/admin/config/form', $data);
    }
}