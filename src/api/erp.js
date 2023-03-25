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
 * @description erp设置
 * @param {Object} param data {Object} 传值参数
 */
export function erpConfig () {
    return request({
        url: 'erp/config',
        method: 'get'
    });
}

/**
 * @description erp门店列表
 * @param {Object} param data {Object} 传值参数
 */
export function erpShop (data) {
    return request({
        url: 'store/erp/shop',
        method: 'get',
		params: data
    });
}

/**
 * @description 导入erp
 * @param {Object} param data {Object} 传值参数
 */
export function erpProduct (data) {
    return request({
        url: 'product/import_erp_product',
        method: 'post',
		    data
    });
}


