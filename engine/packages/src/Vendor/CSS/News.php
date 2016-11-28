<style>
    #news-habblet-container {
        width: 397px;
        margin: 0;
    }

    #news-habblet-container .title {
        height: 90px;
        background: url(<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->custom->news_folder ?>news_top.png) no-repeat;

    }

    #news-habblet-container .title div {
        position: relative;
        width: 250px;
        top: 48px;
        left: 78px;
        text-align: center;
        font-weight: bold;
    }

    #news-habblet-container .title .habblet-close {
        position: relative;
        top: 24px;
        left: 365px;
        cursor: pointer;
        width: 15px;
        height: 15px;
        background: url(<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->custom->news_folder ?>close_0.png) no-repeat;
    }

    #news-habblet-container .title .habblet-close:hover {
        background: url(<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->custom->news_folder ?>close_1.png) no-repeat;
    }

    #news-habblet-container #news-ad {
        padding: 10px 0 10px 42px;
    }

    #news-habblet-container .content-container {
        background: url(<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->custom->news_folder ?>news_mid.png) repeat-y;
    }

    #news-habblet-container #news-articles {
        width: 383px;
        max-height: 410px;
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 11px;
        padding-left: 12px;
        overflow: auto;
    }

    #news-habblet-container .news-footer {
        height: 7px;
        width: 397px;
        background: url(<?= \Hab\Core\HabEngine::getInstance()->getApiSettings()->custom->news_folder ?>news_btm.png) no-repeat;
    }

    #news-habblet-container #news-articles .newsitem-date {
        color: #888;
        font-size: 10px;
        margin-top: 2px;
    }

    #news-articles .news-title {
        font-size: 14px;
        font-weight: bold;
    }

    #news-articles p {
        padding-top: 5px;
    }

    #news-ad .ad-message {
        text-align: left;
        font-size: 9px;
        font-style: italic;
        font-weight: bold;
        padding-top: 8px;
    }

    #news-articles ul.articlelist li {
        padding: 8px 20px;
    }

    #news-articles ul.articlelist li.even {
        background-color: #ecece6;
    }
</style>