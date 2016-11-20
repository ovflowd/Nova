package com.habbohotel.habclient.domain.entity;

import com.habbohotel.habclient.domain.entity.api.Custom;
import com.habbohotel.habclient.domain.entity.api.Emulator;
import com.habbohotel.habclient.domain.entity.api.Hotel;
import com.habbohotel.habclient.domain.entity.api.Swf;

public class ServerApi {
    private Swf swf;

    private Hotel hotel;

    private Emulator emulator;

    private Custom custom;

    public Swf getSwf() {
        return swf;
    }

    public void setSwf(Swf swf) {
        this.swf = swf;
    }

    public Hotel getHotel() {
        return hotel;
    }

    public void setHotel(Hotel hotel) {
        this.hotel = hotel;
    }

    public Custom getCustom() {
        return custom;
    }

    public void setCustom(Custom custom) {
        this.custom = custom;
    }

    public Emulator getEmulator() {
        return emulator;
    }

    public void setEmulator(Emulator emulator) {
        this.emulator = emulator;
    }
}
