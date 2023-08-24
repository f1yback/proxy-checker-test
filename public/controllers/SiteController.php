<?php

namespace app\controllers;

use app\forms\ProxyForm;
use app\services\ProxyService;
use yii\web\Controller;
use yii\web\Request;

class SiteController extends Controller
{

    private ProxyService $proxyService;

    public function __construct($id, $module, ProxyService $proxyService, $config = [])
    {
        $this->proxyService = $proxyService;
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index', ['proxy' => new ProxyForm()]);
    }

    public function actionSaveProxy()
    {
        $response = $this->proxyService->saveForm($this->proxyService->setFormData());
        if (!empty($response) && is_array($response)) {
            return $this->asJson($response);
        }
        return $this->redirect(['/site/proxy-check', 'pool' => $response ?? null]);
    }

    public function actionValidateProxy()
    {
        return $this->asJson($this->proxyService->validateForm($this->proxyService->setFormData()));
    }

    public function actionProxyCheck($pool = null)
    {
        if (empty($pool)) {
            return $this->redirect(['/site/index']);
        }
        return $this->render('proxy-check', ['proxy' => $this->proxyService->getProxyPool($pool)]);
    }
}
