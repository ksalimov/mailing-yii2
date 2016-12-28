<?php

namespace app\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

use app\models\SentMail;
use app\models\MailForm;
use app\models\GmailInbox;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
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
        $gmailInbox = new GmailInbox();
        $mail = $gmailInbox->fetchMail();

        $box = null;
        if(Yii::$app->request->get('box')) {
            $box = Yii::$app->request->get('box');
            Yii::$app->session->set('box', $box);
        } else {
            $box = 'inbox';
            Yii::$app->session->set('box', 'inbox');
        }

        $dataProvider = null;
        if($box == 'inbox') {
            Yii::$app->session->set('box', 'inbox');
            $dataProvider = new ArrayDataProvider([
                'id' => 'inbox',
                'allModels' => $mail,
                'pagination' => [
                    'pageSize' => 20,
                ],
                'sort' => [
                    'attributes' => [
                        'from',
                        'subject',
                        'date',
                    ],
                    'defaultOrder' => [
                        'date' => SORT_DESC,
                    ]
                ],
            ]);
        } else {
            Yii::$app->session->set('box', 'sent');

            $query = SentMail::find();

            $dataProvider = new ActiveDataProvider([
                'id' => 'sent',
                'query' => $query,
                'pagination' => [
                    'pageSize' => 20,
                ],
                'sort' => [
                    'defaultOrder' => [
                        'date' => SORT_DESC,
                    ]
                ],
            ]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'box' => $box,
        ]);
    }

    /**
     * Displays mail page.
     *
     * @return string
     */
    public function actionMail()
    {
        $model = new MailForm();
        if ($model->load(Yii::$app->request->post()) && $model->send(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('mailFormSubmitted');

            $mail = new SentMail();
            $mail->sender = Yii::$app->params['adminEmail'];
            $mail->receiver = $model->email;
            $mail->subject = $model->subject;
            $mail->body = $model->body;
            $mail->date = date_timestamp_get(new \DateTime());
            $mail->save();

            return $this->refresh();
        }
        return $this->render('mail', [
            'model' => $model,
        ]);
    }

    /*
     * Deletes messages and redirects to home page.
     *
     * @return string
     */
    public function actionDelete()
    {
        if(Yii::$app->session->get('box') == 'inbox') {
            $ids = Yii::$app->request->post('ids');
            $gmailInbox = new GmailInbox();
            $gmailInbox->deleteMessages($ids);
        } else {
            $keys = Yii::$app->request->post('keylist');
            if($keys) {
                foreach ($keys as $key) {
                    $mail = SentMail::findOne($key);
                    $mail->delete();
                }
            }
        }

        return $this->goHome();
    }

    /*
     * Displays message page.
     *
     * @return string
     */
    public function actionMessage()
    {
        $box = Yii::$app->session->get('box');
        $msgno = Yii::$app->request->get('id');
        $model = null;

        if($box == 'inbox') {
            $gmailInbox = new GmailInbox();
            $model = $gmailInbox->fetchMessage($msgno);
        } elseif ($box == 'sent') {
            $model = SentMail::findOne($msgno);
        }

        return $this->render('message', [
            'model' => $model,
            'box' => $box,
        ]);
    }
}
