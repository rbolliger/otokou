<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>



<div class="sf_admin_form">

    <?php echo $form->renderFormTag(url_for('@report_create')) ?>

    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
        <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

<!--    <fieldset id="sf_fieldset_none">
        
        
        <?php //echo $form; ?>

        <?php //echo include_partial('report/form_content', array('form' => $form)); ?>

    </fieldset>-->
    
    <fieldset>
        
        <?php echo $form['name']->renderRow(); ?>
        <?php echo $form['vehicles_list']->renderRow(); ?>
    </fieldset>
    
    <fieldset class="sf_fieldset_group">
        <legend>Ranges definition</legend>
        <div class="fieldset_help">Define here the range (distance, date or a mix of both) limiting the scope of the report.</div>
        
        <?php echo $form['date_range']->renderRow(); ?>
        <?php echo $form['kilometers_range']->renderRow(); ?>
    </fieldset>

    <ul class="sf_admin_actions">
        <li class="sf_admin_action_create"><input type="submit" value="Create" /></li>
    </ul>

</form>

</div>
