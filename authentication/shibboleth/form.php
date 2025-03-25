<?php 
defined('C5_EXECUTE') or die('Access denied.');
?>

<form method='post' action='<?php echo  URL::to('/login', 'authenticate', $this->getAuthenticationTypeHandle()) ?>'>

    <?php 
    $user = new User;

    if ($user->isLoggedIn()) {
        header("Location:" . '/dashboard/welcome' );
        exit();
    } else {
        ?>
        <div class="form-group">
        <span>
            <?php //echo  var_dump($rcID); ?>
            <?php echo  t('<b>Shibboleth Sign-In</b><br/>') ?>
            <?php echo  t('Click the "Log in via Shibboleth" button below to log in using your official credentials.') ?>
        </span>
            <hr>
        </div>

	<div class="form-group mb-3 row">
		<div class="col-sm-3 col-form-label pt-0">Remember Me:</div>
		<div class="col-sm-9">
            	<div class="form-check">
                	<input class="form-check-input" type="checkbox" id="shibMaintainLogin" name="shibMaintainLogin" value="1">
                	<label class="form-check-label form-check-remember-me" for="shibMaintainLogin">
                    		Stay signed in for 14 days </label>
            	</div>
		</div>
    	</div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="<?php echo  t('Log in via Shibboleth') ?>"/>
        </div>

        <?php  Core::make('token')->output('login_' . $this->getAuthenticationTypeHandle()); ?>
        <?php 
    }
    ?>
</form>
<hr>
<br/><br/><br/><br/>
 <?php echo  t('<b>ATTENTION:</b>  Only use the Username and Password field if logging into a localized ConcreteCMS account.') ?>
<style type="text/css">
    ul.auth-types > li .ccm-auth-type-icon {
        position: absolute;
        top: 2px;
        left: 0;
    }
</style>
