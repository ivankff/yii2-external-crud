<?php

namespace ivankff\yii2ExternalCrud\events;

use yii\base\Action;
use yii\base\Event;
use yii\db\ActiveRecordInterface;

class ActionDeleteEvent extends Event
{

    /**
     * @var Action
     */
    public $sender;
    /**
     * @var ActiveRecordInterface Модель для удаления
     */
    public $model;
    /**
     * @var bool Выполнить ли удаление
     */
    public $isValid = true;
    /**
     * @var bool Успешно ли прошло сохранение формы
     */
    public $success = false;
    /**
     * @var array Route для редиректа после усешного сохранения
     * в режиме не ajax
     * ['index', 'parent_id' => 0]
     */
    public $successRedirectRoute = ['index'];

}