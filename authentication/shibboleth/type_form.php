<?php 
defined('C5_EXECUTE') or die('Access denied.');

/**
 * @var array $levelSelector
 * @var \Concrete\Core\Form\Service\Form $form
 */

$config = Package::getByHandle('shibboleth')->getConfig();
?>

<fieldset>
    <legend><?php echo  t('Shibboleth Settings') ?></legend>
    <div class="form-group">
        <?php echo  $form->label('authentication_url', t('Authentication Url')) ?>
        <?php echo  $form->text('authentication_url', $config->get('sync.authentication_url')) ?>
        <p class="help-block"><?php echo  t('If Shibboleth session not activated, the url to which ConcreteCMS will' .
                ' redirect you to activate your Shibboleth session.') ?></p>
        <br/>
        <?php echo  $form->label('return_url', t('Return Url')) ?>
        <?php echo  $form->text('return_url', $config->get('sync.return_url')) ?>
        <p class="help-block"><?php echo  t('The Url that Shibboleth will return its call from, if different from above. ' .
                ' Leave blank if both the authentication Url and return request Url are the same.') ?></p>  
    </div>
</fieldset>

<fieldset>
    <legend><?php echo  t('Debug Settings') ?></legend>
    <div class="form-group">
        <?php echo  $form->label('log_level', t('Log Level')) ?>
        <?php echo  $form->select(
            'log_level',
            $levelSelector,
            (int) $config->get('debug.log_level')
        ) ?>
        <p class="help-block"><?php echo  t('The minimum level of detail to be logged to the concrete5 log for the "shibboleth"'
            . ' channel.') ?></p>
    </div>
</fieldset>
