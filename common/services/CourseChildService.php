<?php
namespace common\services;
/**
* This is the template for generating CRUD service class of the specified model.
*/

use common\models\CourseChildSearch;
use common\models\CourseChild;

class CourseChildService extends Service implements CourseChildServiceInterface{
    public function getSearchModel(array $query=[], array $options=[])
    {
         return new  CourseChildSearch();
    }

    public function getModel($id, array $options = [])
    {
        return CourseChild::findOne($id);
    }

    public function newModel(array $options = [])
    {
        $model = new CourseChild();
        $model->loadDefaultValues();
        return $model;
    }
}
