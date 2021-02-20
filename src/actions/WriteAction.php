<?php

namespace ivankff\yii2ExternalCrud\actions;

use Assert\Assertion;
use ivankff\yii2ExternalCrud\events\ActionWriteLoadEvent;
use ivankff\yii2ExternalCrud\events\ActionWriteSaveEvent;
use ivankff\yii2ExternalCrud\events\ActionWriteViewEvent;
use ivankff\yii2ExternalCrud\ModalContentObject;
use ivankff\yii2ExternalCrud\ModalSuccessObject;
use ivankff\yii2ExternalCrud\ModelSaveInterface;
use yii\base\Action;
use Yii;
use yii\base\Model;
use yii\base\ModelEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;

class WriteAction extends Action
{

    const EVENT_INIT = 'init';
    const EVENT_BEFORE_RUN = 'beforeRun';
    const EVENT_AFTER_RUN = 'afterRun';
    const EVENT_BEFORE_LOAD = 'beforeLoad';
    const EVENT_AFTER_LOAD = 'afterLoad';
    const EVENT_BEFORE_SAVE = 'beforeSave';
    const EVENT_AFTER_SAVE = 'afterSave';
    const EVENT_BEFORE_VIEW = 'beforeView';

    /**
     * @var callable Получение модели формы
     */
    public $model;
    /**
     * @var string[] Список ключей, которые надо взять из url
     * ['parent_id', 'page']
     */
    public $additionalQueryParams = [];
    /**
     * @var string
     */
    public $view = 'update';
    /**
     * @var string|null
     * `string` - ключ для Флеш-сообщения
     * `null` - не использовать флеш
     */
    public $flash;
    /**
     * @var string grid-id для перезагрузки после закрытия модального окна
     */
    public $forceReload = '#grid-pjax';
    /**
     * @var bool закрывать или нет окно после нажатия кнопки Сохранить
     */
    public $forceClose = true;

    /**
     * {@inheritdoc}
     * @throws
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
        /** @var Model|ModelSaveInterface $model $model */
        $model = call_user_func($this->model, $this);
        Assertion::isInstanceOf($model, Model::class);
        Assertion::isInstanceOf($model, ModelSaveInterface::class);

        $request = Yii::$app->request;
        $isNewRecord = $model->getIsNewRecord();

        $eventLoad = new ActionWriteLoadEvent(['model' => $model, 'post' => $request->post()]);
        $this->trigger(self::EVENT_BEFORE_LOAD, $eventLoad);
        $eventLoad->isLoaded = $model->load($eventLoad->post);
        $this->trigger(self::EVENT_AFTER_LOAD, $eventLoad);

        $success = false;

        if ($eventLoad->isLoaded) {
            $eventSave = new ActionWriteSaveEvent(['model' => $model]);
            $this->trigger(self::EVENT_BEFORE_SAVE, $eventSave);

            if ($eventSave->isValid && (! $eventSave->runValidation || $model->validate())) {
                if ($model->save($eventSave->runValidation)) {
                    $eventSave->success = true;

                    if ($this->flash)
                        Yii::$app->session->addFlash("{$this->flash}.success", "Изменения сохранены " . Yii::$app->formatter->asDatetime(new \DateTime()) . ".");
                } else {
                    if ($this->flash)
                        Yii::$app->session->addFlash("{$this->flash}.error", "Не удалось сохранить изменения. Обратитесь к администратору.");
                }
            }

            $this->trigger(self::EVENT_AFTER_SAVE, $eventSave);
            $success = $eventSave->success;
        }

        $additionalQueryParams = ArrayHelper::filter($request->get(), $this->additionalQueryParams);
        if ($isNewRecord) {
            $route = ArrayHelper::merge(['index'], $additionalQueryParams);
        } else {
            if (Yii::$app->request->post('exit') === null)
                $route = Yii::$app->getRequest()->getUrl();
            else
                $route = ArrayHelper::merge(['index'], $additionalQueryParams);
        }

        $eventView = new ActionWriteViewEvent([
            'model' => $model,
            'success' => $success,
            'successRedirectRoute' => $route,
            'viewParams' => [
                'model' => $model,
                'additionalQueryParams' => $additionalQueryParams,
            ],
            'modalSuccess' => new ModalSuccessObject(['forceReload' => $this->forceReload, 'forceClose' => $this->forceClose]),
            'modalContent' => new ModalContentObject([
                'title' => $model->modalTitle(),
                'footer' => Html::button('Закрыть', ['class' => 'btn btn-default mr-auto', 'data-dismiss' => "modal"]) .
                    Html::button($isNewRecord ? 'Добавить' : 'Сохранить', ['class' => $isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'type' => "submit"])
            ]),
        ]);
        $this->trigger(self::EVENT_BEFORE_VIEW, $eventView);

        if ($request->isAjax && $eventView->enableAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($eventView->success)
                return ArrayHelper::toArray($eventView->modalSuccess);

            return [
                'title' => $eventView->modalContent->title,
                'content' => null === $eventView->modalContent->content
                    ? call_user_func([$this->controller, 'renderAjax'], $this->view, $eventView->viewParams)
                    : $eventView->modalContent->content,
                'footer' => $eventView->modalContent->footer,
            ];
        }

        if ($eventView->success)
            return $this->controller->redirect($eventView->successRedirectRoute);

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