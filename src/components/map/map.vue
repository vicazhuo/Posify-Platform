<template>
    <div>
        <div id="container" style="width:100%;height:450px;"></div>
    </div>
</template>

<script>
    // +----------------------------------------------------------------------
    // | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
    // +----------------------------------------------------------------------
    // | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
    // +----------------------------------------------------------------------
    // | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
    // +----------------------------------------------------------------------
    // | Author: CRMEB Team <admin@crmeb.com>
    // +----------------------------------------------------------------------
    import { TMap } from "./index";
    export default {
        props: {
            lat: {
                type: Number,
                default: 34.34127
            },
            lon: {
                type: Number,
                default: 108.93984
            },
            mapKey: {
                tyep: String
            },
            address: {
                tyep: String
            }
        },
        data() {
            return {
                geocoder: undefined,
                marker: null,
                resultDatail: {}
            };
        },
        created() {
            this.initMap();
        },
        methods: {
            //初始化地图
            initMap() {
                TMap(this.mapKey).then(qq => {
                    var map;
                    var center = new qq.maps.LatLng(this.lat, this.lon);
                    map = new qq.maps.Map(document.getElementById("container"), {
                        // center: center,
                        zoom: 15
                    });
                    //调用地址解析类
                    this.geocoder = new qq.maps.Geocoder({
                        complete: result => {
                            map.setCenter(result.detail.location);
                            this.marker = new qq.maps.Marker({
                                map,
                                position: result.detail.location
                            });
                            this.resultDatail = result.detail;
                            this.$emit("getCoordinates", result.detail);
                        }
                    });
                    console.log(this.address);
                    this.geocoder.getAddress(center);

                    // this.geocoder.getLocation(this.address);
                    // 开启监听点击
                    qq.maps.event.addListener(map, "click", event => {
                        this.marker.setMap(null);
                        this.marker.position = {
                            lat: event.latLng.getLat(),
                            lng: event.latLng.getLng()
                        };
                        var coord = new qq.maps.LatLng(
                            event.latLng.getLat(),
                            event.latLng.getLng()
                        );
                        this.geocoder.getAddress(coord);
                    });
                });
            },
            // 搜索地点
            searchKeyword(address = "西安") {
                this.marker.setMap(null);
                this.geocoder.getLocation(address);
            }
        }
    };
</script>

<style scoped>
</style>
