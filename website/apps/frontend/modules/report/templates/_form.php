<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>



<div class="sf_admin_form">

    <?php echo $form->renderFormTag(url_for('@report_create')) ?>

    <?php echo $form->renderHiddenFields(false) ?>

    <?php if ($form->hasGlobalErrors()): ?>
        <?php echo $form->renderGlobalErrors() ?>
    <?php endif; ?>

    <fieldset id="sf_fieldset_none">

        <?php foreach ($form as $name => $field): ?>
            <?php
            if ($field->getWidget()->isHidden()) {
                continue;
            }
            ?>
            <?php
            include_partial('report/form_field', array(
                'name' => $name,
                'attributes' => $field->getWidget()->getAttributes(),
                'label' => $field->getWidget()->getLabel(),
                'help' => $field->getParent()->getWidget()->getHelp($name),
                'form' => $form,
                'field' => $field,
                'class' => 'sf_admin_form_row sf_admin_' . strtolower($field->getWidget()->getOption('type')) . ' sf_admin_form_field_' . $name,
            ))
            ?>
        <?php endforeach; ?>

    </fieldset>

    <ul class="sf_admin_actions">
        <li class="sf_admin_action_create"><input type="submit" value="Create" /></li>
    </ul>

</form>

</div>
