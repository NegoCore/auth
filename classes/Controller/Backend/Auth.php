<?php defined('NEGOCORE') or die('No direct script access.');

/**
 * Auth Module
 *
 * @author      Iván Molina Pavana <imp@negocad.mx>
 * @copyright   Copyright (c) 2015, NegoCAD <info@negocad.mx>
 * @version     1.0.0
 */

// --------------------------------------------------------------------------------

class Controller_Backend_Auth extends Controller_Backend {

    /**
     * @var bool Disable authentication
     */
    public $auth_required = false;

    /**
     *
     */
    public function before()
    {
        parent::before();

        if ($this->request->action() == 'login' && Auth::is_logged_in())
        {
            $this->go_home();
        }
    }

    /**
     *
     */
    public function action_index()
    {
        $this->go_backend('auth', 'login');
    }

    /**
     * Login user
     */
    public function action_login()
    {
        if ($this->request->method() == Request::POST)
        {
            $login = $this->request->post();
            if (Auth::instance()->login($login['email'], $login['password'], isset($login['remember'])))
            {
                if ($next_url = Flash::get('redirect'))
                {
                    $this->go($next_url);
                }

                $this->go_backend();
            }

            Messages::error('Por favor, comprueba tus datos de acceso e inténtalo de nuevo.');
        }

        Document::title('Ingresar');
    }

    /**
     *
     */
    public function action_logout()
    {
        $this->auto_render = false;

        Auth::instance()->logout();

        if ($next_url = Flash::get('redirect'))
        {
            $this->go($next_url);
        }

        $this->go_home();
    }

    /**
     * @throws Kohana_Exception
     */
    public function action_create()
    {
        $this->auto_render = false;

        if ($this->request->query('email') && $this->request->query('password'))
        {
            try {

                ORM::factory('User')
                    ->values(array(
                        'email' => $this->request->query('email'),
                        'password' => Auth::instance()->hash($this->request->query('password'))
                    ))->create();

                var_dump($this->request->query());

                exit('Usuario creado correctamente');

            } catch (Database_Exception $e) {
                exit('El usuario ya existe.');
            }
        }
    }
}