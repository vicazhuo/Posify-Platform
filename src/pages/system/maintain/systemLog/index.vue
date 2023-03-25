<template>
    <div>
         <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
            <div class="new_card_pd">
                <Form ref="formValidate" :model="formValidate" 
                inline 
                :label-width="labelWidth" 
                :label-position="labelPosition" @submit.native.prevent>
                    <FormItem :label="fromList.title+'：'">
                         <DatePicker :editable="false" @on-change="onchangeTime" :value="timeVal" format="yyyy/MM/dd"
                            type="datetimerange" placement="bottom-start" placeholder="自定义时间"
                            style="width: 250px;"
                            :options="options"
                            ></DatePicker>
                    </FormItem>
                    <FormItem label="名称：">
                        <Select v-model="formValidate.admin_id" style="width: 250px;" clearable>
                            <Option :value="item.id" v-for="(item,index) in dataList" :key="index">{{item.real_name}}</Option>
                        </Select>
                    </FormItem>
                    <FormItem label="链接：">
                        <Input  placeholder="请输入链接" v-model="formValidate.path" style="width: 250px;" clearable></Input>
                    </FormItem>
                    <FormItem label="IP：">
                        <Input  placeholder="请输入IP" v-model="formValidate.ip" style="width: 250px;margin-right:14px" clearable>
                        </Input>
                        <Button type="primary" @click="userSearchs">查询</Button>
                    </FormItem>
                </Form>
            </div>
       </Card>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <div class="table_box">
                
            </div>
            <Table ref="selection" :columns="columns4" :data="tabList" :loading="loading"
                   no-data-text="暂无数据" highlight-row
                   no-filtered-data-text="暂无筛选结果">
                <template slot-scope="{ row }" slot="nickname">
                    <span>{{row.admin_id + ' / ' + row.admin_name }}</span>
                </template>
                <template slot-scope="{ row }" slot="add_time">
                    <span> {{row.add_time | formatDate}}</span>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" :current="formValidate.page" show-elevator show-total @on-change="pageChange"
                      :page-size="formValidate.limit"  /></div>
        </Card>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import timeOptions from "@/utils/timeOptions";
    import { searchAdminApi, systemListApi } from '@/api/system';
    import { formatDate } from '@/utils/validate';
    export default {
        name: 'systemLog',
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
                fromList: {
                    title: '选择时间',
                    custom: true,
                    fromTxt: [
                        { text: '全部', val: '' },
                        { text: '今天', val: 'today' },
                        { text: '昨天', val: 'yesterday' },
                        { text: '最近7天', val: 'lately7' },
                        { text: '最近30天', val: 'lately30' },
                        { text: '本月', val: 'month' },
                        { text: '本年', val: 'year' }
                    ]
                },
                timeVal: [],
                options: timeOptions,
                formValidate: {
                    limit: 20,
                    page: 1,
                    pages: '',
                    data: '',
                    path: '',
                    ip: '',
                    admin_id: ''
                },
                loading: false,
                tabList: [],
                total: 0,
                columns4: [
                    {
                        title: 'ID',
                        key: 'id',
                        width: 80
                    },
                    {
                        title: 'ID/名称',
                        slot: 'nickname',
                        minWidth: 100
                    },
                    {
                        title: '链接',
                        key: 'path',
                        minWidth: 300
                    },
                    {
                        title: '行为',
                        key: 'page',
                        minWidth: 150
                    },
                    {
                        title: '操作ip',
                        key: 'ip',
                        minWidth: 150
                    },
                    {
                        title: '类型',
                        key: 'type',
                        minWidth: 100
                    },
                    {
                        title: '操作时间',
                        slot: 'add_time',
                        minWidth: 150
                    }
                ],
                dataList: []
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
            this.getSearchAdmin();
            this.getList();
        },
        methods: {
            // 具体日期
            onchangeTime (e) {
                this.timeVal = e;
                this.formValidate.data = this.timeVal.join('-');
                this.formValidate.page = 1;
            },
            // 选择时间
            selectChange (tab) {
                this.formValidate.data = tab;
                this.timeVal = [];
                this.formValidate.page = 1;
                this.getList();
            },
            // 搜索条件
            getSearchAdmin () {
                searchAdminApi().then(async res => {
                    this.dataList = res.data.info;
                }).catch(res => {
                    this.$Message.error(res.msg);
                })
            },
            // 列表
            getList () {
                this.loading = true;
                systemListApi(this.formValidate).then(async res => {
                    let data = res.data
                    this.tabList = data.list;
                    this.total = data.count;
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
            // 搜索
            userSearchs () {
                this.formValidate.page = 1;
                this.getList();
            }
        }
    }
</script>

<style scoped>

</style>
