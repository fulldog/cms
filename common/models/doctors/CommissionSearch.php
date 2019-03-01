<?php

namespace common\models\doctors;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\doctors\DoctorCommission;

/**
 * CommissionSearch represents the model behind the search form about `common\models\doctors\DoctorCommission`.
 */
class CommissionSearch extends DoctorCommission
{
    public $patient_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'hospital_id', 'point', 'created_at', 'updated_at'], 'integer'],
            [['extend1', 'extend2', 'extend3','patient_id'], 'safe'],
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
        $query = DoctorCommission::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $name = $params['CommissionSearch']['patient_id'];
//        unset($params['CommissionSearch']['patient_id']);
        $names_id = [];
        if ($name){
            $names = DoctorPatients::find()->select(['id'])->where(['like','name',$name])->asArray()->all();
            if (!empty($names)){
                foreach ($names as $v){
                    $names_id[] = $v['id'];
                }
            }else{
                $names_id[] = 0;
            }
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (Yii::$app->user->identity->hospital_id){
            $query->andFilterWhere([
                'hospital_id' => Yii::$app->user->identity->hospital_id,
            ]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'hospital_id' => $this->hospital_id,
//            'patient_id' => $this->patient_id,
            'point' => $this->point,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        $query->andFilterWhere(['in','patient_id',$names_id]);
//        $query->andFilterWhere(['like', 'extend1', $this->extend1])
//            ->andFilterWhere(['like', 'extend2', $this->extend2])
//            ->andFilterWhere(['like', 'extend3', $this->extend3]);

        return $dataProvider;
    }
}
