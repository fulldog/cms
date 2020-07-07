<?php
namespace common\services;
/**
* This is the template for generating CRUD service class of the specified model.
*/

use common\models\CourseCate;

class CourseCateService extends Service implements CourseCateServiceInterface{
    public function getSearchModel(array $query=[], array $options=[])
    {
        return null;
    }

    public function getModel($id, array $options = [])
    {
        return CourseCate::findOne($id);
    }

    public function newModel(array $options = [])
    {
        $model = new CourseCate();
        $model->loadDefaultValues();
        return $model;
    }
}
