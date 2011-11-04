<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

    <!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
    <!--[if lt IE 7]> <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
    <!--[if IE 7]>    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
    <!--[if IE 8]>    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" class="no-js lt-ie9" lang="en"> <![endif]-->
    <!--[if gt IE 8]><!--> <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> <!--<![endif]-->


        <head>
            <?php include_http_metas() ?>
            <?php include_metas() ?>
            <?php include_title() ?>
            <link rel="shortcut icon" href="/favicon.ico" />
            <?php include_stylesheets() ?>

            <!--[if lt IE 9]>
                <link rel="stylesheet" href=".css/foundation/ie.css">
        <![endif]-->

            <!-- IE Fix for HTML5 Tags -->
            <!--[if lt IE 9]>
                    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->


            <?php include_javascripts() ?>
        </head>


        <body>
            <div id="header" class="container">
                <div class="row">
                    <?php include_partial('global/topmenu'); ?>
                </div>
            </div>

            <div class="container">
                <div class="row">

                    <div class="four columns">
                        <?php if (has_slot('leftcol')): ?>
                            <?php include_slot('leftcol') ?>
                        <?php else: ?>
                            <h3>Left column</h3>
                        <?php endif; ?>

                        <?php if (has_slot('rightcol')): ?>
                            <?php include_slot('rightcol') ?>
                        <?php else: ?>
                            <h3>Right column</h3>
                        <?php endif; ?>
                    </div>

                    <div class="eight columns">
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

                </div>
            </div>

            <div id="footer" class="container">
                <div class="row">
                    <?php include_partial('global/footer'); ?>
                </div>
            </div>

        </body>
    </html>
