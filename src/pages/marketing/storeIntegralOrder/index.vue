<template>
    <div>
        <!-- <div class="i-layout-page-header">
					<PageHeader class="product_tabs" hidden-breadcrumb>
					  <div slot="title">
					    <router-link :to="{ path: '/admin/marketing/store_integral/index' }"
					      >
                        <div class="font-sm after-line">
                            <span class="iconfont iconfanhui"></span>
                            <span class="pl10">返回</span>
                        </div>
                          </router-link
					    >
					    <span
					      v-text="$route.query.product_id ? '积分订单(兑换记录)' : '积分订单'"
					      class="mr20 ml16"
					    ></span>
					  </div>
					</PageHeader>
        </div> -->
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
            this.getTabs()
        },
        methods: {
            ...mapMutations('admin/integralOrder', [
                'onChangeTabs',
                'getOrderStatus',
                'getOrderTime',
                'getOrderNum',
                'getfieldKey',
                'onChangeTabs',
                'getOrderTabs',
                'getOrderType'
                // 'onChangeChart'
            ]),
            // 订单类型  @on-changeTabs="getChangeTabs"
            getTabs () {
                this.spinShow = true;
                this.$store.dispatch('admin/integralOrder/getOrderTabs', {product_id: this.$route.query.product_id || ""}).then(res => {
                    this.tablists = res.data;
                    // this.onChangeChart(this.tablists)
                    this.spinShow = false;
                }).catch(res => {
                    this.spinShow = false;
                    this.$Message.error(res.msg);
                })
                // getOrdes({}).then(async res => {
                //     this.tablists = res.data;
                //     this.onChangeChart(this.tablists)
                //     this.spinShow = false;
                // }).catch(res => {
                //     this.spinShow = false;
                //     this.$Message.error(res.msg);
                // })
            },
            onClickTab () {
                this.onChangeTabs(Number(this.currentTab))
                this.$refs.productlist.getChangeTabs()
                this.$store.dispatch('admin/integralOrder/getOrderTabs', { type: this.currentTab });
            }
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
