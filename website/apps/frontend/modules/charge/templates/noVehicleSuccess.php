<?php use_helper('I18N', 'Date') ?>


<div id="sf_admin_container">

    <?php slot('content_title'); ?>
    <h1><?php echo __('Add a new vehicle', array(), 'messages') ?></h1>
    <?php end_slot(); ?>

    <div id="sf_admin_content">

        <div class="bigbox">
            It seems that you didn't define a vehicle. In order to add charges, you have to define at least one active vehicle.</br> 
            Please fill the following form to define a new vehicle. After that, you will be redirected to the charge registration form.
        </div>


        <?php include_partial('charge/flashes') ?>



        <div class="sf_admin_form"> 


            <?php echo $form->renderFormTag(url_for('@charge_add_vehicle')) ?>
            <?php echo $form->renderHiddenFields(false) ?>

            <?php if ($form->hasGlobalErrors()): ?>
                <?php echo $form->renderGlobalErrors() ?>
            <?php endif; ?>

            <fieldset>
                <?php echo $form ?>

            </fieldset>
            <ul class="sf_admin_actions">
                <li class="sf_admin_action_create"><input type="submit" value="Save" /></li>
            </ul>

            </form>
        </div>

    </div>


</div>
