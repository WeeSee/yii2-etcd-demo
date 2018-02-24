<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
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
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
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

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
    
    /**
     * Play a little with Etcd.
     *
     * @return string
     */
    public function actionEtcd($keyToRemove = '')
    {
        $model = new \yii\base\DynamicModel(['key', 'value']);
        $model->addRule(['key','value'], 'required');
        
        // setup connection to Etcd
        $etcd = new \weesee\etcd\Etcd([
            'etcdUrl' => 'http://192.168.1.164:49501',
            'root'=>"/yii2-etcd-test/"
        ]);
            
        if ($keyToRemove!='' && $etcd->exists($keyToRemove)) {
            // remove key with value
            $etcd->removeKey($keyToRemove);
        } elseif ($model->load(Yii::$app->request->post())) {
            
            // Set key-value-pair in Etcd
            if ($etcd->exists($model->key))
                $etcd->update($model->key,"changedto: ".$model->value);
            else
                $etcd->set($model->key,"new: ".$model->value);
                
        }

        // get Etcd directory as dataprovider
        $dataProvider = $etcd->getKeyValueAsDataProvider();
        
        return $this->render('etcd', [
            'model'=>$model,
            'dataProvider' => $dataProvider,
        ]);
    }

}
