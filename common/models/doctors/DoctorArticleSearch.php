<?php

namespace common\models\doctors;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\doctors\DoctorArticle;

/**
 * DoctorArticleSearch represents the model behind the search form about `common\models\doctors\DoctorArticle`.
 */
class DoctorArticleSearch extends DoctorArticle
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'hospital_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'desc', 'keywords', 'content'], 'safe'],
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
        $query = DoctorArticle::find();

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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $this->SearchAddHospitalId($query,'id');
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'desc', $this->desc])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
