const {app, BrowserWindow, Menu} = require('electron')
const path = require('path')
const url = require('url')
const request = require('request')
const storage = require('electron-json-storage')

// Keep a global reference of the window object, if you don't, the window will
// be closed automatically when the JavaScript object is garbage collected.
let win, flashName, shockwaveName, winLoading

// Select desired PepperFlash for correct Platform
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

// Nova it's preparing his classes
console.log("[NovaApp] Preparing Modules...")

// Nova Class
function NovaApp() {

  // NovaApp Version
  const HabVersion = '012'

  // Initialize NovaApp and store it contents
  this.initHab = function(needAddServer) {
    console.log("[NovaApp] Preparing now NovaApp for selected server...")

    console.log("[NovaApp] Server Selected: " + global.NovaApp.getVar('ServerUri'))

    console.log("[NovaApp] Selected Token: " + global.NovaApp.getVar('ServerToken'))

    // Retrieve Server Data
    if(needAddServer == true) {
      this.retrieveServerData(function (response) {
        if(response == true) {
          console.log("[NovaApp] Gathered Server Data...")

          // Add New Server to Server List
          global.NovaApp.addServer(global.NovaApp.getData('base'))
        } else {
          console.log("[NovaApp] Failed Retrieving Server Data")
        }
      });
    }

    // Start Communication with Client
    global.NovaApp.startComm();
  }

  // Start NovaApp Communication
  this.startComm = function() {
    // Load Hotel Client from NovaEngine
    win.loadURL(this.getBase() + this.getController('Hotel') + this.getModule('Client') + this.getToken())

    console.log("[NovaApp] Loading Client...")
  }

  // Load Page Function
  this.loadPage = function(loadPage) {
    win.loadURL(url.format({
      pathname: path.join(__dirname, loadPage),
      protocol: 'file:',
      slashes: true
    }))

    console.log('[NovaApp] Loading Page: ' + loadPage)
  }

  // Load Error Page Function
  this.loadError = function(title, message) {
    win.loadURL(url.format({
      pathname: path.join(__dirname, 'error.html'),
      protocol: 'file:',
      slashes: true
    }))

    console.log('[NovaApp] Loading Error Page...')

    this.setError(title, message)
  }

  // Check if Exists ServerList
  this.checkServerList = function(callback) {
    storage.has('serverList', function(error, hasKey) {
      callback(hasKey)
    })
  }

  // Get Server List
  this.getServerList = function(callback) {
    storage.get('serverList', function(error, data) {
      callback(data)
    })
  }

  // Update Server List Data
  this.setServerList = function(serverListData) {
    storage.set('serverList', serverListData, function(error) {
      global.NovaApp.setLocalServerList(serverListData)

      console.log("[Nova] Updated Server List!")
    });
  }

  // Erase the Server List if exists Data Corruption
  this.eraseServerList = function() {
    global.NovaApp.getServerList(function (response) {
      if(response == '{}' || response == 'undefined') {
        storage.clear(function(error) {
          console.log("[NovaApp] Clearing Server List, because Data Corrupted.")
        });
      }
    })
  }

  // Check If an Specific Server Exists
  this.checkServerExistence = function(serverName, callback) {
    this.getServerList(function (serverList) {
      callback(serverList[serverName] != 'undefined')
    });
  }

  // Remove Server from the Server List
  this.removeServer = function(serverName) {
    this.checkServerExistence(serverName, function(response) {
      this.getServerList(function (serverList) {
        delete serverList[serverName]

        global.NovaApp.setServerList(serverList)
    })})
  }

  // Add Server or Update Server to the Server List
  this.addServer = function(serverName) {
    var server = {}

    server[serverName] = {
      base : global.NovaApp.getVar('ServerUri'),
      name : global.NovaApp.getData('name'),
      token : global.NovaApp.getVar('ServerToken')
    }

    this.checkServerExistence(serverName, function(response) {
      if(response == true) {
        global.NovaApp.checkServerList(function (response) {
          if(response == true) {
            serverList.push(server)

            global.NovaApp.setServerList(serverList)
          } else {
           global.NovaApp.getServerList(function (serverList) {
             var serverList = []

             serverList.push(server)

             global.NovaApp.setServerList(serverList)
           })
        }})
      } else {
        global.NovaApp.getServerList(function (serverList) {
          serverList[serverName] = server;

          global.NovaApp.setServerList(serverList)
        })
      }
    })
  }

  // Retrieve Server Data
  this.getServerData = function(serverName, callback) {
    this.checkServerExistence(serverName, function(response) {
      if(response == true) {
        global.NovaApp.getServerList(function (serverList) {
          callback(serverList[serverName])
        })
      } else {
        callback({})
      }
    })
  }

  // Do NovaApp Request
  this.doRequest = function(RequestUri, callback) {
    request(RequestUri, function (error, response, body) {
      if (error) {
        console.log("[NovaApp] Request got error. Sorry.")

        callback(false)
      } else {
        console.log("[NovaApp] Request got OK.")

        callback({Response : response, Body : body})
      }
    })
  }

  // Validate NovaApp Token
  this.validateToken = function(callback) {
    var validToken = false

    this.doRequest(this.createUriWithToken('Users', 'UserAuth'), function(response) {
      if(response == false) {
        console.log("[NovaApp] Token is invalid because can't contact server..")
      } else if(response.Response.headers['content-type'] == 'application/json; charset=utf-8') {
        var answer = JSON.parse(response.Body)

        if(answer.Code == '200') {
          console.log("[NovaApp] Token Validated. All right.")

          // Update Server Token
          global.NovaApp.setVar('ServerToken', answer.NewToken)

          validToken = true
        } else {
          console.log("[NovaApp] Token is invalid. Sorry.")
        }
      } else {
        console.log("[NovaApp] Token is invalid. Sorry.")
      }

      callback(validToken)
    })
  }

  // Validate NovaApp Server
  this.validateServer = function(callback) {
    var validServer = false

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

      callback(validServer)
    })
  }

  // Retrieve Server Data
  this.retrieveServerData = function(callback) {
    var success = false

    this.doRequest(this.createUriWithToken('Hotel', 'HotelSettings'), function(response) {
      if(response == false) {
        console.log("[NovaApp] Server is invalid and with errors. Sorry.")
      } else if(response.Response.headers['content-type'] == 'application/json; charset=utf-8') {
        var answer = JSON.parse(response.Body)

        if(answer.Code == '200') {
          console.log("[NovaApp] OK. Gathering Server Data...")

          // Update Server Token
          global.NovaApp.setVar('ServerToken', answer.NewToken)

          // Set Server Data
          global.NovaApp.setData(answer.Client.hotel)

          success = true

          callback(success)
        } else {
          console.log("[NovaApp] Server is outdated. Sorry.")
        }
      } else {
        console.log("[NovaApp] Server is invalid. Sorry.")
      }
    })
  }

  // Get Rendered Server List
  this.getRenderedServerList = function(callback) {
    this.getServerList(function (response) {
      var updatesHTML = ''

      updatesHTML = '<ul class="tweet_list">'

      var even = 1

      for(var key in response) {
        for(var subKey in response[key]) {
          even++

          updatesHTML += (even % 2 == 0) ? '<li class="tweet_even">' : '<li>'

          updatesHTML += '<b>' + response[key][subKey].name + '</b> (#' + (key + 1) + ')<br/>'

          updatesHTML += '<p>Access the Hotel by clicking <a onclick="openServer(\'' + response[key][subKey].base + '\', \'' + response[key][subKey].token + '\');">here</a><p>'

          updatesHTML += '</li>'
        }
      }

      updatesHTML += '</ul>'

      callback(updatesHTML)
    })
  }

  // Set Server Updates
  this.retrieveUpdates = function(callback) {
    var updatesHTML = ''

    this.doRequest('https://raw.githubusercontent.com/sant0ro/Nova/master/SERVER_MESSAGES.json', function(response) {
      if(response == false) {
        console.log("[NovaApp] Can't communicate with Nova Repository..")
      } else {
        var answer = JSON.parse(response.Body)

        updatesHTML = '<ul class="tweet_list">'

        var even = 0

        answer.messages.forEach(function(entry) {
          even++

          updatesHTML += (even % 2 == 0) ? '<li class="tweet_even">' : '<li>'

          updatesHTML += entry.message

          updatesHTML += '</li>'
        });

        updatesHTML += '</ul>'
      }

      callback(updatesHTML)
    });
  }

  // Last Server Updates
  var lastUpdatesHTML = ''

  // Server Data
  var serverData, localServerList

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
    var URL = this.getBase() + this.getController(ServerController) + this.getModule(ServerModule) + Arguments

    console.log("[NovaApp] Prepraring Context URL: " + URL)

    return URL
  }

  // Create Context URL
  this.createUri = function(ServerController, ServerModule) {
    var URL = this.getBase() + this.getController(ServerController) + this.getModule(ServerModule)

    console.log("[NovaApp] Prepraring Context URL: " + URL)

    return URL
  }

  // Create Context URL with Token
  this.createUriWithToken = function(ServerController, ServerModule) {
    return this.createUriWithArguments(ServerController, ServerModule, this.getToken())
  }

  // Get Server Base URI + Server Base
  this.getBase = function() {
    return this.getVar('ServerProtocol') + this.getVar('ServerUri') + this.getVar('ServerBase')
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
    variables[VarName] = VarValue
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
    return errorVars
  }

  // Set the Error Message
  this.setError = function(title, message) {
    errorVars["Title"] = title
    errorVars["Message"] = message
  }

  // Get App Latest News
  this.getUpdates = function() {
    return lastUpdatesHTML
  }

  // Set App Latest News
  this.setUpdates = function(updatesHTML) {
    lastUpdatesHTML = updatesHTML
  }

  // Set Server Data
  this.setData = function(servData) {
    serverData = servData
  }

  // Get Server Data
  this.getData = function(servVar) {
    return serverData[servVar]
  }

  // Get Local Server List
  this.getLocalServerList = function() {
    return localServerList
  }

  // Set Local Server List
  this.setLocalServerList = function(list) {
    localServerList = list
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
      global.NovaApp.loadError('Invalid Server!', "This isn't a valid Nova server. Be sure that you typed correctly the server url.")
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
      global.NovaApp.initHab(true);
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

// Get Local Server List
function getServerList(callback) {
  global.NovaApp.getRenderedServerList(function (response) {
    callback(response)
  })
}

// Get getError Message
function getError() {
  return global.NovaApp.getError();
}

// Get Last News HTML
function getUpdates() {
  return global.NovaApp.getUpdates();
}

// Start Specific Server
function startSpecificServer(serverUri, userToken) {
  console.log("[NovaApp] Starting a Specific Server from the List... ")

  global.NovaApp.setVar('ServerUri', serverUri)

  global.NovaApp.setVar('ServerToken', userToken)

  global.NovaApp.initHab(false);
}

// Go to Main Page
function addNewServer() {
  global.NovaApp.loadPage('index.html')
}

// Got to Server List
function goToServerList(callback) {
  global.NovaApp.checkServerList(function (response) {
    if(response == true) {
      global.NovaApp.loadPage('servers.html')
    } else {
      callback(false)
    }
  })
}

// Exports addNewServer checkServer Method
exports.addNewServer = addNewServer

// Exports goToServerList checkServer Method
exports.goToServerList = goToServerList

// Exports startSpecificServer checkServer Method
exports.startSpecificServer = startSpecificServer

// Exports getServerList checkServer Method
exports.getServerList = getServerList

// Exports NovaApp checkServer Method
exports.checkServer = checkServer

// Exports NovaApp goBack Method
exports.goBack = goBack

// Exports NovaApp errorMessage Method
exports.getError = getError

// Exports NovaApp checkToken Method
exports.checkToken = checkToken

// Exports NovaApp getUpdates Method
exports.getUpdates = getUpdates

// Create Context Window
function createWindow () {
  winLoading = new BrowserWindow({
    width: 420,
    height: 320,
    center: true,
    resizable: false,
    frame: false,
    minimizable: false,
    acceptFirstMouse: false,
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

      // Check Data Corruption
      global.NovaApp.eraseServerList()

      // Only Uncomment for Development
      // storage.clear(function(error) {
      //    if (error)
      //     throw error;
      //   }
      // );

      // Load Main File
      global.NovaApp.checkServerList(function (response) {
        global.NovaApp.loadPage(response == true ? 'servers.html' : 'index.html')
      });

      // Close Loading Window
      winLoading.close()

      console.log("[NovaApp] Ready!")

      // Emitted when the window is closed.
      win.on('closed', () => {
        // Dereference the window object, usually you would store windows
        // in an array if your app supports multi windows, this is the time
        // when you should delete the corresponding element.
        win = null

        console.log("[NovaApp] Bye.")

        app.quit()
      })
    }, 1000);

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
