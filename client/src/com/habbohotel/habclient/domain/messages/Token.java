package com.habbohotel.habclient.domain.messages;

abstract class Token extends Base {
    private String NewToken;

    public String getNewToken() {
        return NewToken;
    }

    public void setNewToken(String newToken) {
        NewToken = newToken;
    }
}
