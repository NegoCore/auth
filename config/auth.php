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
 * Auth configuration
 *
 * @filesource
 */
return array(
    'hash_method'  => 'sha256',
    'hash_key'     => 'default',
    'lifetime'     => 1209600,
    'session_type' => Session::$default,
    'session_key'  => 'auth_user'
);