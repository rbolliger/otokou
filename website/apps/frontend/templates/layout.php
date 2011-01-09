<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <?php include_http_metas() ?>
        <?php include_metas() ?>
        <?php include_title() ?>
        <link rel="shortcut icon" href="/favicon.ico" />
        <?php include_stylesheets() ?>
        <?php include_javascripts() ?>
    </head>


    <body>
        <div id="header">
            <?php include_partial('global/topmenu'); ?>
        </div>

        <div class="colmask threecol">
            <div class="colmid">
                <div class="colleft">
                    <div class="col1">
                        <?php if ($sf_user->hasFlash('notice')): ?>
                            <div class="flash_notice">
                                <?php echo $sf_user->getFlash('notice') ?>
                            </div>
                        <?php endif ?>

                        <?php if ($sf_user->hasFlash('error')): ?>
                            <div class="flash_error">
                                <?php echo $sf_user->getFlash('error') ?>
                            </div>
                        <?php endif ?>


                        <?php echo $sf_content ?>
                    </div>
                    <div class="col2">
                        <?php if (has_slot('rightcol')): ?>
                            <?php include_slot('rightcol') ?>
                        <?php else: ?>
                            <h1>Right column</h1>
                        <?php endif; ?>
                    </div>
                    <div class="col3">

                        <?php if (has_slot('leftcol')): ?>
                            <?php include_slot('leftcol') ?>
                        <?php else: ?>
                            <h1>Left column</h1>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
        <div id="footer">

            <?php include_partial('global/footer'); ?>
        </div>

    </body>
</html>
