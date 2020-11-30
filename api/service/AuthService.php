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


    /**
     * {@inheritdoc}
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if (is_string($accessToken)) {
            $identity = \Yii::$app->cache->get("auth:" . $accessToken);
            if (!$identity) {
                $identity = $user->loginByAccessToken($accessToken, get_class($this));
            }
            if ($identity !== null) {
                \Yii::$app->cache->set("auth:" . $accessToken, 1);
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }
}