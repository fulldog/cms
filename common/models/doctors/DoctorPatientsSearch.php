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
            [['name', 'phone', 'sex', 'desc','id_number'], 'safe'],
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
        if (Yii::$app->user->identity->hospital_id){
            $query->andFilterWhere([
                'hospital_id' => Yii::$app->user->identity->hospital_id,
            ]);
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'hospital_id' => $this->hospital_id,
            'doctor_id' => $this->doctor_id,
            'is_transfer' => $this->is_transfer,
            'id_number' => $this->id_number,
//            'created_at' => $this->created_at,
//            'updated_at' => $this->updated_at,
            'age' => $this->age,
            'sex' => $this->sex,
            'phone' => $this->phone,
        ]);
        if (isset($params['DoctorPatientsSearch']['created_at']) && $params['DoctorPatientsSearch']['created_at']){
            $created_at = explode('~',$params['DoctorPatientsSearch']['created_at']);
            $query->andFilterWhere(['between','created_at',strtotime($created_at[0]),strtotime($created_at[1])]);
        }
        if (isset($params['DoctorPatientsSearch']['updated_at']) && $params['DoctorPatientsSearch']['updated_at']){
            $updated_at = explode('~',$params['DoctorPatientsSearch']['updated_at']);
            $query->andFilterWhere(['between','created_at',strtotime($updated_at[0]),strtotime($updated_at[1])]);
        }
        $query->andFilterWhere(['like', 'name', $this->name])
//            ->andFilterWhere(['like', 'phone', $this->phone])
//            ->andFilterWhere(['like', 'sex', $this->sex])
            ->andFilterWhere(['like', 'desc', $this->desc]);

        return $dataProvider;
    }

    function getRelateHospital(){
        return $this->hasOne(DoctorHospitals::className(),['id'=>'hospital_id']);
    }

    function getRelateDoctor(){
        return $this->hasOne(DoctorInfos::className(),['id'=>'doctor_id']);
    }

    function IsTransferText(){
        $map = ['否','是'];
        return $map[$this->is_transfer];
    }
}
