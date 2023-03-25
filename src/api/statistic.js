// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import request from "@/plugins/request";

/**
 * 用户基础
 * @param {*} data
 */
export function userBasic(data) {
    return request({
        url: "/statistic/user/get_basic",
        method: "get",
        params: data,
    });
}

/**
 * 用户增长趋势
 * @param {*} data
 */
export function userTrend(data) {
    return request({
        url: "/statistic/user/get_trend",
        method: "get",
        params: data,
    });
}

/**
 * 成交用户
 * @param {*} data
 */
export function userPaid(data) {
    return request({
        url: "/statistic/user/get_paid",
        method: "get",
        params: data,
    });
}

/**
 * 成交用户分析
 * @param {*} data
 */
export function userPaidAnalyse(data) {
    return request({
        url: "/statistic/user/get_paid_analyse",
        method: "get",
        params: data,
    });
}

/**
 * @description 商品统计 商品概括
 * @param {Object} param data {Object} 传值参数
 */
export function statisticBasicApi (params) {
    return request({
        url: '/statistic/product/get_basic',
        method: 'get',
        params
    })
};

/**
 * @description 商品统计 商品概括 统计图
 * @param {Object} param data {Object} 传值参数
 */
export function statisticTrendApi (params) {
    return request({
        url: '/statistic/product/get_trend',
        method: 'get',
        params
    })
};

/**
 * @description 商品统计 商品排行
 * @param {Object} param data {Object} 传值参数
 */
export function statisticProductListApi (params) {
    return request({
        url: '/statistic/product/get_product_ranking',
        method: 'get',
        params
    })
};

/**
 * @description 商品统计 导出
 * @param {Object} param data {Object} 传值参数
 */
export function statisticProductExcel (params) {
    return request({
        url: '/statistic/product/get_excel',
        method: 'get',
        params
    })
};

/**
 * @description 用户统计 用户概括
 * @param {Object} param data {Object} 传值参数
 */
export function statisticUserBasicApi (params) {
    return request({
        url: '/statistic/user/get_basic',
        method: 'get',
        params
    })
};

/**
 * @description 用户统计 用户趋势
 * @param {Object} param data {Object} 传值参数
 */
export function statisticUserTrendApi (params) {
    return request({
        url: '/statistic/user/get_trend',
        method: 'get',
        params
    })
};

/**
 * @description 用户统计 微信用户概括
 * @param {Object} param data {Object} 传值参数
 */
export function statisticWechatApi (params) {
    return request({
        url: '/statistic/user/get_wechat',
        method: 'get',
        params
    })
};

/**
 * @description 用户统计 微信用户趋势
 * @param {Object} param data {Object} 传值参数
 */
export function statisticWechatTrendApi (params) {
    return request({
        url: '/statistic/user/get_wechat_trend',
        method: 'get',
        params
    })
};

/**
 * @description 用户统计 微信用户地域
 * @param {Object} param data {Object} 传值参数
 */
export function statisticWechatRegionApi (params) {
    return request({
        url: '/statistic/user/get_region',
        method: 'get',
        params
    })
};

/**
 * @description 用户统计 用户性别
 * @param {Object} param data {Object} 传值参数
 */
export function statisticWechatSexApi (params) {
    return request({
        url: '/statistic/user/get_sex',
        method: 'get',
        params
    })
};

/**
 * @description 用户统计 导出
 * @param {Object} param data {Object} 传值参数
 */
export function statisticUserExcel (params) {
    return request({
        url: '/statistic/user/get_excel',
        method: 'get',
        params
    })
};

/**
 * @description 交易数据 今天
 * @param {Object} param data {Object} 传值参数
 */
export function statisticTopTradeApi (params) {
    return request({
        url: '/statistic/trade/top_trade',
        method: 'get',
        params
    })
};

/**
 * @description 交易概括
 * @param {Object} param data {Object} 传值参数
 */
export function statisticBottomTradeApi (params) {
    return request({
        url: '/statistic/trade/bottom_trade',
        method: 'get',
        params
    })
};

/**
 * @description 财务管理 -- 资金流水统计
 * @param {Number} param data {Number} 请求参数data
 */
export function getFlowList (data) {
    return request({
        url: `finance/flow/get_list`,
        method: 'get',
        params: data
    })
};

/**
 * @description 资金流水 -- 备注
 * @param {Number} param id {Number} 提现申请id
 */
export function setMarks (id, data) {
    return request({
        url: `finance/flow/set_mark/${id}`,
        method: 'post',
        data
    })
};
