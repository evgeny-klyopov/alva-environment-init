<?php
/**
 * Created by PhpStorm.
 * User: Alva
 * Date: 08.06.2017
 * Time: 01:21
 * @author Alva <mail@klepov.info>
 */

namespace Alva\InitEnvironment;

final class Message
{
    private static $instance;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private $language;
    public function __construct()
    {
        $fileLanguage = __DIR__ . DS . 'language' . DS . LANGUAGE . '.php';
        if (is_file($fileLanguage)) {
            $this->language = include (__DIR__ . DS . 'language' . DS . LANGUAGE . '.php');
        } else {
            throw new \Exception('Not found language file (' . $fileLanguage . ')');
        }
    }

    public function formatMessage($message, $color = false, array $params = [])
    {
        return (
            $color
                ? '<fg=' . $color . '>' . $this->getMessage($message, $params) . '</>'
                : $this->getMessage($message, $params)
        );
    }

    public function getMessage($message, array $params = [])
    {
        if (isset($this->language[$message])) {
            $message = $this->language[$message];

            $placeholders = [];
            foreach ((array) $params as $name => $value) {
                $placeholders['{' . $name . '}'] = $value;
            }

            return ($placeholders === []) ? $message : strtr($message, $placeholders);
        }

        return $message;
    }

    private function __clone()
    {

    }

    private function __wakeup()
    {
    }
}