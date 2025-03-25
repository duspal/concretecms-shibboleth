<?php 

namespace Concrete\Package\Shibboleth\Authentication\Shibboleth;
use Concrete\Core\Authentication\AuthenticationType;
use Concrete\Core\Authentication\AuthenticationTypeController;
use Concrete\Core\Authentication\AuthenticationTypeFailureException;
use Concrete\Package\Shibboleth\Src\Models\Directory;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\User\PostLoginLocation;
use Core;
use Package;
use User;
use UserInfo;

class Controller extends AuthenticationTypeController
{
    protected $config;

    public function __construct(AuthenticationType $type)
    {
        $this->config = Package::getByHandle('shibboleth')->getConfig();
        parent::__construct($type);
    }

    public function getAuthenticationTypeIconHTML()
    {
        return '<img class="ccm-auth-type-icon" src="' .
        h(Core::make('helper/concrete/urls')->getPackageURL(Package::getByHandle('shibboleth'))) .
        '/authentication/shibboleth/icon.png" />';
    }

    public function view()
    {
        $return = $this->config->get('sync.return_url');
	if ( empty($return) ) {
            $return = $this->config->get('sync.authentication_url');
        }
	$return = parse_url( $return );

	$_SERVER['HTTP_REFERER'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : NULL;

	$referrer = parse_url( $_SERVER['HTTP_REFERER'] );
	$referrer['scheme'] = isset($referrer['scheme']) ? $referrer['scheme'] : NULL;

        if( ($return['scheme'] == $referrer['scheme']) && ($return['host'] == $referrer['host']) && ($return['path'] == $referrer['path']) ) {
            $this->authenticate();
        }
    }

    public function getHandle()
    {
        return 'shibboleth';
    }

    public function buildHash(User $u)
    {
        return '';
    }

    public function verifyHash(User $u, $hash)
    {
        return false;
    }

    public function authenticate()
    {
        $post = $this->post();
        $package = \Package::getByHandle('shibboleth');
        $uName = isset($_SERVER["unspecified-id"]) ? $_SERVER["unspecified-id"] : NULL;
        $directory = isset($directory) ? $directory : NULL;
        $displayName = isset($directory->displayName) ? $directory->displayName : NULL;

	if (isset($post["shibMaintainLogin"])) {
            $shibMaintainLogin = $post["shibMaintainLogin"];
        } else {
            $shibMaintainLogin = $_COOKIE["CONCRETE5_SHIB_MAINTAIN_LOGIN"] ?? false;
        }

        $u = new User();
        $ui = UserInfo::getByUserName($uName);
        if ($ui != null) {
            $u = $ui->getUserObject();

            if (isset($post["shibMaintainLogin"])) {
                $shibMaintainLogin = $post["shibMaintainLogin"];
            } else {
                $shibMaintainLogin = $_COOKIE["CONCRETE5_SHIB_MAINTAIN_LOGIN"] ?? false;
            }

            if ( ($u->getUserName() == $_SERVER["unspecified-id"]) && ($ui->getUserEmail() == $_SERVER["HTTP_MAIL"]) ) {
                $package->getLogger()->info(
                    'Authenticated Successfully ' . "\n"
                    . 'Directory: ' . $displayName . "\n"
                    . 'Username: ' . $uName . "\n"
                );

                $u->loginByUserID($u->getUserID());

                if( ($shibMaintainLogin == '1') ) {
                        $options = ['expires' => time() + 1209600, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Lax'];
                } else {
                        $options = ['path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Lax'];
                }
                setcookie("CONCRETE5_LOGIN", 1, $options);

                $options = ['expires' => time() - 3600, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Lax'];
                setcookie("CONCRETE5_SHIB_MAINTAIN_LOGIN", $shibMaintainLogin, $options);

                $pll = $this->app->make(PostLoginLocation::class);
                $response = $pll->getPostLoginRedirectResponse(true);

                header("Location:" . $response->getTargetUrl() );
                exit();
            }
            $package->getLogger()->info(
                'Authentication Failure ' . "\n"
                . 'Directory: ' . $displayName . "\n"
                . 'Username: ' . $uName
            );
        }

        $package->getLogger()->info(
            'Authentication Attempt ' . "\n"
            . 'Directory: ' . $displayName . "\n"
            . 'Username: ' . $uName
        );

        $options = ['expires' => time() + 3600, 'path' => '/', 'secure' => true, 'httponly' => true, 'samesite' => 'Lax'];
        setcookie("CONCRETE5_SHIB_MAINTAIN_LOGIN", $shibMaintainLogin, $options);

        header("Location:" . $this->config->get('sync.authentication_url') );
        exit();
    }

    public function deauthenticate(User $u)
    {
    }

    public function isAuthenticated(User $u)
    {
        return $u->isLoggedIn();
    }

    public function edit()
    {
        $this->set('form', Core::make('helper/form'));

        $logLevels = \Concrete\Core\Logging\Logger::getLevels();
        $levelSelector = [];
        foreach ($logLevels as $level) {
            $levelSelector[$level] = \Concrete\Core\Logging\Logger::getLevelDisplayName($level);
        }
        $levelSelector[PHP_INT_MAX] = t('Disabled');

        $this->set('levelSelector', $levelSelector);
    }

    public function saveAuthenticationType()
    {
        $authenticationUrl = $this->post('authentication_url');
        if (empty($authenticationUrl))
        {
            $authenticationUrl = "";
        }

        $this->config->save('sync.authentication_url', $authenticationUrl);

	$returnUrl = $this->post('return_url');
        if (empty($returnUrl))
        {
            $returnUrl = "";
        }
        $this->config->save('sync.return_url', $returnUrl);

        $this->config->save('debug.log_level', $this->post('log_level'));
    }
}
