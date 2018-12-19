<?php

namespace ivankff\yii2ExternalCrud\events;

use ivankff\yii2ExternalCrud\ModalContentObject;
use ivankff\yii2ExternalCrud\ModalSuccessObject;
use yii\base\Action;
use yii\base\Event;
use yii\base\Model;

class ActionWriteViewEvent extends Event
{

    /**
     * @var Action
     */
    public $sender;
    /**
     * @var Model Модель формы
     */
    public $model;
    /**
     * @var bool Успешно ли прошло сохранение формы
     * Влияет на выполнение редиректа или показ view
     */
    public $success;
    /**
     * @var bool Возможен ли ajax-ответ
     */
    public $enableAjax = true;
    /**
     * @var array Route для редиректа после усешного сохранения
     * в режиме не ajax
     * ['index', 'parent_id' => 0]
     */
    public $successRedirectRoute;
    /**
     * @var array Параметры для рендеринга view
     * [
     *     'model' => $model,
     *     'additionalQueryParams' => $additionalQueryParams,
     * ]
     */
    public $viewParams;
    /**
     * @var ModalSuccessObject возвращаемый массив после успещного сохранения
     * в режиме не ajax
     */
    public $modalSuccess;
    /**
     * @var ModalContentObject контент для ajax и/или не ajax
     */
    public $modalContent;

}