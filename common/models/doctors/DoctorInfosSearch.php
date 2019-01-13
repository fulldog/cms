<?php

namespace common\models\doctors;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\doctors\DoctorInfos;

/**
 * DoctorInfosSearch represents the model behind the search form about `common\models\doctors\DoctorInfos`.
 */
class DoctorInfosSearch extends DoctorInfos
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'uid', 'hospital_id', 'created_at', 'updated_at','recommend','status'], 'integer'],
            [['name', 'doctor_type', 'role', 'hospital_location', 'hospital_name', 'certificate','ills'], 'safe'],
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
        $query = DoctorInfos::find();

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
//            'id' => $this->id,
            'status' => $this->status,
//            'hospital_id' => $this->hospital_id,
            'recommend' => $this->recommend,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
        ]);

        if (isset($params['DoctorInfosSearch']['created_at']) && $params['DoctorInfosSearch']['created_at']){
            $created_at = explode('~',$params['DoctorInfosSearch']['created_at']);
            $query->andFilterWhere(['between','created_at',strtotime($created_at[0]),strtotime($created_at[1])]);
        }
        if (isset($params['DoctorInfosSearch']['updated_at']) && $params['DoctorInfosSearch']['updated_at']){
            $updated_at = explode('~',$params['DoctorInfosSearch']['updated_at']);
            $query->andFilterWhere(['between','created_at',strtotime($updated_at[0]),strtotime($updated_at[1])]);
        }

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'doctor_type', $this->doctor_type])
            ->andFilterWhere(['like', 'role', $this->role])
//            ->andFilterWhere(['like', 'hospital_location', $this->hospital_location])
            ->andFilterWhere(['like', 'ills', $this->ills]);
//            ->andFilterWhere(['like', 'certificate', $this->certificate]);

        return $dataProvider;
    }
}
