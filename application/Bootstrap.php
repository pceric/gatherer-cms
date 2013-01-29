<?php
/**
 * Bootstrap
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Builds a Zend_Navigation menu
     */
    private function pageBuilder(&$view)
    {
        $db = Zend_Registry::get('db');
        $parent_count = 0;
        $first = true;
        $adata = $db->fetchAll("SELECT * FROM menu WHERE parent IS NULL ORDER BY weight,id");
        $pages = array();
        foreach ($adata as $k => $v) {
            if (empty($v['link'])) {
                $first = true; // Reset our first flag for our submenus
                $pages[] = array('label' => $v['name'], 'uri' => '', 'class' => 'nav-header', 'pages' => array());
                $rs = $db->query("SELECT name,link FROM menu WHERE parent = ? ORDER BY weight,id DESC", $v['id']);
                while ($item = $rs->fetch()) {
                    $pages[$parent_count]['pages'][] = array_merge(array('label' => $item['name']), unserialize($item['link']));
                }
                $parent_count++;
            } else {
                $pages[] = array_merge(array('label' => $v['name']), unserialize($v['link']));
                $parent_count++;
            }
        }
        $pages[] = array('label' => 'Admin', 'module' => 'admin', 'resource' => 'mvc:admin');

        $container = new Zend_Navigation($pages);
        Zend_Registry::set('Zend_Navigation', $container);
    }
    
    /**
     * Bootstrap Config
     */
    protected function _initConfig()
    {
        $this->bootstrap('log');
        $log = $this->getResource('log');
        Zend_Registry::set('log', $log);
        $this->bootstrap('db');
        $db = $this->getResource('db');
        Zend_Registry::set('db', $db);
        Zend_Registry::set('config', new GCMS_Config($db));
        $this->bootstrap('cachemanager');
        $manager = $this->getResource('cachemanager');
        Zend_Locale::setCache($manager->getCache('g11n'));
        Zend_Registry::set('Zend_Locale', new Zend_Locale());
        Zend_Translate::setCache($manager->getCache('g11n'));
        $translate = new Zend_Translate('array', APPLICATION_PATH . '/../data/locales', NULL, array('scan' => Zend_Translate::LOCALE_FILENAME,
                                                                                                    'disableNotices' => true));
        Zend_Registry::set('Zend_Translate', $translate);
    }

    /**
     * Bootstrap Controller
     */
    protected function _initController()
    {
        // Start an admin session
        $authNamespace = new Zend_Session_Namespace('gcmsauth');
        // Setup some ACLs
        if (isset($authNamespace->acl)) {
            $acl = unserialize($authNamespace->acl);
        } else {
            $acl = new Zend_Acl();
            $acl->addRole(new Zend_Acl_Role('admin'));
            $acl->add(new Zend_Acl_Resource('mvc:admin'));
            $acl->deny('admin');
        }
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole('admin');
        Zend_Registry::set('acl', $acl);
    }

    /**
     * Bootstrap Smarty view
     */
    protected function _initView()
    {
        $cfg = Zend_Registry::get('config');
        $theme_path = APPLICATION_PATH . '/../public/themes/' . $cfg['sitetheme'];
        
        // Initialize Smarty view
        $view = new GCMS_View_Smarty($this->getOption('smarty'));
        // The ZF auto BaseUrl is horrible so let's do it ourselves
        if (!empty($cfg['siteURL'])) {
            $view->getHelper('BaseUrl')->setBaseUrl($cfg['siteURL']);
        } else {
            $view->getHelper('BaseUrl')->setBaseUrl(substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/")+1));
        }
        $view->doctype('HTML5');
        $view->headTitle($cfg['sitename']);
        $view->headTitle()->setSeparator(' - ');
        $view->headMeta()->setCharset('UTF-8');
        $view->headMeta()->setName('generator', "GCMS v" . GCMS_VERSION);
        if (!empty($cfg['sitekeywords']))
            $view->headMeta()->appendName('keywords', $cfg['sitekeywords']);
        if (!empty($cfg['siteauthor']))
            $view->headMeta()->appendName('author', $cfg['siteauthor']);
        if (!empty($cfg['sitedesc']))
            $view->headMeta()->appendName('description', $cfg['sitedesc']);
        for ($i=1;$i<=3;$i++) {
            if ($cfg["meta{$i}name"] != null)
                $view->headMeta()->appendName($cfg["meta{$i}name"], $cfg["meta{$i}value"]);
        }
        $view->headLink()->setAlternate($view->baseUrl('feed.php'), 'application/atom+xml', 'Atom Feed');
        $view->headLink()->appendAlternate($view->baseUrl('feed.php?RSS'), 'application/rss+xml', 'RSS Feed');
        $view->headScript()->appendFile($view->baseUrl('themes/default/js/mootools-core-1.4.5-full-nocompat-yc.js'));
        $view->headScript()->appendFile($view->baseUrl('themes/default/js/mootools-more-1.4.0.1.js'));
        $view->headScript()->appendFile($view->baseUrl('themes/default/js/moostrap.js'));
        $view->headScript()->appendFile($view->baseUrl('themes/default/js/gcms.js'));
        $view->headScript()->appendScript("var gcms = new MooGCMS({ baseUrl: '" . $view->baseUrl() . "', theme: '" . $cfg['sitetheme'] . "' });");
        // Some admin specific config
        if (Zend_Registry::get('acl')->isAllowed('admin')) {
            $view->headScript()->appendFile($view->baseUrl('themes/default/js/inlineEdit.v3.js'));
            $view->headScript()->appendFile($view->baseUrl('themes/default/js/admin.js'));
            $view->headScript()->appendScript("var gcmsAdmin = new MooGCMSAdmin({ baseUrl: '" . $view->baseUrl() . "' });");
            if ($cfg['editor'] == 'CKEditor') {
                $view->headScript()->appendFile($view->baseUrl("themes/default/js/ckeditor/ckeditor.js"));
            } elseif ($cfg['editor'] == 'TinyMCE') {
                $view->headScript()->appendFile($view->baseUrl("themes/default/js/tiny_mce/tiny_mce.js"));
            }
            $view->append('gcms', array('isAdmin' => true), true);
        } else {
            $view->append('gcms', array('isAdmin' => false), true);
        }
        $view->setBasePath($theme_path);
        $view->addBasePath(APPLICATION_PATH . '/../public/themes/default');
        $view->setHelperPath(APPLICATION_PATH . '/views/helpers', 'My_View_Helper');
        $view->addPluginDir(APPLICATION_PATH . '/views/plugins');
        $view->append('gcms', array('version' => GCMS_VERSION), true);
        $view->append('gcms', array('config' => $cfg), true);
        Zend_Registry::set('view', $view);

        // setup viewRenderer with suffix and view
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        //$viewRenderer->setViewBasePathSpec($theme_path);
        //$viewRenderer->setViewBasePathSpec($cfg['sitetheme']);
        $viewRenderer->setViewScriptPathSpec(':module/:controller/:action.:suffix');
        $viewRenderer->setViewSuffix('tpl');
        $viewRenderer->setView($view);

        // ensure we have layout bootstraped
        $this->bootstrap('layout');
        $layout = Zend_Layout::getMvcInstance();
        // set the tpl suffix to layout also
        $layout->setViewSuffix('tpl');
        $layout->setLayoutPath($theme_path);

        // build our navigation
        $this->pageBuilder($view);
        $view->navigation()->menu()->setUlClass('nav nav-list');

        return $view;
    }
}

