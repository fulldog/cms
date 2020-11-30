<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2020/7/6
 * Time: 23:52
 */

namespace api\service;

use yii\filters\auth\QueryParamAuth;
use yii\web\IdentityInterface;

class AuthService extends QueryParamAuth
{
    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'openid';


    /**
     * {@inheritdoc}
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if (is_string($accessToken)) {
            $identity = \Yii::$app->redis->get($accessToken);
            if ($identity) {
                $identity = unserialize($identity);
                if ($identity instanceof IdentityInterface) {
                    \Yii::$app->redis->expire($accessToken, 86400 + mt_rand(1, 10000));
                    $user->login($identity);
                    return $identity;
                }
            }
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                \Yii::$app->redis->set($accessToken, serialize($identity));
                \Yii::$app->redis->expire($accessToken, 86400 + mt_rand(1, 10000));
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}