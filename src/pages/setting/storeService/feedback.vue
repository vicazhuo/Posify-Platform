<template>
    <div>
       <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
			<div class="new_card_pd">
				<Form ref="formValidate" :model="formValidate" 
                :label-width="labelWidth" 
                :label-position="labelPosition" 
                inline
                class="tabform" 
                @submit.native.prevent>
                    <FormItem label="留言信息：">
                        <Input
                        placeholder="用户昵称/电话/留言内容" element-id="name" 
                        v-model="formValidate.title" 
                        style="width: 250px;"/>
                    </FormItem>
                    <FormItem label="留言时间：">
                        <DatePicker 
                        :editable="false" 
                        @on-change="onchangeTime" 
                        :value="timeVal"  
                        format="yyyy/MM/dd" 
                        type="datetimerange" 
                        :options="options"
                        placement="bottom-start" 
                        placeholder="自定义时间" 
                        style="width: 250px;margin-right:14px;"></DatePicker>
                    </FormItem>
                    <Button type="primary" @click="selChange()">查询</Button>
                    
                </Form>
			</div>
		</Card>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <Table :columns="columns1" :data="list"
                   :loading="loading"
                   no-userFrom-text="暂无数据"
                   no-filtered-userFrom-text="暂无筛选结果">
                <template slot-scope="{ row }" slot="status">
                    <div>{{row.status===1?'已处理':'未处理'}}</div>
                </template>
                <template slot-scope="{ row, index }" slot="action">
                    <a @click="remarks(row.id)">{{row.status===1?'备注':'处理'}}</a>
                    <Divider type="vertical"/>
                    <a @click="del(row,'删除反馈',index)">删除</a>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="count" show-elevator show-total @on-change="pageChange"
                      :page-size="limit"/>
            </div>
        </Card>
    </div>
</template>

<script>
    import { kefuFeedBack, kefuFeedBackEdit } from '@/api/setting';
    import { mapState } from 'vuex';
    import timeOptions from "@/utils/timeOptions";
    export default {
        name: 'feedback',
        data () {
            return {
                loading: false,
                list: [],
                page: 1,
                limit: 15,
                options: timeOptions,
                formValidate: {
                    time: '',
                    title: ''
                },
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
                count: 0,
                columns1: [
                    {
                        title: 'ID',
                        key: 'id',
                        width: 80
                    },
                    {
                        title: '昵称',
                        key: 'rela_name',
                        minWidth: 120
                    },
                    {
                        title: '电话',
                        key: 'phone',
                        minWidth: 120
                    },
                    {
                        title: '内容',
                        key: 'content',
                        minWidth: 320
                    },
                    {
                        title: '状态',
                        slot: 'status',
                        minWidth: 120
                    },
                    {
                        title: '时间',
                        key: 'add_time',
                        minWidth: 120
                    },
                    {
                        title: '操作',
                        slot: 'action',
                        fixed: 'right',
                        minWidth: 150
                    }
                ]
            }
        },
        computed: {
						...mapState("admin/layout", ["isMobile"]),
            labelWidth () {
                return this.isMobile ? undefined : 96
            },
            labelPosition () {
                return this.isMobile ? 'top' : 'right'
            }
        },
        created () {
            this.getList()
        },
        methods: {
            // 备注；
            remarks (id) {
                this.$modalForm(kefuFeedBackEdit(id)).then(() => this.getList());
            },
            // 选择
            selChange () {
                this.page = 1;
                this.getList()
            },
            // 选择时间
            selectChange (tab) {
                this.formValidate.time = tab
                this.timeVal = []
                this.page = 1
                this.getList()
            },
            // 具体日期
            onchangeTime (e) {
                this.timeVal = e
                this.formValidate.time = this.timeVal.join('-')
            },
            getList () {
                kefuFeedBack({
                    page: this.page,
                    limit: this.limit,
                    time: this.formValidate.time,
                    title: this.formValidate.title
                }).then(res => {
                    this.list = res.data.data
                    this.count = res.data.count
                })
            },
            // 删除
            del (row, tit, num) {
                let delfromData = {
                    title: tit,
                    num: num,
                    url: `/app/feedback/${row.id}`,
                    method: 'DELETE',
                    ids: ''
                };
                this.$modalSure(delfromData).then((res) => {
                    this.$Message.success(res.msg);
                    this.list.splice(num, 1);
                }).catch(res => {
                    this.$Message.error(res.msg);
                });
            },
            pageChange (index) {
                this.page = index;
                this.getList();
            }
        }
    }
</script>

<style scoped>

</style>
