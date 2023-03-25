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

const pre = 'marketing_';

export default {
    path: '/admin/marketing',
    name: 'marketing',
    header: 'marketing',
    redirect: {
        name: `${pre}storeSeckill`
    },
    component: BasicLayout,
    children: [
        {
            path: 'store_combination/index',
            name: `${pre}combinalist`,
            meta: {
                auth: ['marketing-store_combination'],
                title: '拼团商品'
            },
            component: () => import('@/pages/marketing/storeCombination/index')
        },
        {
            path: 'store_combination/combina_list',
            name: `${pre}combinaList`,
            meta: {
                auth: ['marketing-store_combination-combina_list'],
                title: '拼团列表'
            },
            component: () => import('@/pages/marketing/storeCombination/combinaList')
        },
        {
            path: 'store_combination/create/:id?/:copy?',
            name: `${pre}storeCombinationCreate`,
            meta: {
                auth: ['marketing-store_combination-create'],
                title: '添加拼团'
            },
            component: () => import('@/pages/marketing/storeCombination/create')
        },
        {
            path: 'store_coupon/index',
            name: `${pre}storeCoupon`,
            meta: {
                auth: ['marketing-store_coupon'],
                title: '优惠券模板'
            },
            component: () => import('@/pages/marketing/storeCoupon/index')
        },
        {
            path: 'store_coupon_issue/index',
            name: `${pre}storeCouponIssue`,
            meta: {
                auth: ['marketing-store_coupon_issue'],
                title: '优惠券列表'
            },
            component: () => import('@/pages/marketing/storeCouponIssue/index')
        },
        {
            path: 'store_coupon_issue/create/:id?',
            name: `${pre}storeCouponCreate`,
            meta: {
                auth: ['admin-marketing-store_coupon_issue-create'],
                title: '添加优惠券'
            },
            component: () => import('@/pages/marketing/storeCouponIssue/create')
        },
				{
				    path: 'discount/list',
				    name: `${pre}discountList`,
				    meta: {
				        auth: ['marketing-discount-list'],
				        title: '限时折扣'
				    },
				    component: () => import('@/pages/marketing/discount/index')
				},
				{
				    path: 'discount/give',
				    name: `${pre}discountGive`,
				    meta: {
				        auth: ['marketing-discount-give'],
				        title: '满送活动'
				    },
				    component: () => import('@/pages/marketing/fullDelivery/index')
				},
				{
				    path: 'discount/add_give/:id?',
				    name: `${pre}addDelivery`,
				    meta: {
				        auth: ['marketing-discount-add_give'],
				        title: '添加满送活动'
				    },
				    component: () => import('@/pages/marketing/addDelivery/index')
				},
				{
				    path: 'discount/full_discount',
				    name: `${pre}fullDiscount`,
				    meta: {
				        auth: ['marketing-discount-full_discount'],
				        title: '满减满折'
				    },
				    component: () => import('@/pages/marketing/fullDiscount/index')
				},
				{
				    path: 'discount/add_discount/:id?',
				    name: `${pre}addFullDiscount`,
				    meta: {
				        auth: ['marketing-discount-add_discount'],
				        title: '添加满减满折'
				    },
				    component: () => import('@/pages/marketing/addFullDiscount/index')
				},
				{
				    path: 'discount/add/:id?',
				    name: `${pre}fullDiscount_add`,
				    meta: {
				        auth: ['marketing-discount-add'],
				        title: '添加限时折扣'
				    },
				    component: () => import('@/pages/marketing/addDiscount/index')
				},
				{
				    path: 'discount/pieces_discount',
				    name: `${pre}piecesDiscount`,
				    meta: {
				        auth: ['marketing-discount-pieces_discount'],
				        title: '第N件N折'
				    },
				    component: () => import('@/pages/marketing/piecesDiscount/index')
				},
				{
				    path: 'discount/add_pieces/:id?',
				    name: `${pre}addPiecesDiscount`,
				    meta: {
				        auth: ['marketing-discount-add_pieces'],
				        title: '添加第N件N折'
				    },
				    component: () => import('@/pages/marketing/addPiecesDiscount/index')
				},
        {
            path: 'store_coupon_user/index',
            name: `${pre}storeCouponUser`,
            meta: {
                auth: ['marketing-store_coupon_user'],
                title: '用户领取记录'
            },
            component: () => import('@/pages/marketing/storeCouponUser/index')
        },
        {
            path: 'coupon/system_config/:type?/:tab_id?',
            name: `${pre}coupon`,
            meta: {
                auth: ['admin-order-storeOrder-index'],
                title: '优惠券配置'
            },
            component: () => import('@/pages/setting/setSystem/index')
        },
        {
            path: 'store_bargain/index',
            name: `${pre}storeBargain`,
            meta: {
                auth: ['marketing-store_bargain'],
                title: '砍价商品'
            },
            component: () => import('@/pages/marketing/storeBargain/index')
        },
        {
            path: 'store_bargain/setting',
            name: `${pre}storeBargainSetting`,
            meta: {
                auth: ['marketing-store_bargain-setting'],
                title: '砍价设置'
            },
            props: {
                typeMole: 'bargain'
            },
            component: () => import('@/components/fromSubmit/commonForm.vue')
        },
        {
            path: 'store_bargain/bargain_list',
            name: `${pre}bargainList`,
            meta: {
                auth: ['marketing-store_bargain-bargain_list'],
                title: '砍价列表'
            },
            component: () => import('@/pages/marketing/storeBargain/bargainList')
        },
        {
            path: 'store_bargain/create/:id?/:copy?',
            name: `${pre}storeBargainCreate`,
            meta: {
                auth: ['marketing-store_bargain-create'],
                title: '添加砍价'
            },
            component: () => import('@/pages/marketing/storeBargain/create')
        },
        {
            path: 'store_seckill/index',
            name: `${pre}storeSeckill`,
            meta: {
                auth: ['marketing-store_seckill'],
                title: '秒杀商品'
            },
            component: () => import('@/pages/marketing/storeSeckill/index')
        },
        {
            path: 'store_seckill_data/index/:id',
            name: `${pre}storeSeckillData`,
            meta: {
                auth: ['marketing-store_seckill-data'],
                title: '秒杀配置'
            },
            component: () => import('@/pages/system/group/list')
        },
        {
            path: 'store_seckill/create/:id?/:copy?',
            name: `${pre}storeSeckillCreate`,
            meta: {
                auth: ['marketing-store_seckill-create'],
                title: '添加秒杀'
            },
            component: () => import('@/pages/marketing/storeSeckill/create')
        },
        {
            path: `integral/system_config/:type?/:tab_id?`,
            name: `${pre}integral`,
            meta: {
                auth: ['marketing-integral-system_config'],
                title: '积分配置'
            },
            props: {
                typeMole: 'integral'
            },
            component: () => import('@/components/fromSubmit/commonForm.vue')
            // component: () => import('@/pages/setting/setSystem/index')
        },
        {
            path: 'user_point/index',
            name: `${pre}userPoint`,
            meta: {
                auth: ['marketing-user_point'],
                title: '积分日志'
            },
            component: () => import('@/pages/marketing/userPoint/index')
        },
		{
		    path: 'integral/signIn',
		    name: `${pre}signIn`,
		    meta: {
		        auth: ['marketing-integral-sign'],
		        title: '积分签到'
		    },
		    component: () => import('@/pages/marketing/signIn/index')
		},
		{
		    path: 'balance_recharge',
		    name: `${pre}recharge`,
		    meta: {
		        auth: ['marketing-balance_recharge'],
		        title: '充值金额'
		    },
		    component: () => import('@/pages/marketing/recharge/index')
		},
        {
            path: 'setup_recharge',
            name: `${pre}setup_recharge`,
            meta: {
                auth: ['marketing-setup-recharge'],
                title: '充值金额'
            },
            props: {
                typeMole: 'recharge'
            },
            component: () => import('@/components/fromSubmit/commonForm.vue')
        },
        {
            path: 'live/live_room',
            name: `${pre}live_room`,
            meta: {
                auth: true,
                title: '直播间管理'
            },
            component: () => import('@/pages/marketing/live/index')
        },
        {
            path: 'live/add_live_room',
            name: `${pre}add_live_room`,
            meta: {
                auth: true,
                title: '直播间管理'
            },
            component: () => import('@/pages/marketing/live/creat_live')
        },
        {
            path: 'live/live_goods',
            name: `${pre}live_goods`,
            meta: {
                auth: true,
                title: '直播间商品管理'
            },
            component: () => import('@/pages/marketing/live/live_goods')
        },
        {
            path: 'live/add_live_goods',
            name: `${pre}add_live_goods`,
            meta: {
                auth: true,
                title: '直播间商品管理'
            },
            component: () => import('@/pages/marketing/live/add_goods')
        },
        {
            path: 'live/anchor',
            name: `${pre}anchor`,
            meta: {
                auth: true,
                title: '主播管理'
            },
            component: () => import('@/pages/marketing/live/anchor')
        },
        {
            path: 'lottery/index',
            name: `${pre}lottery`,
            meta: {
                auth: true,
                title: '抽奖列表'
            },
            component: () => import('@/pages/marketing/lottery/index')
        },
        {
            path: 'lottery/create',
            name: `${pre}create`,
            meta: {
                auth: true,
                title: '创建抽奖'
            },
            component: () => import('@/pages/marketing/lottery/create')
        },
        {
            path: 'lottery/recording_list',
            name: `${pre}recording_list`,
            meta: {
                auth: ['admin-marketing-lottery-recording_list'],
                title: '中奖记录'
            },
            component: () => import('@/pages/marketing/lottery/recordingList')
        },
        {
            path: 'store_discounts/index',
            name: `${pre}store_discounts`,
            meta: {
                auth: true,
                title: '套餐列表'
            },
            component: () => import('@/pages/marketing/storeDiscounts/index')
        },
        {
            path: 'store_discounts/create',
            name: `${pre}store_discounts_create`,
            meta: {
                auth: true,
                title: '创建套餐'
            },
            component: () => import('@/pages/marketing/storeDiscounts/create')
        },
        {
            path: 'store_integral/index',
            name: `${pre}storeIntegral`,
            meta: {
                auth: ['marketing-store_integral'],
                title: '积分商品'
            },
            component: () => import('@/pages/marketing/storeIntegral/index')
        },
        {
            path: 'store_integral/create/:id?/:copy?',
            name: `${pre}storeIntegralCreate`,
            meta: {
                auth: ['marketing-store_integral-create'],
                title: '添加积分商品'
            },
            component: () => import('@/pages/marketing/storeIntegral/create')
        },
        {
            path: 'store_integral/add_store_integral',
            name: `${pre}addStoreIntegral`,
            meta: {
                auth: ['marketing-store_integral-create'],
                title: '批量添加积分商品'
            },
            component: () => import('@/pages/marketing/storeIntegral/addStoreIntegral')
        },
        {
            path: 'store_integral/order_list',
            name: `${pre}storeIntegralOrder`,
            meta: {
                auth: ['marketing-store_integral-order'],
                title: '兑换订单'
            },
            component: () => import('@/pages/marketing/storeIntegralOrder/index')
        },
    ]
};
