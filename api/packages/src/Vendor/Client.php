<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->name; ?> - Play</title>
    <style>
        <?= \Hab\Core\HabTemplate::getVendor('CSS/Client.css') ?>
    </style>
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
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
</body>
</html>
