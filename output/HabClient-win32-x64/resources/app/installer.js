const {app, BrowserWindow, Menu} = require('electron')
const path = require('path')
const url = require('url')
const electronInstaller = require('electron-winstaller');

resultPromise = electronInstaller.createWindowsInstaller({
    appDirectory: __dirname,
    outputDirectory: __dirname,
    authors: 'Claudio Santoro',
    title: 'NovaApp - Future of Habbo',
    exe: 'Nova.exe',
    //iconUrl: 'app/images/icon.ico',
    //setupIcon: 'app/images/icon.ico',
    setupExe: 'NovaInstaller.exe',
    description: "Nova is the future of Habbo Hotel. Play any Habbo, in any platform. For any version.",
    version: '0.1.1'
  });

resultPromise.then(() => console.log("[NovaApp] Created Installer Successfully."), (e) => console.log(`No dice: ${e.message}`));
