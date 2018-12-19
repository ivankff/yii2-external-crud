<?php

namespace ivankff\yii2ExternalCrud\events;

use ivankff\yii2ExternalCrud\ModalContentObject;
use yii\base\Action;
use yii\base\Event;

class ActionViewViewEvent extends Event
{

    /**
     * @var Action
     */
    public $sender;
    /**
     * @var mixed Модель для view
     */
    public $model;
    /**
     * @var bool Возможен ли ajax-ответ
     */
    public $enableAjax = true;
    /**
     * @var array Параметры для рендеринга view
     * [
     *     'model' => $model,
     * ]
     */
    public $viewParams;
    /**
     * @var ModalContentObject контент для ajax и/или не ajax
     */
    public $modalContent;

}