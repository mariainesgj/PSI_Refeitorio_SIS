<?php

namespace backend\modules\api\controllers;

use common\models\Senha;
use Yii;
use yii\db\Query;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Default controller for the `api` module
 */
class SenhaController extends ActiveController
{
    public $modelClass = 'common\models\Senha';
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionPratosSopa($userId, $data)
    {

        if (empty($userId) || empty($data)) {
            Yii::$app->response->statusCode = 400;
            return ['error' => 'Parâmetros inválidos'];
        }

        $query = new Query();
        $result = $query->select([
            'senhas.*',
            'senhas.prato_id',
            'prato.designacao AS nome_prato',
            'sopa.designacao AS nome_sopa',
            'prato.tipo AS tipo_prato',
        ])
            ->from('senhas')
            ->innerJoin('user', 'user.id = senhas.user_id')
            ->innerJoin('pratos AS prato', 'senhas.prato_id = prato.id')
            ->innerJoin('ementas', 'senhas.ementa_id = ementas.id')
            ->innerJoin('pratos AS sopa', 'ementas.sopa = sopa.id')
            ->where(['senhas.data' => $data, 'user.id' => $userId])
            ->all();

        if (empty($result)) {
            Yii::$app->response->statusCode = 404;
            return ['message' => 'Nenhuma senha encontrada para o utilizador e data especificados.'];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $result;
    }
}


