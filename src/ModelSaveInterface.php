<?php

namespace ivankff\yii2ExternalCrud;

interface ModelSaveInterface
{

    /**
     * @return string Название для модального окна
     */
    public function modalTitle();
    /**
     * @return bool Является ли модель моделью для добавления
     */
    public function getIsNewRecord();
    /**
     * @param bool $runValidation
     * @return bool
     */
    public function save($runValidation = true);

}