<template>
    <div>
        <Card :bordered="false" dis-hover class="mt15 ivu-mt" :padding="0">
        <div class="new_card_pd">
            <Form ref="tableFrom" :model="tableFrom" inline :label-width="labelWidth" :label-position="labelPosition" @submit.native.prevent>
                        <FormItem label="是否有效：" >
                            <Select  placeholder="请选择" clearable v-model="tableFrom.status" @on-change="userSearchs" style="width: 250px">
                                <Option value="1">已使用</Option>
                                <Option value="0">未使用</Option>
                                <Option value="2">已过期</Option>
                            </Select>
                        </FormItem>
                        <FormItem label="领取人：" label-for="nickname">
                            <Input  placeholder="请输入领取人" v-model="tableFrom.nickname" clearable style="width: 250px"/>
                        </FormItem>
                        <FormItem label="优惠券搜索：" label-for="coupon_title">
                            <Input placeholder="请输入优惠券名称" v-model="tableFrom.coupon_title" @on-search="userSearchs" style="width: 250px;margin-right:14px"/>
                            <Button type="primary" @click="orderSearch()">查询</Button>
                        </FormItem>
            </Form>
        </div>
        </Card>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <Table :columns="columns1" :data="tableList">
							  <template slot-scope="{ row }" slot="coupon_price">
							     <span v-if="row.coupon_type==1">{{row.coupon_price}}元</span>
							  	 <span v-if="row.coupon_type==2">{{parseFloat(row.coupon_price)/10}}折（{{row.coupon_price.toString().split(".")[0]}}%）</span>
							  </template>	
                <template slot-scope="{ row, index }" slot="is_fail">
                    <Icon type="md-checkmark" v-if="row.is_fail === 0" color="#0092DC" size="14"/>
                    <Icon type="md-close" v-else color="#ed5565" size="14"/>
                </template>
                <template slot-scope="{ row, index }" slot="start_time">
                    <span> {{row.start_time | formatDate}}</span>
                </template>
                <template slot-scope="{ row, index }" slot="end_time">
                    <span> {{row.end_time | formatDate}}</span>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" :current="tableFrom.page" show-elevator show-total @on-change="pageChange"
                      :page-size="tableFrom.limit"/>
            </div>
        </Card>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import { userListApi } from '@/api/marketing';
    import { formatDate } from '@/utils/validate';
    export default {
        name: 'storeCouponUser',
        filters: {
            formatDate (time) {
                if (time !== 0) {
                    let date = new Date(time * 1000);
                    return formatDate(date, 'yyyy-MM-dd hh:mm');
                }
            }
        },
        data () {
            return {
                columns1: [
                    {
                        title: 'ID',
                        key: 'id',
                        width: 80
                    },
                    {
                        title: '优惠券名称',
                        key: 'coupon_title',
                        minWidth: 150
                    },
                    {
                        title: '领取人',
                        key: 'nickname',
                        minWidth: 130
                    },
                    {
                        title: '面值',
                        slot: 'coupon_price',
                        minWidth: 100
                    },
                    {
                        title: '最低消费额',
                        key: 'use_min_price',
                        minWidth: 120
                    },
                    {
                        title: '开始使用时间',
                        slot: 'start_time',
                        minWidth: 150
                    },
                    {
                        title: '结束使用时间',
                        slot: 'end_time',
                        minWidth: 150
                    },
                    {
                        title: '获取方式',
                        key: 'type',
                        minWidth: 150
                    },
                    {
                        title: '是否可用',
                        slot: 'is_fail',
                        minWidth: 120
                    },
                    {
                        title: '状态',
                        key: 'status',
                        minWidth: 170
                    }
                ],
                tableList: [],
                grid: {
                    xl: 7,
                    lg: 7,
                    md: 12,
                    sm: 24,
                    xs: 24
                },
                tableFrom: {
                    status: '',
                    coupon_title: '',
                    nickname: '',
                    page: 1,
                    limit: 15
                },
                total: 0
            }
        },
        computed: {
            ...mapState('admin/layout', [
                'isMobile'
            ]),
            labelWidth () {
                return this.isMobile ? undefined : 96;
            },
            labelPosition () {
                return this.isMobile ? 'top' : 'right';
            }
        },
        created () {
            this.getList();
        },
        methods: {
            // 列表
            getList () {
                this.loading = true;
                this.tableFrom.status = this.tableFrom.status || '';
                userListApi(this.tableFrom).then(async res => {
                    let data = res.data
                    this.tableList = data.list;
                    this.total = res.data.count;
                    this.loading = false;
                }).catch(res => {
                    this.loading = false;
                    this.$Message.error(res.msg);
                })
            },
            pageChange (index) {
                this.tableFrom.page = index;
                this.getList();
            },
            // 搜索()
            orderSearch () {
                this.tableFrom.page = 1;
                this.getList();
            },
            // 表格搜索
            userSearchs () {
                this.tableFrom.page = 1;
                this.getList();
            }
        }
    }
</script>

<style scoped>

</style>