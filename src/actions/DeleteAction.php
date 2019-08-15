<?php

namespace ivankff\yii2ExternalCrud\actions;

use Assert\Assertion;
use ivankff\yii2ExternalCrud\events\ActionDeleteEvent;
use yii\base\Action;

class DeleteAction extends Action
{

    const EVENT_INIT = 'init';
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';
    const EVENT_BEFORE_REDIRECT = 'beforeRedirect';

    /**
     * @var callable Получение модели формы
     */
    public $model;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        Assertion::isCallable($this->model);
        parent::init();
        $this->trigger(self::EVENT_INIT);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $model = call_user_func($this->model, $this);
        Assertion::methodExists('delete', $model);

        $eventView = new ActionDeleteEvent([
            'model' => $model,
        ]);
        $this->trigger(self::EVENT_BEFORE_DELETE, $eventView);

        if ($eventView->isValid && $model->delete()) {
            $eventView->success = true;
            $this->trigger(self::EVENT_AFTER_DELETE, $eventView);
        }

        $this->trigger(self::EVENT_BEFORE_REDIRECT, $eventView);

        return $this->controller->redirect($eventView->successRedirectRoute);
    }

}