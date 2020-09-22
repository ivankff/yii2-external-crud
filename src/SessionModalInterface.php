<?php

namespace ivankff\yii2ExternalCrud;

/**
 */
interface SessionModalInterface
{

    /** @return string префикс для хранения в сессии, чтобы не пересекались с другими сессионными формами */
    public static function sessionPrefix();

    /**
     * @param string $sessionKey
     * @return bool
     */
    public static function isSessionEmpty($sessionKey);

    /** @param string $sessionKey */
    public static function clearSession($sessionKey);

    /** @return string */
    public function getKey();

    /** @return string */
    public function getSessionKey();

    /** @return array */
    public function pack();

    /** @return bool */
    public function delete();

}