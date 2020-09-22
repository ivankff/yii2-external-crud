<?php

namespace ivankff\yii2ExternalCrud\widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use Yii;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 */
class SessionModalListWidgetWidget extends Widget
{

    /** @var string */
    public $pjaxId = 'grid-pjax';
    /** @var string */
    public $title = '';
    /** @var string */
    public $addButtonTitle = 'Добавить';
    /** @var string */
    public $sessionKey;
    /** @var array */
    public $addButtonUrl = [];

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $content = ob_get_clean();

        $addUrl = ArrayHelper::merge($this->addButtonUrl, ['sessionKey' => $this->sessionKey]);

        $html = "<div class=\"card card-outline\">
            <div class=\"card-header with-border\"><h4 class=\"box-title\">{$this->title}" .Html::a("<i class=\"fa fa-plus mr-2\"></i>{$this->addButtonTitle}", $addUrl, ['class' => 'btn btn-sm btn-default ml-2', 'role' => 'modal-remote']). "</h4></div>
            <div class=\"card-body\">";
        $html .= $content;
        $html .= "</div></div>";

        if ($this->pjaxId)
            Pjax::begin(['id' => $this->pjaxId, 'timeout' => 3000, 'enablePushState' => false]);

        echo $html;

        if ($this->pjaxId)
            Pjax::end();
    }

}