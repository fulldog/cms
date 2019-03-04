<?php

namespace common\models\doctors;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\doctors\DoctorNotices;

/**
 * DoctorNoticesSearch represents the model behind the search form about `common\models\doctors\DoctorNotices`.
 */
class DoctorNoticesSearch extends DoctorNotices
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'hospital_id', 'status'], 'integer'],
            [['notice', 'to', 'created_at', 'updated_at'], 'safe'],
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
        $query = DoctorNotices::find();

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
            'status' => $this->status,
        ]);
        $this->SearchAddHospitalId($query);
        $this->SearchAddTime($query,$params,__CLASS__);
        $query->andFilterWhere(['like', 'notice', $this->notice])
            ->andFilterWhere(['like', 'to', $this->to]);

        return $dataProvider;
    }
}
