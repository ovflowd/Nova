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
            <h1><span><img src="<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->custom->logo; ?>"/></span>
            </h1>
        </div>
        <div id="process-content">
            <?= \Hab\Core\HabUpdater::renderUpdates(); ?>
            <div class="fireman">
                <h1>Let's play it!</h1>
                <p>
                    Ready to play <b><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->name; ?></b>?
                    <br/>
                    <br/>
                    <b><?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->name; ?></b> uses 2FA
                    (Two-Factor-Authentication)
                    to Authenticate with Nova Core.
                </p>
                <?php if (isset($_GET['newOne']) && $_GET['newOne'] == true): ?>
                    <p>
                        Okay! We generated a new 2FA Token for you. Remember, we recommend to you to store or save
                        this Token in some place and don't forget it.
                        <br/>
                        <br/>
                        <b>Here is your Token:</b><br/>
                        <input type="text" value="<?= \Hab\Core\TokenManager::getInstance()->createToken(); ?>"
                               class="text-input" title="Here is your Token"/>
                    </p>
                <?php elseif (\Hab\Core\TokenManager::getInstance()->checkToken()): ?>
                    <p>
                        <br/>
                        It's seem that you already have generated a 2FA Token.
                        If you want to generate a new one, <a href="client.php?newOne=true">click here</a>.
                        <br/>
                        <br/>
                        <b>Here is your Token:</b><br/>
                        <input type="text" value="<?= \Hab\Core\TokenManager::getInstance()->getToken(); ?>"
                               class="text-input" title="Here is your Token"/>
                    </p>
                <?php else: ?>
                    <p>
                        It's seems you didn't generated a 2FA Token before. Here it's the Generated Token.
                        Keep it safely and remember, you can generate other anytime.
                        <br/>
                        <br/>
                        <b>Here is your Token:</b><br/>
                        <input type="text" value="<?= \Hab\Core\TokenManager::getInstance()->createToken(); ?>"
                               class="text-input" title="Here is your Token"/>
                    </p>
                <?php endif; ?>
            </div>
            <div class="tweet-container">
                <h2>What's going on?</h2>
                <div class="tweet">
                    <?= \Hab\Core\MessageManager::getInstance()->getMessages(); ?>
                </div>
            </div>
            <div id="footer">
                <p class="copyright">&copy; 2016
                    - <?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->hotel->name; ?><br/>
                    <b>Developed by Claudio Santoro [Nova v<?= ENGINE_VERSION ?>]</b></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>