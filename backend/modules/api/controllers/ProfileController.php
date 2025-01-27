<?php

namespace backend\modules\api\controllers;

use common\models\Profile;
use common\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `api` module
 */
class ProfileController extends ActiveController
{
    public $modelClass = 'common\models\Profile';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update']);
        return $actions;
    }


    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Adicionar autenticação via token
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        return $behaviors;
    }
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionUpdate()
    {
        try {
            $userId = Yii::$app->user->id;
            if (!$userId) {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => 'error',
                    'message' => 'UserId não encontrado'
                ];
            }

            $userModel = User::findOne($userId);
            if (!$userModel) {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => 'error',
                    'message' => 'Utilizador não encontrado'
                ];
            }

            $rawBody = Yii::$app->request->getRawBody();
            $requestData = json_decode($rawBody, true);

            if (empty($requestData)) {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => 'error',
                    'message' => 'Dados ausentes'
                ];
            }

            $userModel->username = $requestData['username'] ?? $userModel->username;
            $userModel->email = $requestData['email'] ?? $userModel->email;

            if (!empty($requestData['password'])) {
                $userModel->password = Yii::$app->security->generatePasswordHash($requestData['password']);
            }

            if (!$userModel->validate() || !$userModel->save()) {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => 'error',
                    'message' => 'Erro ao atualizar o utilizador'
                ];
            }

            $profileModel = Profile::findOne(['user_id' => $userId]);
            if (!$profileModel) {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => 'error',
                    'message' => 'Perfil não encontrado'
                ];
            }

            $profileModel->name = $requestData['name'] ?? $profileModel->name;
            $profileModel->mobile = $requestData['mobile'] ?? $profileModel->mobile;
            $profileModel->street = $requestData['street'] ?? $profileModel->street;
            $profileModel->locale = $requestData['locale'] ?? $profileModel->locale;
            $profileModel->postalCode = $requestData['postalCode'] ?? $profileModel->postalCode;
            $profileModel->role = $requestData['role'] ?? $profileModel->role;
            $profileModel->cozinha_id = $requestData['cozinha_id'] ?? $profileModel->cozinha_id;

            if (!$profileModel->validate() || !$profileModel->save()) {
                Yii::$app->response->statusCode = 500;
                return [
                    'status' => 'error',
                    'message' => 'Erro ao atualizar perfil'
                ];
            }

            $userAttr = $userModel->attributes;
            $profileAttr = $profileModel->attributes;
            unset($userAttr['password_hash'], $userAttr['verification_token'], $userAttr['password_reset_token']);
            $userAttr['profile'] = $profileAttr;

            Yii::$app->response->statusCode = 200;
            return [
                'status' => 'success',
                'message' => 'Utilizador atualizado com sucesso!',
                'data' => [
                    'user' => $userAttr,
                ],
            ];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

}


