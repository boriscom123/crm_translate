<?php

namespace backend\controllers;

use common\models\Translator;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\ContentNegotiator;

/**
 * Translator controller
 */
class TranslatorController extends ActiveController
{
    public $modelClass = 'common\models\Translator';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Override content negotiator to handle JSON responses properly
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        // Configure verbs for API
        $behaviors['verbFilter'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
                'view' => ['GET'],
            ],
        ];

        return $behaviors;
    }
}
