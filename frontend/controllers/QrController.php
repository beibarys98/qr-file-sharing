<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\helpers\Url;

class QrController extends Controller
{
    public function actionIndex()
    {
        $token = Yii::$app->security->generateRandomString(32);

        Yii::$app->db->createCommand()->insert('qr_session', [
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', time() + 300),
        ])->execute();

        return $this->render('index', [
            'token' => $token,
            'qrUrl' => Url::to(['qr/upload', 'token' => $token], true),
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
                $path = 'uploads/' . $token . '_' . time() . '.' . $file->extension;
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
        }

        return $this->render('upload', ['token' => $token]);
    }

    public function actionStorage($token)
    {
        $items = (new \yii\db\Query())
            ->from('qr_storage')
            ->where(['token' => $token])
            ->all();

        return $this->render('storage', ['items' => $items]);
    }
}
