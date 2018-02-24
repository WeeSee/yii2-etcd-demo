<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\grid\ActionColumn;

$this->title = 'Yii2-etcd Demo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        Enter name and value and Submit will send it to Etcd server
        under /yii2-etcd-test root node.
    </p>
    
    <div class="form">
 
        <?php $form = ActiveForm::begin(['layout' => 'inline']); ?>
     
            <?= $form->field($model, 'key',['inputOptions' => ['placeholder'=>'name']]) ?>
            <?= $form->field($model, 'value',['inputOptions' => ['placeholder'=>'value']]) ?>
     
            <div class="form-group">
                <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
            </div>
        <?php ActiveForm::end(); ?>
        
        <br>
    <p>
        All key values pairs found in Etcd under /yii2-etcd-test:
    </p>
        
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'name',
                'value',
                [
                    'class' => ActionColumn::className(),
                    'template' => '{delete}',
                    'buttons' => [
                        'delete' => function ($url, $model, $key) {
                            $options = array_merge([
                                'title' =>'Remove key',
                                'aria-label' => 'Remove key',
                                'data-pjax' => '0',
                            ]);
                            $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-trash"]);
                            $url = ['etcd','keyToRemove'=>$model['name']];
                            return Html::a($icon, $url, $options);
                        },
                    ],
                ],
            ],
        ]) ?>
        
    </div><!-- form -->

</div>
