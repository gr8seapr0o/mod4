<?php
/**
 * class for setting and getting app settings /
 */
class Config {

    protected static $settings = array();

    /**
     * from Config class (global app settings) /
     * @param $key
     * @return mixed|null $setting[key] 
     */
    public static function get($key)
    {
        return isset(self::$settings[$key]) ? self::$settings[$key] : null;
    }

    /**
     * initial global setting /
     * @param $key
     * @param $value
     * @internal param array $settings to Config class (global app settings)
     */
    public static function set($key, $value)
    {
        self::$settings[$key] = $value;
    }

}