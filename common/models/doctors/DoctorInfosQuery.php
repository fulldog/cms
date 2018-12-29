<?php

namespace common\models\doctors;

/**
 * This is the ActiveQuery class for [[DoctorInfos]].
 *
 * @see DoctorInfos
 */
class DoctorInfosQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return DoctorInfos[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return DoctorInfos|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
