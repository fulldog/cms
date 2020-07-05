<?php
namespace common\services;
/**
* This is the template for generating CRUD service class of the specified model.
*/

use app\models\VoteSearch;
use app\models\Vote;

class VoteService extends Service implements VoteServiceInterface{
    public function getSearchModel(array $query=[], array $options=[])
    {
         return new  VoteSearch();
    }

    public function getModel($id, array $options = [])
    {
        return Vote::findOne($id);
    }

    public function newModel(array $options = [])
    {
        $model = new Vote();
        $model->loadDefaultValues();
        return $model;
    }
}
