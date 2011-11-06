<h1>Hello</h1>


    <?php slot('leftcol') ?>
    <?php if ($sf_user->isAuthenticated()) { ?>

        Hello <?php  echo($sf_user->getGuardUser()->getUsername()); ?>

    <?php } else { ?>
        <?php include_component('sfGuardAuth', 'signin_form'); ?>
    <?php } ?>   
    <?php end_slot() ?>