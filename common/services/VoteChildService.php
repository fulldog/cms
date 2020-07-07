<?php
namespace common\services;
/**
* This is the template for generating CRUD service class of the specified model.
*/

use common\models\VoteChildSearch;
use common\models\VoteChild;

class VoteChildService extends Service implements VoteChildServiceInterface{
    public function getSearchModel(array $query=[], array $options=[])
    {
         return new  VoteChildSearch();
    }

    public function getModel($id, array $options = [])
    {
        return VoteChild::findOne($id);
    }

    public function newModel(array $options = [])
    {
        $model = new VoteChild();
        $model->loadDefaultValues();
        return $model;
    }
}
