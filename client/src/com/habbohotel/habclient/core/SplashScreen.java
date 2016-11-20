package com.habbohotel.habclient.core;

import javafx.application.Application;
import javafx.concurrent.Task;
import javafx.geometry.Pos;
import javafx.scene.Scene;
import javafx.scene.control.Label;
import javafx.scene.image.Image;
import javafx.scene.image.ImageView;
import javafx.scene.layout.Pane;
import javafx.scene.layout.StackPane;
import javafx.scene.paint.Color;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.scene.web.WebView;
import javafx.stage.Stage;
import javafx.stage.StageStyle;

public class SplashScreen extends Application {
    private Pane splashLayout;
    private Label progressText;
    private WebView webView;
    private Stage mainStage;

    public static void main(String[] args) throws Exception {
        launch(args);
    }

    @Override
    public void init() {
        ImageView splash = new ImageView(new Image(SplashScreen.class.getClassLoader().getResourceAsStream("assets/Loading.png")));

        ImageView inside = new ImageView(new Image("http://localhost/images/logo.gif"));

        splashLayout = new StackPane();
        splashLayout.setMaxSize(1200, 600);

        progressText = new Label("Loading HabClient Engine...");

        progressText.setTextFill(Color.web("#ffffff"));
        progressText.setFont(Font.font("Arial", FontWeight.BOLD, 20));

        StackPane.setAlignment(progressText, Pos.BOTTOM_CENTER);

        splashLayout.getChildren().add(splash);
        splashLayout.getChildren().add(inside);
        splashLayout.getChildren().add(progressText);

        splashLayout.setStyle("-fx-background-color: transparent; -fx-background-radius: 6; -fx-border-radius: 6; -fx-border-width: 5;");
    }

    @Override
    public void start(final Stage initStage) throws Exception {
        showSplash(initStage);
        initStage.toFront();
        //showMainStage();

//        webView.getEngine().documentProperty().addListener((observableValue, document, document1) -> {
//            if (initStage.isShowing()) {
//
//                mainStage.setIconified(false);
//                initStage.toFront();
//
//                //FadeTransition fadeSplash = new FadeTransition(Duration.seconds(1.2), splashLayout);
//                //fadeSplash.setFromValue(1.0);
//                //fadeSplash.setToValue(0.0);
//                //fadeSplash.setOnFinished(actionEvent -> initStage.hide());
//                //fadeSplash.play();
//            }
//        });
    }

    private void showMainStage() {
        mainStage = new Stage(StageStyle.DECORATED);
        mainStage.setTitle("HabClient - Welcome");
        mainStage.setIconified(true);

        webView = new WebView();
        webView.getEngine().load("http://fxexperience.com/");

        Scene scene = new Scene(webView, 1000, 600);

        webView.prefWidthProperty().bind(scene.widthProperty());
        webView.prefHeightProperty().bind(scene.heightProperty());

        mainStage.setScene(scene);
        mainStage.show();
    }

    private void showSplash(Stage initStage) {
        Scene splashScene = new Scene(splashLayout);

        Label label = (Label) splashLayout.getChildren().get(2);

        Task<Void> task = new Task<Void>() {
            @Override
            public Void call() throws InterruptedException {
                updateMessage("Please Wait! Starting up HabClient...");

                Thread.sleep(3000);

                updateMessage("Loading HabClient Communication Modules..");

                Thread.sleep(5000);

                updateMessage("Starting Communication with HabClient Engine..");

                Thread.sleep(3000);

                updateMessage("Connection made with HabClient Engine at [127.0.0.1:8080]");

                Thread.sleep(2000);

                updateMessage("Authenticating with provided Token....");

                Thread.sleep(2000);

                updateMessage("Authentication OK... Starting Flash Provider...");

                return null;
            }
        };

        task.setOnSucceeded(e -> {
            label.textProperty().unbind();

            label.setText("HabClient Engine is Ready...");
        });

        Thread thread = new Thread(task);
        thread.setDaemon(true);
        thread.start();

        label.textProperty().bind(task.messageProperty());

        splashScene.setFill(Color.TRANSPARENT);

        initStage.initStyle(StageStyle.UNDECORATED);
        initStage.initStyle(StageStyle.TRANSPARENT);
        initStage.setScene(splashScene);

        initStage.show();
    }
}