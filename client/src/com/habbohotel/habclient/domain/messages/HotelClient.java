package com.habbohotel.habclient.domain.messages;

import com.habbohotel.habclient.domain.entity.ServerApi;

public class HotelClient extends Token {
    private ServerApi Client;

    public ServerApi getClient() {
        return Client;
    }

    public void setClient(ServerApi client) {
        Client = client;
    }
}
