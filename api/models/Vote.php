<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/8
 * Time: 20:32
 */

namespace api\models;


class Vote extends \common\models\Vote
{
    public function fields()
    {
        return [
            'id',
            'title',
            'end_time',
            'img',
            "pv",
            "vote_count",
        ];
    }
}