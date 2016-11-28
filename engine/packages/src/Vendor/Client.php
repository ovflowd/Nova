<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->name; ?> - Play</title>
    <style>
        <?= \Hab\Core\HabTemplate::getVendor('CSS/Client.css') ?>
    </style>
    <?= \Hab\Core\HabTemplate::includeVendor('CSS/News.php') ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/swfobject/2.2/swfobject.min.js"></script>
    <script type="text/javascript">
        var BaseUrl = "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->path; ?><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->gordon->base; ?>";

        var flashvars =
        {
            "client.starting": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->custom->loading; ?>",
            "client.allow.cross.domain": "1",
            "client.notify.cross.domain": "0",
            "connection.info.host": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->emulator->ip; ?>",
            "connection.info.port": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->emulator->port; ?>",
            "site.url": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->base; ?>",
            "url.prefix": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->base; ?>",
            "client.reload.url": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->base; ?>client.php?Page=Hotel&SubPage=ShowClient&Token=<?= \Hab\Core\HabEngine::getInstance()->getTokenAuth(); ?>",
            "client.fatal.error.url": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->base; ?>client.php?Page=Hotel&SubPage=ShowClient&Token=<?= \Hab\Core\HabEngine::getInstance()->getTokenAuth(); ?>",
            "logout.url": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->base; ?>logout.php",
            "logout.disconnect.url": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->base; ?>client.php?Page=Hotel&SubPage=ShowClient&Token=<?= \Hab\Core\HabEngine::getInstance()->getTokenAuth(); ?>",
            "client.connection.failed.url": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->base; ?>client.php?Page=Hotel&SubPage=ShowClient&Token=<?= \Hab\Core\HabEngine::getInstance()->getTokenAuth(); ?>",
            "external.variables.txt": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->path; ?><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->gamedata->variables; ?>",
            "external.texts.txt": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->path; ?><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->gamedata->texts; ?>",
            "productdata.load.url": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->path; ?><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->gamedata->productdata; ?>",
            "furnidata.load.url": "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->path; ?><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->gamedata->furnidata; ?>",
            "use.sso.ticket": "1",
            "sso.ticket": "<?= \Hab\Core\HabEngine::getInstance()->getTokenAuth(); ?>",
            "processlog.enabled": "0",
            "flash.client.origin": "popup"
        };

        var params =
        {
            "base": BaseUrl,
            "allowScriptAccess": "always",
            "menu": "false"
        };

        swfobject.embedSWF(BaseUrl + "<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->swf->gordon->flash ?>", "client", "100%", "100%", "10.0.0", "", flashvars, params, null);
    </script>
</head>
<body>
<object type="application/x-shockwave-flash" width="100%" height="100%" id="client"></object>
<div class="top-bar">
    <img src="<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->custom->small_logo ?>">
    <div class="logout">
        <p id="logoutUser">Logout</p>
        <p id="openArticles">Articles</p>
    </div>
    <div class="online">
        <p><?= \Hab\Database\DatabaseQueries::getHotelStatus()->{\Hab\Core\HabEngine::getInstance()->getEngineSettings()->tables->serverColumns->onlineCount} ?>
            Users
            Online</p>
    </div>
</div>
<div id="news-habblet-container" style="position:fixed; display: none;">
    <div class="title">
        <div class="habblet-close"></div>
        <div>Latest News of <?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->name ?></div>
    </div>
    <div class="content-container">
        <div id="news-articles">
            <ul id="news-articlelist" class="articlelist">
                <?php

                $tableItems = \Hab\Core\HabEngine::getInstance()->getEngineSettings()->tables->newsColumns;

                foreach (\Hab\Database\DatabaseQueries::getHotelArticles() as $articleItem):

                    $oddEven = ($articleItem->id % 2 == 0) ? 'even' : 'odd';

                    echo '<li class="' . $oddEven . '">
					
					  <div class="news-title">' . $articleItem->{$tableItems->articleTitle} . '</div>
					  <div class="news-summary">' . $articleItem->{$tableItems->articleContent} . '</div>
					  <div class="newsitem-date">' . $articleItem->{$tableItems->articleDate} . '</div>

					  <div class="clearfix">
					  
						<a target="_blank" class="article-toggle">Read on site</a>
						
					  </div>
					  
					</li>';
                endforeach;

                ?>
            </ul>
        </div>
    </div>
    <div class="news-footer"></div>
</div>
<div id="client-ui">
    <div id="flash-wrapper">
        <div id="flash-container">
            <div id="content" style="width: 400px; margin: 20px auto 0 auto; display: none">
                <div class="cbb clearfix">
                    <h2 class="title">Update your Flash Player to Latest Version..</h2>
                    <div class="box-content">
                        <p>You can install and download Adobe Flash Player here: <a
                                href="http://get.adobe.com/flashplayer/">Install flash player</a>. More instructions for
                            installation can be found here: <a
                                href="http://www.adobe.com/products/flashplayer/productinfo/instructions/">More
                                information</a></p>
                    </div>
                </div>
            </div>
            <noscript>
                &lt;div style="width: 400px; margin: 20px auto 0 auto; text-align: center"&gt;
                &lt;p&gt;If you are not automatically redirected, please &lt;a href="/client/nojs"&gt;click here&lt;/a&gt;&lt;/p&gt;
                &lt;/div&gt;
            </noscript>
        </div>
    </div>
    <div id="content" class="client-content">
    </div>
</div>
<script
    src="https://code.jquery.com/jquery-1.12.4.min.js"
    integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
    crossorigin="anonymous">
</script>
<script>
    try {
        $ = jQuery = module.exports;
        // If you want module.exports to be empty, uncomment:
        // module.exports = {};
    } catch (e) {
    }
</script>
<script
    src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"
    integrity="sha256-xNjb53/rY+WmG+4L6tTl9m6PpqknWZvRt0rO1SRnJzw="
    crossorigin="anonymous">
</script>
<script type="text/javascript">
    $.noConflict();

    var habblet = jQuery('#news-habblet-container');

    habblet.hide();

    jQuery('#openArticles').on('click', function () {
        habblet.show();
        habblet.draggable();
    });

    jQuery('.habblet-close').on('click', function () {
        habblet.hide();
    });

    // Require Electron Module
    var electron = require('electron');

    // Require NovaApp
    var NovaApp = electron.remote.require('./main').NovaApp;

    // Require Language Manager
    var getLang = electron.remote.require('./main').getLang;

    // Require Remote Client Manager
    var remoteClient = electron.remote.require('./main').remoteClient;

    // Listen to Logout Item
    var logoutUser = document.getElementById('logoutUser');

    // Create a Listener for the OnClick on the Submit Button
    logoutUser.addEventListener('click', function () {
        remoteClient('logOut')
    });
</script>
</body>
</html>
