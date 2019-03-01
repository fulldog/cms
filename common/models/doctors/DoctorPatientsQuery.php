<?php

namespace common\models\doctors;

/**
 * This is the ActiveQuery class for [[DoctorPatients]].
 *
 * @see DoctorPatients
 */
class DoctorPatientsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return DoctorPatients[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return DoctorPatients|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    function getPatients($hospital_id=0){
        $data = [];
        if ($hospital_id){
            $this->andFilterWhere(['hospital_id'=>$hospital_id]);
        }
        elseif (\Yii::$app->user->identity->hospital_id){
            $this->andFilterWhere(['hospital_id'=>\Yii::$app->user->identity->hospital_id]);
        }
        $temp = $this->select(['id','name','id_number'])->asArray()->all();
        if (!empty($temp)){
            foreach ($temp as $v){
                $data[$v['id']] = $v['name'].'【'.$v['id_number'].'】';
            }
        }
        return $data;
    }

}
