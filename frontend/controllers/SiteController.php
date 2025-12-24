<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\helpers\Url;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
            ],
            'captcha' => [
                'class' => \yii\captcha\CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex($token = null)
    {
        if ($token) {
            // Find all storage items for this token
            $items = (new \yii\db\Query())
                ->from('qr_storage')
                ->where(['token' => $token])
                ->all();

            foreach ($items as $item) {
                // Delete file from server if type is 'file'
                if ($item['type'] === 'file' && !empty($item['value'])) {
                    $filePath = Yii::getAlias('@webroot') . '/' . $item['value'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            }

            // Delete all storage records for this token
            Yii::$app->db->createCommand()
                ->delete('qr_storage', ['token' => $token])
                ->execute();

            // Optionally delete the session record itself
            Yii::$app->db->createCommand()
                ->delete('qr_session', ['token' => $token])
                ->execute();
        }

        $token = Yii::$app->security->generateRandomString(32);

        Yii::$app->db->createCommand()->insert('qr_session', [
            'token' => $token,
        ])->execute();

        return $this->render('index', [
            'token' => $token,
            'qrUrl' => Url::to(['site/upload', 'token' => $token], true),
        ]);
    }

    public function actionCheckScan($token)
    {
        $scanned = (new \yii\db\Query())
            ->from('qr_session')
            ->where(['token' => $token])
            ->andWhere(['IS NOT', 'scanned_at', null])
            ->exists();

        return $this->asJson(['scanned' => $scanned]);
    }

    public function actionUpload($token)
    {
        Yii::$app->db->createCommand()
            ->update('qr_session', ['scanned_at' => date('Y-m-d H:i:s')], ['token' => $token])
            ->execute();

        if (Yii::$app->request->isPost) {
            $file = UploadedFile::getInstanceByName('file');
            $link = Yii::$app->request->post('link');

            if ($file) {
                $path = 'uploads/' . uniqid() . '.' . $file->extension;

                $file->saveAs($path);

                Yii::$app->db->createCommand()->insert('qr_storage', [
                    'token' => $token,
                    'type' => 'file',
                    'value' => $path,
                    'created_at' => date('Y-m-d H:i:s')
                ])->execute();
            }

            if ($link) {
                Yii::$app->db->createCommand()->insert('qr_storage', [
                    'token' => $token,
                    'type' => 'link',
                    'value' => $link,
                    'created_at' => date('Y-m-d H:i:s')
                ])->execute();
            }

            return $this->redirect(['site/upload', 'token' => $token]);
        }

        return $this->render('upload', ['token' => $token]);
    }

    public function actionStorage($token)
    {
        $items = (new \yii\db\Query())
            ->from('qr_storage')
            ->where(['token' => $token])
            ->all();

        return $this->render('storage', [
            'items' => $items,
            'token' => $token, // <-- add this
        ]);
    }

    public function actionCheckStorage($token, $lastId = 0)
    {
        $exists = (new \yii\db\Query())
            ->from('qr_storage')
            ->where(['token' => $token])
            ->andWhere(['>', 'id', (int)$lastId])
            ->exists();

        return $this->asJson(['updated' => $exists]);
    }
}
