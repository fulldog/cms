<?php

namespace common\models\doctors;

use backend\behaviors\TimeSearchBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * DoctorHospitalsSearch represents the model behind the search form about `common\models\doctors\DoctorHospitals`.
 */
class DoctorHospitalsSearch extends DoctorHospitals
{
    function behaviors()
    {
        return [
            TimeSearchBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['id', ], 'integer'],
            [['hospital_name', 'city', 'address', 'levels','grade'], 'safe'],
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
        $query = DoctorHospitals::find();

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
            'recommend' => $this->recommend,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);

        if ($params['DoctorHospitalsSearch']['created_at']){
            $created_at = explode('~',$params['DoctorHospitalsSearch']['created_at']);
            $query->andFilterWhere(['between','created_at',strtotime($created_at[0]),strtotime($created_at[1])]);
        }
        if ($params['DoctorHospitalsSearch']['updated_at']){
            $updated_at = explode('~',$params['DoctorHospitalsSearch']['updated_at']);
            $query->andFilterWhere(['between','created_at',strtotime($updated_at[0]),strtotime($updated_at[1])]);
        }
        $query->andFilterWhere(['like', 'hospital_name', $this->hospital_name])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'address', $this->address])
//            ->andFilterWhere(['like', 'levels', $this->levels])
            ->andFilterWhere(['like', 'grade', $this->grade]);
//            ->andFilterWhere(['like', 'imgs', $this->imgs]);

        return $dataProvider;
    }
}
