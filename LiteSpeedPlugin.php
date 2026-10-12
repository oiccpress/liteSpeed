<?php

/**
 * LSCache / LiteSpeed Cache plugin
 * 
 * @author Joe Simpson
 * 
 * @class LiteSpeedPlugin
 *
 * @brief LiteSpeedPlugin
 */

namespace APP\plugins\generic\liteSpeed;

use PKP\template\PKPTemplateManager;
use APP\core\Application;
use APP\plugins\generic\letterOfAcceptance\classes\Settings\Actions;
use APP\plugins\generic\letterOfAcceptance\classes\Settings\Manage;
use APP\template\TemplateManager;
use PKP\core\JSONMessage;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;
use PKP\security\Role;

class LiteSpeedPlugin extends GenericPlugin {

    private $backendPage = false;

    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path);

        if ($success && $this->getEnabled()) {
            Hook::add( 'TemplateManager::display', [ $this, 'displayTemplate' ] );
            Hook::add( 'TemplateManager::setupBackendPage', [ $this, 'setupBackendPage' ] );
            Hook::add( 'Template::Layout::Backend::HeaderActions', [ $this, 'headerActions' ] );
        }

        return $success;
    }

    public function headerActions(string $hookName, array $args) : bool
    {
        $img = '/plugins/generic/liteSpeed/recycle-solid-full.svg';
        echo '<a style="display:inline-flex;align-items:center" href="?_clearlc=true">';
        
        echo '<img style="width:22px;height:22px" src="' . $img . '" alt="' . __('plugins.generic.liteSpeed.clearCache') . '" />';

        if(isset($_GET['_clearlc'])) {
            echo '<span style="background:#eee;border-radius:5px;padding:5px">';
            echo __('plugins.generic.liteSpeed.clearCacheDone');
            echo '</span>';
        }
        
        echo '</a>';
        return false;
    }

    public function setupBackendPage(string $hookName, array $args): bool
    {
        $this->backendPage = true;
        return false;
    }

    /**
     * Route requests for the `example` page to a custom page handler
     */
    public function displayTemplate(string $hookName, array $args): bool
    {
        /**
         * @var PKPTemplateManager
         */
        $templateManager = $args[0];
        
        $request = Application::get()->getRequest();
        if(!$request->getUser() && !$this->backendPage && $_SERVER['REQUEST_METHOD'] == 'GET') {
            // cache for 1 hour
            header('X-LiteSpeed-Cache-Control: public,max-age=3600');
            // tell cdn to cache for 30 mins
            header('Cloudflare-CDN-Cache-Control: max-age=1800');
        }

        if($request->getUser()) {
            setcookie('ojs_id', sha1($request->getUser()->getId()), time()+60*60*24*30);
        }

        if(isset($_GET['_clearlc'])) {
            // Clear button has been pressed
            header('X-LiteSpeed-Purge: *');
        }

        return false;
    }

    /**
     * Provide a name for this plugin
     *
     * The name will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDisplayName()
    {
        return __('plugins.generic.liteSpeed.displayName');
    }

    /**
     * Provide a description for this plugin
     *
     * The description will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDescription()
    {
        return __('plugins.generic.liteSpeed.description');
    }

    /**
     * This plugin can be used site-wide or in a specific context. The
     * isSitePlugin check is used to grant access to different users, so this
     * plugin must return true only if the user is currently in the site-wide
     * context.
     *
     * @see PluginGridRow::_canEdit()
     *
     */
    public function isSitePlugin(): bool
    {
        return !Application::get()->getRequest()->getContext();
    }

}