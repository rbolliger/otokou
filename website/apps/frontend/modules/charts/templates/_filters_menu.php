<?php use_helper('I18N', 'jQuery') ?>

<div id="sf_admin_actions_menu">
    <ul>
        <li id="indicator" style="display: none"></li>
        <?php if_javascript(); ?>
        <li>
            <?php
            echo jq_link_to_remote('Show/hide filters', array(
                'update' => 'filters',
                'url' => '@chart_toggle_filters_visibility',
                'method' => 'post',
                'loading' => "$('#indicator').toggle()",
                'complete' => "$('#indicator').toggle()",
            ))
            ?>

        </li>
        <?php end_if_javascript(); ?>

        <noscript>
        <li>
            <?php
            $f = new sfForm();
            echo $f->renderFormTag(url_for('@chart_toggle_filters_visibility'));
            ?>
            <input type="submit"  value="Show/hide filters" class="noscript"/></form>
        </li>
        </noscript>


        </li>
    </ul>
</div>
