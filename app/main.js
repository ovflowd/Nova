const {
  app,
  BrowserWindow,
  Menu
} = require('electron')
const path = require('path')
const url = require('url')
const request = require('request')
const storage = require('electron-json-storage')
const fs = require('fs')
const ini = require('ini')

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
novaLog("Preparing Modules...")

// Instantiates NovaApp
global.NovaApp = new NovaApp();

// Nova Class
function NovaApp() {

  novaLog("Loading Nova Core..")

  // NovaApp Version
  const HabVersion = '012'

  // Language File
  const Language = 'english'

  // Last Server Updates
  var lastUpdatesHTML = ''

  // Server Data
  var serverData, localServerList, systemLanguages

  // NovaApp Variables
  var variables = {
    ServerProtocol: "http://",
    ServerToken: "",
    ServerUri: "",
    ServerBase: "/client.php",
  }

  var errorVars = {
    Title: "",
    Message: ""
  }

  // NovaApp Controllers
  var controllers = {
    Hotel: "?Page=Hotel",
    Users: "?Page=User",
    Engine: "?Page=Engine"
  }

  // NovaApp Modules
  var modules = {
    Token: "&Token=",
    Client: "&SubPage=ShowClient",
    HotelSettings: "&SubPage=Client",
    UserAuth: "&SubPage=Login",
    HotelStatus: "&SubPage=Status",
    UserCount: "&SubPage=OnlineCount",
    VersionCheck: "&SubPage=VersionCheck"
  }

  // Initialize NovaApp and store it contents
  this.initHab = function(needAddServer) {
    novaLangLog("prepareLaunch")

    novaLangLogArgs("selectedServer", this.getVar('ServerUri'))
    novaLangLogArgs("selectedToken", this.getVar('ServerToken'))

    // Retrieve Server Data
    if (needAddServer == true) {
      this.Requests().retrieveServer(function(response) {
        if (response == true) {
          novaLangLog("gatheredServer")

          global.NovaApp.Server().addServer(global.NovaApp.getData('base'))

          global.NovaApp.startComm();
        } else {
          novaLangLog("failedRetrieveServer")
        }
      })
    } else {
      this.startComm();
    }
  }

  // Start NovaApp Communication
  this.startComm = function() {
    this.Requests().validateAuth(function(response) {
      if (response == true) {
        // Load Hotel Client from NovaEngine
        win.loadURL(global.NovaApp.Uri().createUriWithToken('Hotel', 'Client'))

        novaLangLog("loadClient")
      } else {
        global.NovaApp.Load().loadError(getLang("errors", "failedAuthTitle"), getLang("errors", "failedAuthText"))
      }
    })
  }

  // Language Functions
  var languages = function(NovaApp) {
    this.loadLanguages = function() {
      NovaApp.setLanguages(ini.parse(fs.readFileSync(path.join(__dirname + '/languages/', Language + '.ini'), 'utf-8')));
    }

    // Return Language Section
    this.getLanguageSection = function(langVar) {
      return NovaApp.getLanguages()[langVar]
    }

    // Get Language Var
    this.getLanguageVar = function(section, langVar) {
      return getLanguageSection(section)[langVar]
    }

    return this
  }

  // URI Functions
  var uri = function(NovaApp) {
    // Create Context URL with Arguments
    this.createUriWithArguments = function(ServerController, ServerModule, Arguments) {
      var URL = NovaApp.getBase() + NovaApp.getController(ServerController) + NovaApp.getModule(ServerModule) + Arguments

      novaLangLogArgs("prepareContextModule", URL)

      return URL
    }

    // Create Context URL
    this.createUri = function(ServerController, ServerModule) {
      var URL = NovaApp.getBase() + NovaApp.getController(ServerController) + NovaApp.getModule(ServerModule)

      novaLangLogArgs("prepareContextModule", URL)

      return URL
    }

    // Create Context URL with Token
    this.createUriWithToken = function(ServerController, ServerModule) {
      return createUriWithArguments(ServerController, ServerModule, NovaApp.getToken())
    }

    return this
  }

  // Load Functions
  var load = function(NovaApp) {
    // Load Page Function
    this.loadPage = function(loadPage) {
      win.loadURL(url.format({
        pathname: path.join(__dirname, loadPage),
        protocol: 'file:',
        slashes: true
      }))

      novaLangLogArgs("loadingPage", loadPage)
    }

    // Load Error Page Function
    this.loadError = function(title, message) {
      win.loadURL(url.format({
        pathname: path.join(__dirname, 'error.html'),
        protocol: 'file:',
        slashes: true
      }))

      novaLangLog("loadingError")

      NovaApp.setError(title, message)
    }

    return this
  }

  // Server Functions
  var server = function(NovaApp) {
    // Check if Exists ServerList
    this.checkServers = function(callback) {
      storage.has('serverList', function(error, hasKey) {
        callback(hasKey)
      })
    }

    // Get Server List
    this.getServers = function(callback) {
      storage.get('serverList', function(error, data) {
        callback(data)
      })
    }

    // Update Server List Data
    this.storeServers = function(serverListData) {
      storage.set('serverList', serverListData, function(error) {
        global.NovaApp.setLocalServerList(serverListData)

        novaLangLog("serverListUpdated")
      });
    }

    // Erase the Server List if exists Data Corruption
    this.clearServers = function() {
      getServers(function(response) {
        if (response == '{}' || response == 'undefined') {
          storage.clear(function(error) {
            novaLangLog("serverListCleared")
          });
        }
      })
    }

    // Check If an Specific Server Exists
    this.checkServerExistence = function(serverName, callback) {
      getServers(function(serverList) {
        callback(serverList[serverName] != 'undefined')
      });
    }

    // Remove Server from the Server List
    this.removeServer = function(serverName) {
      checkServerExistence(serverName, function(response) {
        global.NovaApp.Server().getServers(function(serverList) {
          delete serverList[serverName]

          global.NovaApp.Server().storeServers(serverList)
        })
      })
    }

    // Add Server or Update Server to the Server List
    this.addServer = function(serverName) {
      var server = {
        base: NovaApp.getVar('ServerUri'),
        name: NovaApp.getData('name'),
        token: NovaApp.getVar('ServerToken')
      }

      checkServers(function(response) {
        if (response == true) {
          global.NovaApp.Server().checkServerExistence(serverName, function(response) {
            if (response == true) {
              global.NovaApp.Server().getServers(function(serverList) {
                serverList[serverName] = server

                global.NovaApp.Server().storeServers(serverList)
              })
            } else {
              global.NovaApp.Server().getServers(function(serverList) {
                serverList[serverName] = server

                global.NovaApp.Server().storeServers(serverList)
              })
            }
          })
        } else {
          var serverList = {}

          serverList[serverName] = server

          global.NovaApp.Server().storeServers(serverList)
        }
      })
    }

    // Retrieve Server Data
    this.getServer = function(serverName, callback) {
      checkServerExistence(serverName, function(response) {
        if (response == true) {
          global.NovaApp.Server().getServers(function(serverList) {
            callback(serverList[serverName])
          })
        } else {
          callback({})
        }
      })
    }

    return this
  }

  // Requests Functions
  var requests = function(NovaApp) {
    // Do NovaApp Request
    this.doRequest = function(RequestUri, callback) {
      request(RequestUri, function(error, response, body) {
        if (error) {
          novaLangLog("requestError")

          callback(false)
        } else {
          novaLangLog("requestOK")

          callback({
            Response: response,
            Body: body
          })
        }
      })
    }

    // Check if we're ready to load Server
    this.validateAuth = function(callback) {
      var serverLoaded = false

      doRequest(NovaApp.Uri().createUriWithToken('Hotel', 'Client'), function(response) {
        if (response == false) {
          novaLangLog("serverInvalid")
        } else if (response.Response.headers['content-type'] == 'application/json; charset=utf-8') {
          var answer = JSON.parse(response.Body)

          if (answer.Code == '403') {
            novaLangLog("serverInvalidToken")
          } else {
            novaLangLog("serverOtherAnswer")
          }
        } else if (response.Response.headers['content-type'] == 'text/html; charset=UTF-8') {
          novaLangLog("serverAuthOK")

          serverLoaded = true
        } else {
          novaLangLog("serverWTF")
        }

        callback(serverLoaded)
      })
    }

    // Validate NovaApp Token
    this.validateToken = function(callback) {
      var validToken = false

      doRequest(NovaApp.Uri().createUriWithToken('Users', 'UserAuth'), function(response) {
        if (response == false) {
          novaLangLog("serverTokenWTF")
        } else if (response.Response.headers['content-type'] == 'application/json; charset=utf-8') {
          var answer = JSON.parse(response.Body)

          if (answer.Code == '200') {
            novaLangLog("serverTokenOK")

            global.NovaApp.setVar('ServerToken', answer.NewToken)

            validToken = true
          } else {
            novaLangLog("serverInvalidToken")
          }
        } else {
          novaLangLog("serverInvalidToken")
        }

        callback(validToken)
      })

      return this
    }

    // Validate NovaApp Server
    this.validateServer = function(callback) {
      var validServer = false

      doRequest(NovaApp.Uri().createUriWithArguments('Engine', 'VersionCheck', '&Version=' + HabVersion), function(response) {
        if (response == false) {
          console.log("[NovaApp] Server is invalid and with errors. Sorry.")
        } else if (response.Response.headers['content-type'] == 'application/json; charset=utf-8') {
          var answer = JSON.parse(response.Body)

          if (answer.Code == '200') {
            novaLangLog("serverOK")

            validServer = true
          } else {
            novaLangLog("serverInvalid")
          }
        } else {
          novaLangLog("serverInvalid")
        }

        callback(validServer)
      })
    }

    // Retrieve Server Data
    this.retrieveServer = function(callback) {
      var success = false

      doRequest(NovaApp.Uri().createUriWithToken('Hotel', 'HotelSettings'), function(response) {
        if (response == false) {
          novaLangLog("serverNON")
        } else if (response.Response.headers['content-type'] == 'application/json; charset=utf-8') {
          var answer = JSON.parse(response.Body)

          if (answer.Code == '200') {
            novaLangLog("serverGathOK")

            global.NovaApp.setVar('ServerToken', answer.NewToken)
            global.NovaApp.setData(answer.Client.hotel)

            success = true
          } else {
            novaLangLog("serverInvalid")
          }
        } else {
          novaLangLog("serverInvalid")
        }

        callback(success)
      })
    }

    return this
  }

  // Renderes Functions
  var renderers = function(NovaApp) {
    // Get Rendered Server List
    this.renderServerList = function(callback) {
      NovaApp.Server().getServers(function(response) {
        var serversHTML = ''

        serversHTML = '<ul class="tweet_list">'

        var even = 1

        for (var key in response) {
          even++

          serversHTML += (even % 2 == 0) ? '<li class="tweet_even">' : '<li>'

          serversHTML += '<b>' + response[key].name + '</b> (#' + (key + 1) + ')<br/>'

          serversHTML += '<p>Access the Hotel by clicking <a onclick="openServer(\'' + response[key].base + '\', \'' + response[key].token + '\');">here</a><p>'

          serversHTML += '</li>'
        }

        serversHTML += '</ul>'

        callback(serversHTML)
      })
    }

    // Set Server Updates
    this.renderNewsHTML = function(callback) {
      var updatesHTML = ''

      NovaApp.Requests().doRequest('https://raw.githubusercontent.com/sant0ro/Nova/master/tweets.json', function(response) {
        if (response == false) {
          console.log("[NovaApp] Can't communicate with Nova Repository..")
        } else {
          var answer = JSON.parse(response.Body)

          updatesHTML = '<ul class="tweet_list">'

          var even = 0

          answer.tweets.forEach(function(entry) {
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

    return this
  }

  this.Languages = function() {
    return languages(this)
  }

  this.Uri = function() {
    return uri(this)
  }

  this.Load = function() {
    return load(this)
  }

  this.Server = function() {
    return server(this)
  }

  this.Requests = function() {
    return requests(this)
  }

  this.Renderers = function() {
    return renderers(this)
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
    errorVars["Title"] = title, errorVars["Message"] = message
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

  // Get Languages
  this.getLanguages = function() {
    return systemLanguages
  }

  // Set Languages
  this.setLanguages = function(languageFiles) {
    systemLanguages = languageFiles
  }
}

// Log Something
function novaLog(content) {
  console.log("[NovaApp] " + content)
}

// Log Something from Language Variables
function novaLangLog(langVariable) {
  novaLog(getLang("logs", langVariable))
}

function novaLangLogArgs(langVariable, logArgs) {
  novaLog(getLang("logs", langVariable) + logArgs)
}

// Get Language Variable
function getLang(section, langVar) {
  return global.NovaApp.Languages().getLanguageVar(section, langVar)
}

// Go to Main Page
function addNewServer() {
  global.NovaApp.Load().loadPage('index.html')
}

// Remote Client Functions
function remoteClient(itemName) {
  switch(itemName) {
    case 'logOut':
      app.quit()
    break;
  }
}

// Exports NovaApp
exports.NovaApp = global.NovaApp

// Exports Language Manager
exports.getLang = getLang

// Exports Nova Log Manager
exports.novaLog = novaLog

// Remote Client
exports.remoteClient = remoteClient

// Create Context Window
function createWindow() {
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

  novaLog("Loading Language System...")

  // Load Languages
  global.NovaApp.Languages().loadLanguages();

  novaLangLog("startupNews")

  // Set Server Updates
  global.NovaApp.Renderers().renderNewsHTML(function(response) {
    global.NovaApp.setUpdates(response);

    setTimeout(function() {
      novaLangLog("startupContextWindow")

      win = new BrowserWindow({
        width: 900,
        height: 700,
        center: true,
        webPreferences: {
          plugins: true,
          allowRunningInsecureContent: true
        }
      })

      novaLangLog("startupLaunchContextWindow")

      // Check Data Corruption
      global.NovaApp.Server().clearServers()

      // Only Uncomment for Development
      // storage.clear(function(error) {
      //    if (error)
      //     throw error;
      //   }
      // );

      // Load Main File
      global.NovaApp.Server().checkServers(function(response) {
        global.NovaApp.Load().loadPage(response == true ? 'servers.html' : 'index.html')
      });

      // Close Loading Window
      winLoading.close()

      novaLangLog("startupReady")

      // Emitted when the window is closed.
      win.on('closed', () => {
        // Dereference the window object, usually you would store windows
        // in an array if your app supports multi windows, this is the time
        // when you should delete the corresponding element.
        win = null

        novaLangLog("startupBye")

        app.quit()
      })
    }, 1000);

    // Emitted when the window is closed.
    winLoading.on('closed', () => {
      // Dereference the window object, usually you would store windows
      // in an array if your app supports multi windows, this is the time
      // when you should delete the corresponding element.
      winLoading = null

      novaLangLog("startupLoadingOK")
    })
  });

  // Create the Application's main menu
  var template = [{
    label: "NovaApp",
    submenu: [{
      label: "About Application",
      selector: "orderFrontStandardAboutPanel:"
    }, {
      type: "separator"
    }, {
      label: "Quit",
      accelerator: "Command+Q",
      click: function() {
        app.quit();
      }
    }]
  }, {
    label: "Edit",
    submenu: [{
      label: "Undo",
      accelerator: "CmdOrCtrl+Z",
      selector: "undo:"
    }, {
      label: "Redo",
      accelerator: "Shift+CmdOrCtrl+Z",
      selector: "redo:"
    }, {
      type: "separator"
    }, {
      label: "Cut",
      accelerator: "CmdOrCtrl+X",
      selector: "cut:"
    }, {
      label: "Copy",
      accelerator: "CmdOrCtrl+C",
      selector: "copy:"
    }, {
      label: "Paste",
      accelerator: "CmdOrCtrl+V",
      selector: "paste:"
    }, {
      label: "Select All",
      accelerator: "CmdOrCtrl+A",
      selector: "selectAll:"
    }]
  }];

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
