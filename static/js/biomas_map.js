function biomas() {
    OpenLayers.ProxyHost= "/portal/proxy.php?url=";
    var map = new OpenLayers.Map('map',{
            controls:[]
        });

    var layer = new OpenLayers.Layer.WMS( "Biomas do Brasil", "http://146.134.16.24/geoserver/ows",
        {
            layers: 'CNCFlora:biomas', 
            format: 'image/gif',
            renders: ["canvas","SVG","VML"]
        });

    var select = new OpenLayers.Layer.Vector("Selection",
                {
                    renders: [ "canvas","SVG","VML"],
                    styleMap: new OpenLayers.Style(OpenLayers.Feature.Vector.style["select"])
                });

    map.addLayers([layer,select]);

    var control = new OpenLayers.Control.GetFeature({
        protocol: OpenLayers.Protocol.WFS.fromWMSLayer(layer),
        box: false,
        hover: false,
        multipleKey: "shiftKey",
        toggleKey: "ctrlKey"
    });

    control.events.register("featureselected", this, function(e) {
        select.addFeatures([e.feature]);
        $(".expedicao").hide();
        $("."+e.feature.data.cd_legend.replace(" ","-").toLowerCase()).show();
    });

    control.events.register("featureunselected", this, function(e) {
        select.removeFeatures([e.feature]);
    });

    map.addControl(control);
    control.activate();

    map.setCenter(new OpenLayers.Bounds(-75,-35,-33,6).getCenterLonLat(), 3);
}
