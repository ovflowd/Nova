package com.habbohotel.habclient.domain.messages;

import com.habbohotel.habclient.domain.entity.ServerUser;

public class UserLogin extends Token {
    private ServerUser User;

    public ServerUser getUser() {
        return User;
    }

    public void setUser(ServerUser user) {
        User = user;
    }
}
