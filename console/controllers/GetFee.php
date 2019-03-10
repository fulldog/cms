<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/3/9
 * Time: 12:02
 */

namespace console\controllers;


use common\models\doctors\DoctorHospitals;
use common\models\Options;
use GuzzleHttp\Client;
use yii\console\Controller;

class GetFee extends Controller
{

    const PAGE_SIZE = 50;

    private $api_url;

    function __construct($id, Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->api_url = Options::findOne(['name' => 'api_url'])->value;
    }

    function actionPull()
    {
        $hospitals = DoctorHospitals::find()->select(['code', 'id'])->asArray()->all();
        if (!empty($hospitals)) {
            foreach ($hospitals as $v) {
                if ($v['code']) {
                    $this->getData($v);
                }
            }
        }
    }

    function sign($hospital_code)
    {
        return md5('id_card' . $hospital_code);
    }

    function getData($hospital, $page = 1)
    {
        $data = [
            'sign' => $this->sign($hospital['code']),
            'start_time' => time() - 5 * 60 + 1,
            'end_time' => time(),
            'id_card' => $this->id_card,
            'hospital_code' => $hospital['code'],
            'page' => $page,
            'limit' => self::PAGE_SIZE,
        ];
    }

    function post()
    {
        $client = new Client();
    }
}