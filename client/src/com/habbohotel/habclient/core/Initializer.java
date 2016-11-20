package com.habbohotel.habclient.core;

import com.habbohotel.habclient.domain.messages.HotelClient;
import javafx.application.Application;
import org.apache.http.HttpResponse;

public class Initializer {

    private static HotelClient serverApi;

    public static void main(String[] args) throws Exception {

        Communication communication = new Communication();

        HttpResponse response = communication.doEngineRequest(Requests.HOTEL_CLIENT.toString());

        serverApi = communication.hotelClient(response);

        Application.launch(SplashScreen.class, args);
    }

    public static HotelClient getServerApi() {
        return serverApi;
    }
}
