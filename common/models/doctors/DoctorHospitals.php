<?php

namespace common\models\doctors;

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
 */
class DoctorHospitals extends My
{
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
            [['created_at', 'updated_at'], 'integer'],
            [['imgs'], 'string'],
            [['hospital_name', 'city', 'address', 'levels'], 'string', 'max' => 255],
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

    static function like($coloum,$value){
        return self::find()->where($coloum.' like "%'.$value.'%"')->all();
    }
}
