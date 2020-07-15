<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-30 18:10
 */

namespace api\models;

class Article extends \common\models\Article
{
    public function fields()
    {
        return [
            'id',
            'title',
            'thumb',
            'sub_title',
            'scan_count',
            'created_at'  => function ($model) {
                return date('Y-m-d', $model->created_at);
            },
            'updated_at'  => function ($model) {
                return date('Y-m-d', $model->updated_at);
            },
            "description" => "summary",
            "content"     => function ($model) {
                return $model->articleContent->content;
            },
        ];
    }
}