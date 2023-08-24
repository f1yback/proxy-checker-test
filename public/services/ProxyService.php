<?php

namespace app\services;

use app\forms\ProxyForm;
use app\jobs\CheckProxyJob;
use app\models\Proxy;
use yii\db\ActiveRecord;
use yii\queue\redis\Queue;
use yii\widgets\ActiveForm;

class ProxyService
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function setFormData(): ProxyForm
    {
        $form = new ProxyForm();
        $form->load(\Yii::$app->request->post());
        return $form;
    }

    private function findProxy(string $ip, int $port): ActiveRecord|null
    {
        return Proxy::find()
            ->where(['ip' => $ip, 'port' => $port])
            ->one();
    }

    public function saveForm(ProxyForm $form): array|int
    {
        if ($form->validate()) {
            $lastPoolElem = Proxy::find()
                ->limit(1)
                ->orderBy('id desc')
                ->select('pool')
                ->asArray()
                ->one();
            $lastPool = empty($lastPoolElem['pool']) ? 1 : $lastPoolElem['pool'] + 1;
            foreach ($form->getParsedProxy() as $line) {
                $proxyData = explode(':', $line);
                $proxy = $this->findProxy($proxyData[0], (int)$proxyData[1]);
                if ($proxy === null) {
                    $proxy = new Proxy();
                }
                $proxy->ip = $proxyData[0];
                $proxy->port = (int)$proxyData[1];
                $proxy->check_status = 0;
                $proxy->pool = (int)$lastPool;
                $proxy->save();
            }
            $this->queue->push(new CheckProxyJob(['poolId' => $lastPool]));
            return $lastPool;
        }
        return $form->errors;
    }

    public function validateForm(ProxyForm $form): array
    {
        return ActiveForm::validate($form);
    }

    public function getProxyPool(int $pool): array
    {
        return Proxy::find()
            ->where(['pool' => $pool])
            ->asArray()
            ->all();
    }
}