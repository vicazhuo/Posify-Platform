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
 * @description 套餐 -- 添加优惠套餐
 */
export function discountsSave(data) {
  return request({
    url: 'marketing/discounts/save',
    method: 'post',
    data
  });
}

/**
 * @description 套餐 -- 套餐列表
 */
export function discountsList(data) {
  return request({
    url: 'marketing/discounts/list',
    method: 'get',
    params: data
  });
}

/**
 * @description 套餐 -- 上下架
 */
export function discountsChangeStatus(data) {
  return request({
    url: `marketing/discounts/set_status/${data.id}/${data.status}`,
    method: 'get',
    params: data
  });
}

/**
 * @description 套餐 -- 获取详情数据
 */
export function discountsGetDetails(id) {
  return request({
    url: 'marketing/discounts/info/' + id,
    method: 'get'
  });
}