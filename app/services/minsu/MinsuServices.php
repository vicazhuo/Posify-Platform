<?php

namespace app\services\minsu;

use app\services\BaseServices;
use GuzzleHttp\Client;

class MinsuServices extends BaseServices
{
    public $client;

    public function __construct()
    {
        $options = [
            "headers" => [
                "Accept" => "application/json, text/plain, */*",
                "Accept-Encoding" => "gzip, deflate, br",
                "Accept-Language" => "zh-CN,zh;q=0.9,en-US;q=0.8,en;q=0.7",
                "Connection" => "keep-alive",
                "Content-Length" => 301,
                "Content-Type" => "application/json;charset=UTF-8",
                "Cookie" => "_fas_uuid=dcdfab97-2ac6-4c57-9b53-30da4bdf1cd2-1655210672915; tj_channel_id=tujia; clientAgent=T_h5; tj_abtest=%255B%257B%2522key%2522%253A%2522T_salogin_831%2522%252C%2522value%2522%253A%2522A%2522%257D%252C%257B%2522key%2522%253A%2522TOC-1341-5KM%2522%252C%2522value%2522%253A%2522C%2522%257D%252C%257B%2522key%2522%253A%2522BnbCt_l3CDR%2522%252C%2522value%2522%253A%2522A%2522%257D%252C%257B%2522key%2522%253A%2522TTJ-13197%2522%252C%2522value%2522%253A%2522A%2522%257D%252C%257B%2522key%2522%253A%2522l_abtest%2522%252C%2522value%2522%253A%2522C%2522%257D%255D; _fas_fe_log_info=%257B%2522lat%2522%253A%2522null%2522%252C%2522lng%2522%253A%2522null%2522%252C%2522cityId%2522%253A%2522null%2522%252C%2522positionErrorMsg%2522%253A%2522%25E5%25BE%2588%25E6%258A%25B1%25E6%25AD%2589%25E7%25B3%25BB%25E7%25BB%259F%25E7%25A6%2581%25E7%2594%25A8%25E4%25BA%2586%25E5%25AE%259A%25E4%25BD%258D%25E5%258A%259F%25E8%2583%25BD%2522%257D; _fas_session_id=mw8shAZzX9tD5h2jd6MeePzxaS3W1655605378056; tujia.com_MobileContext_StartDate=2022-06-19; tujia.com_MobileContext_EndDate=2022-06-20; gr_flag=MC4xMjgyNTc4ODc1MTcxNDY3OF8wX3RyYWtpbmFz; _bfa=1.1655210673684.v4onv0.1.1655210673684.1655605626136.6.16.10650076066; _ubtstatus=%7B%22vid%22%3A%221655210673684.v4onv0%22%2C%22sid%22%3A6%2C%22pvid%22%3A16%2C%22pid%22%3A%2210650076066%22%7D",
                "Host" => "m.tujia.com",
                "LL-INFO" => "wprDtcOLwqEDTQ5Tw7EJwrDDv2bDvRMcwrnCm8KhKh5eDVHDqMKPw6tsa8K5VsOCw4g7K8Oww6ADcV10AH9OwoFNwqstYcKmwo4awpzClcKeAcOFLMOEw7XChMOzwpljDVnDimvDtcKoGj/CosOaw6XClTzCplclwqU5w5nCrsKKAMKGMMK9PTbChMKGwqLCpsKyw7Q+w5jDtltSwqoybcKMV8OAwq85C0Vjw5HDgMKYBMOoDzzCiMOtwr3Dm8Omw4bCq2HDi8KUw7LCnsK1I8Oewr3CkClmw5/CqMO/w7zCsErDjzwhwpHCt8KRaCAmfwHDoEPCv2wuwo/DmsOETMOcQcOxU8OZw4gJw7Zkw4vCkMKxH28HUQHCv0bDscOFK8OIb8OcwpR7w4/Cl0Nzw43DgMOXw6TCiRkWFMKEwrogw4PDhMKVworCqGLDuMKcw6Qgw4bDuMKpbnheTcKDw618UFDDsWbCpMO7A8KdGcOCwr59YMKdBcOxQE8VfjdrUMKRwqBaf2bDqEbDtsKcRsOvw4R+BsOWXMOSw47ChXHDvGk6DMOEw7rDpMOQQFvCkXNIwrMKQXV2P8OVw4MwwrXCqcKfJ8Krwp1nUF0pw74RJ8OwwrrDoMOYf8O3UXzCmS8Uwoxww7tfLcKVw6EiMhd7LcKBw501NGPCiQpYKcOS",
                "Origin" => " https://m.tujia.com",
                "Referer" => "https://m.tujia.com/hotel_guangzhou/",
                "sec-ch-ua-mobile" => "?0",
                "sec-ch-ua-platform" => "Windows",
                "Sec-Fetch-Dest" => "empty",
                'Sec-Fetch-Mode' => "cors",
                "Sec-Fetch-Site" => "same-origin",
                "T-INFO" => "0dbdc847cf5c84370e062740439c8d1fc0a721ad667edebbcd3d45bf3da0e5cc432713a2f20df194c7f30d4172ac536f1abfe982f051ea309a3122de45b185a026778aa51ca62061460299a06fbeaa38z7QaYHnv8WWtv/vWzuPiZO4lTTZYeWykFiPaioOdRuL01+MURo+FMGSMhGhNtV2nDiTHDuWabl1lTKr49juMRhyfdqpWENA=",
                "User-Agent" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.51 Safari/537.36",
                "X-App-Stats" => "curPage=unitDetail",
                "X-TJCH" => 0,
                "X-TJH" => "0516c68517377816a811ec3806ae8ba8c15988de",
                "X-TJP" => 28,
                "X-TJTS" => 1655605626
            ]
        ];
        $this->client = new Client($options);
    }

    /**
     * @param int $hotelId
     * @return false|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getLandlordInfo(int $hotelId)
    {
        $url = "http://m.tujia.com/bnbapp-node-h5/h5/hotel/gethotel?_apitsp=1655605397359&_fasTraceId=1655605397368PYAw14z8_Gs1jkXzbxSfSGM1d9rdn2GD4y5kf";
        $res = $this->client->request("POST", $url, [
            "json" => ["hotelId" => $hotelId]
        ]);
        $data = json_decode($res->getBody()->getContents(), true);
        if ($data['errcode'] == 0) {
            return $data['data']['hotel'];
        }
        return $data['data'];
    }

    public function getLandlordHoserList(int $hotelId)
    {
        $url = "http://m.tujia.com/bnbapp-node-h5/h5/search/searchhousebyhotel/bnb?_apitsp=1655556246180&_fasTraceId=1655556246181cwfiSixc_8mawiGAbRaaz7TQhhAXmY6x0czy6";
        $res = $this->client->request("POST", $url, [
            "json" => [
                "hotelId" => "60060",
                "pageIndex" => 0,
                "pageSize" => 10,
                "unitId" => ""
            ]
        ]);
        $data = json_decode($res->getBody()->getContents(), true);
        if ($data['errcode'] == 0) {
            foreach ($data['data']['items'] as $key => $val) {
                $data['data']['items'][$key]['allowBooking'] = true;
            }
            return $data['data'];
        }
        return $data['data'];
    }

    public function getHoserPrice()
    {

        return [
            "priceModule"=> [
                "product"=> [
                    "minRequireNights"=> 1,
                    "productId"=> 994704,
                    "productNumber"=> "编号：994704",
                    "productName"=> "基础价",
                    "productPrice"=> "680",
                    "finalPrice"=> "544",
                    "markLine"=> true,
                    "activityInfo"=> "%7B%22peopleCount%22%3A1%2C%22extraAttribute%22%3A%220_0_0__544_0_0_0__null_0___1000075__%22%2C%22version%22%3A3%2C%22source%22%3A0%2C%22totalHouseAmount%22%3A544%2C%22totalAddPeopleAmount%22%3A0%2C%22totalCleaningAmount%22%3A0%2C%22maxPeopleCount%22%3A7%2C%22addPeopleAmount%22%3A0%2C%22qdepositFlag%22%3Afalse%2C%22sproductId%22%3A%22994704_null_0_1000075%22%2C%22houseId%22%3A373252%2C%22msHouse%22%3Atrue%2C%22productId%22%3A994704%2C%22checkInDate%22%3A%222022-07-20%22%2C%22checkOutDate%22%3A%222022-07-21%22%2C%22prevTraceId%22%3A%22tu_bnbapp_node_mp_2203211532.10.95.159.52.443.8598_1%22%2C%22referTraceId%22%3A%221647847965719YBJd063P_SsRs7AGth7yjGXhsAARrhQkHR3mb%22%7D",
                    "allowBooking"=> true,
                    "disallowBookingReason"=> null,
                    "newPrices"=> [[
                        "productId"=> 994704,
                        "name"=> "无早餐 | 限时取消",
                        "promotionPrice"=> null,
                        "ruleText"=> "07月06日12=>00前可免费取消",
                        "productPrice"=> "680",
                        "finalPrice"=> "544",
                        "tags"=> null,
                        "selected"=> true,
                        "sproductId"=> "994704_null_0_1000075"
                    ]],
                    "defaultPromotionTips"=> [
                        "choseContent"=> "2个促销活动",
                        "promotionTips"=> [[
                            "icon"=> "https=>//pic.tujia.com/upload/festatic/crn/dacu.png",
                            "title"=> "天天特惠",
                            "text"=> "已减¥136",
                            "tips"=> [],
                            "aloneLine"=> false,
                            "jumpUrl"=> null,
                            "timeStamp"=> 0,
                            "titleTips"=> null,
                            "color"=> "",
                            "type"=> 0
                        ]]
                    ],
                    "defaultProductTip"=> null,
                    "defaultProductStatus"=> 0,
                    "promotionTags"=> [[
                        "tagText"=> [
                            "text"=> "天天特惠",
                            "color"=> "#FF6666",
                            "border"=> null,
                            "tips"=> null,
                            "background"=> [
                                "color"=> "rgba(255,102,102,0.10)",
                                "image"=> null,
                                "gradientColor"=> null
                            ]
                        ],
                        "tagPic"=> null,
                        "tagDesc"=> "限时专享，活动房型入离日期内合计可减136.0元",
                        "tagCode"=> 0,
                        "tagLink"=> null,
                        "aloneLine"=> false,
                        "childTags"=> null
                    ], [
                        "tagText"=> [
                            "text"=> "评价有礼",
                            "color"=> "#FF6666",
                            "border"=> null,
                            "tips"=> null,
                            "background"=> [
                                "color"=> "rgba(255,102,102,0.10)",
                                "image"=> null,
                                "gradientColor"=> null
                            ]
                        ],
                        "tagPic"=> null,
                        "tagDesc"=> "入住后评价，拿最高100元红包+8元等值抵现积分",
                        "tagCode"=> 0,
                        "tagLink"=> null,
                        "aloneLine"=> false,
                        "childTags"=> null
                    ]],
                    "priceDetails"=> [[
                        "title"=> "平均房费",
                        "introduction"=> "￥680",
                        "focus"=> null,
                        "blod"=> false,
                        "icon"=> null,
                        "color"=> null,
                        "tip"=> "",
                        "highLight"=> null,
                        "memberTitle"=> "",
                        "memberLevelStyle"=> null,
                        "maskTagText"=> "",
                        "titleType"=> 0,
                        "marketActivityId"=> 0
                    ], [
                        "title"=> "天天特惠",
                        "introduction"=> "-￥136",
                        "focus"=> null,
                        "blod"=> false,
                        "icon"=> null,
                        "color"=> null,
                        "tip"=> "",
                        "highLight"=> null,
                        "memberTitle"=> "天天特惠",
                        "memberLevelStyle"=> null,
                        "maskTagText"=> "",
                        "titleType"=> 0,
                        "marketActivityId"=> 1000075
                    ]],
                    "expressBooking"=> false,
                    "quickBooking"=> false,
                    "creditButton"=> false,
                    "userCreditLevel"=> null,
                    "activityTips"=> [[
                        "title"=> "天天特惠",
                        "introduction"=> "￥136",
                        "focus"=> null,
                        "blod"=> false,
                        "icon"=> null,
                        "color"=> null,
                        "tip"=> "",
                        "highLight"=> null,
                        "memberTitle"=> "天天特惠",
                        "memberLevelStyle"=> null,
                        "maskTagText"=> "",
                        "titleType"=> 0,
                        "marketActivityId"=> 1000075
                    ]],
                    "redPacketPromoTip"=> "",
                    "promoTotalPriceIcon"=> [
                        "icon"=> "https=>//pic.tujia.com/upload/festatic/publicImages/big_promotion_bg.png",
                        "title"=> "特惠价格！已优惠136元",
                        "text"=> null
                    ],
                    "displayOrigionPrice"=> true
                ]
            ],
            "products" => [
                [
                "id" => 16360298,
                "productType" => 1,
                "name" => "基础价",
                "activityInfo" => "%7B%22houseId%22%3A12586848%2C%22productId%22%3A16360298%2C%22peopleCount%22%3A1%2C%22extraAttribute%22%3A%220_0_0__98_0_0_0__null_0___1000094%2C1000085__%22%2C%22version%22%3A1%2C%22source%22%3A0%2C%22checkInDate%22%3A%222022-02-23%22%2C%22checkOutDate%22%3A%222022-02-24%22%2C%22totalHouseAmount%22%3A145%2C%22totalAddPeopleAmount%22%3A0%2C%22totalCleaningAmount%22%3A0%2C%22maxPeopleCount%22%3A3%2C%22addPeopleAmount%22%3A0%2C%22referTraceId%22%3A%22uXKuK6HySJ0%22%2C%22sproductId%22%3A%2216360298_null_0_1000094%2C1000085%22%2C%22qdepositFlag%22%3Afalse%7D",
                "minRequireNights" => 1,
                "orderStrategy" => null,
                "ruleIntros" => [[
                    "title" => "闪订",
                    "introduction" => "预订无需等待，订单闪电确认",
                    "link" => null,
                    "icon" => "fast_icon",
                    "tips" => null,
                    "deleted" => false,
                    "loginFlag" => false,
                    "color" => null
                ], [
                    "title" => "清洁费",
                    "introduction" => "您可免费享受房东的一客一扫服务",
                    "link" => null,
                    "icon" => null,
                    "tips" => null,
                    "deleted" => false,
                    "loginFlag" => false,
                    "color" => null
                ], [
                    "title" => "发票说明",
                    "introduction" => "房费发票请联系房东",
                    "link" => null,
                    "icon" => null,
                    "tips" => null,
                    "deleted" => false,
                    "loginFlag" => false,
                    "color" => null
                ], [
                    "title" => "退订规则",
                    "introduction" => "取消订单，收取订金的100%作为违约金支付给房东(由房东制定)",
                    "link" => null,
                    "icon" => null,
                    "tips" => null,
                    "deleted" => false,
                    "loginFlag" => false,
                    "color" => "#FF6666"
                ]],
                "productPrice" => 365,
                "finalPrice" => 145,
                "cashIntros" => null,
                "returnCashSettingType" => 1,
                "allowBooking" => true,
                "disallowBookingReason" => null,
                "defaultProductTip" => "新手已减220元",
                "displayTipIcon" => false,
                "defaultProductTipText" => null,
                "exclusiveTipItems" => null,
                "defaultProductStatus" => 1,
                "quickBooking" => 0,
                "finalPriceText" => null,
                "priceValidate" => false,
                "priceFavourable" => false,
                "youKe" => false,
                "checkInDate" => "2022-02-23",
                "checkOutDate" => "2022-02-24",
                "bottomActivity" => null,
                "isLmProduct" => false,
                "isExpressBooking" => true,
                "isDefaultDisplay" => true
            ]],
            "isAcceptBookingTime" => true,
            "cancelRules" => [[
                "tip" => "取消订单/r/n收取房费的100%",
                "strDate" => "2022.2.23",
                "strTime" => "12=>00",
                "introduction" => "入住当天",
                "backColor" => "#F4F6F8",
                "tipColor" => "#666666",
                "deleted" => true,
                "isDeleted" => true
            ], [
                "tip" => "提前离店/r/n收取剩余房费的100%",
                "strDate" => "2022.2.24",
                "strTime" => "",
                "introduction" => "离店当天",
                "backColor" => "#F4F6F8",
                "tipColor" => "#666666",
                "deleted" => false,
                "isDeleted" => false
            ]],
            "cancelTexts" => [[
                "title" => "闪订",
                "introduction" => "预订无需等待，订单闪电确认",
                "link" => null,
                "icon" => "fast_icon",
                "tips" => null,
                "deleted" => false,
                "loginFlag" => false,
                "color" => null
            ], [
                "title" => "清洁费",
                "introduction" => "您可免费享受房东的一客一扫服务",
                "link" => null,
                "icon" => null,
                "tips" => null,
                "deleted" => false,
                "loginFlag" => false,
                "color" => null
            ], [
                "title" => "发票说明",
                "introduction" => "房费发票请联系房东",
                "link" => null,
                "icon" => null,
                "tips" => null,
                "deleted" => false,
                "loginFlag" => false,
                "color" => null
            ], [
                "title" => "退订规则",
                "introduction" => "取消订单，收取订金的100%作为违约金支付给房东(由房东制定)",
                "link" => null,
                "icon" => null,
                "tips" => null,
                "deleted" => false,
                "loginFlag" => false,
                "color" => "#FF6666"
            ]],
            "promotionLinks" => [[
                "type" => 14,
                "enabled" => true,
                "title" => "新手立减",
                "text" => "新手专享折扣",
                "navigateUrl" => null,
                "navigateUrlText" => null,
                "navigateUrlTextColor" => null,
                "timeStamp" => 0,
                "promotion" => false,
                "goClientType" => 0,
                "iconCode" => null,
                "icon" => "https=>//pic.tujia.com/upload/festatic/app/qiang.png",
                "titleColor" => null,
                "titleBackGroundColor" => null,
                "backColor" => "#FFF3F3",
                "textColor" => "#FF6666",
                "useTextColor" => false,
                "minDays" => 0,
                "orderDate" => null,
                "bargainEndDate" => null
            ]],
            "promotionPic" => null,
            "shareInfo" => null,
            "productTag" => [
                "icon" => "https=>//pic.tujia.com/upload/festatic/app/hui.png",
                "productTags" => [[
                    "type" => 14,
                    "text" => "新手立减",
                    "color" => "#FF9645",
                    "background" => [
                        "color" => "#FFFFFF",
                        "image" => null,
                        "gradientColor" => null
                    ],
                    "border" => [
                        "color" => "#FF9645",
                        "width" => 0
                    ],
                    "orderIndex" => 0,
                    "tip" => null,
                    "jumpUrl" => null,
                    "colorType" => 0
                ]],
                "partake" => false,
                "partakeText" => null
            ],
            "redPacketTagData" => null,
            "dateActivityContent" => null,
            "houseTags" => [[
                "type" => 0,
                "text" => "宽松取消保障",
                "color" => "#FF9645",
                "background" => null,
                "border" => null,
                "orderIndex" => 0,
                "tip" => "支持入住前1天免费取消，部分房屋支持入住当天12点前免费取消，实际规则以房屋规则为准",
                "jumpUrl" => null,
                "colorType" => 2
            ]],
            "popupUpJson" => "[\"page\"=>\"HOUSE_DETAIL_PAGE\",\"condition\"=>\"[\\\"cityId\\\"=>68,\\\"checkInDate\\\"=>\\\"2022-02-23\\\",\\\"checkOutDate\\\"=>\\\"2022-02-24\\\",\\\"houseId\\\"=>12586848,\\\"hotelId\\\"=>4710756,\\\"canBook\\\"=>true,\\\"bookingCount\\\"=>1,\\\"peopleCount\\\"=>1,\\\"userRedeemText\\\"=>null,\\\"houseDetailUserRedeem\\\"=>null]\"]",
            "productCalendarTip" => [
                "title" => null,
                "text" => null,
                "type" => 0,
                "zdDate" => null,
                "days" => 0,
                "checkInDate" => null,
                "checkOutDate" => null
            ],
            "loginGuidance" => [
                "icon" => "https=>//pic.tujia.com/upload/festatic/crn/hongbaonew.png",
                "focusText" => [
                    "text" => "「登录」后可享受更多优惠",
                    "focusText" => "「登录」"
                ],
                "link" => [
                    "enabled" => true,
                    "title" => "立即登录",
                    "text" => null,
                    "navigateUrl" => null
                ]
            ]
        ];
    }

    public function getHouseDetails()
    {
        return [
            "unitId" => 9995616,
            "unitGlobalId" => "0ffb66e8-00d2-4438-a624-1aff9587db98",
            "unitName" => "华侨城欢乐海岸玛雅水上乐园、清晖园、渔人码头/意式复式2居室（1.8大床）",
            "houseTypeName" => "普通公寓",
            "address" => "顺德绿地中心公寓(云阳路绿地中心4座公寓大堂)",
            "introduction" => "点击我头像，还有更多房源可选择.离华侨城欢乐海岸玛雅水上乐园5分钟车程，公寓地处大良市中心黄金商圈地段，复式设计，每一件家具都是房东精心选择，个，公寓都是超高配置，房间配有舒适的大双人床，高级床垫，舒服的家具等，客厅落地玻璃窗可俯瞰整个桂畔湿地公园江景，顺峰山湖景，华侨城，区政府等，阳光充足，白天在阳光的照射下显得格外温馨浪漫。\n\n\n房间配备：浴巾，毛巾，牙刷，沐浴，洗发，吹风机，拖鞋都有哒\n\n\n家中设备齐全，电视、空调、百兆宽带、冰箱、独立卫浴、24小时热水等，空气净化器，除螨，驱蚊器等，五星级床上用品，由房东亲自设计轻奢、意式风公寓，\n无论短暂停留，还是悠长度假，都能带给您自然、舒适、恬静、整洁、清新、温馨、细致、贴心，希望它为你带来一段既舒适又温暖人心的旅途体验，旅途中的家！",
            "unitTrafficInfos" => [[
                "title" => "房屋描述",
                "introduction" => "点击我头像，还有更多房源可选择.离华侨城欢乐海岸玛雅水上乐园5分钟车程，公寓地处大良市中心黄金商圈地段，复式设计，每一件家具都是房东精心选择，个，公寓都是超高配置，房间配有舒适的大双人床，高级床垫，舒服的家具等，客厅落地玻璃窗可俯瞰整个桂畔湿地公园江景，顺峰山湖景，华侨城，区政府等，阳光充足，白天在阳光的照射下显得格外温馨浪漫。\n\n\n房间配备：浴巾，毛巾，牙刷，沐浴，洗发，吹风机，拖鞋都有哒\n\n\n家中设备齐全，电视、空调、百兆宽带、冰箱、独立卫浴、24小时热水等，空气净化器，除螨，驱蚊器等，五星级床上用品，由房东亲自设计轻奢、意式风公寓，\n无论短暂停留，还是悠长度假，都能带给您自然、舒适、恬静、整洁、清新、温馨、细致、贴心，希望它为你带来一段既舒适又温暖人心的旅途体验，旅途中的家！"
            ], [
                "title" => "交通位置",
                "introduction" => "关于位置：顺德大良云阳路绿地中心4座公寓（顺峰山公园湖畔湿地公园旁）\n1.位于顺德大良云阳路绿地中心4座公寓，即导航/打车——大良绿地中心4座公寓大堂\n\n2.离华侨城欢乐海岸玛雅水上乐园5分钟车程、渔人码头15分钟车程、清晖园6分钟车程、长鹿休闲农庄6分钟车程\n\n\n离宝林寺3.6公里，凤城食都4.5公里，清晖园2.8公里，长鹿休闲度假农庄8.8公里，轻轨顺德站6.5公里，高速公路口3.4公里\n\n3.广州白云机场—机场快线到（顺德大良嘉信城市广场）—打车到云阳路绿地中心4座公寓大堂既可\n\n4.轻轨/动车—顺德站—然后搭车8分钟左右到\n\n5.大巴—顺德汽车客运总站大良站—然后打车6分钟左右到\n\n6.楼下有多条公交线路及公共免费停车位，共享单车等，出行十分便捷\n\n"
            ]],
            "translatedIntroduction" => null,
            "introductionTransDes" => null,
            "cityId" => 68,
            "cityName" => "佛山",
            "unitSourceType" => 1,
            "cityTerritoryType" => 1,
            "unitInstanceCount" => 2,
            "timeZone" => 8,
            "longitude" => 113.293767,
            "latitude" => 22.83981200565501,
            "geoCoordSysType" => "bd09ll",
            "unitTagsBitValue" => 0,
            "hotelId" => 3436651,
            "unitHighLights" => null,
            "minRequireNights" => 1,
            "unitLevel" => 2,
            "productFilter" => null,
            "houseTags" => [[
                "tagType" => 6,
                "tagName" => "实拍"
            ], [
                "tagType" => 9,
                "tagName" => "途家优选"
            ], [
                "tagType" => 10,
                "tagName" => "智能门锁"
            ], [
                "tagType" => 12,
                "tagName" => "超赞房东"
            ]],
            "defaultPictureURL" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011812051994_700_467.jpg",
            "pictureList" => [[
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011812051994_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011812051994_1100_733.jpg",
                "orderIndex" => -1,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011812296855_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011812296855_1100_733.jpg",
                "orderIndex" => 1,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011811536959_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011811536959_1100_733.jpg",
                "orderIndex" => 2,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011743475817_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011743475817_1100_733.jpg",
                "orderIndex" => 3,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011746464881_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011746464881_1100_733.jpg",
                "orderIndex" => 4,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011746347235_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011746347235_1100_733.jpg",
                "orderIndex" => 5,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011748019791_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011748019791_1100_733.jpg",
                "orderIndex" => 6,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011748164540_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011748164540_1100_733.jpg",
                "orderIndex" => 7,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011740386520_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011740386520_1100_733.jpg",
                "orderIndex" => 8,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011742153284_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011742153284_1100_733.jpg",
                "orderIndex" => 9,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011742429144_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011742429144_1100_733.jpg",
                "orderIndex" => 10,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011742489230_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011742489230_1100_733.jpg",
                "orderIndex" => 11,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011744429411_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011744429411_1100_733.jpg",
                "orderIndex" => 12,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011745233469_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011745233469_1100_733.jpg",
                "orderIndex" => 13,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011744551785_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011744551785_1100_733.jpg",
                "orderIndex" => 14,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011743526529_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011743526529_1100_733.jpg",
                "orderIndex" => 15,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011744298044_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011744298044_1100_733.jpg",
                "orderIndex" => 16,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011746372374_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011746372374_1100_733.jpg",
                "orderIndex" => 17,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011747077448_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011747077448_1100_733.jpg",
                "orderIndex" => 18,
                "pictureExplain" => null
            ], [
                "title" => "客厅",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011747202807_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011747202807_1100_733.jpg",
                "orderIndex" => 19,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749469821_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749469821_1100_733.jpg",
                "orderIndex" => 20,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749332247_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749332247_1100_733.jpg",
                "orderIndex" => 21,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011751192444_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011751192444_1100_733.jpg",
                "orderIndex" => 22,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011813362750_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011813362750_1100_733.jpg",
                "orderIndex" => 23,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011813281361_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011813281361_1100_733.jpg",
                "orderIndex" => 24,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749247757_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749247757_1100_733.jpg",
                "orderIndex" => 25,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749024215_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749024215_1100_733.jpg",
                "orderIndex" => 26,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749521218_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749521218_1100_733.jpg",
                "orderIndex" => 27,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011750297318_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011750297318_1100_733.jpg",
                "orderIndex" => 28,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749399979_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011749399979_1100_733.jpg",
                "orderIndex" => 29,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011750367078_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011750367078_1100_733.jpg",
                "orderIndex" => 30,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011750296441_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011750296441_1100_733.jpg",
                "orderIndex" => 31,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011809312061_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011809312061_1100_733.jpg",
                "orderIndex" => 32,
                "pictureExplain" => null
            ], [
                "title" => "卧室1",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011810278184_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011810278184_1100_733.jpg",
                "orderIndex" => 33,
                "pictureExplain" => null
            ], [
                "title" => "厨房",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011751403572_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011751403572_1100_733.jpg",
                "orderIndex" => 34,
                "pictureExplain" => null
            ], [
                "title" => "厨房",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011751395910_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011751395910_1100_733.jpg",
                "orderIndex" => 35,
                "pictureExplain" => null
            ], [
                "title" => "卫生间",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011752318396_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011752318396_1100_733.jpg",
                "orderIndex" => 36,
                "pictureExplain" => null
            ], [
                "title" => "卫生间",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011753122311_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011753122311_1100_733.jpg",
                "orderIndex" => 37,
                "pictureExplain" => null
            ], [
                "title" => "阳台",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011753514466_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011753514466_1100_733.jpg",
                "orderIndex" => 38,
                "pictureExplain" => null
            ], [
                "title" => "阳台",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011754381536_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011754381536_1100_733.jpg",
                "orderIndex" => 39,
                "pictureExplain" => null
            ], [
                "title" => "外景",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011755403623_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011755403623_1100_733.jpg",
                "orderIndex" => 40,
                "pictureExplain" => null
            ], [
                "title" => "外景",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011755163332_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011755163332_1100_733.jpg",
                "orderIndex" => 41,
                "pictureExplain" => null
            ], [
                "title" => "其他",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011756446430_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011756446430_1100_733.jpg",
                "orderIndex" => 42,
                "pictureExplain" => null
            ], [
                "title" => "其他",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080008399421_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080008399421_1100_733.jpg",
                "orderIndex" => 43,
                "pictureExplain" => null
            ], [
                "title" => "其他",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080007444467_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080007444467_1100_733.jpg",
                "orderIndex" => 44,
                "pictureExplain" => null
            ], [
                "title" => "其他",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080008395201_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080008395201_1100_733.jpg",
                "orderIndex" => 45,
                "pictureExplain" => null
            ], [
                "title" => "其他",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080007458429_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080007458429_1100_733.jpg",
                "orderIndex" => 46,
                "pictureExplain" => null
            ], [
                "title" => "其他",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080007183225_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080007183225_1100_733.jpg",
                "orderIndex" => 47,
                "pictureExplain" => null
            ], [
                "title" => "其他",
                "url" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080007194268_700_467.jpg",
                "albumUrl" => "https://pic.tujia.com/upload/landlordunit/day_190708/thumb/201907080007194268_1100_733.jpg",
                "orderIndex" => 48,
                "pictureExplain" => null
            ]],
            "recommendedGuests" => 4,
            "grossArea" => 85,
            "businessType" => 4,
            "businessTypeName" => "自然人房东",
            "houseResource" => 1,
            "liveWithHouseowner" => false,
            "cameraLink" => null,
            "unitVRURL" => null,
            "unitVideoURL" => "https://staticfile.tujia.com/upload/landlordunit/day_200721/201903071344192414.mp4",
            "unitVideoTimeSpan" => 0,
            "roomCountSummary" => "2室1厅 1卫1厨",
            "unitSimpleIntros" => ["普通公寓 85平米", "可做饭"],
            "bedSummary" => "宜住4人 2床",
            "bedDescriptions" => ["大床(2×1.8米) 1张", "大床(2×1.8米) 1张"],
            "shareSetting" => [
                "shareTitle" => "我在途家上发现了一个超赞的公寓哦：华侨城欢乐海岸玛雅水上乐园、清晖园、渔人码头/意式复式2居室（1.8大床），高品质酒店式服务公寓管理，私密、安静，尽享如住家中的温馨，适合4人入住，快来看看吧~ 链接：https://go.tujia.com/1054/?code=MobileClientShare&mref=share&id=9995616&uid=9995616&tjmcode=6",
                "shareImageUrl" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011812051994_700_467.jpg",
                "shareDescription" => "普通公寓 85平米 宜住4人 2床",
                "shareUrl" => "https://go.tujia.com/1054/?code=MobileClientShare&mref=share&id=9995616&uid=9995616&tjmcode=6",
                "shareUrlForWeChatSmallApp" => "/pages/index/index?nextPath=%2Fpages%2FunitDetails%2FunitDetails&nextPars=%7B%22unitId%22%3A%229995616%22%7D&code=_UnitDetail ",
                "shareMessage" => "我在途家上发现了一个超赞的公寓哦：华侨城欢乐海岸玛雅水上乐园、清晖园、渔人码头/意式复式2居室（1.8大床），高品质酒店式服务公寓管理，私密、安静，尽享如住家中的温馨，适合4人入住，快来看看吧~ 链接：https://go.tujia.com/1054/?code=MobileClientShare&mref=share&id=9995616&uid=9995616&tjmcode=6",
                "enumAppShareChannel" => null,
                "isReturnShareResult" => null,
                "isAppendShareUser" => null,
                "shareTip" => "长按二维码，发现更多精彩",
                "appendShareUser" => null,
                "returnShareResult" => null
            ],
            "unitFacilityGroups" => [[
                "groupId" => 0,
                "groupName" => "基础设施",
                "icon" => "base_icon",
                "unitFacilities" => [[
                    "name" => "无线网络",
                    "icon" => "e062",
                    "isDeleted" => false,
                    "orderIndex" => 1,
                    "deleted" => false
                ], [
                    "name" => "空调（冷暖)",
                    "icon" => "e04c",
                    "isDeleted" => false,
                    "orderIndex" => 2,
                    "deleted" => false
                ], [
                    "name" => "电视",
                    "icon" => "e043",
                    "isDeleted" => false,
                    "orderIndex" => 4,
                    "deleted" => false
                ], [
                    "name" => "洗衣机",
                    "icon" => "e065",
                    "isDeleted" => false,
                    "orderIndex" => 5,
                    "deleted" => false
                ], [
                    "name" => "冰箱",
                    "icon" => "e039",
                    "isDeleted" => false,
                    "orderIndex" => 6,
                    "deleted" => false
                ], [
                    "name" => "空气净化器",
                    "icon" => "e04d",
                    "isDeleted" => false,
                    "orderIndex" => 8,
                    "deleted" => false
                ], [
                    "name" => "暖气",
                    "icon" => "e055",
                    "isDeleted" => false,
                    "orderIndex" => 10,
                    "deleted" => false
                ], [
                    "name" => "电热水壶",
                    "icon" => "e059",
                    "isDeleted" => false,
                    "orderIndex" => 11,
                    "deleted" => false
                ], [
                    "name" => "电吹风",
                    "icon" => "e03d",
                    "isDeleted" => false,
                    "orderIndex" => 12,
                    "deleted" => false
                ], [
                    "name" => "熨斗/挂烫机",
                    "icon" => "e044",
                    "isDeleted" => false,
                    "orderIndex" => 13,
                    "deleted" => false
                ], [
                    "name" => "晾衣架",
                    "icon" => "e04f",
                    "isDeleted" => false,
                    "orderIndex" => 15,
                    "deleted" => false
                ], [
                    "name" => "装饰字画",
                    "icon" => "e121",
                    "isDeleted" => false,
                    "orderIndex" => 19,
                    "deleted" => false
                ], [
                    "name" => "沙发",
                    "icon" => "e12a",
                    "isDeleted" => false,
                    "orderIndex" => 20,
                    "deleted" => false
                ]]
            ], [
                "groupId" => 1,
                "groupName" => "卫浴设施",
                "icon" => "bath_icon",
                "unitFacilities" => [[
                    "name" => "可洗热水澡",
                    "icon" => "e047",
                    "isDeleted" => false,
                    "orderIndex" => 1,
                    "deleted" => false
                ], [
                    "name" => "洗浴用品",
                    "icon" => "e063",
                    "isDeleted" => false,
                    "orderIndex" => 2,
                    "deleted" => false
                ], [
                    "name" => "毛巾",
                    "icon" => "e051",
                    "isDeleted" => false,
                    "orderIndex" => 3,
                    "deleted" => false
                ], [
                    "name" => "牙具",
                    "icon" => "e069",
                    "isDeleted" => false,
                    "orderIndex" => 4,
                    "deleted" => false
                ], [
                    "name" => "独立卫浴",
                    "icon" => "e046",
                    "isDeleted" => false,
                    "orderIndex" => 5,
                    "deleted" => false
                ], [
                    "name" => "浴巾",
                    "icon" => "e023",
                    "isDeleted" => false,
                    "orderIndex" => 7,
                    "deleted" => false
                ], [
                    "name" => "智能马桶盖",
                    "icon" => "e12c",
                    "isDeleted" => false,
                    "orderIndex" => 10,
                    "deleted" => false
                ]]
            ], [
                "groupId" => 2,
                "groupName" => "厨房设施",
                "icon" => "cook_icon",
                "unitFacilities" => [[
                    "name" => "烹饪锅具",
                    "icon" => "e056",
                    "isDeleted" => false,
                    "orderIndex" => 1,
                    "deleted" => false
                ], [
                    "name" => "餐具",
                    "icon" => "e03a",
                    "isDeleted" => false,
                    "orderIndex" => 2,
                    "deleted" => false
                ], [
                    "name" => "电磁炉",
                    "icon" => "e040",
                    "isDeleted" => false,
                    "orderIndex" => 3,
                    "deleted" => false
                ], [
                    "name" => "刀具菜板",
                    "icon" => "e03f",
                    "isDeleted" => false,
                    "orderIndex" => 6,
                    "deleted" => false
                ], [
                    "name" => "电饭煲",
                    "icon" => "e041",
                    "isDeleted" => false,
                    "orderIndex" => 7,
                    "deleted" => false
                ], [
                    "name" => "洗涤用具",
                    "icon" => "e066",
                    "isDeleted" => false,
                    "orderIndex" => 10,
                    "deleted" => false
                ]]
            ], [
                "groupId" => 4,
                "groupName" => "周边500米",
                "icon" => null,
                "unitFacilities" => [[
                    "name" => "超市",
                    "icon" => "e02d",
                    "isDeleted" => false,
                    "orderIndex" => 1,
                    "deleted" => false
                ], [
                    "name" => "菜市场",
                    "icon" => "e02e",
                    "isDeleted" => false,
                    "orderIndex" => 2,
                    "deleted" => false
                ], [
                    "name" => "餐厅",
                    "icon" => "e0d8",
                    "isDeleted" => false,
                    "orderIndex" => 3,
                    "deleted" => false
                ], [
                    "name" => "药店",
                    "icon" => "e031",
                    "isDeleted" => false,
                    "orderIndex" => 4,
                    "deleted" => false
                ], [
                    "name" => "儿童乐园",
                    "icon" => "e0da",
                    "isDeleted" => false,
                    "orderIndex" => 6,
                    "deleted" => false
                ], [
                    "name" => "提款机",
                    "icon" => "e030",
                    "isDeleted" => false,
                    "orderIndex" => 7,
                    "deleted" => false
                ], [
                    "name" => "公共花园",
                    "icon" => "e120",
                    "isDeleted" => false,
                    "orderIndex" => 9,
                    "deleted" => false
                ]]
            ], [
                "groupId" => 5,
                "groupName" => "特色及其他",
                "icon" => null,
                "unitFacilities" => [[
                    "name" => "智能门锁",
                    "icon" => "e024",
                    "isDeleted" => false,
                    "orderIndex" => 1,
                    "deleted" => false
                ], [
                    "name" => "观景露台",
                    "icon" => "e0d2",
                    "isDeleted" => false,
                    "orderIndex" => 2,
                    "deleted" => false
                ], [
                    "name" => "保安",
                    "icon" => "e02b",
                    "isDeleted" => false,
                    "orderIndex" => 6,
                    "deleted" => false
                ], [
                    "name" => "灭火器",
                    "icon" => "e054",
                    "isDeleted" => false,
                    "orderIndex" => 8,
                    "deleted" => false
                ], [
                    "name" => "火灾警报器",
                    "icon" => "e048",
                    "isDeleted" => false,
                    "orderIndex" => 9,
                    "deleted" => false
                ], [
                    "name" => "电梯",
                    "icon" => "e06f",
                    "isDeleted" => false,
                    "orderIndex" => 10,
                    "deleted" => false
                ], [
                    "name" => "窗户",
                    "icon" => "e03c",
                    "isDeleted" => false,
                    "orderIndex" => 11,
                    "deleted" => false
                ], [
                    "name" => "落地窗",
                    "icon" => "e125",
                    "isDeleted" => false,
                    "orderIndex" => 12,
                    "deleted" => false
                ]]
            ], [
                "groupId" => 6,
                "groupName" => "服务",
                "icon" => null,
                "unitFacilities" => [[
                    "name" => "行李寄存",
                    "icon" => "e0db",
                    "isDeleted" => false,
                    "orderIndex" => 1,
                    "deleted" => false
                ]]
            ]],
            "checkInRules" => [[
                "title" => "入住 | 退房",
                "checkInRuleFloat" => false,
                "items" => [[
                    "introduction" => "14=>00后 | 12=>00前",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ]]
            ], [
                "title" => "卫生打扫",
                "checkInRuleFloat" => false,
                "items" => [[
                    "introduction" => "1客1扫",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ]]
            ], [
                "title" => "被单更换",
                "checkInRuleFloat" => false,
                "items" => [[
                    "introduction" => "1客1换",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ]]
            ], [
                "title" => "洗漱更换",
                "checkInRuleFloat" => false,
                "items" => [[
                    "introduction" => "1客1换",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ]]
            ], [
                "title" => "毛巾更换",
                "checkInRuleFloat" => false,
                "items" => [[
                    "introduction" => "1客1换",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ]]
            ], [
                "title" => "接待要求",
                "checkInRuleFloat" => true,
                "items" => [[
                    "introduction" => "接待儿童",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ], [
                    "introduction" => "接待老人",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ], [
                    "introduction" => "接待外宾",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ], [
                    "introduction" => "不可带宠物",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => true
                ], [
                    "introduction" => "不可吸烟",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => true
                ], [
                    "introduction" => "不可聚会",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => true
                ], [
                    "introduction" => "可以做饭",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ], [
                    "introduction" => "允许商业拍摄",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ]]
            ]],
            "checkinOtherInfo" => [[
                "title" => "加人",
                "checkInRuleFloat" => false,
                "items" => [[
                    "introduction" => "标准入住4人，不可加人",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ]]
            ], [
                "title" => "做饭",
                "checkInRuleFloat" => false,
                "items" => [[
                    "introduction" => "可做饭(餐具要自行清洗，否则会在押金扣50元做为清洗费\n)",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ]]
            ], [
                "title" => "聚会",
                "checkInRuleFloat" => false,
                "items" => [[
                    "introduction" => "房间内不允许聚会",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ]]
            ], [
                "title" => "商业拍摄",
                "checkInRuleFloat" => false,
                "items" => [[
                    "introduction" => "允许商业拍摄",
                    "tip" => null,
                    "textLink" => null,
                    "isDeleted" => false
                ]]
            ]],
            "relatedPosition" => null,
            "unitCommentSummary" => [
                "sortName" => null,
                "totalCount" => 95,
                "pictureCommentCount" => 39,
                "recommendedCount" => 95,
                "recommendedPercentage" => 100.0,
                "overall" => 4.9,
                "cleanliness" => 4.9,
                "services" => 4.9,
                "houseFacilities" => 0.0,
                "houseDecoration" => 4.9,
                "surrounding" => 0.0,
                "traffic" => 4.9,
                "price" => 4.7,
                "convenience" => 4.9,
                "scoreItems" => [[
                    "scoreType" => 2,
                    "scoreName" => "如实描述",
                    "score" => 4,
                    "doubleScore" => 4.9
                ], [
                    "scoreType" => 4,
                    "scoreName" => "服务周到",
                    "score" => 4,
                    "doubleScore" => 4.9
                ], [
                    "scoreType" => 3,
                    "scoreName" => "位置便利",
                    "score" => 4,
                    "doubleScore" => 4.9
                ], [
                    "scoreType" => 1,
                    "scoreName" => "干净卫生",
                    "score" => 4,
                    "doubleScore" => 4.9
                ], [
                    "scoreType" => 5,
                    "scoreName" => "高性价比",
                    "score" => 4,
                    "doubleScore" => 4.7
                ], [
                    "scoreType" => 6,
                    "scoreName" => "入住便捷",
                    "score" => 4,
                    "doubleScore" => 4.9
                ]],
                "commentTags" => null,
                "scoreTitle" => "超赞",
                "beyondSurroundingUnitPercentage" => null,
                "commentTrend" => null,
                "commentTagVo" => [[
                    "type" => 1,
                    "topic" => "全部",
                    "emotiontype" => 1,
                    "count" => 95,
                    "selected" => 1,
                    "selectParam" => "1,0",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ], [
                    "type" => 2,
                    "topic" => "有图",
                    "emotiontype" => 1,
                    "count" => 39,
                    "selected" => 0,
                    "selectParam" => "2,0",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ], [
                    "type" => 3,
                    "topic" => "最新",
                    "emotiontype" => 1,
                    "count" => 0,
                    "selected" => 0,
                    "selectParam" => "3,0",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ], [
                    "type" => 4,
                    "topic" => "老板不错",
                    "emotiontype" => 1,
                    "count" => 33,
                    "selected" => 0,
                    "selectParam" => "4,183",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ], [
                    "type" => 4,
                    "topic" => "下次还想入住",
                    "emotiontype" => 1,
                    "count" => 22,
                    "selected" => 0,
                    "selectParam" => "4,599",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ], [
                    "type" => 4,
                    "topic" => "房间干净",
                    "emotiontype" => 1,
                    "count" => 20,
                    "selected" => 0,
                    "selectParam" => "4,196",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ], [
                    "type" => 4,
                    "topic" => "位置不错",
                    "emotiontype" => 1,
                    "count" => 16,
                    "selected" => 0,
                    "selectParam" => "4,149",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ], [
                    "type" => 4,
                    "topic" => "房间装修不错",
                    "emotiontype" => 1,
                    "count" => 12,
                    "selected" => 0,
                    "selectParam" => "4,82",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ], [
                    "type" => 4,
                    "topic" => "设施挺好",
                    "emotiontype" => 1,
                    "count" => 6,
                    "selected" => 0,
                    "selectParam" => "4,308",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ], [
                    "type" => 4,
                    "topic" => "适合自驾",
                    "emotiontype" => 1,
                    "count" => 6,
                    "selected" => 0,
                    "selectParam" => "4,387",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ], [
                    "type" => 4,
                    "topic" => "附近有公交站",
                    "emotiontype" => 1,
                    "count" => 1,
                    "selected" => 0,
                    "selectParam" => "4,466",
                    "color" => "#666666",
                    "backgroundColor" => "#F5F5F5"
                ]],
                "commentBrief" => null,
                "userAvatars" => null,
                "comment" => null
            ],
            "landlordInfo" => [
                "id" => 3436651,
                "hotelName" => "喜航之家",
                "logoUrl" => "https://pic.tujia.com/upload/customeravatar/day_210922/thumb/202109221104148865_150_150.jpg",
                "hasInvoice" => false,
                "serviceHotline" => "021-26137886,3456692%23",
                "serviceHotlineDescription" => "拨打021-26137886转3456692",
                "serviceHotlineTip" => "",
                "realTimeServiceHotlinePattern" => "[\"hotelId\"=>3436651,\"rba\"=>false,\"hotelPhone\"=>\"139DYHI8686", "rbaHotelPhone\"=>null,\"defaultPhone\"=>\"021-26137886,3456692%23", "overseas\"=>false,\"im\"=>true,\"key\"=>null,\"secondHotelPhone\"=>\"139Ccdm3772", "secondKey\"=>null,\"HotelType\"=>1,\"Identifier\"=>\"3436651", "DefaultServiceHotline\"=>\"021-26137886,3456692%23", "DefaultServiceHotlineDescription\"=>\"拨打021-26137886转3456692", "IsNewHotel\"=>true,\"UnitId\"=>0]",
                "zmCreditDetail" => null,
                "verifiedDetail" => "实名验证",
                "hotelBrandDetail" => null,
                "ownerName" => null,
                "isGreat" => 1,
                "acceptBreachUser" => false,
                "landlordLevelUrl" => "",
                "landlordLevelTip" => null,
                "expressBooking" => false,
                "hostID" => 25838989,
                "isExpressBooking" => false
            ],
            "imSummary" => [
                "chatID" => 25838989,
                "chatTeamId" => null,
                "chatSesstionType" => 0,
                "chatType" => 2,
                "chatName" => "喜航之家",
                "houseSummary" => [
                    "houseName" => "华侨城欢乐海岸玛雅水上乐园、清晖园、渔人码头/意式复式2居室（1.8大床）",
                    "defaultPictureURL" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011812051994_700_467.jpg",
                    "recommendedGuests" => 4,
                    "roomCountSummary" => "2室 1厅 1 卫",
                    "unitDetailURL" => "https://go.tujia.com/1054/?id=9995616&uid=9995616&tjmcode=6",
                    "houseTypeName" => "普通公寓"
                ],
                "landlordAccountId" => 0,
                "hotelLogo" => null,
                "newChatId" => "ae9ee316d27e48c58605930272aab33a",
                "ctripCityId" => 0,
                "ctripHotel" => false,
                "tripHotelId" => null,
                "enableIm" => false,
                "unitIMSummary" => [
                    "unitName" => "华侨城欢乐海岸玛雅水上乐园、清晖园、渔人码头/意式复式2居室（1.8大床）",
                    "defaultPictureURL" => "https://pic.tujia.com/upload/landlordunit/day_190201/thumb/201902011812051994_700_467.jpg",
                    "recommendedGuests" => 4,
                    "roomCountSummary" => "2室1厅1卫",
                    "unitDetailURL" => "https://go.tujia.com/1054/?id=9995616&uid=9995616&tjmcode=6"
                ]
            ],
            "promotionLinks" => null,
            "cancelRules" => null,
            "cancelTexts" => null,
            "lineDescription" => null,
            "bedCount" => 2,
            "housingType" => [
                "bedroomCount" => 2,
                "livingroomCount" => 1,
                "bathroomCount" => 1,
                "kitchenCount" => 1,
                "balconyCount" => 1,
                "studyCount" => 0,
                "shareBathroom" => false,
                "rentType" => null,
                "houseDesc" => null
            ],
            "indoorPermits" => [4, 32, 64],
            "extraBedLimit" => 0,
            "addPeopleNum" => 0,
            "active" => true,
            "checkInReceptionTime" => "14=>00",
            "checkOutLatestTime" => "12=>00",
            "unitExtraVo" => [
                "qualification" => [
                    "name" => "优选",
                    "description" => "品质更高的精选房屋",
                    "introduction" => ["品质房源", "优质服务", "更多好评"]
                ],
                "qualificationPicList" => null,
                "baseBrief" => [[
                    "icon" => "house_icon",
                    "title" => "整套出租",
                    "description" => "85㎡/普通公寓",
                    "tips" => null
                ], [
                    "icon" => "housetype_icon",
                    "title" => "2室1厅 1卫1厨",
                    "description" => "可做饭",
                    "tips" => null
                ], [
                    "icon" => "bedtype_icon",
                    "title" => "宜住4人 2床",
                    "description" => "大床(2×1.8米) 1张",
                    "tips" => ["大床(2×1.8米) 1张", "大床(2×1.8米) 1张"]
                ]],
                "houseBrief" => [
                    "icon" => null,
                    "title" => "高档型民宿",
                    "description" => "严选优质设施，尽享品质住宿体验",
                    "tips" => null
                ],
                "maxPicUrl" => null,
                "houseSafeDescription" => "配备保安及智能门锁，入住更安心",
                "realPhoto" => true,
                "qualificationUrl" => null
            ],
            "housetag" => [[
                "text" => "可做饭",
                "backgroundColor" => "#363434",
                "color" => "#ff9645"
            ],
                [
                    "text" => "近超市",
                    "backgroundColor" => "#ffefe3",
                    "color" => "#ff9645"
                ],
                [
                    "text" => "自行入住",
                    "backgroundColor" => "#ffefe3",
                    "color" => "#ff9645"
                ],
                [
                    "text" => "落地窗",
                    "backgroundColor" => "#ffefe3",
                    "color" => "#ff9645"
                ],
                [
                    "text" => "观景露台",
                    "backgroundColor" => "#ffefe3",
                    "color" => "rgb(247, 210, 148)"
                ]
            ]

        ];
    }

}