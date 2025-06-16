<?php
/*
 * User Delete Module
 * Module file
 * 
 * @author Alexander Rebello
 * @version 2.3.4
 * @license MIT
 */

class UserDelete_Module extends Module
{

    private Language $_language;
    private Language $_userDelete_language;

    public function __construct(Language $language, Language $userDelete_language, Pages $pages)
    {
        $this->_language = $language;
        $this->_userDelete_language = $userDelete_language;

        $name = 'UserDelete';
        $author = '<a href="https://www.alexander-rebello.de" target="_blank" rel="nofollow noopener">Alexander Rebello</a>';
        $module_version = '2.2.3';
        $nameless_version = '2.2.3';

        parent::__construct($this, $name, $author, $module_version, $nameless_version);

        // Define URLs which belong to this module
        $pages->add('UserDelete', '/user/delete', 'pages/user/delete.php', 'cc_delete_account');
    }

    public function onPageLoad(
        User $user,
        Pages $pages,
        Cache $cache,
        $smarty,
        iterable $navs,
        Widgets $widgets,
        TemplateBase $template
    ) {
        // Register permissions
        PermissionHandler::registerPermissions('UserDelete', [
            'delete_user.delete' => 'Profile Settings &raquo; Delete own account',
        ]);
    }

    public function onInstall() {}

    public function onUninstall() {}

    public function onEnable() {}

    public function onDisable() {}

    public function getDebugInfo(): array
    {
        return [];
    }
}
