<?php

namespace ivankff\yii2ExternalCrud\actions;

use Assert\Assertion;
use ivankff\yii2ExternalCrud\events\ActionViewViewEvent;
use ivankff\yii2ExternalCrud\ModalContentObject;
use yii\base\Action;
use Yii;
use yii\base\ModelEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;

class ViewAction extends Action
{

    const EVENT_INIT = 'init';
    const EVENT_BEFORE_RUN = 'beforeRun';
    const EVENT_AFTER_RUN = 'afterRun';
    const EVENT_BEFORE_VIEW = 'beforeView';

    /**
     * @var callable Получение модели формы
     */
    public $model;
    /**
     * @var null|string|\Closure|array Формирование title модального окна из модель
     * `null` - не использовать модель для формирования title
     * @see ArrayHelper::getValue()
     */
    public $modalTitle;
    /**
     * @var string
     */
    public $view = 'view';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        Assertion::isCallable($this->model);
        Assertion::notBlank($this->view);
        parent::init();
        $this->trigger(self::EVENT_INIT);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $model = call_user_func($this->model, $this);

        $request = Yii::$app->request;

        $eventView = new ActionViewViewEvent([
            'model' => $model,
            'viewParams' => [
                'model' => $model,
            ],
            'modalContent' => new ModalContentObject([
                'title' => 'Быстрый просмотр',
                'footer' => Html::button('Закрыть', ['class' => 'btn btn-default mr-auto', 'data-dismiss' => "modal"]),
            ]),
        ]);
        $this->trigger(self::EVENT_BEFORE_VIEW, $eventView);

        if ($request->isAjax && $eventView->enableAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return [
                'title' => $eventView->modalContent->title,
                'content' => null === $eventView->modalContent->content
                    ? call_user_func([$this->controller, 'renderAjax'], $this->view, $eventView->viewParams)
                    : $eventView->modalContent->content,
                'footer' => $eventView->modalContent->footer,
            ];
        }

        return null === $eventView->modalContent->content
            ? call_user_func([$this->controller, 'render'], $this->view, $eventView->viewParams)
            : $eventView->modalContent->content;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeRun()
    {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_RUN, $event);

        return $event->isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function afterRun()
    {
        $this->trigger(self::EVENT_AFTER_RUN);
    }

}