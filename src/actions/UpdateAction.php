<?php

namespace ivankff\yii2ExternalCrud\actions;

class UpdateAction extends WriteAction
{

    /**
     * @var string
     */
    public $view = 'update';

    /**
     * {@inheritdoc}
     */
    public function run($id)
    {
        return $this->_run(call_user_func($this->model, $id, $this), $id);
    }

}