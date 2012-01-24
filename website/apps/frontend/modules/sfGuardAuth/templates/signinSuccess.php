<?php use_helper('I18N') ?>

<?php slot('content_title') ?>
<h1><?php echo __('Signin', null, 'sf_guard') ?></h1>
<?php end_slot(); ?>


<div class="bigbox">
    <p>The page you are requesting requires a valid account.</p>

<div class="signin_form">
    <?php echo get_partial('sfGuardAuth/signin_form', array('form' => $form)) ?>
</div>

</div>