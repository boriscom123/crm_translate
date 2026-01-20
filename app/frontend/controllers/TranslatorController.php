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

        return $this->render('index', [
            'translators' => $translators
        ]);
    }
}