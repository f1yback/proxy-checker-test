<?php

namespace app\jobs;

use app\models\Proxy;
use yii\base\BaseObject;
use yii\queue\JobInterface;

class CheckProxyJob extends BaseObject implements JobInterface
{
    public int $poolId;
    private array $collection;
    private array $typeResponse;
    private array $curlResponse;

    /**
     * Запуска процесса сбора информации
     * @return void
     */
    private function process(): void
    {
        $this->checkTypes();
        $this->handle();
    }

    /**
     * Возможные типы прокси
     * @return string[]
     */
    private function getProxyTypes(): array
    {
        return [
            CURLPROXY_HTTP => 'http',
            CURLPROXY_HTTPS => 'https',
            CURLPROXY_SOCKS4 => 'socks4',
            CURLPROXY_SOCKS5 => 'socks5',
        ];
    }

    /**
     * Метод для сбора всей необходимой информации, полученной из multi curl
     * @return void
     */
    private function handle(): void
    {
        foreach ($this->typeResponse as $key => $item) {
            foreach ($this->getProxyTypes() as $k => $v) {
                if (!empty($item[$v])) {
                    $dataArray = json_decode($item[$v], 1);
                    $this->collection[$key]->type = $v;
                    $this->collection[$key]->status = Proxy::STATUS_ACTIVE;
                    $this->collection[$key]->country = $dataArray['geoplugin_countryName'] ?? '?';
                    $this->collection[$key]->city = $dataArray['geoplugin_city'] ?? '?';
                    $this->collection[$key]->real_ip = $dataArray['geoplugin_request'] ?? '?';
                    $this->collection[$key]->timeout = $this->curlResponse[$key][$v];
                    break;
                }
            }
            if ($this->collection[$key]->type === null) {
                $this->collection[$key]->status = Proxy::STATUS_INACTIVE;
            }
            $this->collection[$key]->check_status = Proxy::CHECK_STATUS_DONE;
            $this->collection[$key]->save();
        }
    }

    /**
     * Проверка проксей в multicurl процессе
     * @return void
     */
    private function checkTypes(): void
    {
        $curls = [];
        $mh = curl_multi_init();
        foreach ($this->collection as $key => $item) {
            foreach ($this->getProxyTypes() as $k => $v) {
                $curls[$key][$v] = curl_init();
                curl_setopt($curls[$key][$v], CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip={$item->ip}");
                curl_setopt($curls[$key][$v], CURLOPT_HEADER, 0);
                curl_setopt($curls[$key][$v], CURLOPT_CONNECTTIMEOUT, 7);
                curl_setopt($curls[$key][$v], CURLOPT_TIMEOUT, 7);
                curl_setopt($curls[$key][$v], CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curls[$key][$v], CURLOPT_PROXYTYPE, $k);
                curl_setopt($curls[$key][$v], CURLOPT_PROXY, "{$item->ip}:{$item->port}");
                curl_multi_add_handle($mh, $curls[$key][$v]);
            }
        }
        do {
            $status = curl_multi_exec($mh, $active);
            if ($active) {
                curl_multi_select($mh);
            }
        } while ($active && $status === CURLM_OK);
        foreach ($this->collection as $key => $item) {
            foreach ($this->getProxyTypes() as $v) {
                $this->typeResponse[$key][$v] = curl_multi_getcontent($curls[$key][$v]);
                $this->curlResponse[$key][$v] = curl_getinfo($curls[$key][$v], CURLINFO_TOTAL_TIME);
                curl_multi_remove_handle($mh, $curls[$key][$v]);
            }
        }
        curl_multi_close($mh);
    }

    /**
     * @param $queue
     * @return mixed|void
     */
    public function execute($queue)
    {
        $this->collection = Proxy::findAll(['pool' => $this->poolId]);
        if (!empty($this->collection)) {
            $this->process();
        }
    }
}