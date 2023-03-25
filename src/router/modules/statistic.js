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
}

const pre = 'statistic_';

export default {
    path: '/admin/statistic',
    name: 'statistic',
    header: 'statistic',
    redirect: {
        name: `${pre}product`
    },
    component: BasicLayout,
    children: [
        {
            path: 'product',
            name: `${pre}product`,
            meta: {
                title: '商品统计'
            },
            component: () => import('@/pages/statistic/product/index')
        },
        {
            path: 'user',
            name: `${pre}user`,
            meta: {
                title: '用户统计'
            },
            component: () => import('@/pages/statistic/user/index')
        },
        {
            path: 'transaction',
            name: `${pre}transaction`,
            meta: {
                title: '交易统计'
            },
            component: () => import('@/pages/statistic/transaction/index')
        },
        {
            path: 'capital',
            name: `${pre}capital`,
            meta: {
                      auth: ['admin-statistic-capital'],
                title: '资金流水'
            },
            component: () => import('@/pages/statistic/capital/index')
        }
    ]
};
