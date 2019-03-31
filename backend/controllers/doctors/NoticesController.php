<?php

namespace backend\controllers\doctors;

use common\models\doctors\DoctorChats;
use common\models\doctors\DoctorHospitals;
use common\models\doctors\DoctorInfos;
use Yii;
use common\models\doctors\DoctorNoticesSearch;
use common\models\doctors\DoctorNotices;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use backend\actions\ViewAction;

/**
 * NoticesController implements the CRUD actions for DoctorNotices model.
 */
class NoticesController extends \yii\web\Controller
{
    /**
     * @auth
     * - item group=转诊平台 category=公告 description-get=列表 sort=0 method=get
     * - item group=转诊平台 category=公告 description-get=查看 sort=0 method=get  
     * - item group=转诊平台 category=公告 description=创建 sort-get=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=公告 description=修改 sort=0 sort-post=0 method=get,post  
     * - item group=转诊平台 category=公告 description-post=删除 sort=0 method=post  
     * @return array
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function () {

                    $searchModel = new DoctorNoticesSearch();
                    $dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                    return [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel,
                    ];

                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => DoctorNotices::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => DoctorNotices::className(),
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => DoctorNotices::className(),
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => DoctorNotices::className(),
            ],

        ];
    }

    function actionNotices()
    {
        $model = new DoctorChats();

        $myMsg = DoctorChats::find()->where(['to' => $this->getUser(), 'status' => 0])
            ->groupBy('from')
            ->select('count(*) as count,from,to')
            ->asArray()
            ->all();
        if (!empty($myMsg)) {
            foreach ($myMsg as $k => $v) {
                if (strpos($v['from'], 'hp') !== false) {
                    $hid = str_replace('hp', '', $v['from']);
                    $myMsg[$k]['name'] = DoctorHospitals::findOne(['id' => $hid])->hospital_name;
                } elseif ($v['from'] == 'admin') {
                    $myMsg[$k]['name'] = 'admin';
                } else {
                    $myMsg[$k]['name'] = DoctorInfos::findOne(['id' => $v['from']])->name;
                }
            }
        }
        return $this->render('notices', [
            'model' => $model,
            'myMsg' => $myMsg,
        ]);
    }

    function getUser()
    {
        if (Yii::$app->user->identity->username == 'admin') {
            return 'admin';
        } else if (Yii::$app->user->identity->hospital_id > 0) {
            return 'hp' . Yii::$app->user->identity->hospital_id;
        } else {
            return 'job' . Yii::$app->user->identity->job_number;
        }
    }


    function actionChat($to, $refresh = false)
    {
        $message = new DoctorChats();
        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {

            $message->content = Yii::$app->request->post('content');
            $message->created_at = time();
            $message->to = $to;

            if (Yii::$app->user->identity->username == 'admin') {
                $message->from = 'admin';
            } else {
                $message->from = 'hp' . Yii::$app->user->identity->hospital_id;
            }

            if ($to == $message->from) {
                exit(json_encode([
                    'code' => 0,
                    'msg' => '不能自己发给自己'
                ]));
            }

            exit(json_encode([
                'code' => $message->save() ? 1 : 0,
                'msg' => $message->getFirstErrors(),
                'time' => date('Y-m-d H:i:s')
            ]));
        }

        $toU = '';
        if (strpos($to, 'hp') !== false) {
            $toU = DoctorHospitals::findOne(['id' => str_replace('hp', '', $to)]);
        } elseif ($to == 'admin') {
            $toU['avatar'] = Yii::$app->user->identity->avatar;
        } else {
            $toU = DoctorInfos::findOne(['id' => $to]);
        }

        $data = $message::find()->where([
            'from' => $this->getUser(),
            'to' => $to,
        ])->orWhere([
            'from' => $to,
            'to' => $this->getUser(),
        ])->orderBy(['id' => SORT_DESC])->limit(5)->asArray()->all();

        if ($refresh){
            DoctorChats::updateAll(['status' => 1], [
                'to' => $this->getUser(),
                'from' => $to
            ]);
        }

        return $this->render('chat', [
            'data' => $data,
            'my_avatar' => Yii::$app->user->identity->avatar,
            'ta_avatar' => $toU ? $toU['avatar'] : '',
            'from' => $this->getUser(),
            'to' => $to
        ]);
    }

    function actionGetChat($from, $first = false)
    {
        $hospital_id = Yii::$app->user->identity->hospital_id;
        $query = DoctorChats::find();

        $to = 'admin';
        if (Yii::$app->user->identity->username != 'admin') {
            $to = 'hp' . $hospital_id;
        }
        $query->where(['to' => $to]);
        $query->andWhere(['from' => $from]);
        $query->andWhere(['status' => 0]);
        $data = $query->asArray()->all();

        if (!empty($data)) {
            foreach ($data as &$v) {
                $v['created_at'] = Yii::$app->formatter->asDatetime($v['created_at']);
                unset($v);
            }
        }

        if ($first) {
            return $data;
        }

        if (!empty($data)) {
            DoctorChats::updateAll(['status' => 1], [
                'to' => $to,
                'from' => $from
            ]);
        }
        exit(json_encode([
            'code' => 1,
            'data' => $data
        ]));
    }
}
