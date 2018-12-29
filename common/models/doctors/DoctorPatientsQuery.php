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
}
