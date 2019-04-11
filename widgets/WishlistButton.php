<?php

namespace alchilyakov\wishlist\widgets;

use yii\helpers\Html;
use alchilyakov\wishlist\models\Wishlist;
use yii\helpers\Url;
use yii;

class WishlistButton extends \yii\base\Widget {

    public $anchorActive = NULL;
    public $anchorUnactive = NULL;
    public $anchorTitleActive = NULL;
    public $anchorTitleUnactive = NULL;
    public $model = NULL;
    public $cssClass = NULL;
    public $cssClassInList = NULL;
    public $htmlTag = 'div';
    public $type = 0;

    public function init() {
        parent::init();

        \alchilyakov\wishlist\assets\WidgetAsset::register($this->getView());

        if ($this->anchorActive === NULL) {
            $this->anchorActive = 'В избранном';
        }

        if ($this->anchorUnactive === NULL) {
            $this->anchorUnactive = 'В избранное';
        }

        if ($this->anchorTitleActive === NULL) {
            $this->anchorTitleActive = 'В избранном';
        }

        if ($this->anchorTitleUnactive === NULL) {
            $this->anchorTitleUnactive = 'Добавить в избранное';
        }

        $anchor = [
            'active' => $this->anchorActive,
            'unactive' => $this->anchorUnactive,
            'activeTitle' => $this->anchorTitleActive,
            'unactiveTitle' => $this->anchorTitleUnactive
        ];

        if ($this->cssClass === NULL) {
            $this->cssClass = 'hal-wishlist-button';
        }

        if ($this->cssClassInList === NULL) {
            $this->cssClassInList = 'in-list';
        }

        $this->getView()->registerJs('wishlist.' . $this->model->formName() . '_' . $this->type . ' = ' . json_encode($anchor));

        return true;
    }

    public function run() {
        if (!is_object($this->model)) {
            return false;
        }

        $text = $this->anchorUnactive;
        $model = $this->model;
        $options = [
            'class' => $this->cssClass,
            'data-role' => 'hal_wishlist_button',
            'data-url' => Url::toRoute('/wishlist/element/add'),
            'data-action' => 'add',
            'data-in-list-css-class' => $this->cssClassInList,
            'data-item-id' => $model->id,
            'data-model' => get_class($model),
            'title' => $this->anchorTitleUnactive,
            'data-type-wish' => $this->type,
        ];
        $conditions = [
            'model' => get_class($model),
            'item_id' => $model->id,
            'type_wish' => $this->type,
        ];

        if (Yii::$app->user->isGuest) {
            $conditions['token'] = Yii::$app->request->cookies->getValue('uwl_token', null);
        } else {
            $conditions['user_id'] = \Yii::$app->user->getId();
        }

        if (Wishlist::find()->where($conditions)->exists()) {
            $text = $this->anchorActive;
            $options['title'] = $this->anchorTitleActive;
            $options['class'] .= ' ' . $this->cssClassInList;
            $options['data-action'] = 'remove';
            $options['data-url'] = Url::toRoute('/wishlist/element/remove');
        }

        return Html::tag($this->htmlTag, $text, $options);
    }

}
