<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proxy".
 *
 * @property int $id
 * @property string $ip
 * @property int $port
 * @property string $type
 * @property string $country
 * @property string $city
 * @property int|null $status
 * @property int|null $pool
 * @property float|null $timeout
 * @property string|null $real_ip
 * @property int|null $check_status
 */
class Proxy extends \yii\db\ActiveRecord
{

    public const STATUS_INACTIVE = 0;
    public const STATUS_ACTIVE = 1;
    public const CHECK_STATUS_WAIT = 0;
    public const CHECK_STATUS_DONE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proxy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ip', 'port'], 'required'],
            [['port', 'status', 'check_status', 'pool'], 'integer'],
            [['timeout'], 'number'],
            [['ip', 'type', 'country', 'city', 'real_ip'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'Ip',
            'port' => 'Port',
            'type' => 'Type',
            'pool' => 'Pool',
            'country' => 'Country',
            'city' => 'City',
            'status' => 'Status',
            'timeout' => 'Timeout',
            'real_ip' => 'Real Ip',
            'check_status' => 'Check Status',
        ];
    }
}
