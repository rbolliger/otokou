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
