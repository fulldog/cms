<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/8
 * Time: 23:11
 */

namespace api\controllers;

use api\models\Vote;
use common\models\VoteChild;
use common\models\VoteRecord;
use yii\rest\Controller;
use api\service\AuthService;
use api\service\Output;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;

class VoteController extends Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                //使用ComopositeAuth混合认证
                'class' => CompositeAuth::className(),
                'optional' => [
//                    'index',//无需access-token的action
//                    'list'
                ],
                'authMethods' => [
                    HttpBasicAuth::className(),
                    HttpBearerAuth::className(),
                    [
                        'class' => AuthService::className(),
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
//                'actions' => [
//                    'info' => ['GET'],
//                ],
            ],
        ]);
    }

    public function actionIndex()
    {
        $id = \Yii::$app->request->get('id');
        $data = Vote::find()->where(['id' => $id])->andWhere(['>', 'end_time', time()])->asArray()->one();
        if ($data) {
            $data['uv'] = VoteRecord::find()->where(['vid' => $data['id']])->distinct('uid')->count();
            $data['childList'] = VoteChild::find()->where(['vid' => $data['id']])->asArray()->all();
            $data['userCount'] = count($data['childList']);
//            Vote::updateAll(['pv' => $data['pv'] + 1], ['id' => $data['id']]);
            \Yii::$app->db->createCommand("update " . Vote::tableName() . " set pv=`pv`+1 where id=:id", [':id' => $data['id']])->query();
        }
        return Output::out($data);
    }

    public function actionDetail()
    {
        $id = \Yii::$app->request->get('id');
        $data = VoteChild::findOne(['id' => $id]);
        \Yii::$app->db->createCommand("update " . Vote::tableName() . " set pv=`pv`+1 where id=:id", [':id' => $data->vid])->query();
        \Yii::$app->db->createCommand("update " . VoteChild::tableName() . " set pv=`pv`+1 where id=:id", [':id' => $data->id])->query();
//        Vote::updateAll(['pv' => '`pv`+1'], ['id' => $data->vid]);
//        VoteChild::updateAll(['pv' => $data->pv + 1], ['id' => $data->id]);
        return Output::out($data);
    }

    public function actionAdd()
    {
        $date = date('Y-m-d');
        $id = \Yii::$app->request->get('id');
        $data = VoteChild::find()->where(['id' => $id])->asArray()->one();
        if ($data) {
            $uid = \Yii::$app->user->identity->getId();
            $todayCnt = VoteRecord::find()
                ->where(['uid' => $uid, 'vid' => $data['vid'], 'vcid' => $data['vid'], 'date' => $date])
                ->count();
            if ($todayCnt >= 1) {
                return Output::out([], 0, '今日已投票');
            } else {
                $dao = new VoteRecord();
                $dao->date = $date;
                $dao->vid = $data['vid'];
                $dao->vcid = $data['id'];
                $dao->uid = $uid;
                if ($dao->save()) {
                    \Yii::$app->db->createCommand("update " . Vote::tableName() . " set vote_count=`vote_count`+1 where id=:id", [':id' => $data['vid']])->query();
                    \Yii::$app->db->createCommand("update " . VoteChild::tableName() . " set vote_count=`vote_count`+1 where id=:id", [':id' => $data['id']])->query();
                    return Output::out($data);
                }
                return Output::out([], 0, current($dao->getFirstErrors()));
            }
        } else {
            return Output::out([], 0, '投票不存在');
        }
    }
}