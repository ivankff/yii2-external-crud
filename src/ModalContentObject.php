<?php

namespace ivankff\yii2ExternalCrud;

use yii\base\BaseObject;

class ModalContentObject extends BaseObject
{

    /**
     * @var string Наименование модального окна
     */
    public $title;
    /**
     * @var null|string Контент модального окна
     * `string` если хотим переопределить контент
     * `null` если контент формируется по умолчанию рендерингом view
     */
    public $content;
    /**
     * @var string Футер модального окна
     */
    public $footer;

}