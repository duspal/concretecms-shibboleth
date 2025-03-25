<?php 

namespace Concrete\Package\Shibboleth;

use AssetList;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Logging\Logger;
use Concrete\Core\Package\Package;
use Events;
use Exception;
use Job;
use Route;

class Controller extends Package
{
    protected $pkgHandle = 'shibboleth';
    protected $appVersionRequired = '5.7.4';
    protected $pkgVersion = '0.2.0';

    protected $shibLogger;

    public function getPackageName()
    {
        return t('Simple Shibboleth Authentication for ConcreteCMS');
    }

    public function getPackageDescription()
    {
        return t('Provides Shibboleth user authentication when mod_shib is installed on your hosting server.');
    }

    public function install()
    {
        // check requirements
        if (version_compare(PHP_VERSION, '5.4.0', 'lt')) {
            throw new Exception(t('Installation requires PHP 5.4 or greater. %s is currently installed.', PHP_VERSION));
        }

        $pkg = parent::install();

        $this->installAuthenticationTypes($pkg);
        $this->setDefaultSettings($pkg->getConfig());
    }

    public function upgrade()
    {
        parent::upgrade();

        $this->installAuthenticationTypes($this);
        $this->setDefaultSettings($this->getConfig());
    }

    protected function installAuthenticationTypes($pkg)
    {
        try {
            AuthenticationType::getByHandle('shibboleth');
        } catch (\Exception $e) {
            $at = AuthenticationType::add('shibboleth', 'Shibboleth', 0, $pkg);
            $at->disable();
        }
    }

    /**
     * @param \Concrete\Core\Config\Repository\Liaison $config
     */
    protected function setDefaultSettings($config)
    {
        if ($config->get('sync.authentication_url') === null) {
            $config->save('sync.authentication_url', "");
        }

	if ($config->get('sync.return_call_url') === null) {
            $config->save('sync.return_call_url', "");
        }

        if ($config->get('debug.log_level') === null) {
            $config->save('debug.log_level', Logger::WARNING);
        }
    }

    public function on_start()
    {
        $this->registerAssets();
        $this->registerEvents();
    }

    protected function registerAssets()
    {
    }

    protected function registerEvents()
    {
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        if (!$this->shibLogger) {
            $level = $this->getConfig()->get('debug.log_level', Logger::WARNING);
	    $this->shibLogger = new Logger('shibboleth');
        }

        return $this->shibLogger;
    }
}
