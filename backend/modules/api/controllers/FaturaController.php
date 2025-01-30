<?php

namespace backend\modules\api\controllers;

use Yii;
use yii\db\Query;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Default controller for the `api` module
 */
class FaturaController extends ActiveController
{
    public $modelClass = 'common\models\Fatura';


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

    public function actionByUser()
    {
        $user_id = Yii::$app->user->id;

        if (empty($user_id)) {
            throw new BadRequestHttpException('O parâmetro "user_id" é obrigatório.');
        }

        $query = new Query();
        $faturas = $query->select(['id', 'data', 'total_iliquido', 'total_iva', 'total_doc'])
            ->from('faturas')
            ->where(['user_id' => $user_id])
            ->orderBy(['data' => SORT_DESC])
            ->all();

        foreach ($faturas as &$fatura) {
            $fatura_linhas = (new Query())
                ->select(['id', 'senha_id', 'preco', 'taxa_iva', 'quantidade'])
                ->from('linhasfaturas')
                ->where(['fatura_id' => $fatura['id']])
                ->all();

            $fatura['linhas'] = $fatura_linhas;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            "status" => "success",
            "message" => "Faturas listadas com sucesso",
            "data" => $faturas,
        ];
    }

}


