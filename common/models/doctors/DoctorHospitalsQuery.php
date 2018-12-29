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
}
