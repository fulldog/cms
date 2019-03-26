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
            [['status', 'recommend','transfer'], 'integer'],
            [['hospital_name', 'city', 'address','grade','province','area','invite'], 'safe'],
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
            'transfer' => $this->transfer,
            'recommend' => $this->recommend,
            'status' => $this->status,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);
        $this->SearchAddHospitalId($query,'id');
        $this->SearchAddTime($query,$params,__CLASS__);

        if (\Yii::$app->user->identity->job_number){
            $query->andFilterWhere([
                'invite'=>\Yii::$app->user->identity->job_number
            ]);
        }

        $query->andFilterWhere(['like', 'hospital_name', $this->hospital_name])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'address', $this->address])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'grade', $this->grade]);

        return $dataProvider;
    }
}
