<?php

/**
 * @var \app\models\Proxy[] $proxy
 */

$this->title = 'Проверка прокси';
$js = <<<JS
setInterval(function () {
    $.pjax.reload({container: "#my-pjax-container"})
}, 1000);
JS;

$this->registerJs($js);
?>

<div class="proxy-check-block">
    <?php \yii\widgets\Pjax::begin(['id' => 'my-pjax-container']) ?>
    <table class="table table-responsive table-bordered">
        <tr>
            <th>ip:port</th>
            <th>type</th>
            <th>country/city</th>
            <th>status</th>
            <th>timeout</th>
            <th>real ip</th>
        </tr>
        <?php foreach($proxy as $key => $val): ?>
            <tr>
                <td><?= "{$val['ip']}:{$val['port']}" ?></td>
                <td><?= $val['type'] ?></td>
                <td><?= "{$val['country']}/{$val['city']}" ?></td>
                <td><?= $val['status'] === \app\models\Proxy::STATUS_ACTIVE ? 'active' : 'inactive' ?></td>
                <td><?= $val['timeout'] ?></td>
                <td><?= $val['real_ip'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php \yii\widgets\Pjax::end() ?>
</div>