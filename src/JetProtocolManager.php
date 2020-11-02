<?php

class JetProtocolManager
{
    const TRANSPORTER  = 'tp';
    const CONSULPORTER = 'cp';

    /**
     * @var array
     */
    protected static $protocols = array();

    /**
     * @param mixed $protocol
     * @return array|null
     */
    public static function get($protocol)
    {
        return self::isRegistered($protocol) ? self::$protocols[$protocol] : array();
    }

    /**
     * @param string $protocol
     * @param array $metadatas
     * @return void
     */
    public static function register($protocol, $metadatas = array())
    {
        self::$protocols[$protocol] = $metadatas;
    }

    /**
     * @param string $protocol
     * @return bool
     */
    public static function isRegistered($protocol)
    {
        return isset(self::$protocols[$protocol]);
    }

    /**
     * @param string $protocol
     * @return void
     */
    public static function deregister(string $protocol)
    {
        unset(static::$protocols[$protocol]);
    }
}
