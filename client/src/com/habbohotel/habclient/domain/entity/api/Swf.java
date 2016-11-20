package com.habbohotel.habclient.domain.entity.api;

import com.habbohotel.habclient.domain.entity.api.swf.Gamedata;
import com.habbohotel.habclient.domain.entity.api.swf.Gordon;

public class Swf {
    private String path;

    private Gordon gordon;

    private Gamedata gamedata;

    public String getPath() {
        return path;
    }

    public void setPath(String path) {
        this.path = path;
    }

    public Gordon getGordon() {
        return gordon;
    }

    public void setGordon(Gordon gordon) {
        this.gordon = gordon;
    }

    public Gamedata getGamedata() {
        return gamedata;
    }

    public void setGamedata(Gamedata gamedata) {
        this.gamedata = gamedata;
    }
}
