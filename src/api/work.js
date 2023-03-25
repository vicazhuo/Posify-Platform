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
 * @description 企业微信组织架构
 */
export function workTree() {
    return request({
        url: 'work/tree',
        method: 'get'
    });
}

/**
 * @description 
 */
 export function workLabel() {
    return request({
        url: 'work/label',
        method: 'get'
    });
}

// adminapi/work/synchMember
/**
 * @description 同步企业微信成员
 */
 export function workSynchMember() {
    return request({
        url: 'work/synchMember',
        method: 'post'
    });
}

/**
 * @description 获取渠道二维码分类列表
 */
 export function workChannelCate() {
    return request({
        url: 'work/channel/cate',
        method: 'get'
    });
}

/**
 * @description 获取创建渠道二维码分类表单
 */
 export function workCateCreate() {
    return request({
        url: 'work/channel/cate/create',
        method: 'get'
    });
}

/**
 * @description 获取修改渠道二维码分类表单
 */
 export function workCateEdit(id) {
    return request({
        url: `/work/channel/cate/${id}/edit`,
        method: 'get'
    });
}

/**
 * @description 删除渠道二维码分类
 */
 export function workCateDel(id) {
    return request({
        url: `/work/channel/cate/${id}`,
        method: 'delete'
    });
}

/**
 * @description 获取渠道二维码列表
 */
 export function workChannelCode(data) {
    return request({
        url: 'work/channel/code',
        method: 'get',
        params: data
    });
}

/**
 * @description 保存渠道二维码
 */
 export function saveWorkCode(data) {
    return request({
        url: `work/channel/code`,
        method: 'post',
        data
    });
}

/**
 * @description 获取渠道二维码
 */
 export function getWorkCodeInfo(id) {
    return request({
        url: `work/channel/code/${id}`,
        method: 'get',
    });
}

/**
 * @description 获取扫描渠道码添加的客户列表
 */
 export function getWorkCodeClient(params) {
    return request({
        url: `work/channel/code/client`,
        method: 'get',
        params
    });
}

/**
 * @description 删除渠道二维码
 */
 export function workCodeDelete(id) {
    return request({
        url: `work/channel/code/${id}`,
        method: 'delete',
    });
}

/**
 * @description 修改渠道二维码
 */
 export function workCodeUpdate(id,data) {
    return request({
        url: `work/channel/code/${id}`,
        method: 'put',
        data
    });
}

/**
 * @description批量移动分类
 */
 export function workBranchCate(data) {
    return request({
        url: `work/channel/code/bactch/cate`,
        method: 'post',
        data
    });
}

/**
 * @description 获取企业微信部门
 */
 export function workDepartment() {
    return request({
        url: `work/department`,
        method: 'get'
    });
}

/**
 * @description 获取企业微信员工列表
 */
 export function workMember(params) {
    return request({
        url: `work/member`,
        method: 'get',
        params
    });
}

/**
 * @description 获取企业微信客户群聊
 */
 export function workGroupChat(params) {
    return request({
        url: `work/group_chat`,
        method: 'get',
        params
    });
}

/**
 * @description 同步客户群
 */
 export function workGroupSynch() {
    return request({
        url: `work/group_chat/synch`,
        method: 'post'
    });
}

/**
 * @description 保存欢迎语
 */
 export function workWelcomeSave(data) {
    return request({
        url: `work/welcome`,
        method: 'post',
        data
    });
}

/**
 * @description 获取欢迎语列表
 */
 export function getWelcomeList(params) {
    return request({
        url: `work/welcome`,
        method: 'get',
        params
    });
}

/**
 * @description 欢迎语详情
 */
 export function getWelcomeInfo(id) {
    return request({
        url: `work/welcome/${id}`,
        method: 'get',
    });
}

/**
 * @description 修改欢迎语
 */
 export function welcomeUpdate(id,data) {
    return request({
        url: `work/welcome/${id}`,
        method: 'put',
        data
    });
}

/**
 * @description 保存自动拉群配置
 */
 export function groupChatAuthSave(data) {
    return request({
        url: `work/group_chat_auth`,
        method: 'post',
        data
    });
}

/**
 * @description 获取欢自动拉群配置列表
 */
 export function getGroupChatList(params) {
    return request({
        url: `work/group_chat_auth`,
        method: 'get',
        params
    });
}

/**
 * @description 获取欢自动拉群详情
 */
 export function getGroupChatInfo(id) {
    return request({
        url: `work/group_chat_auth/${id}`,
        method: 'get',
    });
}

/**
 * @description 修改自动拉群配置
 */
 export function UpdateGroupChat(id,data) {
    return request({
        url: `work/group_chat_auth/${id}`,
        method: 'put',
        data
    });
}

/**
 * @description 企业微信客户列表
 */
 export function getWorkClientList(params) {
    return request({
        url: `work/client`,
        method: 'get',
        params
    });
}

/**
 * @description 获取企业微信客户群聊成员
 */
 export function getGroupChatMember(params) {
    return request({
        url: `work/group_chat/member`,
        method: 'get',
        params
    });
}

/**
 * @description 同步企业微信客户
 */
 export function workClientSynch() {
    return request({
        url: `work/client/synch`,
        method: 'get'
    });
}

/**
 * @description 客户群统计
 */
 export function workGroupStatistics(params) {
    return request({
        url: `work/group_chat/statistics`,
        method: 'get',
        params
    });
}

/**
 * @description 客户群统计列表数据
 */
 export function workGroupStatisticsList(params) {
    return request({
        url: `work/group_chat/statisticsList`,
        method: 'get',
        params
    });
}

/**
 * @description 获取客户群发列表
 */
 export function getGroupTemplateList(params) {
    return request({
        url: `work/group_template`,
        method: 'get',
        params
    });
}

/**
 * @description 保存客户群发
 */
 export function workGroupTemplateSave(data) {
    return request({
        url: `work/group_template`,
        method: 'post',
        data
    });
}

/**
 * @description 客户群发详情
 */
 export function workGroupTemplateInfo(id) {
    return request({
        url: `work/group_template/${id}`,
        method: 'get',
    });
}

/**
 * @description 删除客户群发
 */
 export function workGroupTemplateDelete(id) {
    return request({
        url: `work/group_template/${id}`,
        method: 'delete',
    });
}

/**
 * @description 企业微信朋友圈列表
 */
 export function getWorkMomenttList(params) {
    return request({
        url: `work/moment`,
        method: 'get',
        params
    });
}

/**
 * @description 保存企业微信发送朋友圈
 */
 export function workMomentSave(data) {
    return request({
        url: `/work/moment`,
        method: 'post',
        data
    });
}

/**
 * @description 获取客户群群发列表
 */
export function getGroupTemplateChatList(params) {
    return request({
        url: `work/group_template_chat`,
        method: 'get',
        params
    });
}

/**
 * @description 保存客户群群发
 */
export function workGroupTemplateChatSave(data) {
    return request({
        url: `work/group_template_chat`,
        method: 'post',
        data
    });
}

/**
 * @description 删除客户群群发
 */
 export function workGroupTemplateChatDelete(id) {
    return request({
        url: `work/group_template_chat/${id}`,
        method: 'delete',
    });
}

/**
 * @description 获取客户群群发详情
 */
export function workGroupTemplateChatInfo(id) {
    return request({
        url: `work/group_template_chat/${id}`,
        method: 'get',
    });
}

/**
 * @description 提醒发送
 */
 export function workGroupTemplateSendMsg(data) {
    return request({
        url: `work/group_template/sendMessage`,
        method: 'post',
        data
    });
}

/**
 * @description 查找成员下的跟踪客户
 */
 export function workClientCount(data) {
    return request({
        url: `work/client/count`,
        method: 'post',
        data
    });
}

/**
 * @description 获取群发成员列表
 */
 export function groupTemplateMemberList(id,params) {
    return request({
        url: `work/group_template/memberList/${id}`,
        method: 'get',
        params
    });
}

/**
 * @description 获取群发客户列表
 */
 export function groupTemplateClientList(id,params) {
    return request({
        url: `work/group_template/clientList/${id}`,
        method: 'get',
        params
    });
}

/**
 * @description 获取群发群列表
 */
 export function groupChatList(id,params) {
    return request({
        url: `work/group_template_chat/groupChatList/${id}`,
        method: 'get',
        params
    });
}

/**
 * @description 获取群发群主列表
 */
export function groupChatOwnerList(id,params) {
    return request({
        url: `work/group_template_chat/groupChatOwnerList/${id}`,
        method: 'get',
        params
    });
}

/**
 * @description 获取群发群主下的群列表
 */
export function groupOwnerChatList(params) {
    return request({
        url: `work/group_template_chat/getOwnerChatList`,
        method: 'get',
        params
    });
}
    
/**
 * @description 批量设置企业微信客户标签
 */
export function workClientBatchLabel(data) {
     return request({
         url: `work/client/batchLabel`,
         method: 'post',
         data
        });
    }

/**
 * @description 企业微信发送朋友圈详情
 */
 export function workMomentInfo(id) {
    return request({
        url: `work/moment/${id}`,
        method: 'get'
    });
}

/**
 * @description 获取企业微信发送过朋友圈成员列表
 */
 export function workMomentList(params) {
    return request({
        url: `work/moment_list`,
        method: 'get',
        params
    });
}

/**
 * @description 修改企业微信客户
 */
 export function workClientEdit(id,data) {
    return request({
        url: `work/client/${id}`,
        method: 'put',
        data
    });
}
