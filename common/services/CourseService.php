<?php
namespace common\services;
/**
* This is the template for generating CRUD service class of the specified model.
*/

use common\models\CourseSearch;
use common\models\Course;

class CourseService extends Service implements CourseServiceInterface{
    public function getSearchModel(array $query=[], array $options=[])
    {
         return new  CourseSearch();
    }

    public function getModel($id, array $options = [])
    {
        return Course::findOne($id);
    }

    public function newModel(array $options = [])
    {
        $model = new Course();
        $model->loadDefaultValues();
        return $model;
    }
}
