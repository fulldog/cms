<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/6
 * Time: 23:52
 */

namespace api\service;

use yii\filters\auth\QueryParamAuth;

class AuthService extends QueryParamAuth
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'openid';
    const OPEN_ID_TABLE = 'hash:openid_table';


    /**
     * {@inheritdoc}
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if (is_string($accessToken)) {
            $identity = \Yii::$app->redis->hget(static::OPEN_ID_TABLE, $accessToken);
            if (!$identity) {
                $identity = $user->loginByAccessToken($accessToken, get_class($this));
                if ($identity !== null) {
                    \Yii::$app->redis->hset(static::OPEN_ID_TABLE, $accessToken, 1);
                    return $identity;
                }
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}