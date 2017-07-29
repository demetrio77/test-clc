<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use app\models\User;
use app\models\UploadForm;
use yii\web\UploadedFile;
use app\services\ParseBudgetFile;

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
                'successCallback' => [$this, 'successAuth']
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
            return Yii::$app->user->login($User);
        }
        
        return false;
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
    
    public function actionUpload()
    {
        $model = new UploadForm;
        
        if (Yii::$app->request->isPost) {
            $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
            
            if (($fullPath = $model->upload())!==false) {
                $Parser = new ParseBudgetFile($fullPath, Yii::$app->params['sheetName']);
                
                if (($BudgetData = $Parser->parse())!==false) {
                    
                    return $this->goHome();
                }
                else {
                    $model->addError('uploadFile', $Parser->getError());
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }
        }
        
        return $this->render('upload', ['model' => $model ]);
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
