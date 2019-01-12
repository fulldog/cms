<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/26 0026
 * Time: 16:53
 */

namespace frontend\controllers;

use backend\models\form\BannerForm;
use backend\models\form\BannerTypeForm;
use common\models\doctors\DoctorHospitalsQuery;
use common\models\Options;
use Yii;
use frontend\models\form\SignupForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use common\models\LoginForm;
use common\models\doctors\DoctorHospitals;
use common\models\doctors\DoctorInfos;
use common\models\doctors\DoctorPatients;
use frontend\models\User;

class IndexController extends BaseController
{

    function init()
    {
        parent::init();
    }

    function actionIndex()
    {
        return [
            'code'=>1,
            'msg'=>'',
            'doctors'=>DoctorInfos::findAll(),
            'users'=>User::find()->select('*')->all()
        ];
    }

    function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'verbs' => [
                'actions' => [
                    'login' => ['POST'],
                    'register' => ['POST'],
                    'submit_info' => ['POST'],
                    'create_patient' => ['POST'],
                    'transfer_patient' => ['POST'],
                ],
            ],
            'access' => [
                'only' => ['submit_info', 'create_patient', 'transfer_patient', 'transfer_patient_list', 'patient_detail', 'my_patient_list','getme'],
            ]
        ]);
    }

    /**
     * password username
     * @return array
     * @throws \Throwable
     */
    function actionLogin()
    {
        if (!hasLogin()) {
            $model = new LoginForm();
            $model->username = Yii::$app->request->post('tel');
            $model->password = Yii::$app->request->post('password');
            if (!$model->login()) {
                return [
                    'code' => 0,
                    'msg' => '账号或密码错误'
                ];
            }
        }
        return [
            'data' => ArrayHelper::merge(['is_complete'=>$this->getDoctor()],Yii::$app->user->identity),
            'code' => 1,
            'msg' => ''
        ];
    }

    function actionRegister()
    {
        $username = Yii::$app->request->post('tel');
        $password = Yii::$app->request->post('password');

        if (User::findOne(['username' => $username])) {
            return [
                'code' => 0,
                'msg' => '改账号已被注册'
            ];
        } else {
            $user = new SignupForm();
            $user->username = $username;
            $user->password = $password;
            $user->email = $username.'@qq.com';

            if ($user->signup()) {
                $model = new LoginForm();
                $model->username = $username;
                $model->password = $password;
                $model->login();
                return [
                    'data' => ArrayHelper::merge(['is_complete'=>$this->getDoctor()],Yii::$app->user->identity),
                    'code' => 1,
                    'msg' => '注册成功',

                ];
            } else {
                return [
                    'code' => 0,
                    'msg' => '注册失败，请检查参数'
                ];
            }
        }
    }


    /**
     * 完善信息表单
     * Parma:{name,doctor_type,role,hospital_location,hospital_name,certificate(base64)}
     */
    function actionSubmit_info()
    {
        $_post = Yii::$app->request->post();
        $uid = $this->_getUid();
        $model = DoctorInfos::findOne(['uid'=>$uid]);
        $msg = '添加成功';
        if (!$model){
            $msg = '修改成功';
            $model = new DoctorInfos();
        }
        if ($res = $this->_setValForObj($model, $_post)) {
//            $hospital = new DoctorHospitals();
//            $hospital->name = $res->hospital_name;
//            $hospital->address = $res->hospital_location;
//            $hospital->save();
//            $res->hospital_id = $hospital->id;
            $this->_save($res);
            return [
                'data' => $res->toArray(),
                'code' => 1,
                'msg' => $msg
            ];
        }
        return [
            'code' => 0,
            'msg' => 'error'
        ];
    }

    /**
     * 创建病人
     * @return array
     */
    function actionCreate_patient()
    {
        $_post = Yii::$app->request->post();
        $_post['doctor_id'] = $this->_getUid();
        $_post['hospital_id'] = DoctorInfos::getHospitalIdByUid($_post['doctor_id']);
        if ($_post['hospital_id'] && ($res = $this->_setValForObj(new DoctorPatients(), $_post, true))) {
            return [
                'data' => $res->toArray(),
                'code' => 1,
                'msg' => 'succ'
            ];
        }
        return [
            'code' => 0,
            'msg' => '参数错误'
        ];
    }

    /**
     * 转移病人
     * @return array
     */
    function actionTransfer_patient()
    {
        $_post = Yii::$app->request->post();
        if (!$_post['patient_id'] || $_post['hospital_id']) {
            return [
                'code' => 0,
                'msg' => '参数错误'
            ];
        }
        $patient = DoctorPatients::findOne(['id' => $_post['patient_id']]);
//        $patient->is_transfer = 0;
        $patient->hospital_id = $_post['hospital_id'];
        $patient->doctor_id = 0;
        if ($patient->update()) {
            return [
                'data' => $patient->toArray(),
                'code' => 1,
                'msg' => 'succ'
            ];
        }
        return [
            'code' => 0,
            'msg' => 'error'
        ];
    }

    function actionMy_patient_list(){
        return [
            'data' => DoctorPatients::getPatientsByDoctorId($this->_getUid()),
            'code' => 1,
            'msg' => ''
        ];
    }

    /**
     *转给我的病人
     */
    function actionTransfer_patient_list()
    {
        return [
            'data' => DoctorPatients::getPatientsByDoctorId($this->_getUid(),true),
            'code' => 1,
            'msg' => ''
        ];
    }

    /**
     * 病人明细
     * @return array
     */
    function actionPatient_detail()
    {
        $Patient_id = Yii::$app->request->get('patient_id');
        return [
            'data' => DoctorPatients::findOne(['id' => $Patient_id]),
            'code' => 1,
            'msg' => ''
        ];
    }

    /**
     * 医院搜索
     * @return array
     */
    function actionSearch_hospital()
    {
        $search_word = Yii::$app->request->get('search_word');
        return [
            'data'=>DoctorHospitals::like('hospital_name',$search_word)
        ];
    }

    function actionGetme(){
        return ArrayHelper::merge(['is_complete'=>$this->getDoctor()],Yii::$app->user->identity);
    }

    function getDoctor($uid=''){
        if (!$uid){
            $uid = Yii::$app->user->getId();
        }
        $info = DoctorInfos::findOne(['uid'=>$uid]);
        return empty($info) ? null : $info;
    }

    /**
     * 首页信息
        描述：首页含有banner、推荐医院、推荐医生。
        这三个在后台可以分别设置。是否可以放在一个接口里，还是分三个接口
        index_info
        param: {}
        result: {banners: [‘xxx.jpg’], recommend_hospitals: [],recommend_doctors: [] }
        recommend_hospitals 信息按照新增医院的信息返回
     */
    function actionIndex_info(){
        //banners
        $banner = BannerTypeForm::find()->where(['type' => Options::TYPE_BANNER,'name'=>'index'])->asArray()->one();
        $imgs = [];
        if (!empty($banner)){
            $imgs = \Qiniu\json_decode($banner['value']);
        }
        return [
            'banners'=>$imgs,
            'recommend_hospitals'=>DoctorHospitals::findAll(['recommend'=>1,'status'=>1]),
            'recommend_doctors'=>DoctorInfos::findAll(['recommend'=>1,'status'=>1]),
        ];
    }

    function actionError(){
        return [
            'code'=>'0',
            'msg'=>'not found!',
            'httpCode'=>404
        ];
    }
}