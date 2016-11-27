<sub>![](https://github.com/sant0ro/Nova/raw/master/docs/Beta.gif) ![](https://github.com/sant0ro/Nova/raw/master/docs/Rat.png)</sub>
------------------------------------------
<sup>The External HH Engine, Client & API</sup><br/>
[![Github All Releases](https://img.shields.io/github/downloads/sant0ro/habclient/total.svg)]() [![license](https://img.shields.io/github/license/mashape/apistatus.svg)]() [![Build Status](https://travis-ci.org/sant0ro/habclient.svg?branch=master)](https://travis-ci.org/sant0ro/habclient)

<hr>
![](https://github.com/sant0ro/Nova/raw/master/Welcome.png)

<h2>About Nova</h2>

<b>What is Nova</b><br/>
Nova it's an External HH Client made in Node.js with Electron, it's also an Engine and also an API. Nova allows you to play any Retro Server with the same Client, without opening browser and downloading anything. Without needing to authenticate again. Nova works in any OS. Nova it's for you.

<b>What are the advantages of Nova?</b><br/>
Nova uses 2FA (Two-Factor-Authentication) to Login into the Hotel. After the first Login, your account is saved in the Nova Repository in your PC. Nova works with Flash Hotels (Habbo Beta+, R38+, R63{A,B,C+}) and in the future also with Shockwave Clients.

Nova is secure, fast, neaty and responsive. Allowing you to see how many users are online, logout, read the hotel news and many more functionalities. You can play any hotel that you regularly plays.* Nova is convenient. Nova it's the future.

__* The Hotel need to use Nova__

<h2>How to use Nova Development Version</h2>

<h3>I want to configure Nova</h3>

* Download latests builds of Nova Engine and HabClient App in Releases Section
* Configure the Nova Engine Settings on `client.php`
* You can run a test environment of Nova by starting PHP Built-In server (explained above)
* Package the HabClient App
* Open the client (Engine) `http://yoursite.com:8080/client.php`
* Copy the Token Hash
* Open Nova App
* Enter your hotel URI (eg.: `http://yoursite.com:8080`)
* Enter the Generated Token, and press the Button.
* Client will load instantly.

_Observation.:_ Be sure that your Emulator is running! And that the External_Variables are configured correctly.

<h3>I want to play HabClient with a Development Server that is deployed</h3>

* Open the client (Engine) `http://yoursite.com:8080/client.php`
* Copy the Token Hash
* Open Nova App
* Enter your hotel URI (eg.: `http://yoursite.com:8080`)
* Enter the Generated Token, and press the Button.
* Client will load instantly.

<h2>How to build Nova Engine? (Development)</h2>

<h3>For Nova Engine</h3>

* First you need clone or Download a ZIP of this repository.
* Open your console and Build Nova by entering this on your console:

```bash
cd engine/
php build.php
```

* After that you can run Nova API by entering this on your console:

```bash
cd engine/
php -S 0.0.0.0:8080 ./
```

* Nova will be running at port 8080, you can access it by http://localhost/client.php

_Observation.:_ Remember that the Nova Engine works directly from the client.php

<h3>For Nova Electron App</h3>

* You need Have Installed `NPM` Package Manager
* After Installing it, open your console and enter this:

```bash
npm install -g electron
npm install -g electron-packager
npm install -g electron-menus
npm install -g electron-json-storage
npm install -g --save-dev electron-winstaller
```

* Now Build for your Platform:

<h4>Darwin (OS X)</h4>

```bash
electron-packager app/ Nova --version 1.4.7 --platform darwin --out output/ --icon app/icon.icns
```

<h4>Windows (x64)</h4>

```bash
electron-packager app/ Nova --version 1.4.7 --platform win32 --out output/ --icon app/icon.ico
```

<h4>Linux</h4>

```bash
electron-packager app/ Nova --version 1.4.7 --platform linux --out output/
```

* Nova was builded successfully ;) (Multi Platform)

<h2>How to Install Nova? (Production)</h2>

<b>I'm a Player, how i install Nova?</b><br/>
You only need download the Nova directly from the Releases Pages of this Repository, or directly from the Retro Server that you play. 

_Observation.:_ We remember you that you need Java JRE to run HabClient. Java JRE it's the same mechanism used for playing Minecraft.

<b>I'm an owner/developer of a Retro Server, how i use Nova?</b><br/>
See our guide in the Wiki Page, by clicking here. All documentation about Installation, and Customization can be find in the Wiki Page.

You also can see the API Documentation by clicking here.

<h2>Considerations about Flash</h2>

**Nova** actually uses PepperFlash, since Electron emulates a Chrome Container. Actually i'm researching to enable NPAPI Plugins, like Adobe Shockwave (Adobe Flash v18.0) (Netscape API). Since the Pepper Flash (Adobe Flash v24.0+) (Pepper API) excludes the Adobe Shockwave Directory and obviously the Adobe Shockwave Flash.

This will be the biggest **blocking step** in this project. Winning this phase, will literally make the project working for Shockwave.

Anyways, i'm actually gathering the PepperFlash Plugin Files {.plugin, .dll, .so} for their respective SO's, Other mission will be updating continuously the Flash versions.
