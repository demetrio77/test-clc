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
use app\services\SaveBudgetData;
use yii\data\ActiveDataProvider;
use app\models\ImportFile;
use yii\web\NotFoundHttpException;

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
        $dataProvider = new ActiveDataProvider([
            'query' => ImportFile::find(),
            'sort'=> [
                'defaultOrder' => [
                    'year' => SORT_DESC,
                    'month' => SORT_DESC,
                    'id' => SORT_DESC
                ]
            ]
        ]);
        
        return $this->render('index', ['dataProvider' => $dataProvider ]);
    }
    
    public function actionUpload()
    {
        $model = new UploadForm;
        
        if (Yii::$app->request->isPost) {
            $model->folder = \Yii::$app->params['uploadPath'];
            $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
            
            if (($fullPath = $model->upload())!==false) {
                $Parser = new ParseBudgetFile($fullPath, Yii::$app->params['sheetName']);
                $Error = null;
                
                if (($budgetData = $Parser->parse())!==false) {
                    try {
                        $SaveService = new SaveBudgetData($fullPath, $budgetData);
                        if ($SaveService->save()){
                            return $this->redirect(['view','id' => $SaveService->getImportId()]);
                        }
                        
                        $Error = 'Не удалось сохранить данные';
                    }
                    catch (\Exception $e){
                        $Error = $e->getMessage();   
                    }                    
                }
                else {
                    $Error = $Parser->getError();
                }
                
                $model->addError('uploadFile', $Error);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
        }
        
        return $this->render('upload', ['model' => $model ]);
    }
    
    public function actionView($id)
    {
        $ImportFile = ImportFile::findOne($id);
        
        if (!$ImportFile) {
            throw new NotFoundHttpException('Страница не найдена');
        }
        
        return $this->render('view', ['model' => $ImportFile]);
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
        
        return $this->render('login');
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
