<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\resource\models;

use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\controllers\admin\AdminController;
use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\modules\resource\components\EasyThumbnailImage;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "resource".
 *
 * @property integer    $id
 * @property integer    $type
 * @property string     $title
 * @property string     $description
 * @property string     $alias
 * @property string     $extension
 * @property string     $createAt
 * @property string     $updateAt
 * @property integer    $version
 *
 */

abstract class Resource extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    const TYPE_IMAGE = 1;
    const TYPE_JSON = 2;
    const TYPE_VIDEO = 3;

    const UPLOAD_PATH = 'uploads';

    const MAX_FILE_SIZE = 209715200; /*200MB*/

    /** @var UploadedFile */
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resource';
    }

    /**
     * @return string[]
     */
    public static function withRelatives()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alias', 'type'], 'required'],
            [['type'], 'integer'],
            [['title', 'alias'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 0xffff],
            [['version'], 'default', 'value' => 0],
            [['file'], 'file', 'maxSize' => static::MAX_FILE_SIZE,
                'on' => [ApiController::UPDATE_SCENARIO, AdminController::UPDATE_SCENARIO]
            ],
            [['file'], 'file', 'maxSize' => static::MAX_FILE_SIZE, 'skipOnEmpty' => false,
                'on' => [ApiController::CREATE_SCENARIO, AdminController::CREATE_SCENARIO]
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    /**
     * @return string[]
     */
    public function getTypeNames()
    {
        return [
            null => '',
            static::TYPE_IMAGE => 'image',
            static::TYPE_JSON => 'JSON',
            static::TYPE_VIDEO => 'video',
        ];
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        $typeNames = $this->getTypeNames();
        if (key_exists($this->type, $typeNames)) {
            return $typeNames[$this->type];
        }
        return '';
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if(file_exists($this->generateRootPath())) {
            return Url::toRoute([$this->generatePath()], true);
        }

        return '';
    }

    /**
     * @return string
     */
    public function generateRootPath()
    {
        return \Yii::getAlias('@webroot')
            . '/' . $this->generatePath();
    }

    /**
     * @return string
     */
    public function generatePath()
    {
        $string = sprintf('%010u', $this->id); // 1 -> '0000000001'
        return Resource::UPLOAD_PATH
            . '/' . $this->alias
            . '/' . substr($string, 0, 4)
            . '/' . substr($string, 5, 3)
            . '/' . $string
            . '.' . $this->extension;
    }

    /**
     * Uses for email embedding
     *
     * @return string
     */
    public function getBase64Encoded()
    {
        if ($path = $this->generateRootPath()) {
            if (!file_exists($path)) {
                return '';
            }
            return 'data:' . mime_content_type($path) . ';base64,'
                . base64_encode(file_get_contents($path));
        }
        return '';
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getThumbnailImageUrl()
    {
        if ($this->type != static::TYPE_IMAGE) {
            return '';
        }
        if (\Yii::getAlias('@web', false) === false) {
            return '';
        }
        $result = '';
        try {
            $result = Url::toRoute(
                EasyThumbnailImage::thumbnailFileUrl(
                    $this->generatePath(),
                    88,
                    88,
                    EasyThumbnailImage::THUMBNAIL_INSET,
                    '.' . $this->getImageType()
                ),
                true
            );
        } catch (\Exception $e) {
            ExceptionLog::saveException($e);
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getImageSize()
    {
        if ($this->type != static::TYPE_IMAGE) {
            return '';
        }

        $path = $this->generateRootPath();
        if (!file_exists($path)) {
            return '';
        }

        list($width, $height) = getimagesize($path);
        return $width . 'x' . $height;
    }

    /**
     * @return string
     */
    public function getImageType()
    {
        if ($this->type != static::TYPE_IMAGE) {
            return '';
        }

        $path = $this->generateRootPath();
        if (!file_exists($path)) {
            return '';
        }

        //todo needs add other types
        switch (exif_imagetype($path)) {
            case IMAGETYPE_PNG:
                return 'png';
                break;
            default :
                return 'jpg';
        }
    }

    /**
     * @return string
     */
    public function getRelatedRecordLinks()
    {
        $links = '';
        foreach ($this->getRelatedRecords() as $index => $relatedRecord) {
            if (!in_array($index, $this->withRelatives())) {
                continue;
            }
            if ($relatedRecord) {
                foreach ($relatedRecord as $object) {
                    /** @var CoreModel $object */
                    $modelView = $modelName = (new \ReflectionClass($object))->getShortName();

                    $objectName = $object->id;
                    if (isset($object->name)) {
                        $objectName = $object->name;
                    } elseif (isset($object->title)) {
                        $objectName = $object->title;
                    }

                    $controllerName = strtolower(preg_replace('/(?<!^)[A-Z]+/', '-$0', $modelName));

                    $links .= Html::beginTag('div') . Html::a(
                            $modelView . ': ' . $objectName,
                            Url::to(['admin/' . $controllerName . '/view', 'id' => $object->id])
                        ) . Html::endTag('div');
                }
            }
        }

        return $links;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadFile()
    {
        if (!$this->file) {
            $this->file = UploadedFile::getInstance($this, 'file');
        }
        return $this->file;
    }

    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        $this->getUploadFile();
        return parent::beforeValidate();
    }


    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->createAt = time();
        }
        $this->updateAt = time();
        $this->version++;
        if ($this->getUploadFile()) {
            $this->extension = $this->getUploadFile()->getExtension();
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritDoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->uploadFile();

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritDoc
     */
    public function beforeDelete()
    {
        $path = $this->generatePath();
        if (is_file($path)) {
            unlink($path);
        }
        return parent::beforeDelete();
    }


    /**
     * @return bool
     */
    protected function uploadFile()
    {
        if ($this->getUploadFile()) {
            $path = $this->generatePath();
            FileHelper::createDirectory(dirname($path), $mode = 0775, $recursive = true);
            return $this->getUploadFile()->saveAs($path);
        }
        return false;
    }

    /**
     * @return array
     */
    public function getItemForApi()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->getUrl(),
            'alias' => $this->alias,
            'extension' => $this->extension,
            'createAt' => $this->createAt,
            'updateAt' => $this->updateAt,
            'version' => $this->version,
        ];
    }

    /**
     * @return array
     */
    public function getItemShortForApi()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->getUrl(),
            'version' => $this->version,
        ];
    }

    /**
     * @param \reinvently\ondemand\core\modules\resource\models\Resource[] $resources
     * @return array|null
     */
    public static function getArrayForApi($resources)
    {
        $response = [];
        foreach ($resources as $resource) {
            if ($resource) {
                $response[$resource->alias] = $resource->getItemShortForApi();
            }
        }

        if (!$response) {
            return null;
        }

        return $response;
    }
}