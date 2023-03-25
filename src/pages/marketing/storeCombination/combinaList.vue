<template>
    <div class="article-manager">
        <Card :bordered="false" dis-hover class="ivu-mt" :padding= "0">
            <div class="new_card_pd">
            <Form ref="formValidate" :model="formValidate"  inline :label-width="labelWidth" :label-position="labelPosition" @submit.native.prevent>
                <FormItem label="时间选择：">
                    <DatePicker :editable="false" @on-change="onchangeTime" :value="timeVal"  format="yyyy/MM/dd" type="datetimerange" placement="bottom-start" placeholder="自定义时间" style="width: 250px;"
                    :options="options"></DatePicker>
                </FormItem>
                <FormItem label="拼团状态：">
                    <Select v-model="formValidate.status" placeholder="请选择" clearable style="width: 250px;margin-right:14px">
                        <Option :value="1">进行中</Option>
                        <Option :value="2">已完成</Option>
                        <Option :value="3">未完成</Option>
                    </Select>
                    <Button type="primary" @click="userSearchs()">查询</Button>
                </FormItem>
                 <FormItem >

                </FormItem>
            </Form>
            </div>
        </Card>
        <cards-data :cardLists="cardLists" v-if="cardLists.length>=0"></cards-data>
        <Card :bordered="false" dis-hover class="ive-mt">
            <Table :columns="columns1" :data="tableList" :loading="loading" highlight-row
                   no-userFrom-text="暂无数据"
                   no-filtered-userFrom-text="暂无筛选结果">
                <template slot-scope="{ row }" slot="avatar">
                    <viewer>
                        <div class="tabBox_img">
                            <img v-lazy="row.avatar">
                        </div>
                    </viewer>
                </template>
                <template slot-scope="{ row }" slot="nickname">
                    <span> {{row.nickname + ' / ' + row.uid}}</span>
										<span style="color: #ed4014;" v-if="row.delete_time != null"> (已注销)</span>
                </template>
                <!-- <template slot-scope="{ row }" slot="title">
                    <span> {{row.title + ' / ' + row.cid}}</span>
                </template> -->
                <template slot-scope="{ row }" slot="title">
                    <Tooltip max-width="200" placement="bottom">
                        <span class="line2">{{row.title + ' / ' + row.cid}}</span>
                        <p slot="content">{{row.title + ' / ' + row.cid}}</p>
                    </Tooltip>
                </template>
                <template slot-scope="{ row }" slot="add_time">
                    <span> {{row.add_time | formatDate}}</span>
                </template>
                <template slot-scope="{ row }" slot="stop_time">
                    <span> {{row.stop_time | formatDate}}</span>
                </template>
                <template slot-scope="{ row }" slot="status">
                    <Tag color="green" size="medium" v-show="row.status === 1">进行中</Tag>
                    <Tag color="cyan" size="medium" v-show="row.status === 2">已完成</Tag>
                    <Tag color="volcano" size="medium" v-show="row.status === 3">未完成</Tag>
                </template>
                <template slot-scope="{ row }" slot="action">
                    <a @click="Info(row)" >查看详情</a>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" :current="formValidate.page" show-elevator show-total @on-change="pageChange"
                      :page-size="formValidate.limit"/>
            </div>
        </Card>

        <!-- 详情模态框-->
        <Modal v-model="modals" class="tableBox"  scrollable  footer-hide closable title="查看详情" :mask-closable="false"  width="750">
            <Table ref="selection" :columns="columns2" :data="tabList3" :loading="loading2"
                   no-data-text="暂无数据" highlight-row max-height="600" size="small"
                   no-filtered-data-text="暂无筛选结果">
                <template slot-scope="{ row, index }" slot="nickname">
                    <span> {{row.nickname + ' / ' + row.uid}}</span>
										<span style="color: #ed4014;" v-if="row.delete_time != null"> (已注销)</span>
                </template>
                <template slot-scope="{ row, index }" slot="avatar">
                    <viewer>
                        <div class="tabBox_img">
                            <img v-lazy="row.avatar">
                        </div>
                    </viewer>
                </template>
                <template slot-scope="{ row, index }" slot="action">
                    <Tag color="volcano" v-show="row.is_refund != 0">已退款</Tag>
                    <Tag color="cyan" v-show="row.is_refund === 0">未退款</Tag>
                </template>
            </Table>
        </Modal>
    </div>
</template>

<script>
    import cardsData from '@/components/cards/cards';
    import { mapState } from 'vuex';
    import { formatDate } from '@/utils/validate';
    import { combineListApi, orderPinkListApi, statisticsApi } from '@/api/marketing';
    import timeOptions from "@/utils/timeOptions";
    export default {
        name: 'combinalist',
        filters: {
            formatDate (time) {
                if (time !== 0) {
                    let date = new Date(time * 1000);
                    return formatDate(date, 'yyyy-MM-dd hh:mm');
                }
            }
        },
        components: { cardsData },
        data () {
            return {
                cardLists: [],
                modals: false,
                options: timeOptions,
                grid: {
                    xl: 7,
                    lg: 10,
                    md: 12,
                    sm: 12,
                    xs: 24
                },
                loading: false,
                formValidate: {
                    status: '',
                    data: '',
                    page: 1,
                    limit: 15
                },
                columns1: [
                    {
                        title: '头像',
                        slot: 'avatar',
                        minWidth: 150
                    },
                    {
                        title: '开团团长',
                        slot: 'nickname',
                        minWidth: 170
                    },
                    {
                        title: '开团时间',
                        slot: 'add_time',
                        minWidth: 150
                    },
                    {
                        title: '拼团商品',
                        slot: 'title',
                        minWidth: 400
                    },
                    {
                        title: '几人团',
                        key: 'people',
                        minWidth: 120
                    },
                    {
                        title: '几人参加',
                        key: 'count_people',
                        minWidth: 100
                    },
                    {
                        title: '结束时间',
                        slot: 'stop_time',
                        minWidth: 150
                    },
                    {
                        title: '状态',
                        slot: 'status',
                        minWidth: 100
                    },
                    {
                        title: '操作',
                        slot: 'action',
                        fixed: 'right',
                        minWidth: 170
                    }
                ],
                tableList: [],
                total: 0,
                timeVal: [],
                loading2: false,
                tabList3: [],
                columns2: [
                    {
                        title: 'ID',
                        key: 'id',
                        width: 80
                    },
                    {
                        title: '用户名称',
                        slot: 'nickname',
                        minWidth: 150
                    },
                    {
                        title: '用户头像',
                        slot: 'avatar'
                    },
                    {
                        title: '订单编号',
                        key: 'order_id'
                    },
                    {
                        title: '金额',
                        key: 'price'
                    },
                    {
                        title: '订单状态',
                        slot: 'action'
                    }
                ],
                rows: {}
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
            this.getStatistics();
        },
        methods: {
            // 拼团统计
            getStatistics () {
                statisticsApi().then(async res => {
                    let data = res.data
                    this.cardLists = data.res;
                }).catch(res => {
                    this.$Message.error(res.msg);
                })
            },
            // 查看详情
            Info (row) {
                this.modals = true;
                this.rows = row;
                orderPinkListApi(row.id).then(async res => {
                    let data = res.data
                    this.tabList3 = data.list;
                    this.loading = false;
                }).catch(res => {
                    this.loading = false;
                    this.$Message.error(res.msg);
                })
            },
            // 具体日期
            onchangeTime (e) {
                this.timeVal = e
                if(this.timeVal[0]===''){
                    this.formValidate.data = '';
                }else {
                    this.formValidate.data = this.timeVal.join('-');
                }
                this.formValidate.page = 1;
            },
            // 选择时间
            selectChange (tab) {
                this.formValidate.page = 1;
                this.formValidate.data = tab;
                this.timeVal = [];
                this.getList();
            },
            // 列表
            getList () {
                this.loading = true;
                this.formValidate.status = this.formValidate.status || '';
                combineListApi(this.formValidate).then(async res => {
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
                this.formValidate.page = index;
                this.getList();
            },
            // 表格搜索
            userSearchs () {
                this.formValidate.page = 1;
                this.getList();
            }
        }
    }
</script>

<style scoped lang="stylus">
    .tabBox_img
        width 36px
        height 36px
        border-radius:4px
        cursor pointer
        img
            width 100%
            height 100%
</style>
