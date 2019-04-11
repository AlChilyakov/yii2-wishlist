Yii2-wishlist
==========

Модуль избранного для Yii2 фреймворка. ДЛЯ любых ПОЛЬЗОВАТЕЛЕЙ

основан на samwolf1982/yii2-wishlist

добавлено полее type_wish-int def=0
 
 для типа лайка например лайки и сравнения 

Установка
---------------------------------
Выполнить команду

```
composer require alchilyakov/yii2-wishlist
```

Либо в composer.json строку:

```
"alchilyakov/yii2-wishlist": "dev-master",
```

Далее, мигрируем базу:

```
php yii migrate --migrationPath=vendor/alchilyakov/yii2-wishlist/migrations
```

Подключение и настройка
---------------------------------
В конфигурационный файл приложения добавить модуль и компонент wishlist

```php
    'modules' => [
        'wishlist' => [
            'class' => 'alchilyakov\wishlist\Module',
            'dbDateExpired' => 'CURDATE() + INTERVAL 7 DAY', //дата истечения срока действия избранного в БД
            'cokieDateExpired' => time() + 86400 * 365, //Время жизни куки с токеном
        ],
        //...
    ],

    'components' => [
        'wishlist' => [
            'class' => 'alchilyakov\wishlist\Wishlist'
        ],
        //...
    ],
```

Компоненты
===========
Получить вишлист ТЕКУЩЕГО пользователя (возвращает массив моделей добавленных в вишлист объектов):
```php
\Yii::$app->wishlist->getUserWishList($type=0)   тип  по умолчанию 0 
```

Получить количество добавленных в вишлист объектов для текущего пользователя
```php
\Yii::$app->wishlist->getUserWishlistAmount()
```


``` fix
add if present class
```

Виджеты
==========
Кнопка добавить/убрать объект в избранное.

```php

<?php
use alchilyakov\wishlist\widgets\WishlistButton;
?>

<?php /* Выведет кнопку "добавить в избранное" */ ?>
<?= WishlistButton::widget([
	'model' => $model
]) ?>

<?php /* Выведет кнопку "добавить в избранное" с пользовательскими параметрами */ ?>
<?= WishlistButton::widget([
	'model' => $model, // модель для добавления
	'anchorActive' => 'В избранном', // свой текст активной кнопки
	'anchorUnactive' => 'В избранное', // свой текст неактивной кнопки
  	'anchorTitleActive' => 'В избранном',//Свой текст подсказки активной кнопки
  	'anchorTitleUnactive' => 'В избранное',//свой текст подсказки неактивной кнопки
	'htmlTag' => 'a', // тэг
	'cssClass' => 'custom_class', // свой класс
    	'cssClassInList' => 'custom_class', // свой класс для добавленного объекта
	'type' => 0, // Тип лайка (int)
]) ?>

```

Дефолтные css-стили
```css

.hal-wishlist-button {
    font-weight: 700;
}

.hal-wishlist-button:before {
    content: "\f08a";
    font: 400 15px/31px "FontAwesome";
    color: white;
    background: #929292; /* цвет сердечка */
    width: 30px;
    text-align: center;
    display: inline-block;
    height: 30px;
    margin: 0 6px 0 0;
    -moz-border-radius: 50px;
    -webkit-border-radius: 50px;
    border-radius: 50px;
}

.hal-wishlist-button:hover {
    cursor: pointer;
}

.in-list:before {
    background: #CC63B0; /* цвет сердечка */
}




```
