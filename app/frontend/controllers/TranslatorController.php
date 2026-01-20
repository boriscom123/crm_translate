<?php

namespace frontend\controllers;

use yii\web\Controller;
use common\models\Translator;

class TranslatorController extends Controller
{
    public function actionIndex()
    {
        // Fetch translators from the database
        $translators = Translator::find()->all();

        // Set breadcrumbs explicitly to control the labels
        $this->view->params['breadcrumbs'] = [
            ['label' => 'Главная', 'url' => ['/site/index']],
            ['label' => 'Переводчики']
        ];

        return $this->render('index', [
            'translators' => $translators
        ]);
    }
}