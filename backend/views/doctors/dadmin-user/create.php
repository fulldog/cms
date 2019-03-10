<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-25 11:14
 */
use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Admin Users'), 'url' => Url::to(['index'])],
    ['label' => Yii::t('app', 'Create') . Yii::t('app', 'Admin Users')],
];
/**
 * @var $model backend\models\User
 */
?>
<h3>注：业务员的账号即为其推广码！</h3>
<?= $this->render('_form', [
    'model' => $model,
]); ?>
