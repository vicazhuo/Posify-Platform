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

const pre = 'store_';

export default {
	path: '/admin/store',
	name: 'store',
	header: 'store',
	redirect: {
		name: `${pre}statistics`
	},
	component: BasicLayout,
	children: [
		{
			path: 'store_menus/index',
			name: `${pre}storeMenus`,
			meta: {
				auth: ['admin-store_menus-index'],
				title: '门店菜单'
			},
			component: () => import('@/pages/store/storeMenus/index')
		},
		{
			path: 'cashier_menus/index',
			name: `${pre}cashierMenus`,
			meta: {
				auth: ['admin-cashier_menus-index'],
				title: '收银台菜单'
			},
			component: () => import('@/pages/store/cashierMenus/index')
		},
		{
			path: 'statistics',
			name: `${pre}statistics`,
			meta: {
				auth: ['admin-store-store_statistics'],
				title: '运营概况'
			},
			component: () => import('@/pages/store/statistics/index')
		},
		{
			path: 'store/index',
			name: `${pre}storeList`,
			meta: {
				auth: ['admin-store-store_list'],
				title: '门店列表'
			},
			component: () => import('@/pages/store/storeList/index')
		},
		{
			path: 'order/index',
			name: `${pre}order`,
			meta: {
				auth: ['admin-store-store_order'],
				title: '门店订单'
			},
			component: () => import('@/pages/store/order/index')
		},
		{
			path: 'capital/index',
			name: `${pre}capital`,
			meta: {
				auth: ['admin-store-capital-index'],
				title: '门店流水'
			},
			component: () => import('@/pages/store/capital/index')
		},
		{
			path: 'bill/index',
			name: `${pre}bill`,
			meta: {
				auth: ['admin-store-bill-index'],
				title: '账单记录'
			},
			component: () => import('@/pages/store/bill/index')
		},
		{
			path: 'cash/index',
			name: `${pre}cash`,
			meta: {
				auth: ['admin-store-cash-index'],
				title: '转账申请'
			},
			component: () => import('@/pages/store/cash/index')
		},
		{
			path: 'finance/set/:type?/:tab_id?',
			name: `${pre}setting`,
			meta: {
				auth: ['admin-store-finance-set'],
				title: '财务设置'
			},
			props: {
                typeMole: 'finance'
            },
            component: () => import('@/components/fromSubmit/commonForm.vue')
		},
	]
};
