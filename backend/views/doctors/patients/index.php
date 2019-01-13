<?php

use backend\widgets\Bar;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;
use backend\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\doctors\DoctorPatientsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = yii::t('app_doctor', 'Doctor Patients');
$this->params['breadcrumbs'][] = yii::t('app_doctor', 'Doctor Patients');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
                <?php //$this->render('_search', ['model' => $searchModel]); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => CheckboxColumn::className()],

                        'id',
                        'hospital_id',
                        'doctor_id',
                        'is_transfer',
                        'name',
                        // 'phone',
                        // 'sex',
                        // 'id_number',
                        // 'desc:ntext',
                        // 'created_at',
                        // 'updated_at',
                        // 'age',

                        ['class' => ActionColumn::className(),],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
