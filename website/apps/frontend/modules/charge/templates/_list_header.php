
    <form id="sf_admin_max_per_page" class="max_per_page" action="<?php echo url_for('charge_collection', array('action' => 'maxPerPage')) ?>" method="post">
            <?php
            echo $pager->form->renderGlobalErrors();
            echo $pager->form->renderHiddenFields();


            //echo 'Show';
            
            echo $pager->form['max_per_page']->render(array('onchange' => 'this.form.submit()'));
            echo $pager->form['max_per_page']->renderError();
            echo $pager->form['max_per_page']->renderLabel('Items per page');
            ?>
            <noscript><input type="submit" value="Submit" /></noscript>
    </form>


<?php //  ?>