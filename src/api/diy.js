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
 * @description DIY模板列表
 * @param {Object} param data {Object} 传值参数
 */
export function diyList (data) {
    return request({
        url: 'diy/get_list',
        method: 'get',
        params: data
    });
}

/**
 * @description 保存DIY数据
 * @param {Object} param data {Object} 传值参数
 */
export function diySave (id, data) {
    return request({
        url: 'diy/save/' + id,
        method: 'post',
        data: data
    });
}

/**
 * @description 获取DIY数据
 * @param {Object} param data {Object} 传值参数
 */
export function diyGetInfo (id, data) {
    return request({
        url: 'diy/get_info/' + id,
        method: 'get',
        params: data
    });
}

/**
 * @description 删除DIY数据
 * @param {Object} param data {Object} 传值参数
 */
export function diyDel (id,data) {
    return request({
        url: 'diy/del/' + id,
        method: 'delete',
        data
    });
}

/**
 * @description 使用diy模板
 * @param {Object} param data {Object} 传值参数
 */
export function setStatus (id,data) {
    return request({
        url: 'diy/set_status/' + id,
        method: 'put',
        data
    });
}

/**
 * @description 恢复模板初始数据
 * @param {Object} param data {Object} 传值参数
 */
export function recovery(id) {
    return request({
        url: 'diy/recovery/' + id,
        method: 'get'
    });
}

/**
 * @description 设置初始数据
 * @param {Object} param data {Object} 传值参数
 */
export function setDefault(id) {
    return request({
        url: 'diy/set_default_data/' + id,
        method: 'get'
    });
}

/**
 * @description 获取分类
 */
export function categoryList () {
    return request({
        url: '/cms/category_list',
        method: 'get'
    });
}

/**
 * @description 获取产品分类
 */
export function getCategory () {
    return request({
        url: 'diy/get_category',
        method: 'get'
    });
}

/**
 * @description 获取链接列表
 */
export function getUrl () {
    return request({
        url: 'diy/get_url',
        method: 'get'
    });
}

/**
 * @description 获取商品列表
 */
export function getProduct (data) {
    return request({
        url: 'diy/get_product',
        method: 'get',
        params: data
    });
}

/**
 * @description 获取产品一或二级分类
 */
export function getByCategory(data) {
    return request({
        url: 'diy/get_by_category',
        method: 'get',
        params: data
    });
}

/**
 * @description 使用diy模板(判断是否显示周边门店列表)
 * @param {Object} param data {Object} 传值参数
 */
export function storeStatus() {
    return request({
        url: 'diy/get_store_status',
        method: 'get'
    });
}

/**
 * @description 使用diy模板(活动商品)
 * @param {Object} param data {Object} 传值参数
 */
export function getGroomList(type,data) {
    return request({
        url: 'diy/groom_list/'+type,
        method: 'get',
        params: data
    });
}

/**
 * @description 换色 -- 一键换色、分类提交；
 */
export function colorChange (status,name) {
    return request({
        url: `diy/color_change/${status}/${name}`,
        method: 'put'
    });
}

/**
 * @description 换色 -- 一键换色、分类信息；
 */
export function getColorChange (name) {
    return request({
        url: `diy/get_color_change/${name}`,
        method: 'get'
    });
}

/**
 * @description 小程序 -- 二维码；
 */
export function getRoutineCode (id) {
    return request({
        url: `diy/get_routine_code/${id}`,
        method: 'get'
    });
}

/**
 * @description 个人中心-获取信息；
 */
export function getMember () {
    return request({
        url: `diy/get_member`,
        method: 'get'
    });
}

/**
 * @description 个人中心-提交信息；
 */
export function memberSave (data) {
    return request({
        url: `diy/member_save`,
        method: 'post',
        data: data
    });
}

/**
 * @description 页面链接-获取分类；
 */
export function pageCategory () {
    return request({
        url: `diy/get_page_category`,
        method: 'get'
    });
}

/**
 * @description 页面链接-获取链接；
 */
export function pageLink (id) {
    return request({
        url: `diy/get_page_link/${id}`,
        method: 'get'
    });
}

/**
 * @description 页面链接-自定义链接提交；
 */
export function saveLink (data,id) {
    return request({
        url: `diy/save_link/${id}`,
        method: 'post',
        data: data
    });
}


