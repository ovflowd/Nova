const {app, BrowserWindow} = require('electron')
const path = require('path')
const url = require('url')

// Keep a global reference of the window object, if you don't, the window will
// be closed automatically when the JavaScript object is garbage collected.
let win
let pluginName

switch (process.platform) {
  case 'win32':
    pluginName = 'pepflashplayer.dll'
    break
  case 'darwin':
    pluginName = 'PepperFlashPlayer.plugin'
    break
  case 'linux':
    pluginName = 'libpepflashplayer.so'
    break
}

console.log("[HabClient] Preparing Modules...");

// Start HabClient Function
function startHabClient(serverName, serverToken) {
  console.log("[HabClient] Server Selected: " + serverName)

  console.log("[HabClient] Selected Token: " + serverToken)

  console.log("Preparing now HabClient for selected server...")

  global.habclient.ServerUri = serverName;
  global.habclient.ServerToken = serverToken;

  initHabClient();
}

// Init HabClient for Selected Server
function initHabClient() {
  win.loadURL(global.habclient.ServerUri + global.habclient.ServerController + "&Token=" + global.habclient.ServerToken)

  console.log("[HabClient] Preparing... " + global.habclient.ServerUri + global.habclient.ServerController + "&Token=" + global.habclient.ServerToken)
}

// define habclient
global.habclient = {
  ServerToken : "", // Provisory Master Token
  ServerUri : "",
  ServerController : "/client.php?Page=Hotel&SubPage=ShowClient"
}

app.commandLine.appendSwitch('ppapi-flash-path', path.join(__dirname, pluginName))

exports.startHabClient = startHabClient;

function createWindow () {
  // Create the browser window.
  win = new BrowserWindow({
    width: 900,
    height: 700,
    //titleBarStyle: 'hidden',
    //transparent: true,
    //frame:false,
    //toolbar: false,
    center: true,
    webPreferences: {
        plugins: true,
        allowRunningInsecureContent: true
    }})

  // and load the index.html of the app.
  win.loadURL(url.format({
    pathname: path.join(__dirname, 'index.html'),
    protocol: 'file:',
    slashes: true
  }))

  // Emitted when the window is closed.
  win.on('closed', () => {
    // Dereference the window object, usually you would store windows
    // in an array if your app supports multi windows, this is the time
    // when you should delete the corresponding element.
    win = null
  })

  win.setMenu(null)
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
