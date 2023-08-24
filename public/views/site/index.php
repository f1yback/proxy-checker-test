<?php

use app\forms\ProxyForm;

/**
 * @var yii\web\View $this
 * @var ProxyForm $proxy
 */

$this->title = 'Прокси-чекер';
$this->registerJsFile('/js/ajax-form.js', ['depends' => \yii\web\JqueryAsset::class]);
?>
<div class="site-index">
<?php $f = \yii\widgets\ActiveForm::begin([
            'action' => ['/site/save-proxy'],
            'id' => 'form-save',
            'enableAjaxValidation' => true,
            'validationUrl' => ['/site/validate-proxy']
]) ?>
<?= $f->field($proxy, 'proxyInput')->textarea(['rows' => 20]) ?>
<?= \yii\helpers\Html::button('Проверить', ['type' => 'submit', 'class' => 'btn btn-success']) ?>
<?php \yii\widgets\ActiveForm::end() ?>
</div>