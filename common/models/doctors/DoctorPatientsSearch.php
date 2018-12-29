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
            [['id', 'hospital_id', 'doctor_id', 'is_transfer', 'id_number', 'created_at', 'updated_at', 'age'], 'integer'],
            [['name', 'tel', 'sex', 'desc'], 'safe'],
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
            'hospital_id' => $this->hospital_id,
            'doctor_id' => $this->doctor_id,
            'is_transfer' => $this->is_transfer,
            'id_number' => $this->id_number,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'age' => $this->age,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'tel', $this->tel])
            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'desc', $this->desc]);

        return $dataProvider;
    }
}
