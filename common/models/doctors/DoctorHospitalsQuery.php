<?php

namespace common\models\doctors;

/**
 * This is the ActiveQuery class for [[DoctorHospitals]].
 *
 * @see DoctorHospitals
 */
class DoctorHospitalsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return DoctorHospitals[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return DoctorHospitals|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    function getHospitals($hospital_id=0): array {
        $data = [];
        if ($hospital_id){
            $this->andFilterWhere(['id'=>$hospital_id]);
        }
        elseif (\Yii::$app->user->identity->hospital_id){
            $this->andFilterWhere(['id'=>\Yii::$app->user->identity->hospital_id]);
        }
        $temp = $this->select(['id','hospital_name'])->asArray()->all();
        if (!empty($temp)){
            foreach ($temp as $v){
                $data[$v['id']] = $v['hospital_name'];
            }
        }
        return $data;
    }
}
