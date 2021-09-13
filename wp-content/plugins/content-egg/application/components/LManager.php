<?php

namespace ContentEgg\application\components;

defined('\ABSPATH') || exit;

use ContentEgg\application\Plugin;
use ContentEgg\application\admin\LicConfig;

/**
 * LManager class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2021 keywordrush.com
 */
class LManager {

    const CACHE_TTL = 86400;

    private $data = null;
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new self;

        return self::$instance;
    }

    public function adminInit()
    {
        \add_action('admin_notices', array($this, 'displayNotice'));
        $this->hideNotice();
    }

    public function getData($force = false)
    {
        if (!LicConfig::getInstance()->option('license_key'))
            return array();

        if (!$force && $this->data !== null)
            return $this->data;

        $this->data = $this->getCache();
        if ($this->data === false || $force)
        {

            $data = $this->remoteRetrieve();
            if (!$data || !is_array($data))
                $data = array();

            $this->data = $data;
            $this->saveCache($this->data);
        }

        return $this->data;
    }

    public function remoteRetrieve()
    {
        if (!$response = Plugin::apiRequest(array('method' => 'POST', 'timeout' => 10, 'body' => $this->getRequestArray('license'))))
            return false;

        if (!$result = json_decode(\wp_remote_retrieve_body($response), true))
            return false;

        return $result;
    }

    public function saveCache($data)
    {
        \set_transient(Plugin::getShortSlug() . '_' . 'ldata', $data, self::CACHE_TTL);
    }

    public function getCache()
    {
        return \get_transient(Plugin::getShortSlug() . '_' . 'ldata');
    }

    public function deleteCache()
    {
        \delete_transient(Plugin::getShortSlug() . '_' . 'ldata');
    }

    private function getRequestArray($cmd)
    {
        return array('cmd' => $cmd, 'd' => parse_url(\site_url(), PHP_URL_HOST), 'p' => Plugin::product_id, 'v' => Plugin::version(), 'key' => LicConfig::getInstance()->option('license_key'));
    }

    public function isConfigPage()
    {
        if ($GLOBALS['pagenow'] == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'content-egg-lic')
            return true;
        else
            return false;
    }

    public function displayNotice()
    {
        if (!$data = LManager::getInstance()->getData())
            return;

        if ($data['activated_on'] && $data['activated_on'] != preg_replace('/^www\./', '', strtolower(parse_url(\site_url(), PHP_URL_HOST))))
        {
            $this->displayLicenseMismatchNotice();
            return;
        }

        if (time() >= $data['expiry_date'])
        {
            $this->displayExpiredNotice($data);
            return;
        }

        $days_left = floor(($data['expiry_date'] - time()) / 3600 / 24);
        if ($days_left >= 0 && $days_left <= 21)
        {
            $this->displayExpiresSoonNotice($data);
            return;
        }

        if ($this->isConfigPage())
        {
            $this->displayActiveNotice($data);
            return;
        }
    }

    public function displayActiveNotice(array $data)
    {
        $this->addInlineCss();
        $purchase_uri = '/product/purchase/1017';
        $days_left = floor(($data['expiry_date'] - time()) / 3600 / 24);

        echo '<div class="notice notice-success egg-notice"><p>';
        echo sprintf(__('License status: <span class="egg-label egg-label-%s">%s</span>.', 'content-egg'), strtolower($data['status']), strtoupper($data['status']));
        if ($data['status'] == 'active')
            echo ' ' . __('You are receiving automatic updates.', 'content-egg');
        echo '<br />' . sprintf(__('Expires at %s (%d days left).', 'content-egg'), gmdate('F d, Y H:i', $data['expiry_date']) . ' GMT', $days_left);
        echo '</p>';
        echo '<p>';
        $this->displayCheckAgainButton();

        echo ' ' . sprintf('<a class="button-primary" target="_blank" href="%s">%s</a>', Plugin::website . '/login?return=' . urlencode($purchase_uri), "&#10003; " . __('Extend now', 'content-egg'));
        if ((int) $data['extend_discount'])
            echo ' <small>' . sprintf(__('with a %d%% discount', 'content-egg'), $data['extend_discount']) . '</small>';

        echo '</p></div>';
    }

    public function displayExpiresSoonNotice(array $data)
    {
        if (\get_transient('cegg_hide_notice_lic_expires_soon') && !$this->isConfigPage())
            return;
        
        $this->addInlineCss();        
        $purchase_uri = '/product/purchase/1017';
        $days_left = floor(($data['expiry_date'] - time()) / 3600 / 24);
        echo '<div class="notice notice-warning egg-notice">';
        echo '<p>';
        if (!$this->isConfigPage())
        {
            $hide_notice_uri = \add_query_arg(array('cegg_hide_notice' => 'lic_expires_soon', '_cegg_notice_nonce' => \wp_create_nonce('hide_notice')), $_SERVER['REQUEST_URI']);
            echo '<a href="' . $hide_notice_uri . '" class="egg-notice-close notice-dismiss">' . __('Dismiss', 'content-egg') . '</a>';
        }
        echo '<strong>' . __('License expires soon', 'content-egg') . '</strong><br />';
        echo sprintf(__('Your %s license expires at %s (%d days left).', 'content-egg'), Plugin::getName(), gmdate('F d, Y H:i', $data['expiry_date']) . ' GMT', $days_left);
        echo ' ' . __('You will not receive automatic updates, bug fixes, and technical support.', 'content-egg');
        echo '</p>';
        echo '<p>';
        $this->displayCheckAgainButton();
        echo ' ' . sprintf('<a class="button-primary" target="_blank" href="%s">%s</a>', Plugin::website . '/login?return=' . urlencode($purchase_uri), "&#10003; " . __('Extend now', 'content-egg'));
        if ((int) $data['extend_discount'])
            echo ' <span class="egg-label egg-label-success">' . sprintf(__('with a %d%% discount', 'content-egg'), $data['extend_discount']) . '</span>';
        echo '</p>';
        echo '</div>';
    }

    public function displayExpiredNotice(array $data)
    {
        if (\get_transient('cegg_hide_notice_lic_expired') && !$this->isConfigPage())
            return;        
        
        $this->addInlineCss();        
        $purchase_uri = '/product/purchase/1017';
        echo '<div class="notice notice-error egg-notice">';
        echo '<p>';
        
        if (!$this->isConfigPage())
        {
            $hide_notice_uri = \add_query_arg(array('cegg_hide_notice' => 'lic_expired', '_cegg_notice_nonce' => \wp_create_nonce('hide_notice')), $_SERVER['REQUEST_URI']);
            echo '<a href="' . $hide_notice_uri . '" class="egg-notice-close notice-dismiss">' . __('Dismiss', 'content-egg') . '</a>';
        }
        
        echo '<strong>' . __('License expired', 'content-egg') . '</strong><br />';
        echo sprintf(__('Your %s license expired on %s.', 'content-egg'), Plugin::getName(), gmdate('F d, Y H:i', $data['expiry_date']) . ' GMT');
        echo ' ' . __('You are not receiving automatic updates, bug fixes, and technical support.', 'content-egg');
        echo '</p>';
        echo '<p>';
        $this->displayCheckAgainButton();
        echo ' ' . sprintf('<a class="button-primary" target="_blank" href="%s">%s</a>', Plugin::website . '/login?return=' . urlencode($purchase_uri), "&#10003; " . __('Renew now', 'content-egg'));
        echo '</p></div>';
    }

    public function displayLicenseMismatchNotice()
    {
        $this->addInlineCss();        
        echo '<div class="notice notice-error egg-notice"><p>';
        echo '<img src=" ' . \ContentEgg\PLUGIN_RES . '/img/logo.png' . '" width="40" />';
        echo '<strong>' . __('License mismatch', 'content-egg') . '</strong><br />';
        echo sprintf(__("Your %s license doesn't match your current domain.", 'content-egg'), Plugin::getName());
        echo ' ' . sprintf(__('If you wish to continue using the plugin then you must <a target="_blank" href="%s">revoke</a> the license and then <a href="%s">reactivate</a> it again or <a target="_blank" href="%s">buy a new license</a>.', 'content-egg'), Plugin::panelUri, \get_admin_url(\get_current_blog_id(), 'admin.php?page=content-egg-lic'), 'https://www.keywordrush.com/contentegg/pricing');
        echo '</p></div>';
    }

    public function displayCheckAgainButton()
    {
        echo '<form style="display: inline;" action=" ' . \get_admin_url(\get_current_blog_id(), 'admin.php?page=content-egg-lic') . '" method="POST">';
        echo '<input type="hidden" name="cegg_cmd" id="cegg_cmd" value="refresh" />';
        echo '<input type="hidden" name="nonce_refresh" value="' . \wp_create_nonce('license_refresh') . '"/>';
        echo '<input type="submit" name="submit3" id="submit3" class="button" value="&#8635; ' . __('Check again', 'content-egg') . '" />';
        echo '</form>';
    }

    public function hideNotice()
    {
        if (!isset($_GET['cegg_hide_notice']))
            return;

        if (!isset($_GET['_cegg_notice_nonce']) || !\wp_verify_nonce($_GET['_cegg_notice_nonce'], 'hide_notice'))
            return;

        $notice = $_GET['cegg_hide_notice'];

        if (!in_array($notice, array('lic_expires_soon', 'lic_expired')))
            return;

        if ($notice == 'lic_expires_soon')
            $expiration = 7 * 24 * 3600;
        elseif ($notice == 'lic_expired')
            $expiration = 90 * 24 * 3600;
        else
            $expiration = 0;
        
        \set_transient('cegg_hide_notice_' . $notice, true, $expiration);

        \wp_redirect(\remove_query_arg(array('cegg_hide_notice', '_cegg_notice_nonce'), \wp_unslash($_SERVER['REQUEST_URI'])));
        exit;
    }
    
    public function addInlineCss()
    {
        echo '<style>.egg-notice a.egg-notice-close {position:static;float:right;top:0;right0;padding:0;margin-top:-20px;line-height:1.23076923;text-decoration:none;}.egg-notice a.egg-notice-close::before{position: relative;top: 18px;left: -20px;}.egg-notice img {float:left;width:40px;padding-right:12px;}</style>';
    }

}
