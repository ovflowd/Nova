<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>
        <?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->name; ?> - Client
    </title>
    <style>
        <?= \Hab\Core\HabTemplate::getVendor('CSS/Home.css') ?>
    </style>
</head>
<body>

<div id="container">
    <div id="content">
        <div id="header" style="margin-bottom:20px;" class="clearfix">
            <h1><span><img src="https://imgur.com/yWgYZ8n.gif"/></span></h1>
        </div>
        <div id="process-content">
            <div class="fireman">
                <h1>Let's play it!</h1>
                <p>
                    Ready to play <b><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->name; ?></b>?
                    <br/>
                    <br/>
                    So let's click on <i>Enter Client</i> button, and play the Hotel by using <b>HabClient!</b>
                </p>
                <p>
                    <br/>
                    <a class="button-blue" href="<?= \Hab\Core\HabUtils::generateExternal(); ?>">Enter Client!</a>
                </p>
            </div>
            <div class="tweet-container">
                <h2>What's going on?</h2>
                <div class="tweet">
                    <ul class="tweet_list">
                        <li>
                            <b><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->name; ?></b>
                            Uses HabClient, the future of Retro Servers Clients. <br/>
                            With <b>HabClient</b> you can play easily any hotel, without the needing of any browser.
                        </li>
                        <li class="tweet_even">
                            Does you already know how <b>HabClient</b> work? So click in the Enter Client button.<br/>
                            <b>Does not?</b> Download latest version of HabClient by clicking <a>here</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="footer">
                <p class="copyright">&copy; 2016
                    - <?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->name; ?><br/>
                    <b>Developed by Claudio Santoro [HabClient]</b></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>