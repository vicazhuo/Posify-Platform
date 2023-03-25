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
 * @description 用户管理--列表
 * @param {Object} param data {Object} 传值参数
 */
export function userList(data) {
    return request({
        url: 'user/user',
        method: 'get',
        params: data
    });
}

/**
 * @description 编辑表单数据
 * @param {Number} param id {Number} 会员id
 */
export function getUserData(id) {
    return request({
        url: `user/user/${id}/edit`,
        method: 'get'
    });
}

/**
 * @description 会员管理修改显示
 * @param {Object} param data {Object} 传入的状态值，用户id
 */
export function isShowApi(data) {
    return request({
        url: `user/set_status/${data.status}/${data.id}`,
        method: 'put'
    });
}

/**
 * @description 优惠券列表
 * @param {Object} param params {Object} 传值
 */
export function couponApi(params) {
    return request({
        url: `marketing/coupon/grant`,
        method: 'get',
        params
    });
}

/**
 * @description 发送优惠券
 * @param {Object} param data {Object} 传值
 */
export function sendCouponApi(data) {
    return request({
        url: `marketing/coupon/user/grant`,
        method: 'POST',
        data
    });
}

/**
 * @description 修改积分余额表单
 * @param {Number} param id {Number} 用户id
 */
export function editOtherApi(id) {
    return request({
        url: `user/edit_other/${id}`,
        method: 'get'
    });
}

/**
 * @description 会员管理-详情
 * @param {Number} param id {Number} 用户id
 */
export function detailsApi(id) {
    return request({
        url: `user/user/${id}`,
        method: 'get'
    });
}

/**
 * @description 会员管理详情中tab选项
 * @param {Number} param id {Number} 用户id
 */
export function infoApi(data) {
    return request({
        url: `user/one_info/${data.id}`,
        method: 'get',
        params: data.datas
    });
}

/**
 * @description 会员等级-列表
 * @param {Object} param data {Object} 传值参数
 */
export function levelListApi(data) {
    return request({
        url: 'user/user_level/vip_list',
        method: 'get',
        params: data
    });
}

/**
 * @description 会员等级-编辑表单
 * @param {Number} param id {Number} 会员等级id
 */
export function levelEditApi(id) {
    return request({
        url: `user/user_level/set_value/${id}`,
        method: 'PUT'
    });
}

/**
 * @description 会员等级-修改显示隐藏
 * @param {Number} param id {Number} 会员等级id
 */
export function setShowApi(data) {
    return request({
        url: `user/user_level/set_show/${data.id}/${data.is_show}`,
        method: 'PUT'
    });
}

/**
 * @description 会员等级-编辑表单
 * @param {Number} param id {Number} 会员等级id
 */
// export function addApi (data) {
//     return request({
//         url: 'user/user_level',
//         method: 'post',
//         data
//     });
// }

/**
 * @description 会员等级任务-列表
 * @param {Object} param data {Object} 传值参数
 */
export function taskListApi(id, data) {
    return request({
        url: `user/user_level/task/${id}`,
        method: 'get',
        params: data
    });
}

/**
 * @description 会员等级任务-修改显示隐藏
 * @param {Number} param data.id {Number} 会员等级任务id
 * @param {Number} param data.is_show {Number} 会员等级任务显示隐藏
 */
export function setTaskShowApi(data) {
    return request({
        url: `user/user_level/set_task_show/${data.id}/${data.is_show}`,
        method: 'PUT'
    });
}

/**
 * @description 会员等级任务-任务是否达成
 * @param {Number} param data.id {Number} 会员等级任务id
 * @param {Number} param data.is_must {Number} 会员等级任务是否务必达成
 */
export function setTaskMustApi(data) {
    return request({
        url: `user/user_level/set_task_must/${data.id}/${data.is_must}`,
        method: 'PUT'
    });
}

/**
 * @description 会员等级任务-新建表单 编辑表单
 * @param {Object} param data {Object} 会员等级任务对象传值
 */
export function createTaskApi(data) {
    return request({
        url: `/user/user_level/create_task`,
        method: 'get',
        params: data
    });
}

/**
 * @description 会员等级-创建表单
 * @param {Object} param data {Object} 会员等级任务对象传值
 */
export function createApi(id) {
    return request({
        url: `user/user_level/create`,
        method: 'get',
        params: id
    });
}

/**
 * @description 会员管理 --- 赠送会员等级
 * @param {Number} param id {Number} 会员id
 */
export function giveLevelApi(id) {
    return request({
        url: `user/give_level/${id}`,
        method: 'get'
    });
}

/**
 * @description 会员等级-删除
 * @param {Number} param id {Number} 会员等级id
 */
export function delLevelApi(id) {
    return request({
        url: `user/user_level/delete/${id}`,
        method: 'PUT'
    });
}

/**
 * @description 会员分组-列表
 * @param {Object} param data {Object} 传值参数
 */
export function userGroupApi(data) {
    return request({
        url: 'user/user_group/list',
        method: 'get',
        params: data
    });
}

/**
 * @description 会员删除 --- 删除分组
 * @param {Number} param id {Number} 会员id
 */
export function groupDelApi(id) {
    return request({
        url: `user/user_group/del/${id}`,
        method: 'DELETE'
    });
}

/**
 * @description 会员添加表单/删除表单 --- 表单
 * @param {Number} param id {Number} 会员id
 */
export function groupAddApi(id) {
    return request({
        url: `user/user_group/add/${id}`,
        method: 'get'
    });
}

/**
 * @description 个人中心 --- 修改密码
 * data 请求参数
 */
export function updtaeAdmin(data) {
    return request({
        url: `setting/update_admin`,
        method: 'PUT',
        data
    });
}

/**
 * @description 个人中心 --- 设置会员等级
 * data 请求参数
 */
export function userSetGroup(data) {
    return request({
        url: `user/set_group`,
        method: 'post',
        data
    });
}

/**
 * @description 个人中心 --- 会员标签列表
 * data 请求参数
 */
export function userLabelApi(data) {
    return request({
        url: `user/user_label`,
        method: 'get',
        params: data
    });
}

/**
 * @description 个人中心 --- 会员标签表单生成
 * data 请求参数
 */
export function userLabelAddApi(id,data) {
    return request({
        url: `user/user_label/add/${id}`,
        method: 'get',
				params: data
    });
}

/**
 * @description 个人中心 --- 获取设置会员标签表单
 * data 请求参数
 */
export function userSetLabelApi(data) {
    return request({
        url: `user/set_label`,
        method: 'post',
        data
    });
}

/**
 * @description 获取标签分类（全部）
 * data 请求参数
 */
export function userLabelAll(data) {
    return request({
        url: `user/user_label_cate/all`,
        method: 'get',
        params: data
    });
}

/**
 * @description 获取用户标签分类编辑表单
 * data 请求参数
 */
export function userLabelEdit(id) {
    return request({
        url: `user/user_label_cate/${id}/edit`,
        method: 'get'
    });
}

/**
 * @description 获取用户标签分类创建表单
 * data 请求参数
 */
export function userLabelCreate(id) {
    return request({
        url: `user/user_label_cate/create`,
        method: 'get'
    });
}

/**
 * 批次卡列表
 */
export function userMemberBatch(data) {
    return request({
        url: '/user/member_batch/index',
        method: 'get',
        params: data
    });
}

/**
 * 生成批次卡
 * @param {*} id id
 */
export function memberBatchSave(id, data) {
    return request({
        url: `/user/member_batch/save/${id}`,
        method: 'post',
        data
    });
}

/**
 * 列表操作（启用，名称修改）
 * @param {*} id id
 */
export function memberBatchSetValue(id, data) {
    return request({
        url: `/user/member_batch/set_value/${id}`,
        method: 'get',
        params: data
    });
}

/**
 * 会员卡列表
 * @param {*} id id
 */
export function userMemberCard(id, data) {
    return request({
        url: `/user/member_card/index/${id}`,
        method: 'get',
        params: data
    });
}

/**
 * 会员卡导出
 * @param {*} id id
 */
export function exportMemberCard(id, data) {
    return request({
        url: `/export/memberCard/${id}`,
        method: 'get',
        params: data
    });
}

/**
 * 会员类型
 */
export function userMemberShip() {
    return request({
        url: '/user/member/ship',
        method: 'get'
    });
}

/**
 * 编辑会员类型
 * @param {*} id id
 * @param {*} data data
 */
export function memberShipSave(id, data) {
    return request({
        url: `/user/member_ship/save/${id}`,
        method: 'post',
        data
    });
}

/**
 * 同步用户
 */
export function userSynchro() {
    return request({
        url: `/user/user/syncUsers`,
        method: 'get'
    });
}

/**
 * 添加用户
 */
export function getUserSaveForm() {
    return request({
        url: `/user/user/create`,
        method: 'get'
    });
}

/**
 * 兑换会员卡二维码
 */
export function userMemberScan() {
    return request({
        url: '/user/member_scan',
        method: 'get'
    });
}

/**
 * 设置用户标签
 */
export function putUserLabel(uid, data) {
    return request({
        url: `user/label/${uid}`,
        method: 'post',
        data
    });
}

/**
 * 会员权益编辑
 * @param {*} data
 */
export function memberRightSave(data) {
    return request({
        url: `user/member_right/save/${data.id}`,
        method: 'post',
        data
    })
}

/**
 * 会员权益
 */
export function memberRight() {
    return request({
        url: 'user/member/right',
        method: 'get'
    });
}

/**
 * 会员卡记录
 */
export function memberRecord(data) {
    return request({
        url: '/user/member/record',
        method: 'get',
        params: data
    });
}
/**
 * 会员记录选择类型
 */
export function memberTab() {
    return request({
        url: 'user/member/ship_select',
        method: 'get'
    });
}
/**
 * @description 会员列表开关
 * @param {Number} param id {Number}
 */
export function memberCardStatus(data) {
    return request({
        url: `user/member_card/set_status`,
        method: 'get',
        params: data
    })
}

/**
 * @description 开关
 * @param {Number} param id {Number}
 */
export function memberCard(data) {
    return request({
        url: `user/member_ship/set_ship_status`,
        method: 'get',
        params: data
    })
}

/**
 * 编辑会员协议
 * @param {*} id
 */
export function memberAgreementSave(id, data) {
    return request({
        url: `user/member_agreement/save/${id}`,
        method: 'post',
        data
    });
}

/**
 * 会员协议
 */
export function memberAgreement() {
    return request({
        url: `user/member/agreement`,
        method: 'get'
    });
}

/**
 * @description 会员管理 --- 赠送会员时长
 * @param {Number} param id {Number} 会员id
 */
export function giveLevelTimeApi(id) {
    return request({
        url: `user/give_level_time/${id}`,
        method: 'get'
    })
}

/**
 * 获取用户标签
 */
export function getUserLabel(uid) {
    return request({
        url: `user/label/${uid}`,
        method: 'get'
    });
}

/**
 * 批量提交用户标签
 */
export function putUsersSetLabel(data) {
    return request({
        url: `user/save_set_label`,
        method: 'put',
		data
    });
}

/**
 * 设置用户标签
 */
 export function deleteCard (id) {
    return request({
        url: `user/member_ship/delete/${id}`,
        method: 'DELETE'
    });
}

/**
 * 个人中心
 */
 export function setInfo (id) {
    return request({
        url: `setting/info`,
        method: 'get'
    });
}

/**
 * 浏览记录
 * @param {*} id 
 * @returns 
 */
export function visitList (data) {
    return request({
        url: `user/visit_list/${data.id}`,
        method: 'get',
        params: data.datas
    });
}

/**
 * 推荐人记录
 * @param {*} id 
 * @returns 
 */
export function spreadList (data) {
    return request({
        url: `user/spread_list/${data.id}`,
        method: 'get',
        params: data.datas
    });
}

/**
 * 保存用户信息
 * @param {*} data 
 * @returns 
 */
export function putUserApi(data) {
    return request({
        url: `user/user/${data.uid}`,
        method: 'put',
        data
    });
}

/**
 * 编辑分销说明
 * @param {*} id
 */
 export function agentAgreementSave(id, data) {
    return request({
        url: `agent/set_agent_agreement/${id}`,
        method: 'post',
        data
    });
}

/**
 * 分销说明
 */
export function agentAgreement() {
    return request({
        url: `agent/get_agent_agreement`,
        method: 'get'
    });
}

/**
 * @description 同步企业微信标签
 */
 export function workLabelSync() {
    return request({
        url: `user/synchro/work/label`,
        method: 'get'
    });
}