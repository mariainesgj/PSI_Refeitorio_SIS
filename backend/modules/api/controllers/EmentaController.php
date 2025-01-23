<?php

namespace backend\modules\api\controllers;

use common\models\Profile;
use Yii;
use yii\db\Query;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Default controller for the `api` module
 */
class EmentaController extends ActiveController
{
    public $modelClass = 'common\models\Ementa';

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

    public function actionPratosSopaComSenhas() {
        $userId = Yii::$app->user->id;
        $profile = Profile::findOne(['user_id' => $userId]);


        $subQuery = new Query();

        $subQuery = $subQuery->select('lc.id, c.user_id, lc.prato_id, lc.ementa_id, c.status')
            ->from('carrinhos c')
            ->leftJoin('linhascarrinhos lc', 'lc.carrinho_id = c.id');

        $query = new Query();


        $result = $query->select([
            'e.id',
            'e.cozinha_id',
            'e.data',
            'nor.id AS prato_normal_id',
            'nor.designacao AS prato_normal_nome',
            'veg.id AS prato_vegetariano_id',
            'veg.designacao AS prato_vegetariano_nome',
            'sop.designacao AS sopa_nome',
            's.id AS senha_id',
            's.prato_id AS senha_prato',
            'senha_prato.designacao AS senha_prato_nome',
            'senha_prato.tipo AS senha_prato_tipo',
            'lcn.id AS linha_carrinho_normal_id',
            'lcv.id AS linha_carrinho_vegetariano_id',
            'lcn.status as carrinho_status'
        ])
            ->from('ementas e')
            ->innerJoin('pratos nor', 'e.prato_normal = nor.id')
            ->innerJoin('pratos veg', 'e.prato_vegetariano = veg.id')
            ->innerJoin('pratos sop', 'e.sopa = sop.id')
            ->leftJoin('senhas s', 's.ementa_id = e.id AND s.user_id = :user_id')
            ->leftJoin('pratos senha_prato', 's.prato_id = senha_prato.id')
            ->leftJoin([ 'lcn' => $subQuery], 'lcn.prato_id = nor.id and lcn.user_id = :user_id and e.id = lcn.ementa_id ')
            ->leftJoin([ 'lcv' => $subQuery], 'lcv.prato_id = veg.id and lcv.user_id = :user_id and e.id = lcv.ementa_id ')
            ->andWhere(['between', 'e.data', new \yii\db\Expression('CURRENT_DATE()'), new \yii\db\Expression('DATE_ADD(CURRENT_DATE(), INTERVAL 7 DAY)')])
            ->andWhere(['e.cozinha_id' => $profile->cozinha_id])
            ->addParams([':user_id' => $userId])
            ->all();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            "status" => "success",
            "message" => "Sucesso!",
            "data" => $result
        ];
    }



}

