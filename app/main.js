const {app, BrowserWindow, Menu} = require('electron')
const path = require('path')
const url = require('url')
const request = require('request')

// Keep a global reference of the window object, if you don't, the window will
// be closed automatically when the JavaScript object is garbage collected.
let win, flashName, shockwaveName, winLoading

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
console.log("[NovaApp] Preparing Modules...");

function NovaApp() {

  // NovaApp Version
  const HabVersion = '0111';

  // Initialize NovaApp and store it contents
  this.initHab = function() {
    console.log("[NovaApp] Server Selected: " + this.getVar('ServerUri'))

    console.log("[NovaApp] Selected Token: " + this.getVar('ServerToken'))

    console.log("Preparing now NovaApp for selected server...")

    this.startComm();
  }

  // Start NovaApp Communication
  this.startComm = function() {
    win.loadURL(this.getBase() + this.getController('Hotel') + this.getModule('Client') + this.getToken())

    console.log("[NovaApp] Loading Client...")
  }

  // Load Page
  this.loadPage = function(loadPage) {
    win.loadURL(url.format({
      pathname: path.join(__dirname, loadPage),
      protocol: 'file:',
      slashes: true
    }))

    console.log('[NovaApp] Loadin Page: ' + loadPage)
  }

  this.loadError = function(Title, Message) {
    win.loadURL(url.format({
      pathname: path.join(__dirname, 'error.html'),
      protocol: 'file:',
      slashes: true
    }))

    console.log('[NovaApp] Loading Error Page...')

    this.setError(Title, Message)
  }

  // Do NovaApp Request
  this.doRequest = function(RequestUri, callback) {
    request(RequestUri, function (error, response, body) {
      if (error) {
        console.log("[NovaApp] Request got error. Sorry.")

        callback(false);
      } else {
        console.log("[NovaApp] Request got OK.")

        callback({Response : response, Body : body});
      }
    })
  }

  // Validate NovaApp Token
  this.validateToken = function(callback) {
    var validToken = false;

    this.doRequest(this.createUriWithToken('Users', 'UserAuth'), function(response) {
      if(response == false) {
        console.log("[NovaApp] Token is invalid because can't contact server..")
      } else if(response.Response.headers['content-type'] == 'application/json; charset=utf-8') {
        var answer = JSON.parse(response.Body)

        if(answer.Code == '200') {
          console.log("[NovaApp] Token Validated. All right.")

          global.NovaApp.setVar('ServerToken', answer.NewToken)

          validToken = true
        } else {
          console.log("[NovaApp] Token is invalid. Sorry.")
        }
      } else {
        console.log("[NovaApp] Token is invalid. Sorry.")
      }

      callback(validToken);
    });
  }

  // Validate NovaApp Server
  this.validateServer = function(callback) {
    var validServer = false;

    this.doRequest(this.createUriWithArguments('Engine', 'VersionCheck', '&Version=' + HabVersion), function(response) {
      if(response == false) {
        console.log("[NovaApp] Server is invalid and with errors. Sorry.")
      } else if(response.Response.headers['content-type'] == 'application/json; charset=utf-8') {
        var answer = JSON.parse(response.Body)

        if(answer.Code == '200') {
          console.log("[NovaApp] Server Validated. All right.")

          validServer = true
        } else {
          console.log("[NovaApp] Server is outdated. Sorry.")
        }
      } else {
        console.log("[NovaApp] Server is invalid. Sorry.")
      }

      callback(validServer);
    });
  }

  // Set Server Updates
  this.retrieveUpdates = function(callback) {
    var updatesHTML = '';

    this.doRequest('https://raw.githubusercontent.com/sant0ro/Nova/master/SERVER_MESSAGES.json', function(response) {
      if(response == false) {
        console.log("[NovaApp] Can't communicate with Nova Repository..")
      } else {
        var answer = JSON.parse(response.Body)

        updatesHTML = '<ul class="tweet_list">';

        var even = 0;

        answer.messages.forEach(function(entry) {
          even++;

          updatesHTML += (even % 2 == 0) ? '<li class="tweet_even">' : '<li>';

          updatesHTML += entry.message;

          updatesHTML += '</li>';
        });

        updatesHTML += '</ul>';
      }

      callback(updatesHTML);
    });
  }

  // Last Server Updates
  var lastUpdatesHTML = '';

  // NovaApp Variables
  var variables = {
    ServerProtocol : "http://",
    ServerToken : "",
    ServerUri : "",
    ServerBase : "/client.php",
  }

  var errorVars = {
    Title : "Error!",
    Message : "System got Error..."
  }

  // NovaApp Controllers
  var controllers = {
    Hotel : "?Page=Hotel",
    Users : "?Page=User",
    Engine : "?Page=Engine"
  }

  // NovaApp Modules
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

    console.log("[NovaApp] Prepraring Context URL: " + URL)

    return URL;
  }

  // Create Context URL
  this.createUri = function(ServerController, ServerModule) {
    var URL = this.getBase() + this.getController(ServerController) + this.getModule(ServerModule);

    console.log("[NovaApp] Prepraring Context URL: " + URL)

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

  // Get NovaApp Variable
  this.getVar = function(VarName) {
    return variables[VarName]
  }

  // Set a Variable Value
  this.setVar = function(VarName, VarValue) {
    variables[VarName] = VarValue;
  }

  // Get NovaApp Controllers
  this.getController = function(VarName) {
    return controllers[VarName]
  }

  // Get NovaApp Modules
  this.getModule = function(VarName) {
    return modules[VarName]
  }

  // Get Error Message
  this.getError = function() {
    return errorVars;
  }

  // Set the Error Message
  this.setError = function(Title, Message) {
    errorVars["Title"] = Title;
    errorVars["Message"] = Message;
  }

  this.getUpdates = function() {
    return lastUpdatesHTML;
  }

  this.setUpdates = function(updatesHTML) {
    lastUpdatesHTML = updatesHTML;
  }
}

// Instantiates NovaApp
global.NovaApp = new NovaApp();

// Check Server Vality
function checkServer(serverName) {
  console.log("[NovaApp] Starting Server Validation")

  global.NovaApp.setVar('ServerUri', serverName)

  global.NovaApp.validateServer(function (response) {
    if(response == false) {
      //global.NovaApp.loadPage('invalid-server.html')
      global.NovaApp.loadError('Invalid Server!', "This isn't a valid HabClient server. Be sure that you typed correctly the server url.")
    } else {
      global.NovaApp.loadPage('token.html')
    }
  })
}

// Check Token Function
function checkToken(serverToken) {
  console.log("[NovaApp] Starting Token Validation")

  global.NovaApp.setVar('ServerToken', serverToken)

  global.NovaApp.validateToken(function (response) {
    if(response == false) {
      global.NovaApp.loadError('Invalid Token!', "Sorry, but the authentication result with the response that you SSO Token is invalid. Please check if the Token is valid, or if you copied it correctly.")
    } else {
      global.NovaApp.initHab();
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

// Get getError Message
function getError() {
  return global.NovaApp.getError();
}

// Get Last News HTML
function getUpdates() {
  return global.NovaApp.getUpdates();
}

// Exports NovaApp checkServer Method
exports.checkServer = checkServer;

// Exports NovaApp goBack Method
exports.goBack = goBack;

// Exports NovaApp errorMessage Method
exports.getError = getError;

// Exports NovaApp checkToken Method
exports.checkToken = checkToken;

// Exports NovaApp getUpdates Method
exports.getUpdates = getUpdates;

// Create Context Window
function createWindow () {
  winLoading = new BrowserWindow({
    width: 420,
    height: 320,
    center: true,
    resizable: false,
    frame: false,
    minimizable: false,
    maximizable: false,
    movable: false,
  })

  // Load Main File
  winLoading.loadURL(url.format({
    pathname: path.join(__dirname, 'loading.html'),
    protocol: 'file:',
    slashes: true
  }))

  console.log("[NovaApp] Loading Last News...")

  // Set Server Updates
  global.NovaApp.retrieveUpdates(function (response) {
    global.NovaApp.setUpdates(response);

    setTimeout(function() {
      console.log("[NovaApp] Creating Context Window...")

      win = new BrowserWindow({
        width: 900,
        height: 700,
        center: true,
        webPreferences: {
          plugins: true,
          allowRunningInsecureContent: true
        }
      })

      console.log("[NovaApp] Launching Context Window...")

      // Load Main File
      win.loadURL(url.format({
        pathname: path.join(__dirname, 'index.html'),
        protocol: 'file:',
        slashes: true
      }))

      console.log("[NovaApp] Ready!")

      // Emitted when the window is closed.
      win.on('closed', () => {
        // Dereference the window object, usually you would store windows
        // in an array if your app supports multi windows, this is the time
        // when you should delete the corresponding element.
        win = null

        console.log("[NovaApp] Bye.")
      })
    }, 3000);

    // Emitted when the window is closed.
    winLoading.on('closed', () => {
      // Dereference the window object, usually you would store windows
      // in an array if your app supports multi windows, this is the time
      // when you should delete the corresponding element.
      winLoading = null

      console.log("[NovaApp] Loading OK.")
    })
  });


  // Create the Application's main menu
    var template = [{
        label: "NovaApp",
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
