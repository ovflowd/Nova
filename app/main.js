const {app, BrowserWindow, Menu} = require('electron')
const path = require('path')
const url = require('url')
const request = require('request')

// Keep a global reference of the window object, if you don't, the window will
// be closed automatically when the JavaScript object is garbage collected.
let win, flashName, shockwaveName

// Select Which Platform being Used.
switch (process.platform) {
  case 'win32':
    flashName = 'PepperFlashPlayer.dll'
    break
  case 'darwin':
    flashName = 'PepperFlashPlayer.plugin'
    break
  case 'linux':
    flashName = 'libpepflashplayer.so'
    break
}

// Load Pepper Flash (PPAPI)
app.commandLine.appendSwitch('ppapi-flash-path', path.join('assets/plugins/', flashName))

// Defined that it's preparing modules
console.log("[HabClient] Preparing Modules...");

function HabClient() {

  // HabClient Version
  const HabVersion = '0111';

  // Initialize HabClient and store it contents
  this.initHab = function() {
    console.log("[HabClient] Server Selected: " + this.getVar('ServerUri'))

    console.log("[HabClient] Selected Token: " + this.getVar('ServerToken'))

    console.log("Preparing now HabClient for selected server...")

    this.startComm();
  }

  // Start HabClient Communication
  this.startComm = function() {
    win.loadURL(this.getBase() + this.getController('Hotel') + this.getModule('Client') + this.getToken())

    console.log("[HabClient] Loading Client...")
  }

  // Load Page
  this.loadPage = function(loadPage) {
    win.loadURL(url.format({
      pathname: path.join(__dirname, loadPage),
      protocol: 'file:',
      slashes: true
    }))
  }

  // Do HabClient Request
  this.doRequest = function(RequestUri, callback) {
    request(RequestUri, function (error, response, body) {
      if (error) {
        console.log("[HabClient] Request got error. Sorry.")

        callback(false);
      } else {
        console.log("[HabClient] Request got OK.")

        callback({Response : response, Body : body});
      }
    })
  }

  // Validate HabClient Token
  this.validateToken = function(callback) {
    var validToken = false;

    this.doRequest(this.createUriWithToken('Users', 'UserAuth'), function(response) {
      if(response == false) {
        console.log("[HabClient] Token is invalid because can't contact server..")
      } else if(response.Response.headers['content-type'] == 'application/json; charset=utf-8') {
        var answer = JSON.parse(response.Body)

        if(answer.Code == '200') {
          console.log("[HabClient] Token Validated. All right.")

          global.habclient.setVar('ServerToken', answer.NewToken)

          validToken = true
        } else {
          console.log("[HabClient] Token is invalid. Sorry.")
        }
      } else {
        console.log("[HabClient] Token is invalid. Sorry.")
      }

      callback(validToken);
    });
  }

  // Validate HabClient Server
  this.validateServer = function(callback) {
    var validServer = false;

    this.doRequest(this.createUriWithArguments('Engine', 'VersionCheck', '&Version=' + HabVersion), function(response) {
      if(response == false) {
        console.log("[HabClient] Server is invalid and with errors. Sorry.")
      } else if(response.Response.headers['content-type'] == 'application/json; charset=utf-8') {
        var answer = JSON.parse(response.Body)

        if(answer.Code == '200') {
          console.log("[HabClient] Server Validated. All right.")

          validServer = true
        } else {
          console.log("[HabClient] Server is outdated. Sorry.")
        }
      } else {
        console.log("[HabClient] Server is invalid. Sorry.")
      }

      callback(validServer);
    });
  }

  // HabClient Variables
  var variables = {
    ServerProtocol : "http://",
    ServerToken : "",
    ServerUri : "",
    ServerBase : "/client.php",
  }

  // HabClient Controllers
  var controllers = {
    Hotel : "?Page=Hotel",
    Users : "?Page=User",
    Engine : "?Page=Engine"
  }

  // HabClient Modules
  var modules = {
    Token : "&Token=",
    Client : "&SubPage=ShowClient",
    HotelSettings : "&SubPage=Client",
    UserAuth : "&SubPage=Login",
    HotelStatus : "&SubPage=Status",
    UserCount : "&SubPage=OnlineCount",
    VersionCheck : "&SubPage=VersionCheck"
  }

  // Create Context URL with Arguments
  this.createUriWithArguments = function(ServerController, ServerModule, Arguments) {
    var URL = this.getBase() + this.getController(ServerController) + this.getModule(ServerModule) + Arguments;

    console.log("[HabClient] Prepraring Context URL: " + URL)

    return URL;
  }

  // Create Context URL
  this.createUri = function(ServerController, ServerModule) {
    var URL = this.getBase() + this.getController(ServerController) + this.getModule(ServerModule);

    console.log("[HabClient] Prepraring Context URL: " + URL)

    return URL;
  }

  // Create Context URL with Token
  this.createUriWithToken = function(ServerController, ServerModule) {
    return this.createUriWithArguments(ServerController, ServerModule, this.getToken())
  }

  // Get Server Base URI + Server Base
  this.getBase = function() {
    return this.getVar('ServerProtocol') + this.getVar('ServerUri') + this.getVar('ServerBase');
  }

  // Get Server Token
  this.getToken = function() {
    return this.getModule('Token') + this.getVar('ServerToken')
  }

  // Get HabClient Variable
  this.getVar = function(VarName) {
    return variables[VarName]
  }

  // Set a Variable Value
  this.setVar = function(VarName, VarValue) {
    variables[VarName] = VarValue;
  }

  // Get HabClient Controllers
  this.getController = function(VarName) {
    return controllers[VarName]
  }

  // Get HabClient Modules
  this.getModule = function(VarName) {
    return modules[VarName]
  }
}

// Instantiates HabClient
global.habclient = new HabClient();

// Check Server Vality
function checkServer(serverName) {
  console.log("[HabClient] Starting Server Validation")

  global.habclient.setVar('ServerUri', serverName)

  global.habclient.validateServer(function (response) {
    if(response == false) {
      global.habclient.loadPage('invalid-server.html')
    } else {
      global.habclient.loadPage('token.html')
    }
  })
}

// Check Token Function
function checkToken(serverToken) {
  console.log("[HabClient] Starting Token Validation")

  global.habclient.setVar('ServerToken', serverToken)

  global.habclient.validateToken(function (response) {
    if(response == false) {
      global.habclient.loadPage('invalid-token.html')
    } else {
      global.habclient.initHab();
    }
  })
}

// Go Back to Home Page
function goBack() {
  win.loadURL(url.format({
    pathname: path.join(__dirname, 'index.html'),
    protocol: 'file:',
    slashes: true
  }))
}

// Exports HabClient checkServer Method
exports.checkServer = checkServer;

// Exports HabClient goBack Method
exports.goBack = goBack;

// Exports HabClient checkToken Method
exports.checkToken = checkToken;

// Create Context Window
function createWindow () {
  win = new BrowserWindow({
    width: 900,
    height: 700,
    center: true,
    webPreferences: {
        plugins: true,
        allowRunningInsecureContent: true
    }})

  console.log("[HabClient] Launching Context Window...")

  win.loadURL(url.format({
    pathname: path.join(__dirname, 'index.html'),
    protocol: 'file:',
    slashes: true
  }))

  console.log("[HabClient] Ready!")

  // Emitted when the window is closed.
  win.on('closed', () => {
    // Dereference the window object, usually you would store windows
    // in an array if your app supports multi windows, this is the time
    // when you should delete the corresponding element.
    win = null

    console.log("[HabClient] Bye.")
  })

  // Create the Application's main menu
    var template = [{
        label: "HabClient",
        submenu: [
            { label: "About Application", selector: "orderFrontStandardAboutPanel:" },
            { type: "separator" },
            { label: "Quit", accelerator: "Command+Q", click: function() { app.quit(); }}
        ]}, {
        label: "Edit",
        submenu: [
            { label: "Undo", accelerator: "CmdOrCtrl+Z", selector: "undo:" },
            { label: "Redo", accelerator: "Shift+CmdOrCtrl+Z", selector: "redo:" },
            { type: "separator" },
            { label: "Cut", accelerator: "CmdOrCtrl+X", selector: "cut:" },
            { label: "Copy", accelerator: "CmdOrCtrl+C", selector: "copy:" },
            { label: "Paste", accelerator: "CmdOrCtrl+V", selector: "paste:" },
            { label: "Select All", accelerator: "CmdOrCtrl+A", selector: "selectAll:" }
        ]}
    ];

    Menu.setApplicationMenu(Menu.buildFromTemplate(template));
}

// This method will be called when Electron has finished
// initialization and is ready to create browser windows.
// Some APIs can only be used after this event occurs.
app.on('ready', createWindow)

// Quit when all windows are closed.
app.on('window-all-closed', () => {
  // On macOS it is common for applications and their menu bar
  // to stay active until the user quits explicitly with Cmd + Q
  if (process.platform !== 'darwin') {
    app.quit()
  }
})

app.on('activate', () => {
  // On macOS it's common to re-create a window in the app when the
  // dock icon is clicked and there are no other windows open.
  if (win === null) {
    createWindow()
  }
})
