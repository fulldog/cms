<?php
namespace common\services;
/**
* This is the template for generating CRUD service class of the specified model.
*/

use common\models\CoursePasswordSearch;
use common\models\CoursePassword;

class CoursePasswordService extends Service implements CoursePasswordServiceInterface{
    public function getSearchModel(array $query=[], array $options=[])
    {
         return new  CoursePasswordSearch();
    }

    public function getModel($id, array $options = [])
    {
        return CoursePassword::findOne($id);
    }

    public function newModel(array $options = [])
    {
        $model = new CoursePassword();
        $model->loadDefaultValues();
        return $model;
    }
}
