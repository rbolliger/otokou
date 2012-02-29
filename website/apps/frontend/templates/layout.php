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
            <link rel="stylesheet" type="text/css" media="screen" href="/css/../sfZurbFoundationPlugin/css/ie.css">
    <![endif]-->

        <!-- IE Fix for HTML5 Tags -->
        <!--[if lt IE 9]>
                <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->


        <?php include_javascripts() ?>
    </head>


    <body>

        <div id="content">

            <div id="header" class="container">
                <div class="row">
                    <?php include_partial('global/topmenu'); ?>
                </div>
            </div>

            <div id="title" class="container">
                <div class="row">
                    <?php include_partial('global/title'); ?>
                </div>
            </div>

            <div id="main" class="container">
                <div class="row">
                    
                    <?php if (has_slot('content_title')): ?>
                        <div class="ten columns offset-by-two content_title">
                            <?php include_slot('content_title') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (has_slot('leftcol')): ?>
                        <div class="three  columns">
                            <?php include_slot('leftcol') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (has_slot('leftcol')): ?>
                        <div class="eight columns">
                        <?php else: ?>
                            <div class="eight columns centered">
                            <?php endif; ?>
                                
                            <?php echo $sf_content ?>
                        </div>
                    </div>
                </div>

                <div class="push"></div>
            </div>


            <div id="footer" class="container">
                <div class="row">
                    <?php include_partial('global/footer'); ?>
                </div>
            </div>

    </body>
</html>
