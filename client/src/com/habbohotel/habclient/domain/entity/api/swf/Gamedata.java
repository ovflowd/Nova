package com.habbohotel.habclient.domain.entity.api.swf;

public class Gamedata {
    private String variables;

    private String texts;

    private String overrideVariables;

    private String overrideTexts;

    private String furnidata;

    private String productdata;

    public String getVariables() {
        return variables;
    }

    public void setVariables(String variables) {
        this.variables = variables;
    }

    public String getTexts() {
        return texts;
    }

    public void setTexts(String texts) {
        this.texts = texts;
    }

    public String getOverrideVariables() {
        return overrideVariables;
    }

    public void setOverrideVariables(String overrideVariables) {
        this.overrideVariables = overrideVariables;
    }

    public String getOverrideTexts() {
        return overrideTexts;
    }

    public void setOverrideTexts(String overrideTexts) {
        this.overrideTexts = overrideTexts;
    }

    public String getFurnidata() {
        return furnidata;
    }

    public void setFurnidata(String furnidata) {
        this.furnidata = furnidata;
    }

    public String getProductdata() {
        return productdata;
    }

    public void setProductdata(String productdata) {
        this.productdata = productdata;
    }
}
