<template>
    <div>
        <productlist-details v-if="currentTab === 'article' || 'project' || 'app'" ref="productlist" ></productlist-details>
        <Spin size="large" fix v-if="spinShow"></Spin>
    </div>
</template>

<script>
    import productlistDetails from './orderlistDetails';
    import { mapMutations } from 'vuex';
    export default {
        name: 'list',
        components: {
            productlistDetails
        },
        data () {
            return {
                spinShow: false,
                currentTab: '',
                data: [],
                tablists: null
            }
        },
        created () {
            this.getOrderType('');
            this.getOrderStatus('');
            this.getOrderTime('');
            this.getOrderNum('');
            this.getfieldKey('');
            this.onChangeTabs('');
        },
        beforeDestroy () {
            this.getOrderType('');
            this.getOrderStatus('');
            this.getOrderTime('');
            this.getOrderNum('');
            this.getfieldKey('');
            this.onChangeTabs('');
        },
        mounted () {
            
        },
        methods: {
            ...mapMutations('admin/order', [
                'getOrderStatus',
                'getOrderTime',
                'getOrderNum',
                'getfieldKey',
                'onChangeTabs',
                'getOrderType'
                // 'onChangeChart'
            ]),
        }
    }
</script>
<style scoped lang="stylus">
    .product_tabs >>> .ivu-tabs-bar
      margin-bottom 0px !important
    .product_tabs >>> .ivu-page-header-content
      margin-bottom 0px !important
    .product_tabs >>> .ivu-page-header-breadcrumb
        margin-bottom 0px !important
</style>
