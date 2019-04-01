<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\resource\models;

use igogo5yo\uploadfromurl\UploadFromUrl;
use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use reinvently\ondemand\core\components\loggers\models\ExceptionLog;
use reinvently\ondemand\core\components\model\CoreModel;
use reinvently\ondemand\core\components\transport\ApiInterface;
use reinvently\ondemand\core\components\transport\ApiTransportTrait;
use reinvently\ondemand\core\controllers\admin\AdminController;
use reinvently\ondemand\core\controllers\rest\ApiController;
use reinvently\ondemand\core\exceptions\LogicException;
use reinvently\ondemand\core\modules\resource\components\EasyThumbnailImage;
use yii\db\Exception;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\imagine\Image;
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

class Resource extends CoreModel implements ApiInterface
{
    use ApiTransportTrait;

    const TYPE_IMAGE = 1;
    const TYPE_JSON = 2;
    const TYPE_VIDEO = 3;

    const UPLOAD_PATH = 'uploads';

    const MAX_FILE_SIZE = 209715200; /*200MB*/

    const UPLOAD_FROM_URL_SCENARIO = 'upload_from_url_scenario';

    /** @var UploadedFile */
    public $file;

    /** @var UploadFromUrl */
    public $fileFromUrl;

    /** @var string */
    public $url;

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
     * @param $url
     * @param $width
     * @param $height
     * @param $alias
     * @param bool $deleteOrigin
     * @return \reinvently\ondemand\core\modules\resource\models\Resource
     * @throws LogicException
     */
    static protected function createTempResource($url, $width, $height, $alias, $deleteOrigin = true)
    {
        $resource = new Resource();
        $resource->type = Resource::TYPE_IMAGE;
        $resource->alias = 'temp';
        $resource->url = $url;

        $resource->setScenario(Resource::UPLOAD_FROM_URL_SCENARIO);
        if ($resource->save()) {
            $resource2 = new Resource();
            $resource2->type = Resource::TYPE_IMAGE;
            $resource2->alias = $alias;

            $resource2 = static::createThumbnailResource($resource->generateRootPath(), $resource2, $width, $height);
        } else {
            throw new LogicException(Json::encode($resource->getErrors()));
        }

        if ($deleteOrigin) {
            $resource->delete();
        }

        return $resource2;

    }

    /**
     * @param $path
     * @param \reinvently\ondemand\core\modules\resource\models\Resource $resource
     * @param $width
     * @param $height
     * @return \reinvently\ondemand\core\modules\resource\models\Resource
     * @throws LogicException
     */
    static protected function createThumbnailResource($path, $resource, $width, $height)
    {
        $extensions = FileHelper::getExtensionsByMimeType(FileHelper::getMimeType(
            $path, null, false));

        if (!$extensions) {
            throw new LogicException('Invalid ExtensionsByMimeType');
        }


        $invalidExtension = true;
        foreach ($extensions as $extension) {
            $invalidExtension = false;
            $resource->extension = $extension;

            try {
                if (!$resource->save()) {
                    throw new LogicException(Json::encode($resource->getErrors()));
                }

                $mode = ManipulatorInterface::THUMBNAIL_INSET;

                $box = new Box($width, $height);
                $image = Image::getImagine()->open($path);
                $image = $image->thumbnail($box, $mode);

                $filename = $resource->generateRootPath();
                FileHelper::createDirectory(dirname($filename), $mode = 0777, $recursive = true);
                $image->save($filename);
            } catch (\Imagine\Exception\InvalidArgumentException $exception) {
                $invalidExtension = true;
                continue;
            }
            break;
        }

        if ($invalidExtension) {
            throw new LogicException('Invalid ExtensionsByMimeType');
        }

        return $resource;
    }

    /**
     * @param $url
     * @param $width
     * @param $height
     * @param $alias
     * @param bool $deleteOrigin
     * @return \reinvently\ondemand\core\modules\resource\models\Resource
     */
    static public function generateThumbnailByUrl($url, $width, $height, $alias, $deleteOrigin = true)
    {
        try {
            return self::createTempResource($url, $width, $height, $alias, $deleteOrigin);
        } catch (\Exception $e) {
            ExceptionLog::saveException($e);
        }
        return null;
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
            [['description'], 'string', 'max' => 0xfffe],
            [['version'], 'default', 'value' => 0],
            [['file'], 'file', 'maxSize' => static::MAX_FILE_SIZE,
                'on' => [ApiController::UPDATE_SCENARIO, AdminController::UPDATE_SCENARIO]
            ],
            [['file'], 'file', 'maxSize' => static::MAX_FILE_SIZE, 'skipOnEmpty' => false,
                'on' => [ApiController::CREATE_SCENARIO, AdminController::CREATE_SCENARIO]
            ],
            [['fileFromUrl'], 'igogo5yo\uploadfromurl\FileFromUrlValidator',
                'maxSize' => static::MAX_FILE_SIZE, 'skipOnEmpty' => false,
                'on' => [static::UPLOAD_FROM_URL_SCENARIO]
            ],
            [['url'], 'string', 'on' => [static::UPLOAD_FROM_URL_SCENARIO]],
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
            . '/' . substr($string, 4, 3)
            . '/' . $string
            . ($this->extension ? '.' . $this->extension : '');
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
                    $this->generateRootPath(),
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
                    if (isset($object->name) && $object->name) {
                        $objectName = $object->name;
                    } elseif (isset($object->title) && $object->title) {
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
     * @return UploadFromUrl
     */
    public function getUploadFromUrl()
    {
        if (!$this->fileFromUrl && $this->getScenario() == static::UPLOAD_FROM_URL_SCENARIO) {
            $this->fileFromUrl = UploadFromUrl::getInstance($this, 'url');
        }
        return $this->fileFromUrl;
    }

    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        $this->getUploadFile();
        $this->getUploadFromUrl();
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
        if ($this->getUploadFromUrl()) {
            $this->extension = $this->getUploadFromUrl()->extension;
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
        $path = $this->generateRootPath();
        if (is_file($path) && file_exists($path)) {
            unlink($path);
        }
        return parent::beforeDelete();
    }


    /**
     * @return bool
     */
    protected function uploadFile()
    {
        $path = $this->generateRootPath();
        if ($this->getUploadFile()) {
            FileHelper::createDirectory(dirname($path), $mode = 0777, $recursive = true);
            return $this->getUploadFile()->saveAs($path);
        }
        if ($this->getUploadFromUrl()) {
            FileHelper::createDirectory(dirname($path), $mode = 0777, $recursive = true);
            return $this->getUploadFromUrl()->saveAs($path);
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