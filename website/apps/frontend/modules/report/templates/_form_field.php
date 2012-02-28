
<div class="<?php echo $class ?><?php $form[$name]->hasError() and print ' errors' ?>">
    <?php echo $form[$name]->renderError() ?>
    <div>
        <?php echo $form[$name]->renderLabel($label) ?>

        <div class="content"><?php echo $form[$name]->render($attributes instanceof sfOutputEscaper ? $attributes->getRawValue() : $attributes) ?></div>

        <?php if ($help): ?>
            <div class="help"><?php echo __($help, array(), 'messages') ?></div>
        <?php elseif ($help = $form[$name]->renderHelp()): ?>
            <div class="help"><?php echo $help ?></div>
        <?php endif; ?>
    </div>
</div>

