<?php use_helper('I18N') ?>

<form action="<?php echo url_for('@sf_guard_signin') ?>" method="post">
    <table>

        <tfoot>
            <tr>
                <td colspan="2">
                    <input type="submit" value="<?php echo __('Signin', null, 'sf_guard') ?>" />
                </td>
            </tr>
            <?php $routes = $sf_context->getRouting()->getRoutes() ?>
            <?php if (isset($routes['sf_guard_forgot_password'])): ?>
                <tr>
                    <td colspan="2">
                        <a href="<?php echo url_for('@sf_guard_forgot_password') ?>"><?php echo __('Forgot your password?', null, 'sf_guard') ?></a>
                    </td>
                </tr>
            <?php endif; ?>

            <?php if (isset($routes['sf_guard_register'])): ?>
                <tr>
                    <td colspan="2">
                        <a href="<?php echo url_for('@sf_guard_register') ?>"><?php echo __('Want to register?', null, 'sf_guard') ?></a>
                    </td>
                </tr>
            <?php endif; ?>
            </tr>
        </tfoot>
        <tbody>
            <?php echo $form ?>
        </tbody>
    </table>
</form>