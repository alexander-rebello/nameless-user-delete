<?php
/*
 * User Delete Module
 * Initialization file
 * 
 * @author Alexander Rebello
 * @version 1.0.0
 * @license MIT
 */
// Initialise user delete language
$userDelete_language = new Language(ROOT_PATH . '/modules/UserDelete/language');

// Add Delete Account link to UserCP navigation
$cc_nav->add('cc_delete_account', 'Delete Account', URL::build('/user/delete'));

// Initialize the module
require_once ROOT_PATH . '/modules/UserDelete/module.php';
$module = new UserDelete_Module($language, $userDelete_language, $pages);
