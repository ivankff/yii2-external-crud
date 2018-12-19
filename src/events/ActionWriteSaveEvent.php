<?php

namespace ivankff\yii2ExternalCrud\events;

use ivankff\yii2ExternalCrud\ModelSaveInterface;
use yii\base\Action;
use yii\base\Event;
use yii\base\Model;

class ActionWriteSaveEvent extends Event
{

    /**
     * @var Action
     */
    public $sender;
    /**
     * @var Model|ModelSaveInterface
     */
    public $model;
    /**
     * @var bool Запускать ли валидацию перед сохранением
     */
    public $runValidation = true;
    /**
     * @var bool Выполнить ли сохранение
     */
    public $isValid = true;
    /**
     * @var bool Успешно ли прошло сохранение формы
     */
    public $success = false;

}