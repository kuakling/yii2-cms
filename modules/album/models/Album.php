<?php

namespace anda\cms\modules\album\models;

use Yii;
use anda\cms\modules\post\models\Post as ActiveRecord;
use yii\helpers\FileHelper;


class Album extends ActiveRecord
{
    const STATUS_PUBLISHED = 1;
    const STATUS_UNPUBLISHED = 0;

    const ALBUM_FOLDER = 'album';
    const GALLERY_FOLDER = 'gallery';

    public $myName = 'album';



    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $this->prepareDirectories();
    }

    public function afterDelete()
    {
        parent::afterDelete(); // TODO: Change the autogenerated stub

        $model = self::find()->where(['id' => $this->id])->one();
        if ($model === null) {
            $modelDirs = $this->getDirectories();
            if (is_dir($modelDirs['base'])) {
                FileHelper::removeDirectory($modelDirs['base']);
            }
        }
    }





    /*************Upload Widget**********/
    public function getDirectories()
    {
        $dirs['base'] = $this->masterModule->uploadDir.'/'.self::ALBUM_FOLDER.'/'.self::GALLERY_FOLDER.'/'.$this->id;
        $dirs['full'] = $dirs['base'].'/full';
        $dirs['thumb'] = $dirs['base'].'/thumb';
        $dirs['temp'] = $dirs['base'].'/temp';

        return $dirs;
    }
    public function prepareDirectories()
    {
        foreach ($this->directories as $dir){
            if(!is_dir($dir)){
                FileHelper::createDirectory($dir);
            }
        }
    }

    public function getBaseImages()
    {
        $dirs = $this->directories;
        $files = [];
        $images = [];
        if(is_dir($dirs['thumb'])){
            $files = FileHelper::findFiles($dirs['thumb']);
        }
        sort($files);
        foreach ($files as $file){
            if($this->isImage($file)){
                $images[] = $file;
            }
        }
        return $images;
    }

    public function getImages()
    {
        $items = [];
        $dirs = $this->getDirectories();
        $images = $this->getBaseImages();
        foreach ($images as $key => $image) {
            $items[] = [
                'full' => $this->resolvePath($dirs['full'].'/'.basename($image)),
                'thumb' => $this->resolvePath($dirs['thumb'].'/'.basename($image))
            ];
        }

        return $items;
    }

    public function getInitialPreview()
    {
        $imagesUrl = [];
        foreach ($this->baseImages as $image){
            $imagesUrl[] = $this->resolvePath($image);
        }

        return $imagesUrl;
    }

    public function getInitialPreviewConfig()
    {
        $imageInfo = [];
        foreach ($this->baseImages as $image){
            $imageInfo[] = $this->imageInfo($image);
        }

        return $imageInfo;
    }

    /**
     * Replaces all placeholders in path variable with corresponding values.
     */
    protected function resolvePath($path)
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $uploadUrl = rtrim($this->masterModule->uploadUrl, '/');
        $filePath = ltrim(str_replace($this->masterModule->uploadDir, '', $path), '/');
        return $uploadUrl . '/' . $filePath;
    }

    protected function imageInfo($path)
    {
        $pathFullSize = str_replace('/thumb/', '/full/', $path);
        if($this->isImage($pathFullSize)){
            $info = [
                'caption' => basename($pathFullSize),
                'size' => filesize($pathFullSize),
                'key' => basename($pathFullSize),
            ];
        }else{
            $info = [
                'caption' => 'Image error.',
                'size' => 'Image error.',
            ];
        }

        return $info;
    }


    public function isImage($filePath){
        return @is_array(getimagesize($filePath)) ? true : false;
    }
}
