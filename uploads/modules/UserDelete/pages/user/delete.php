<?php
/*
 * User Delete Module
 * Page file
 * 
 * @author Alexander Rebello
 * @version 2.3.4
 * @license MIT
 */

// Must be logged in
if (!$user->isLoggedIn()) {
    Redirect::to(URL::build('/'));
}

// Always define page name for navbar
const PAGE = 'cc_delete_account';
$page_title = $userDelete_language->get('user', 'delete_account');
require_once ROOT_PATH . '/core/templates/frontend_init.php';

$errors = [];

// Check if user has permission to delete their account
if ($user->hasPermission('delete_user.delete')) {
    if (Input::exists()) {
        if (Token::check(Input::get('token'))) {
            $validation = Validate::check($_POST, [
                'password' => [
                    Validate::REQUIRED => true
                ],
                'username_confirm' => [
                    Validate::REQUIRED => true
                ]
            ]);

            if ($validation->passed()) {
                // Additional safety check: prevent admin from deleting their account
                if ($user->hasPermission('administrator')) {
                    $admin_count = DB::getInstance()->query('SELECT COUNT(*) as count FROM nl2_users u 
                    JOIN nl2_users_groups ug ON u.id = ug.user_id 
                    JOIN nl2_groups g ON ug.group_id = g.id 
                    WHERE JSON_EXTRACT(g.permissions, "$.administrator") = 1 AND u.id != ?', [$user->data()->id]);

                    if ($admin_count->first()->count > 0) {
                        $errors[] = $userDelete_language->get('user', 'cannot_delete_admin');
                    }
                }

                // Check if password is correct
                if (!password_verify(Input::get('password'), $user->data()->password)) {
                    $errors[] = $userDelete_language->get('user', 'incorrect_password');
                }

                // Check if username confirmation matches
                if (Input::get('username_confirm') !== $user->data()->username) {
                    $errors[] = $userDelete_language->get('user', 'username_does_not_match');
                }

                // If no errors, anonymize the account
                if (empty($errors)) {
                    try {
                        $db = DB::getInstance();
                        $user_id = $user->data()->id;
                        $original_username = $user->data()->username;

                        // Start transaction for atomic anonymization
                        $db->query('START TRANSACTION');

                        try {
                            // Generate anonymized data
                            $anonymous_hash = substr(hash('sha256', $user_id . time() . $original_username), 0, 8);
                            $anonymous_username = 'DeletedUser_' . $anonymous_hash;
                            $anonymous_email = 'deleted_' . $anonymous_hash . '@anonymized.local';
                            $invalid_password = 'DELETED_ACCOUNT_' . hash('sha256', $user_id . time() . 'invalid');
                            $deletion_timestamp = time();

                            // Anonymize the main user record - preserve content but remove personal data
                            $db->query('UPDATE nl2_users SET 
                            username = ?,
                            nickname = ?,
                            password = ?,
                            email = ?,
                            reset_code = NULL,
                            lastip = -1,
                            active = 0,
                            signature = "This account has been deleted.",
                            profile_views = 0,
                            gravatar = 0,
                            has_avatar = 0,
                            avatar_updated = 0,
                            banner = NULL,
                            last_online = ?,
                            user_title = "Deleted User"
                        WHERE id = ?', [
                                $anonymous_username,
                                $anonymous_username,
                                $invalid_password,
                                $anonymous_email,
                                $deletion_timestamp,
                                $user_id
                            ]);

                            // Remove from all groups (but keep content)
                            $db->query('DELETE FROM nl2_users_groups WHERE user_id = ?', [$user_id]);

                            // Create "Deleted Users" group if it doesn't exist, then add user to it
                            $deleted_group_check = $db->query('SELECT id FROM nl2_groups WHERE name = "Deleted Users" LIMIT 1');
                            if ($deleted_group_check->count() > 0) {
                                $deleted_group_id = $deleted_group_check->first()->id;
                            } else {
                                // Create the "Deleted Users" group
                                $db->query('INSERT INTO nl2_groups (
                                name, 
                                group_html, 
                                group_username_color, 
                                group_username_css, 
                                admin_cp, 
                                staff, 
                                permissions, 
                                default_group, 
                                `order`, 
                                force_tfa, 
                                deleted
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                                    'Deleted Users',
                                    '<span class="badge" style="background-color: #6c757d; color: #fff;">Deleted User</span>',
                                    '#6c757d',
                                    null,
                                    0,
                                    0,
                                    '{}',
                                    0,
                                    999,
                                    0,
                                    0
                                ]);
                            }

                            // Get the ID of the newly created group
                            $deleted_group_id_query = $db->query('SELECT id FROM nl2_groups WHERE name = "Deleted Users" ORDER BY id DESC LIMIT 1');
                            $deleted_group_id = $deleted_group_id_query->first()->id;

                            // Add user to the "Deleted Users" group
                            $db->query('INSERT INTO nl2_users_groups (user_id, group_id) VALUES (?, ?)', [$user_id, $deleted_group_id]);

                            // Remove personal relationships but keep content
                            $db->query('DELETE FROM nl2_blocked_users WHERE user_id = ? OR user_blocked_id = ?', [$user_id, $user_id]);
                            $db->query('DELETE FROM nl2_topics_following WHERE user_id = ?', [$user_id]);

                            // Clean up OAuth integrations (remove personal connections)
                            $db->query('DELETE FROM nl2_oauth_users WHERE user_id = ?', [$user_id]);

                            // Remove alerts TO this user, keep alerts FROM this user (anonymized)
                            $db->query('DELETE FROM nl2_alerts WHERE user_id = ?', [$user_id]);

                            // If we get here, commit the transaction
                            $db->query('COMMIT');
                            // Log the account anonymization
                            Log::getInstance()->log('user_anonymized', 'User ID: ' . $user_id . ' (' . $original_username . ') anonymized their account');

                            // Log out and redirect
                            Log::getInstance()->log(Log::Action('user/logout'));
                            $user->admLogout();
                            $user->logout();
                            Session::flash('home', $language->get('user', 'successfully_logged_out'));
                            Redirect::to(URL::build('/'));
                        } catch (Exception $inner_e) {
                            // Rollback transaction on any error
                            $db->query('ROLLBACK');
                            throw $inner_e;
                        }
                    } catch (Exception $e) {
                        $errors[] = $userDelete_language->get('general', 'error_occurred');
                        Log::getInstance()->log('user_delete_error', 'Error deleting user: ' . $e->getMessage());
                    }
                }
            } else {
                $errors = $validation->errors();
            }
        } else {
            $errors[] = $userDelete_language->get('general', 'invalid_token');
        }
    }
} else {
    // User does not have permission to delete their account
    $errors[] = $userDelete_language->get('user', 'no_permission_delete');
}

if (Session::exists('success_post')) {
    $template->getEngine()->addVariable('SUCCESS_MESSAGE', Session::flash('success_post'));
}

if (!empty($errors)) {
    $template->getEngine()->addVariable('ERRORS', $errors);
}

// Language values
$template->getEngine()->addVariables([
    'USER_CP' => $userDelete_language->get('user', 'user_cp'),
    'DELETE_ACCOUNT' => $userDelete_language->get('user', 'delete_account'),
    'DELETE_ACCOUNT_INFO' => $userDelete_language->get('user', 'delete_account_info'),
    'CURRENT_PASSWORD' => $userDelete_language->get('user', 'current_password'),
    'USERNAME_CONFIRM' => $userDelete_language->get('user', 'username_confirm'),
    'USERNAME_CONFIRM_INFO' => $userDelete_language->get('user', 'username_confirm_info'),
    'DELETE_ACCOUNT_BUTTON' => $userDelete_language->get('user', 'delete_account'),
    'CANCEL' => $userDelete_language->get('general', 'cancel'),
    'CONFIRM_DELETE' => $userDelete_language->get('user', 'confirm_delete_account'),
    'WARNING' => $userDelete_language->get('general', 'warning'),
    'TOKEN' => Token::get(),
    'USERNAME' => Output::getClean($user->data()->username)
]);

// Load modules + template
Module::loadPage($user, $pages, $cache, $smarty, [$navigation, $cc_nav, $staffcp_nav], $widgets, $template);

require ROOT_PATH . '/core/templates/cc_navbar.php';

$template->onPageLoad();

require ROOT_PATH . '/core/templates/navbar.php';
require ROOT_PATH . '/core/templates/footer.php';

// Display template
$template->displayTemplate('user/delete_account');
