<sub>![](http://imgur.com/yWgYZ8n.gif)</sub>
------------------------------------------
<sup>HH External Client Interface & API</sup>

<h2>About HabClient</h2>

<b>What is HabClient</b><br/>
HabClient it's an External Game Client for HH made in Java. And also an API Communication Standards in PHP for the Java App. HabClient allows you play any HHotel directly from an Universal Client made in Java. You can play it in any Operational System that supports Java. 

<b>What are the advantages of HabClient?</b><br/>
HabClient allows you to play independent of the browsers any Flash or Shockwave HH Client. HabClient was made and intented only to be used for Retro HH Servers. Since it's pre-requisite it's the API Engine in PHP.

HabClient it's more secure and convenient. You can play more feastely. For Server Administrators you have many advantages to use it.

<h2>How to use HabClient Development Version</h2>

<h3>I want to configure HabClient </h3>

* Download latests builds of HabClient Engine and HabClient App in Releases Section
* Configure the HabClient Engine Settings on `client.php`
* You can run a test environment of HabClient by starting PHP Built-In server (explained above)
* Package the HabClient App
* Open the client (Engine) `http://yoursite.com:8080/client.php`
* Copy the Token Hash
* Open HabClient App
* Enter your hotel URI (eg.: `http://yoursite.com:8080`)
* Enter the Generated Token, and press the Button.
* Client will load instantly.

_Observation.:_ Be sure that your Emulator is running! And that the External_Variables are configured correctly.

<h3>I want to play HabClient with a Development Server that is deployed</h3>

* Open the client (Engine) `http://yoursite.com:8080/client.php`
* Copy the Token Hash
* Open HabClient App
* Enter your hotel URI (eg.: `http://yoursite.com:8080`)
* Enter the Generated Token, and press the Button.
* Client will load instantly.

<h2>How to build HabClient Engine? (Development)</h2>

<h3>For HabClient Engine</h3>

* First you need clone or Download a ZIP of this repository.
* Open your console and Build HabClient by entering this on your console:

```bash
cd api/
php build.php
```

* After that you can run HabClient API by entering this on your console:

```bash
cd api/
php -S 0.0.0.0:8080 ./
```

* HabClient will be running at port 8080, you can access it by http://localhost/client.php

_Observation.:_ Remember that the HabClient Engine works directly from the client.php

<h3>For HabClient Electron App</h3>

* You need Have Installed `NPM` Package Manager
* After Installing it, open your console and enter this:

```bash
npm install -g electron
npm install -g electron-packager
npm install -g electron-menus
```

* Now Build for your Platform:

<h4>Darwin (OS X)</h4>

```bash
electron-packager client/ HabClient --version 1.4.7 --platform darwin --out output/ --icon client/icon.icns
```

<h4>Windows (x64)</h4>

```bash
electron-packager client/ HabClient --version 1.4.7 --platform win32 --out output/ --icon client/icon.ico
```

<h4>Linux</h4>

```bash
electron-packager client/ HabClient --version 1.4.7 --platform linux --out output/
```

* Your app was builded successfully ;) (Multi Platform)

<h2>How to Install HabClient? (Production)</h2>

<b>I'm a Player, how i install HabClient?</b><br/>
You only need download the HabClient directly from the Releases Pages of this Repository, or directly from the Retro Server that you play. 

_Observation.:_ We remember you that you need Java JRE to run HabClient. Java JRE it's the same mechanism used for playing Minecraft.

<b>I'm an owner/developer of a Retro Server, how i use HabClient?</b><br/>
See our guide in the Wiki Page, by clicking here. All documentation about Installation, and Customization can be find in the Wiki Page.

You also can see the API Documentation by clicking here.

<h2>Considerations about Flash</h2>

**HabClient** actualy uses PepperFlash, since Electron emulates a Chrome Container. Actually i'm researching to enable NPAPI Plugins, like Adobe Shockwave (Adobe Flash v18.0) (Netscape API). Since the Pepper Flash (Adobe Flash v24.0+) (Pepper API) excludes the Adobe Shockwave Directory and obviously the Adobe Shockwave Flash.

This will be the biggest **blocking step** in this project. Winning this phase, will literally make the project working for Shockwave.

Anyways, i'm actually gathering the PepperFlash Plugin Files {.plugin, .dll, .so} for their respective SO's, Other mission will be updating continuously the Flash versions.

<h2>How to contribute to HabClient?</h2>
Soon.

<h2>Many tahnks</h2>

Thanks for supporting HabClient.
