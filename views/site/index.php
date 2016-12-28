<?php

use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = 'Mail client';
?>
<div class="site-index">

    <div class="body-content">

        <div class="row">
            <div class="col-lg-9 col-lg-offset-3">
                <p>
                    <a class="btn btn-default" href="<?= Url::to(['site/mail']) ?>">Написать письмо</a>
                    <a id="delete_mail" class="btn btn-default">Удалить выбранные письма</a>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-3">
                <h2 style="margin-top: 0">Почта</h2>
                <?php if($box == 'inbox'): ?>
                    <p class="active">Входящие</p>
                    <p><a href="<?= Url::toRoute(['site/index', 'box' => 'sent']) ?>">Отправленные</a></p>
                <?php else: ?>
                    <p><a href="<?= Url::toRoute(['site/index', 'box' => 'inbox']) ?>">Входящие</a></p>
                    <p class="active">Отправленные</p>
                <?php endif; ?>
            </div>
            <div class="col-lg-9">
                <?php Pjax::begin(); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'rowOptions'   => function ($model, $key, $index, $grid) {
                        return [
                            'class' => 'select',
                            'id' => $model['id'],
                        ];
                    },
                    'columns' => [
                        ['class' => 'yii\grid\CheckboxColumn'],
                        [
                            'attribute' => 'receiver',
                            'label' => 'Получатель',
                            'visible' => $dataProvider->id == 'sent',
                        ],
                        [
                            'attribute' => 'from',
                            'label' => 'Отправитель',
                            'visible' => $dataProvider->id == 'inbox',
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
                    ],
                ]) ?>
                <?php Pjax::end() ?>
            </div>
        </div>

    </div>
</div>
