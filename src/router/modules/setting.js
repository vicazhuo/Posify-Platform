// +----------------------------------------------------------------------
// | CRMEB [ CRMEB赋能开发者，助力企业发展 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2021 https://www.crmeb.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed CRMEB并不是自由软件，未经许可不能去掉CRMEB相关版权
// +----------------------------------------------------------------------
// | Author: CRMEB Team <admin@crmeb.com>
// +----------------------------------------------------------------------
import BasicLayout from '@/layouts/basic-layout';

const meta = {
    auth: true
};

const pre = 'setting_';

export default {
    path: '/admin/setting',
    name: 'setting',
    header: 'setting',
    redirect: {
        name: `${pre}shop_base`
    },
    component: BasicLayout,
    children: [
				{
						path: 'system_config',
						name: `${pre}setSystem`,
						meta: {
								auth: ['setting-system-config'],
								title: '系统设置'
						},
						component: () => import('@/pages/setting/setSystem/index')
				},
        {
            path: 'shop/base',
            name: `${pre}shop_base`,
            meta: {
                auth: ['setting-shop-base'],
                title: '基础设置'
            },
            component: () => import('@/pages/setting/shop/base')
        },
        {
            path: 'shop/trade',
            name: `${pre}shop_trade`,
            meta: {
                auth: ['setting-shop-trade'],
                title: '交易设置'
            },
            component: () => import('@/pages/setting/shop/trade')
        },
        {
            path: 'shop/pay',
            name: `${pre}shop_pay`,
            meta: {
                auth: ['setting-shop-pay'],
                title: '支付设置'
            },
            component: () => import('@/pages/setting/shop/pay')
        },
        {
            path: 'shop/agreemant',
            name: `${pre}shop_agreemant`,
            meta: {
                auth: ['setting-shop-agreement'],
                title: '政策协议'
            },
            component: () => import('@/pages/setting/shop/agreemant')
        },
        {
            path: 'third_party',
            name: `${pre}third_party`,
            meta: {
                auth: ['setting-third-party'],
                title: '身份管理'
            },
            component: () => import('@/pages/setting/third_party/index')
        },
        {
            path: 'distribution/deliver',
            name: `${pre}distributionDeliver`,
            meta: {
                auth: ['setting-distribution-deliver'],
                title: '发货设置'
            },
            component: () => import('@/pages/setting/distribution/deliver')
        },
        {
            path: 'system_role/index',
            name: `${pre}systemRole`,
            meta: {
                auth: ['setting-system-role'],
                title: '身份管理'
            },
            component: () => import('@/pages/setting/systemRole/index')
        },
        {
            path: 'system_admin/index',
            name: `${pre}systemAdmin`,
            meta: {
                auth: ['setting-system-list'],
                title: '管理员列表'
            },
            component: () => import('@/pages/setting/systemAdmin/index')
        },
        {
            path: 'system_menus/index',
            name: `${pre}systemMenus`,
            meta: {
                auth: ['setting-system-menus'],
                title: '权限规则'
            },
            component: () => import('@/pages/setting/systemMenus/index')
        },
        {
            path: 'notification/index',
            name: `${pre}notification`,
            meta: {
                auth: ['setting-system-config'],
                title: '消息管理'
            },
            component: () => import('@/pages/setting/notification/index')
        },
        {
            path: 'notification/notificationEdit',
            name: `${pre}notificationEdit`,
            meta: {
                auth: ['setting-notification'],
                title: '消息编辑'
            },
            component: () => import('@/pages/setting/notification/notificationEdit')
        },
        {
            path: 'system_config/:type?/:tab_id?',
            name: `${pre}setApp`,
            meta: {
                ...meta,
                title: '应用设置'
            },
            component: () => import('@/pages/setting/setSystem/index')
        },
        {
            path: 'system_config/payment/:type?/:tab_id?',
            name: `${pre}payment`,
            meta: {
                ...meta,
                title: '支付配置'
            },
            component: () => import('@/pages/setting/setSystem/index')
        },
        {
            path: 'system_config_retail/:type?/:tab_id?',
            name: `${pre}distributionSet`,
            meta: {
                ...meta,
                title: '分销设置'
            },
            props: {
                typeMole: 'distribution'
            },
            component: () => import('@/components/fromSubmit/commonForm.vue')
            // component: () => import('@/pages/setting/setSystem/index')
        },
        {
            path: 'membership_level/index',
            name: `${pre}membershipLevel`,
            meta: {
                ...meta,
                title: '分销等级'
            },
            component: () => import('@/pages/setting/membershipLevel/index')
        },
        {
            path: 'system_config_message/:type?/:tab_id?',
            name: `${pre}message`,
            meta: {
                auth: ['setting-system-config-message'],
                title: '短信开关'
            },
            component: () => import('@/pages/setting/setSystem/index')
        },
        {
            path: 'system_config_logistics/:type?/:tab_id?',
            name: `${pre}logistics`,
            meta: {
                auth: ['setting-system-config-logistics'],
                title: '物流配置'
            },
            component: () => import('@/pages/setting/setSystem/index')
        },
        {
            path: 'sms/sms_config/index',
            name: `${pre}config`,
            meta: {
                auth: ['setting-sms-sms-config'],
                title: '一号通账户'
            },
            component: () => import('@/pages/notify/smsConfig/index')
        },
        {
            path: 'sms/sms_template_apply/index',
            name: `${pre}smsTemplateApply`,
            meta: {
                auth: ['setting-sms-config-template'],
                title: '短信模板'
            },
            component: () => import('@/pages/notify/smsTemplateApply/index')
        },
        {
            path: 'sms/sms_pay/index',
            name: `${pre}smsPay`,
            meta: {
                auth: ['setting-sms-sms-template'],
                title: '套餐购买'
            },
            component: () => import('@/pages/notify/smsPay/index')
        },
        {
            path: 'sms/sms_template_apply/commons',
            name: `${pre}commons`,
            meta: {
                ...meta,
                title: '公共短信模板'
            },
            component: () => import('@/pages/notify/smsTemplateApply/index')
        },
        {
            path: 'system_group_data/index/:id',
            name: `${pre}groupDataIndex`,
            meta: {
                auth: ['setting-system-group_data-index'],
                title: '首页导航按钮'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/slide/:id',
            name: `${pre}groupDataSlide`,
            meta: {
                auth: ['setting-system-group_data-slide'],
                title: '首页幻灯片'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/sign/:id',
            name: `${pre}groupDataSign`,
            meta: {
                auth: ['setting-system-group_data-sign'],
                title: '签到天数配置'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/order/:id',
            name: `${pre}groupDataOrder`,
            meta: {
                auth: ['setting-system-group_data-order'],
                title: '订单详情动态图'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/user/:id',
            name: `${pre}groupDataUser`,
            meta: {
                auth: ['setting-system-group_data-user'],
                title: '个人中心菜单'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/new/:id',
            name: `${pre}groupDataNew`,
            meta: {
                auth: ['setting-system-group_data-new'],
                title: '首页滚动新闻'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/search/:id',
            name: `${pre}groupDataNewSearch`,
            meta: {
                auth: ['setting-system-group_data-search'],
                title: '热门搜索'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/hot/:id',
            name: `${pre}groupDataHot`,
            meta: {
                auth: ['setting-system-group_data-hot'],
                title: '热门榜单推荐'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/new_product/:id',
            name: `${pre}groupDataNewProduct`,
            meta: {
                auth: ['setting-system-group_data-new_product'],
                title: '首发新品推荐'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/promotion/:id',
            name: `${pre}groupDataPromotion`,
            meta: {
                auth: ['setting-system-group_data-promotion'],
                title: '促销单品推荐'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/poster/:id',
            name: `${pre}groupDataPoster`,
            meta: {
                auth: ['setting-system-group_data-poster'],
                title: '个人中心分销海报'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/best/:id',
            name: `${pre}groupDataBest`,
            meta: {
                auth: ['setting-system-group_data-best'],
                title: '精品推荐'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/activity/:id',
            name: `${pre}groupDataActivity`,
            meta: {
                auth: ['setting-system-group_data-activity'],
                title: '首页活动区域图片'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/system/:id',
            name: `${pre}groupDataSystem`,
            meta: {
                auth: ['setting-system-group_data-system'],
                title: '首页配置'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data/hot_money/:id',
            name: `${pre}groupDataHotMoney`,
            meta: {
                auth: ['admin-setting-system_group_data-hot_money'],
                title: '首页超值爆款'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_group_data',
            name: `${pre}systemGroupData`,
            meta: {
                auth: ['admin-setting-pages-links'],
                title: '数据配置'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'merchant/system_store/index',
            name: `${pre}systemStore`,
            meta: {
                auth: ['setting-system-config-merchant'],
                title: '门店设置'
            },
            component: () => import('@/pages/setting/systemStore/index')
        },
        {
            path: 'freight/express/index',
            name: `${pre}freight`,
            meta: {
                auth: ['setting-freight-express'],
                title: '物流公司'
            },
            component: () => import('@/pages/setting/freight/index')
        },
        {
            path: 'store_service/index',
            name: `${pre}service`,
            meta: {
                auth: ['admin-setting-store_service-index'],
                title: '客服管理'
            },
            component: () => import('@/pages/setting/storeService/index')
        },
        {
            path: 'store_service/speechcraft',
            name: `${pre}speechcraft`,
            meta: {
                auth: ['admin-setting-store_service-speechcraft'],
                title: '客服话术'
            },
            component: () => import('@/pages/setting/storeService/speechcraft')
        },
        {
            path: 'store_service/feedback',
            name: `${pre}feedback`,
            meta: {
                auth: ['admin-setting-store_service-feedback'],
                title: '用户留言'
            },
            component: () => import('@/pages/setting/storeService/feedback')
        },
        {
            path: 'freight/city/list',
            name: `${pre}dada`,
            meta: {
                auth: ['setting-system-city'],
                title: '城市数据'
            },
            component: () => import('@/pages/setting/cityDada/index')
        },
        {
            path: 'freight/shipping_templates/list',
            name: `${pre}templates`,
            meta: {
                auth: ['setting-shipping-templates'],
                title: '运费模板'
            },
            component: () => import('@/pages/setting/shippingTemplates/index')
        },
        {
            path: 'merchant/system_store/list',
            name: `${pre}store`,
            meta: {
                auth: ['setting-merchant-system-store'],
                title: '提货点'
            },
            component: () => import('@/pages/setting/storeList/index')
        },
        {
            path: 'merchant/system_store_staff/index',
            name: `${pre}staff`,
            meta: {
                auth: ['setting-merchant-system-store-staff'],
                title: '核销员'
            },
            component: () => import('@/pages/setting/clerkList/index')
        },
        {
            path: 'merchant/system_verify_order/index',
            name: `${pre}order`,
            meta: {
                auth: ['setting-merchant-system-verify-order'],
                title: '核销订单'
            },
            component: () => import('@/pages/setting/verifyOrder/index')
        },
        // {
        //     path: 'pages/diy',
        //     name: `${pre}diy`,
        //     meta: {
        //         auth: ['admin-setting-pages-diy'],
        //         title: '首页装修'
        //     },
        //     component: () => import('@/pages/setting/devise/index')
        // },
        {
            path: 'pages/special',
            name: `${pre}special`,
            meta: {
                auth: ['setting-diy-special'],
                title: '专题页设计'
            },
            component: () => import('@/pages/setting/special/list')
        },
        // {
        //     path: 'pages/special/diy',
        //     name: `${pre}special_diy`,
        //     meta: {
        //         auth: ['setting-diy-special-diy'],
        //         title: '专题页设计'
        //     },
        //     component: () => import('@/pages/setting/special/index')
        // },
        {
            path: 'pages/links',
            name: `${pre}links`,
            meta: {
                auth: ['admin-setting-pages-links'],
                title: '页面链接'
            },
            component: () => import('@/pages/setting/devise/links')
        },
        {
            path: 'pages/template',
            name: `${pre}template`,
            meta: {
                auth: ['setting-pages-template'],
                title: '模板'
            },
            component: () => import('@/pages/setting/devise/template')
        },
        {
            path: 'system_group_data/kf_adv',
            name: `${pre}kfAdv`,
            meta: {
                auth: ['setting-system-group_data-kf_adv'],
                title: '客服页面广告'
            },
            component: () => import('@/pages/system/group/kfAdv')
        },
        {
            path: 'userAgreement/index',
            name: `${pre}userAgreement`,
            meta: {
                auth: ['setting-system-group_data-kf_adv'],
                title: '隐私协议'
            },
            component: () => import('@/pages/setting/userAgreement/index.vue')
        },
        {
            path: 'system_group_data/pc/:id',
            name: `${pre}groupDataPc`,
            meta: {
                auth: ['setting-system-group_data-pc'],
                title: 'PC主页轮播'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'system_config_member_right/:type?/:tab_id?',
            name: `${pre}right`,
            meta: {
                auth: ['setting-system-config-member-right'],
                title: '会员权益'
            },
            component: () => import('@/pages/setting/setSystem/index')
        },
        {
            path: 'delivery_service/index',
            name: `${pre}deliveryService`,
            meta: {
                auth: ['setting-delivery-service'],
                title: '配送员列表'
            },
            component: () => import('@/pages/setting/deliveryService/index')
        },
		//平台零时
		{
		    path: 'platform/index',
		    name: `${pre}platform`,
		    meta: {
		        auth: ['setting-freight-express'],
		        title: '门店'
		    },
		    component: () => import('@/pages/platform/index/index')
		},
		{
		    path: 'platform/list/index',
		    name: `${pre}list`,
		    meta: {
		        auth: ['setting-freight-express'],
		        title: '门店列表'
		    },
		    component: () => import('@/pages/platform/list/index')
		},
		{
		    path: 'platform/order/index',
		    name: `${pre}orderExpress`,
		    meta: {
		        auth: ['setting-freight-express'],
		        title: '门店订单'
		    },
		    component: () => import('@/pages/platform/order/index')
		},
		{
		    path: 'platform/bill/index',
		    name: `${pre}bill`,
		    meta: {
		        auth: ['setting-freight-express'],
		        title: '账单记录'
		    },
		    component: () => import('@/pages/platform/bill/index')
		},
		{
		    path: 'platform/setting/index',
		    name: `${pre}setting`,
		    meta: {
		        auth: ['setting-freight-express'],
		        title: '财务设置'
		    },
		    component: () => import('@/pages/platform/setting/index')
		},
        {
		    path: 'storage',
		    name: `${pre}storage`,
		    meta: {
		        // auth: ['setting-storage'],
		        title: '存储设置'
		    },
		    component: () => import('@/pages/setting/storage/index')
		}
    ]
};
