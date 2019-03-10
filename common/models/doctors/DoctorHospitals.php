<?php

namespace common\models\doctors;

use backend\models\DadminUser;
use Yii;

/**
 * This is the model class for table "{{%doctor_hospitals}}".
 *
 * @property int $id
 * @property string $hospital_name 医院名称
 * @property string $city 城市
 * @property string $province
 * @property string $area
 * @property string $address 地址
 * @property string $levels 等级
 * @property string $grade 等级
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property string $imgs 图片
 * @property int $recommend
 * @property int $status
 * @property string $code
 * @property string $tel
 * @property string $invite
 * @property string $avatar
 */
class DoctorHospitals extends My
{

    protected $recommend_text;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%doctor_hospitals}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hospital_name', 'province','tel'], 'required'],
            [['created_at', 'updated_at','recommend'], 'integer'],
            [['imgs', 'grade', 'area', 'status','tel', 'invite','avatar'], 'safe'],
            [['hospital_name', 'city', 'address', 'levels', 'code',], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'hospital_name' => '医院名称',
            'city' => '城市',
            'area' => '区域',
            'province' => '省份',
            'grade' => '等级',
            'address' => '地址',
            'levels' => '等级',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'imgs' => '图片',
            'recommend' => '是否推荐',
            'status' => '状态',
            'code' => '医院代码',
            'tel' => '电话',
            'invite' => '被邀请码',
            'avatar' => '头像'
        ];
    }

    /**
     * {@inheritdoc}
     * @return DoctorHospitalsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DoctorHospitalsQuery(get_called_class());
    }

    static function like($coloum, $value, $page)
    {
        $query = self::find()->limit(20)->offset(20 * $page)->where(['status' => 1]);
        if (!$value) {
            return $query->all();
        }
        return $query->andWhere($coloum . ' like "%' . $value . '%"')->all();
    }

    function beforeSave($insert)
    {
        if ($this->imgs && is_array($this->imgs)) {
            $this->imgs = json_encode($this->imgs);
        }

        if (empty($this->code)) {
            $this->code = $this->createCode();
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    function afterFind()
    {
        if ($this->imgs) {
            $this->imgs = \Qiniu\json_decode($this->imgs);
        }
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    static function getHospitalInfo($hid, $key = '')
    {
        $info = self::findOne(['id' => $hid]);
        if ($info) {
            if ($key) return $info->$key;

            return $info;
        }
        return null;
    }

    function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }


    function getRelatedAdmin()
    {
        return $this->hasOne(DadminUser::className(), ['hospital_id' => 'id']);
    }

    function createCode($length = null)
    {
        if (!$length) $length = 10;

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        if (self::findOne(['code'=>$randomString])){
            return $this->createCode();
        }
        return $randomString;
    }
}
