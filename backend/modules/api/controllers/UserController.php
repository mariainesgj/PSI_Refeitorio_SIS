<?php

namespace backend\modules\api\controllers;

use common\models\Profile;
use common\models\User;
use Yii;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;

/**
 * Default controller for the `api` module
 */
class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function auth($username, $password)
    {
        $user = \common\models\User::findByUsername($username);
        if ($user && $user->validatePassword($password))
        {
            $this->user=$user;
            return $user;
        }
        throw new \yii\web\ForbiddenHttpException('Incorect username or password'); //Error 403
    }

    public function actionLogin()
    {
        $rawBody = Yii::$app->request->getRawBody();
        $request = json_decode($rawBody, true);

        $username = $request['username'] ?? null;
        $password = $request['password'] ?? null;

        try {
            $user = $this->auth($username, $password);

            $profile = Profile::findOne(['user_id' => $user->id]);

            if (!$profile) {
                throw new BadRequestHttpException('Perfil não encontrado para este usuário.');
            }


            $userAttr = $user->attributes;
            $userAttr["profile"] = $profile->attributes;
            unset($userAttr["password_hash"], $userAttr["auth_key"], $userAttr["verification_token"], $userAttr["password_reset_token"] );

            return [
                'status' => 'success',
                'message' => 'Login bem-sucedido!',
                'data' => [
                    'user' => $userAttr,
                ],
                'access_token' => $user->getAuthKey(),
                'token_type' => 'bearer',
            ];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }



    public function actionRegister()
    {
        try {
            $rawBody = Yii::$app->request->getRawBody();
            $requestData = json_decode($rawBody, true);

            if (empty($requestData)) {
                throw new BadRequestHttpException('Dados estão ausentes.');
            }

            $username = $requestData['username'] ?? null;
            $email = $requestData['email'] ?? null;
            $password = $requestData['password'] ?? null;

            $name = $requestData['name'] ?? null;
            $mobile = $requestData['mobile'] ?? null;
            $street = $requestData['street'] ?? null;
            $locale = $requestData['locale'] ?? null;
            $postalCode = $requestData['postalCode'] ?? null;
            $role = $requestData['role'] ?? null;
            $cozinha_id = $requestData['cozinha_id'] ?? null;

            if (!$username || !$email || !$password) {
                throw new BadRequestHttpException('Campos obrigatórios (username, email, password) estão faltando.');
            }

            $userModel = new User([
                'username' => $username,
                'email' => $email,
                'password' => Yii::$app->security->generatePasswordHash($password),
                'status' => 10,
                'auth_key' => Yii::$app->security->generateRandomString(),
                'verification_token' => Yii::$app->security->generateRandomString(),
            ]);

            if (!$userModel->validate() || !$userModel->save()) {
                throw new BadRequestHttpException('Erro ao criar o usuário. Verifique se o nome de usuário ou email já estão em uso.');
            }

            $profileModel = new Profile([
                'name' => $name,
                'mobile' => $mobile,
                'street' => $street,
                'locale' => $locale,
                'postalCode' => $postalCode,
                'role' => $role,
                'user_id' => $userModel->id,
                'cozinha_id' => $cozinha_id,
            ]);

            if (!$profileModel->validate() || !$profileModel->save()) {
                throw new BadRequestHttpException('Falha ao criar o perfil.');
            }

            $userAttr = $userModel->attributes;
            $profileAttr = $profileModel->attributes;
            unset(
                $userAttr["password_hash"],
                $userAttr["auth_key"],
                $userAttr["verification_token"],
                $userAttr["password_reset_token"]
            );
            $userAttr["profile"] = $profileAttr;

            Yii::$app->response->statusCode = 200;  // HTTP status code 200
            return [
                'status' => 'success',
                'message' => 'Usuário criado com sucesso!',
                'data' => [
                    'user' => $userAttr,
                ],
            ];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;  // HTTP status code 500
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }



}
