<?php

use yii\db\Migration;

class m260119_120001_seed_translators_table extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('{{%translators}}', [
            'name',
            'email',
            'weekday_availability',
            'weekend_availability'
        ], [
            [
                'Иванов Иван',
                'ivanov@example.com',
                true,
                false
            ],
            [
                'Петров Петр',
                'petrov@example.com',
                true,
                false
            ],
            [
                'Сидоров Алексей',
                'sidorov@example.com',
                true,
                false
            ],
            [
                'Кузнецов Сергей',
                'kuznetsov@example.com',
                false,
                true
            ],
            [
                'Волков Дмитрий',
                'volkov@example.com',
                false,
                true
            ],
            [
                'Смирнова Анна',
                'smirnova@example.com',
                true,
                true
            ],
            [
                'Попова Мария',
                'popova@example.com',
                true,
                true
            ]
        ]);
    }

    public function safeDown()
    {
        $this->delete('{{%translators}}', 'email IN (
            "ivanov@example.com",
            "petrov@example.com",
            "sidorov@example.com",
            "kuznetsov@example.com",
            "volkov@example.com",
            "smirnova@example.com",
            "popova@example.com"
        )');
    }
}