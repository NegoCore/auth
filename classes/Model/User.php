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
 * Class Model_User
 */
class Model_User extends Model_Auth_User {

    /**
     * Created column
     * @var array
     */
    protected $_created_column = array(
        'column' => 'created_at',
        'format' => true
    );
}