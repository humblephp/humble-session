<?php

namespace Humble\Session;

class Session implements \ArrayAccess
{
    private $cookieParams;
    private $sessionHandler;

    public function __construct(array $cookieParams = array(), \SessionHandlerInterface $sessionHandler = null)
    {
        $this->cookieParams = $cookieParams;
        $this->sessionHandler = $sessionHandler;

        $this->start();
    }

    public function start()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('session has already been started');
        }

        if ($this->sessionHandler) {
            session_set_save_handler($this->sessionHandler, true);
        }

        $cookieParams = array_merge(session_get_cookie_params(), $this->cookieParams);

        session_set_cookie_params(
            $cookieParams['lifetime'],
            $cookieParams['path'],
            $cookieParams['domain'],
            $cookieParams['secure'],
            $cookieParams['httponly']
        );

        ini_set('session.gc_maxlifetime', $cookieParams['lifetime']);

        session_start();
    }

    public function regenerate()
    {
        session_regenerate_id(true);
    }

    public function destroy()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException('session has not been started');
        }

        $_SESSION = array();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                0,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    public function offsetGet($offset)
    {
        return $_SESSION[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        $_SESSION[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($_SESSION[$offset]);
    }
}
