<?php

namespace anda\cms\models;

use Yii;

/**
 * This is the model class for table "web_setting".
 *
 * @property integer $id
 * @property string $type
 * @property string $name
 * @property string $language
 * @property string $value
 */
class Setting extends \anda\cms\base\Model
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%'.self::getTablePrefix().'setting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['value'], 'string'],
            [['type', 'name'], 'string', 'max' => 100],
            [['language'], 'string', 'max' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'name' => 'Name',
            'language' => 'Language',
            'value' => 'Value',
        ];
    }

    public static function getTypes()
    {
        return [
            'general' => ['label'=>'General','icon'=>'fa fa-globe'],
            'reading' => ['label'=>'Reading','icon'=>'fa fa-eye'],
        ];
    }
}
