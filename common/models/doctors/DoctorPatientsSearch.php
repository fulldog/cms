<?php

namespace common\models\doctors;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\doctors\DoctorPatients;

/**
 * DoctorPatientsSearch represents the model behind the search form about `common\models\doctors\DoctorPatients`.
 */
class DoctorPatientsSearch extends DoctorPatients
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'hospital_id', 'doctor_id', 'is_transfer', 'age'], 'integer'],
            [['name', 'phone', 'sex', 'desc','id_number','invite'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = DoctorPatients::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'hospital_id' => $this->hospital_id,
            'doctor_id' => $this->doctor_id,
            'is_transfer' => $this->is_transfer,
            'id_number' => $this->id_number,
            'age' => $this->age,
            'sex' => $this->sex,
            'phone' => $this->phone,
        ]);
        $this->SearchAddHospitalId($query);
        $this->SearchAddTime($query,$params,__CLASS__);

        if (\Yii::$app->user->identity->job_number){
            $query->andFilterWhere([
                'invite'=>\Yii::$app->user->identity->username
            ]);
        }
        $query->andFilterWhere(['like', 'name', $this->name])
//            ->andFilterWhere(['like', 'phone', $this->phone])
//            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'desc', $this->desc]);

        return $dataProvider;
    }

}
