<?php
/**
 * @see 医生账户
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/13
 * Time: 20:51
 */

namespace common\models\doctors;


use frontend\models\User;

class DoctorUser extends User
{

    public function rules()
    {
        return [
            [['username', 'password', 'repassword'], 'string'],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
            [['username', 'email'], 'unique'],
            ['email', 'email'],
            [['repassword'], 'compare', 'compareAttribute' => 'password'],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['username',  'password', 'repassword'], 'required', 'on' => ['create']],//'email'
            [['username', ], 'required', 'on' => ['update']],//'email'
        ];
    }
}