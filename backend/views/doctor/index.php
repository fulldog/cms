<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Doctor Infos';
$this->params['breadcrumbs'][] = yii::t('app', 'Doctor Infos');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
                    <?php Pjax::begin(); ?>            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        ['class' => CheckboxColumn::className()],

//                        'id',
                        'uid',
                        'hospital_id',
                        'name',
                        'doctor_type',
                        // 'role',
                        // 'hospital_location',
                        // 'hospital_name',
                        // 'certificate:ntext',
                        // 'create_at',
                        // 'update_at',

                        ['class' => ActionColumn::className(),],
                    ],
                ]); ?>
<?php Pjax::end(); ?>            </div>
        </div>
    </div>
</div>
