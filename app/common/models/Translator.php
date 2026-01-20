<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%translators}}".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property bool $weekday_availability
 * @property bool $weekend_availability
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class Translator extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%translators}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['weekday_availability', 'weekend_availability'], 'boolean'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'email'], 'string', 'max' => 255],
            [['email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'weekday_availability' => 'Weekday Availability',
            'weekend_availability' => 'Weekend Availability',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets translator availability based on day of week
     * @param string|null $date Date in 'Y-m-d' format, defaults to current date
     * @return bool
     */
    public function isAvailableOnDate($date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $dayOfWeek = date('w', strtotime($date)); // 0 (for Sunday) through 6 (for Saturday)
        $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6); // Sunday = 0, Saturday = 6

        if ($isWeekend) {
            return $this->weekend_availability;
        } else {
            return $this->weekday_availability;
        }
    }
}
