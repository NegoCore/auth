<?php defined('NEGOCORE') or die('No direct script access.');

/**
 * Auth Module
 *
 * @author      Iván Molina Pavana <imp@negocad.mx>
 * @copyright   Copyright (c) 2015, NegoCAD <info@negocad.mx>
 * @version     1.0.0
 */

// --------------------------------------------------------------------------------

/**
 * Class NegoCore_Auth
 */
class NegoCore_Auth {

    /**
     * @var Auth
     */
    protected static $_instance;

    /**
     * @return Auth
     * @throws Kohana_Exception
     */
    public static function instance()
    {
        if ( ! self::$_instance)
        {
            self::$_instance = new Auth(Kohana::$config->load('auth'));
        }

        return self::$_instance;
    }

    // ----------------------------------------------------------------------

    /**
     * Get user directly
     *
     * @param mixed $default
     * @return mixed|Model_User
     */
    public static function get_user($default = null)
    {
        return Auth::instance()->get_user_session($default);
    }

    // ----------------------------------------------------------------------

    /**
     * Get logged in user ID
     *
     * @return mixed|null
     */
    public static function user_id()
    {
        return self::is_logged_in() ? self::get_user()->pk() : null;
    }

    // ----------------------------------------------------------------------

    /**
     * Check if there is an active session.
     *
     * @return bool
     */
    public static function is_logged_in()
    {
        return self::instance()->logged_in();
    }

    // ----------------------------------------------------------------------

    /**
     * @var Session
     */
    protected $_session;

    /**
     * @var Config_Group
     */
    protected $_config;

    /**
     * Load session & configuration options
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->_config = $config;

        $this->_session = Session::instance($this->_config['session_type']);
    }

    // ----------------------------------------------------------------------

    /**
     * Get the currently logged in user from the session
     *
     * @param mixed $default
     * @return mixed|Model_User
     */
    public function get_user_session($default = null)
    {
        return $this->_session->get($this->_config['session_key'], $default);
    }

    // ----------------------------------------------------------------------

    /**
     * Check if there is an active session.
     *
     * @return bool
     */
    public function logged_in()
    {
        $user = $this->get_user_session();

        if ($user instanceof Model_User && $user->loaded())
            return true;

        return false;
    }

    // ----------------------------------------------------------------------

    /**
     * @param $email
     * @param $password
     * @param bool|false $remember
     * @return bool
     * @throws Kohana_Exception
     */
    public function login($email, $password, $remember = false)
    {
        $user = ORM::factory('User');
        $user->where('email', '=', $email)->find();

        if ($user->loaded() && $user->password === $this->hash($password))
        {
            if ($remember === true)
            {

            }

            $this->_complete_login($user);

            return true;
        }

        return false;
    }

    // ----------------------------------------------------------------------

    /**
     * @param bool $destroy
     * @return bool
     */
    public function logout($destroy = false)
    {
        if ($destroy === true)
        {
            $this->_session->destroy();
        }
        else
        {
            $this->_session->delete($this->_config['session_key']);

            $this->_session->regenerate();
        }

        return ! $this->logged_in();
    }

    // ----------------------------------------------------------------------

    /**
     * @param $str
     * @return string
     * @throws Kohana_Exception
     */
    public function hash($str)
    {
        if ( ! $this->_config['hash_key'])
            throw new Kohana_Exception('A valid hash key must be set in your auth config.');

        return hash_hmac($this->_config['hash_method'], $str, $this->_config['hash_key']);
    }

    // ----------------------------------------------------------------------

    /**
     * Complete the
     * @param $user Model_User
     */
    protected function _complete_login($user)
    {
        // Update login
        $user->logins = new Database_Expression('logins + 1');
        $user->last_login = time();

        $user->update();

        // Session
        $this->_session->regenerate();

        // Store
        $this->_session->set($this->_config['session_key'], $user);
    }
}