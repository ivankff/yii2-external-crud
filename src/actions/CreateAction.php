<?php

namespace ivankff\yii2ExternalCrud\actions;

class CreateAction extends WriteAction
{

    /**
     * @var string
     */
    public $view = 'update';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->_run(call_user_func($this->model, $this));
    }

}