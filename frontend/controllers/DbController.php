<?php
/**
 * Created by PhpStorm.
 * User: weilone
 * Date: 2019/1/12
 * Time: 11:58
 */

namespace frontend\controllers;


class DbController extends BaseController
{

    function actionAlert(){

        $sql = [
            'ALTER TABLE `doctor_hospitals`
CHANGE COLUMN `name` `hospital_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT \'医院名称\' AFTER `id`,
ADD COLUMN `province`  varchar(255) NULL AFTER `imgs`,
ADD COLUMN `area`  varchar(255) NULL AFTER `province`,
ADD COLUMN `grade`  varchar(255) NULL AFTER `area`;',
            'ALTER TABLE `doctor_infos`
ADD COLUMN `address`  varchar(255) NULL AFTER `updated_at`,
ADD COLUMN `province`  varchar(255) NULL AFTER `updated_at`,
ADD COLUMN `area`  varchar(255) NULL AFTER `updated_at`,
ADD COLUMN `city`  varchar(255) NULL AFTER `updated_at`;',
            'ALTER TABLE `admin_user`
ADD COLUMN `hospital_id`  int(10) NOT NULL DEFAULT 0 AFTER `updated_at`;',
            'ALTER TABLE `admin_user`
MODIFY COLUMN `email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT \'管理员邮箱\' AFTER `password_reset_token`;',
            'ALTER TABLE `doctor_infos`
ADD COLUMN `ills`  text NULL COMMENT \'擅长疾病\' AFTER `updated_at`;'

        ];

        try{
            foreach ($sql as $s){
                \Yii::$app->db->createCommand($s)->execute();
            }
        }catch (\Exception $e){
            echo $e->getMessage();
            exit();
        }
    }
}