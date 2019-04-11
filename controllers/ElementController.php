<?php

namespace alchilyakov\wishlist\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use alchilyakov\wishlist\models\Wishlist;
use yii\helpers\Url;
use yii\db\Expression;

/**
 * Default controller for the `wishlist` module
 */
class ElementController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'add' => ['post'],
                    'remove' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    /**
     * Добавить в избранное
     * @return [type] [description]
     */
    public function actionAdd() {
        $wishlistModel = new Wishlist();
        $postData = \Yii::$app->request->post();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uwlToken = Yii::$app->request->cookies->getValue('uwl_token', \Yii::$app->security->generateRandomString());
        $checkModel = self::getWishlist($postData['model'], $postData['itemId'], $postData['typeWish']);

        if ($checkModel) {
            return [
                'response' => true,
                'url' => Url::toRoute('/wishlist/element/remove'),
                'type_wish' => isset($postData['typeWish']) ? $postData['typeWish'] : 0,
            ];
        }

        Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'uwl_token',
            'value' => $uwlToken,
            'expire' => (int) (time() + \Yii::$app->getModule('wishlist')->cokieDateExpired),
        ]));

        $wishlistModel->token = $uwlToken;
        $wishlistModel->token_expire = new Expression(\Yii::$app->getModule('wishlist')->dbDateExpired);
        $wishlistModel->model = $postData['model'];
        $wishlistModel->item_id = $postData['itemId'];
        $wishlistModel->type_wish = isset($postData['typeWish']) ? $postData['typeWish'] : 0;

        if (!Yii::$app->user->isGuest) {
            $wishlistModel->user_id = \Yii::$app->user->id;
        }

        if ($wishlistModel->save()) {
            return [
                'count' => Wishlist::find()->where(['user_id' => \Yii::$app->user->id,])->count(),
                'response' => true,
                'url' => Url::toRoute('/wishlist/element/remove'),
            ];
        } else {
            return [
                'count' => Wishlist::find()->where(['user_id' => \Yii::$app->user->id,])->count(),
                'response' => $wishlistModel->getErrors(),
                'url' => Url::toRoute('/wishlist/element/remove'),
            ];
        }

        return [
            'response' => false
        ];
    }

    /**
     * Удалить из избранного
     * @return [type] [description]
     */
    public function actionRemove() {
        $postData = \Yii::$app->request->post();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $elementModel = self::getWishlist($postData['model'], $postData['itemId'], $postData['typeWish']);

        if ((isset($elementModel) && $elementModel->delete()) || empty($elementModel)) {
            return [
                'count' => Wishlist::find()->where(['user_id' => \Yii::$app->user->id,])->count(),
                'response' => true,
                'url' => Url::toRoute('/wishlist/element/add'),
                'type_wish' => isset($postData['typeWish']) ? $postData['typeWish'] : 0,
            ];
        }

        return [
            'response' => false
        ];
    }

    private static function getWishlist($model, $id, $type) {
        $conditions = [
            'model' => $model,
            'item_id' => $id,
            'type_wish' => isset($type) ? $type : 0,
        ];

        if (Yii::$app->user->isGuest) {
            $conditions['token'] = Yii::$app->request->cookies->getValue('uwl_token', null);
        } else {
            $conditions['user_id'] = \Yii::$app->user->getId();
        }

        return Wishlist::find()->where($conditions)->one();
    }

}
