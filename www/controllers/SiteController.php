<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [ 'login', 'error', 'auth'],
                        'allow'   => true,
                        'roles'   => [ '?' ],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?']
                    ],
                    [
                       'allow' => true,
                       'roles' => ['@']
                    ],
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'successAuth'],
                //'redirectView' => '@app/views/site/auth.php',
            ],
        ];
    }
    
    public function successAuth($client)
    {
        $data = $client->getUserAttributes();
        $username = isset($data['emails'][0]['value']) ? $data['emails'][0]['value'] : '';
        if ($username) {
            $User = User::findByUsername($username);
            
            if (!$User) {
                $User = User::createByClient($username);
            }            
            Yii::$app->user->login($User);
        }
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            $this->goHome();
        }
        
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

}
