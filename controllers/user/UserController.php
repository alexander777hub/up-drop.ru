<?php


namespace app\controllers\user;

/**
 * Class UserController
 * @package app\controllers
 */
class UserController extends \dektrium\user\controllers\SecurityController
{
    public function actionCreate()
    {
        echo "HERERE";
        exit;
    }
        public function actionLogin()
        {

            return parent::actionLogin(); // TODO: Change the autogenerated stub
        }
}