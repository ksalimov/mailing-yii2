<?php

/* @var $this yii\web\View */
/* @var $model app\models\Gmail */

use yii\widgets\DetailView;

$this->title = 'Message';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-message">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'from',
                'label' => 'Отправитель',
                'visible' => $box == 'inbox',
            ],
            [
                'attribute' => 'receiver',
                'label' => 'Получатель',
                'visible' => $box == 'sent',
            ],
            [
                'attribute' => 'subject',
                'label' => 'Тема письма',
            ],
            [
                'attribute' => 'date',
                'label' => 'Дата',
                'format' => ['datetime', 'dd.MM.yyyy HH:mm'],
            ],
            [
                'attribute' => 'body',
                'label' => 'Сообщение',
                'format' => 'html',
            ],
        ],
    ]) ?>

</div>
