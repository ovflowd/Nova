package com.habbohotel.habclient.core;

import com.google.gson.Gson;
import com.habbohotel.habclient.domain.messages.HotelClient;
import com.habbohotel.habclient.domain.messages.UserLogin;
import org.apache.http.HttpResponse;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClientBuilder;
import org.apache.http.util.EntityUtils;

import java.io.IOException;

class Communication {

    // This will be removed futurely and changed by Main[Args]
    private String masterToken = "HabClient-48ac6b41574f59e58f74c00f8dffc5aa";

    private String serverUri = "http://localhost:8080/client.php";

    HttpResponse doRequest(String url) throws IOException {
        CloseableHttpClient httpClient = HttpClientBuilder.create().build();

        HttpGet request = new HttpGet(url);

        return httpClient.execute(request);
    }

    HttpResponse doEngineRequest(String request) throws IOException {
        return doRequest(serverUri + request + "&Token=" + masterToken);
    }

    HotelClient hotelClient(HttpResponse response) throws IOException {
        String json = EntityUtils.toString(response.getEntity(), "UTF-8");

        Gson gson = new Gson();

        return gson.fromJson(json, HotelClient.class);
    }

    UserLogin userLogin(HttpResponse response) throws IOException {
        String json = EntityUtils.toString(response.getEntity(), "UTF-8");

        Gson gson = new Gson();

        return gson.fromJson(json, UserLogin.class);
    }


    String getServerUri() {
        return serverUri;
    }
}
