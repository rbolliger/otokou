<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>



<div class="sf_admin_form">

    <?php echo $form->renderFormTag(url_for('@report_create')) ?>

    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
        <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <fieldset id="sf_fieldset_none">
        
        
        <?php echo $form; ?>

        <?php //echo include_partial('report/form_content', array('form' => $form)); ?>

    </fieldset>

    <ul class="sf_admin_actions">
        <li class="sf_admin_action_create"><input type="submit" value="Create" /></li>
    </ul>

</form>

</div>
