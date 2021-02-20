<?php

namespace ivankff\yii2ExternalCrud\events;

use yii\base\Action;
use yii\base\Event;
use yii\base\Model;

/**
 */
class ActionWriteLoadEvent extends Event
{

    /**
     * @var Action
     */
    public $sender;
    /**
     * @var Model form model
     */
    public $model;
    /**
     * @var array POST data
     */
    public $post;
    /**
     * @var null|bool
     * @see Model::load()
     */
    public $isLoaded;

}