package com.habbohotel.habclient.core;

public enum Requests {

    HOTEL_CLIENT("?Page=Hotel&SubPage=Client"),
    USER_LOGIN("?Page=User&SubPage=Login");

    private final String name;

    private Requests(String s) {
        name = s;
    }

    public boolean equalsName(String otherName) {
        return otherName != null && name.equals(otherName);
    }

    public String toString() {
        return this.name;
    }
}
