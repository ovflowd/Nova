const {app, BrowserWindow, Menu} = require('electron')
const path = require('path')
const url = require('url')

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
app.commandLine.appendSwitch('ppapi-flash-path', path.join(__dirname, flashName))

// Defined that it's preparing modules
console.log("[HabClient] Preparing Modules...");

function HabClient() {

  // Initialize HabClient and store it contents
  this.initHab = function(serverName, serverToken) {
    console.log("[HabClient] Server Selected: " + serverName)

    console.log("[HabClient] Selected Token: " + serverToken)

    console.log("Preparing now HabClient for selected server...")

    // Set Internal Variables
    variables.ServerToken = serverToken;
    variables.ServerUri = serverName;
  }

  // Start HabClient Communication
  this.startComm = function() {
    console.log("[HabClient] Preparing [ContextURL]: " + this.getBase() + this.getController('Hotel') + this.getModule('Client') + this.getToken())

    win.loadURL(this.getBase() + this.getController('Hotel') + this.getModule('Client') + this.getToken())

    console.log("[HabClient] Loading Client...")
  }

  // HabClient Variables
  var variables = {
    ServerToken : "",
    ServerUri : "",
    ServerBase : "/client.php",
  }

  // HabClient Controllers
  var controllers = {
    Hotel : "?Page=Hotel",
    Users : "?Page=Users"
  }

  // HabClient Modules
  var modules = {
    Token : "&Token=",
    Client : "&SubPage=ShowClient",
    HotelSettings : "&SubPage=Client",
    UserAuth : "&SubPage=Login",
    HotelStatus : "&SubPage=Status",
    UserCount : "&SubPage=OnlineCount"
  }

  // Get Server Base URI + Server Base
  this.getBase = function() {
    return this.getVar('ServerUri') + this.getVar('ServerBase');
  }

  // Get Server Token
  this.getToken = function() {
    return this.getModule('Token') + this.getVar('ServerToken')
  }

  // Get HabClient Variable
  this.getVar = function(VarName) {
    return variables[VarName]
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

// Start HabClient Function
function startHabClient(serverName, serverToken) {
    global.habclient.initHab(serverName, serverToken);

    global.habclient.startComm();
}

// Exports HabClient Init Method
exports.startHabClient = startHabClient;

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
