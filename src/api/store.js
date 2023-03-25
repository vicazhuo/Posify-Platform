// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import request from '@/plugins/request';
/**
 *店员列表-获取门店
 */
export function staffListInfo(data) {
    return request({
        url: 'merchant/store_list',
        method: 'get',
	    params: data
    });
}


/**
 *订单-订单列表
 */
export function orderList(data) {
	return request({
		url: `store/order/list`,
		method: 'get',
		params: data
	});
}

/**
 *订单-订单头部数据
 */
export function orderChart(data) {
	return request({
		url: `store/order/chart`,
		method: 'get',
		params: data
	});
}

/**
 *订单-获取门店订单头部统计
 */
export function orderHeader(data) {
	return request({
		url: `store/order/header`,
		method: 'get'
	});
}

/**
 *订单-充值订单列表
 */
export function orderRecharge(data) {
	return request({
		url: `store/recharge`,
		method: 'get',
		params: data
	});
}

/**
 *订单-付费会员订单列表
 */
export function orderVip(data) {
	return request({
		url: `store/vip_order`,
		method: 'get',
		params: data
	});
}

/**
 *订单-获取订单编辑表单
 */
export function getOrdeDatas(id) {
	return request({
		url: `store/order/edit/${id}`,
		method: 'get'
	});
}

/**
 * 订单-获取快递公司
 */
export function getExpressData(status) {
    return request({
        url: `order/express_list?status=` + status,
        method: 'get'
    })
};

/**
 * @description 发送货提交表单
 * @param {Number} param data.id {Number} 订单id
 * @param {Object} param data.datas {Object} 表单信息
 */
export function putDelivery(data) {
    return request({
        url: `/order/delivery/${data.id}`,
        method: 'put',
        data: data.datas
    });
};

/**
 * @description 拆单发送货
 * @param {Number} param data.id {Number} 订单id
 * @param {Object} param data.datas {Object} 表单信息
 */
export function splitDelivery(data) {
    return request({
        url: `/order/split_delivery/${data.id}`,
        method: 'put',
        data: data.datas
    });
};

/**
 * 电子面单模板
 * @param {com} data 快递公司编号
 */
export function orderExpressTemp(data) {
    return request({
        url: '/order/express/temp',
        method: 'get',
        params: data
    });
}

/**
 * 订单时获取所有配送员列表
 */
export function orderDeliveryList() {
    return request({
        url: '/order/delivery/list',
        method: 'get'
    });
}

// 面单默认配置信息
export function orderSheetInfo() {
    return request({
        url: '/order/sheet_info',
        method: 'get'
    });
}

/**
 * @description 获取订单可拆分商品列表
 * @param {Object} param data {Object} 传值参数
 */
export function splitCartInfo(id) {
    return request({
        url: `order/split_cart_info/${id}`,
        method: 'get'
    })
};

/**
 * @description 配送信息表单
 * @param {Number} param id {Number} 订单id
 */
export function getDistribution(id) {
    return request({
        url: `store/order/distribution/${id}`,
        method: 'get'
    });
};

/**
 * @description 订单号核销
 */
export function writeUpdate(id) {
    return request({
        url: `store/order/write_update/${id}`,
        method: 'put'
    });
}

/**
 * @description 订单物流信息
 * @param {Number} param id {Number} 订单id
 */
export function getExpress(id) {
    return request({
        url: `/order/express/${id}`,
        method: 'get'
    });
};

/**
 * @description 订单表单详情数据
 * @param {Number} param id {Number} 订单id
 */
export function getDataInfo(id) {
    return request({
        url: `store/order/info/${id}`,
        method: 'get'
    });
};

/**
 * @description 获取订单记录
 * @param {Number} param data.id {Number} 订单id
 * @param {String} param data.datas {String} 分页参数
 */
export function getOrderRecord(data) {
    return request({
        url: `/order/status/${data.id}`,
        method: 'get',
        params: data.datas
    });
};

/**
 * @description 修改备注信息
 * @param {Number} param data.id {Number} 订单id
 * @param {String} param data.remark {String} 备注信息
 */
export function putRemarkData(data) {
    return request({
        url: `order/remark/${data.id}`,
        method: 'put',
        data: data.remark
    });
};


/**
 * @description 修改充值备注信息
 * @param {Number} param data.id {Number} 订单id
 * @param {String} param data.remark {String} 备注信息
 */
export function putRechargeRemarkData(data) {
    return request({
        url: `store/recharge/remark/${data.id}`,
        method: 'put',
        data: data.remark
    });
};

/**
 * @description 修改会员备注信息
 * @param {Number} param data.id {Number} 订单id
 * @param {String} param data.remark {String} 备注信息
 */
export function putVipRemarkData(data) {
    return request({
        url: `store/vip/remark/${data.id}`,
        method: 'put',
        data: data.remark
    });
};

/**
 * @description 子订单列表---拆单
 * @param {Object} param data {Object} 传值参数
 */
export function splitOrderList(id) {
    return request({
        url: `order/split_order/${id}`,
        method: 'get'
    })
};

/**
 * @description 售后订单
 * @param {Object} param data {Object} 传值参数
 */
export function orderRefundList(data) {
    return request({
        url: 'refund/list',
        method: 'get',
        params: data
    })
};

/**
 * @description 获取退款表单数据
 * @param {Number} param id {Number} 订单id
 */
export function getRefundFrom(id) {
    return request({
        url: `/order/refund/${id}`,
        method: 'get'
    });
};

/**
 * @description 获取不退款表单数据
 * @param {Number} param id {Number} 订单id
 */
export function getnoRefund(id) {
    return request({
        url: `/order/no_refund/${id}`,
        method: 'get'
    });
};

/**
 * @description 获取退积分表单
 * @param {Number} param id {Number} 订单id
 */
export function refundIntegral(id) {
    return request({
        url: `/order/refund_integral/${id}`,
        method: 'get'
    });
};

/**
 * @description 导出
 */
export function orderExport(data,type) {
    return request({
        url: `store/order/export/${type}`,
        method: 'post',
        data
    });
}




/**
 *门店流水-获取列表
 */
export function storeFinanceInfo(data) {
    return request({
        url: 'store/finance_flow/list',
        method: 'get',
		params:data
    });
}

/**
 *门店流水--备注
 */
export function storeFinanceMarkApi(id,data) {
    return request({
        url: `store/finance_flow/mark/${id}`,
        method: 'put',
		params:data
    });
}


/**
 *门店流水--获取账单记录列表
 */
export function storeFfundRecordApi(data) {
    return request({
        url: `store/finance_flow/fund_record`,
        method: 'get',
		params:data
    });
}


/**
 *门店流水--账单记录列表-账单详情
 */
export function storeFfundRecordInfoApi(data) {
    return request({
        url: `store/finance_flow/fund_record_info`,
        method: 'get',
		params:data
    });
}

/**
 *门店流水--账单记录列表-账单下载
 */
export function exportfundRecordApi(data) {
    return request({
        url: `/export/storeFinanceRecord`,
        method: 'get',
		params:data
    });
}
/**
 *转账申请-申请列表
 */
export function storeExtractInfo(data) {
    return request({
        url: '/store/extract/list',
        method: 'get',
		params:data
    });
}

/**
 *转账申请-备注
 */
export function storeExtractMarkApi(id,data) {
    return request({
        url: `store/extract/mark/${id}`,
        method: 'post',
		data
		
    });
}



/**
 *转账申请-审核
 */
export function storeExtractVerifyApi(id,data) {
    return request({
        url: `store/extract/verify/${id}`,
        method: 'post',
		data
		
    });
}


/**
 *转账申请-转账
 */
export function storepaying(id) {
    return request({
        url: `store/extract/transfer/${id}`,
        method: 'get'
		
    });
}



/**
 *转账申请-设置
 */
export function headerListApi (data) {
    return request({
        url: 'store/finance/header_basics',
        method: 'get',
        params: data
    });
}

/**
 *转账申请-设置-表单
 */
export function dataFromApi (data,url) {
    return request({
        url: url,
        // url: '/setting/config/edit_basics',
        method: 'get',
        params: data
    });
}


/**
 *门店列表-获取列表数据 
 */
export function storeListApi (data) {
    return request({
        url: 'store/store',
        method: 'get',
        params: data
    });
}

/**
 * @description 门店列表 -- 门店修改信息；
 */
export function storeGetInfoApi (id) {
    return request({
        url: `store/store/get_info/${id}`,
        method: 'get'
    });
}

/**
 * @description 门店设置 获取省市区街道
 */
export function cityApi (data) {
    return request({
        url: 'city',
        method: 'get',
        params: data
    });
}

/**
 * @description 门店设置 获取当前登录门店信息
 */
export function storeUpdateApi (id,data) {
    return request({
        url: `store/store/${id}`,
        method: 'post',
		params: data
    });
}

/**
 * @description 门店设置 获取地图key
 */
export function keyApi () {
    return request({
        url: 'store/store/address',
        method: 'get'
    });
}

/**
 * 店设置 进入门店
 */
export function storeLogin (id) {
    return request({
        url: `store/store/login/${id}`,
        method: 'get'
    });
}

/**
 * 门店设置 修改营业状态
 */
export function storeSetShowApi (id,type) {
    return request({
        url: `store/store/set_show/${id}/${type}`,
        method: 'put'
    });
}


/**
门店订单 分配
 */
export function storeShareApi (data) {
    return request({
        url: `store/share/order`,
        method: 'post',
		params: data
    });
}

export function headerApi (data) {
    return request({
        url: 'store/home/header',
        method: 'get',
		params:data

    });
}

export function orderCharts (data) {
    return request({
        url: 'store/home/orderChart',
        method: 'get',
		params: data
    });
}


export function storeApi (data) {
    return request({
        url: 'store/home/store',
        method: 'get',
		params: data
    });
}

export function operateApi (data) {
    return request({
        url: 'store/home/operate',
        method: 'get',
		params: data
    });
}



export function resetApi (id) {
    return request({
        url: `store/store/reset_admin/${id}`,
        method: 'get'
    });
}

export function exportTableList (id,keyword,data) {
    return request({
        url: `export/storeFlowExport?store_id=${id}&keyword=${keyword}&data=${data}`,
        method: 'get'
    });
}