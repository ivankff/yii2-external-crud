<?php

namespace ivankff\yii2ExternalCrud;

use Assert\Assertion;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use Yii;

/**
 */
class BaseSessionModalForm extends Model implements ModelSaveInterface, SessionModalInterface
{

    /**
     * @var string ключ записи
     * @notice: в представлении обязательно должен быть hiddenInput с этим параментом
     */
    public $key;

    /** @var string ид сессии для основной формы, которая открыта */
    protected $_sessionKey;
    /** @var bool */
    protected $_isNewRecord = false;

    /**
     * @param string $sessionKey
     * @param string|null $itemKey null - запись считается новой
     * @param array $config
     */
    public function __construct($sessionKey, $itemKey = null, array $config = [])
    {
        Assertion::notBlank($sessionKey);

        if (! $itemKey) {
            $this->_isNewRecord = true;
            $itemKey = Yii::$app->security->generateRandomString();
        }

        $this->_sessionKey = $sessionKey;
        $this->key = $itemKey;

        parent::__construct($config);
    }

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        if ($this->getKey()) {
            if ($array = ArrayHelper::getValue(Yii::$app->session->get($this->_sessionKey . static::sessionPrefix()), $this->getKey())) {
                $this->setAttributes($array, false);
            }
        }

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key'], 'required'],
        ];
    }

    /** @return string */
    public static function sessionPrefix() { return ''; }

    /**
     * @param string $sessionKey
     * @return bool
     */
    public static function isSessionEmpty($sessionKey)
    {
        if (! Yii::$app->session->has($sessionKey . static::sessionPrefix()))
            return true;

        $session = Yii::$app->session->get($sessionKey . static::sessionPrefix());
        return !is_array($session);
    }

    /**
     * @param string $sessionKey
     * @return static[]
     */
    public static function loadFromSession($sessionKey)
    {
        if (static::isSessionEmpty($sessionKey))
            return [];

        $items = [];

        foreach (Yii::$app->session->get($sessionKey . static::sessionPrefix()) as $key => $item)
            $items[$key] = new static($sessionKey, $key);

        return $items;
    }

    /**
     * @param string $sessionKey
     */
    public static function clearSession($sessionKey)
    {
        Yii::$app->session->remove($sessionKey . static::sessionPrefix());
    }

    /** @return mixed */
    public function getKey() { return $this->key; }

    /** @return string */
    public function getSessionKey() { return $this->_sessionKey; }

    /** @return array */
    public function pack() { return $this->getAttributes(); }

    /** {@inheritdoc} */
    public function modalTitle() { return $this->_isNewRecord ? 'Добавить' : 'Изменить'; }

    /** {@inheritdoc} */
    public function getIsNewRecord() { return $this->_isNewRecord; }

    /**
     * @return bool
     */
    public function delete()
    {
        if (! $items = Yii::$app->session->get($this->_sessionKey . static::sessionPrefix()))
            return false;

        $removed = ArrayHelper::remove($items, $this->key);
        Yii::$app->session->set($this->_sessionKey . static::sessionPrefix(), $items);

        return !empty($removed);
    }

    /**
     * @param bool $runValidation
     * @return bool
     */
    public function save($runValidation = true)
    {
        if ($runValidation && !$this->validate())
            return false;

        $items = Yii::$app->session->get($this->_sessionKey . static::sessionPrefix());
        $key = $this->getKey();
        $items[$key] = $this->pack();
        Yii::$app->session->set($this->_sessionKey . static::sessionPrefix(), $items);

        return true;
    }

}