<form action="<?php echo url_for('charge_collection', array('action' => 'maxPerPage')) ?>" method="post">
    <div class="max_per_page">
        <?php
        echo $pager->form->renderGlobalErrors();
        echo $pager->form->renderHiddenFields();


        //echo 'Show';

        echo $pager->form['max_per_page']->render(array('onchange' => 'this.form.submit()'));
        echo $pager->form['max_per_page']->renderLabel('Items per page');
        echo $pager->form['max_per_page']->renderError();
        ?>
        <noscript><input type="submit" value="Submit" /></noscript>
    </div>
</form>


