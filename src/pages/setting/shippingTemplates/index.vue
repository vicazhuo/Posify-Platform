<template>
    <div>
        <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
			<div class="new_card_pd">
                <Form ref="levelFrom" :model="levelFrom" inline :label-width="labelWidth" :label-position="labelPosition" @submit.native.prevent>
                    <FormItem label="搜索："  label-for="keyword">
                        <Input v-model="levelFrom.name" placeholder="请输入模板名称" style="width:250px;margin-right:14px"/>
                        <Button type="primary" @click="userSearchs">查询</Button>
                    </FormItem>
                </Form>
			</div>
		</Card>
        <Card :bordered="false" dis-hover class="ivu-mt">
           <Button type="primary" @click="freight">添加运费模板</Button>
            <Table :columns="columns1" :data="levelLists" ref="table" class="mt25"
                   :loading="loading" highlight-row
                   no-userFrom-text="暂无数据"
                   no-filtered-userFrom-text="暂无筛选结果">
                <template slot-scope="{ row }" slot="icons">
                    <viewer>
                        <div class="tabBox_img">
                            <img v-lazy="row.icon">
                        </div>
                    </viewer>
                </template>
                <template slot-scope="{ row, index }" slot="action">
                    <a @click="edit(row.id)">修改</a>
                    <Divider type="vertical" v-if="row.id!==1"/>
                    <a @click="del(row,'删除运费模板',index)" v-if="row.id!==1">删除</a>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" :current="levelFrom.page" show-elevator show-total @on-change="pageChange"
                      :page-size="levelFrom.limit"/>
            </div>
        </Card>
        <!-- 运费模板-->
        <freight-template ref="template"></freight-template>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import { templatesApi } from '@/api/setting';
    import freightTemplate from '@/components/freightTemplate/index';

    export default {
        name: 'setting_templates',
        components: { freightTemplate },
        data () {
            return {
                grid: {
                    xl: 7,
                    lg: 7,
                    md: 12,
                    sm: 24,
                    xs: 24
                },
                loading: false,
                columns1: [
                    {
                        title: 'ID',
                        key: 'id',
                        width: 80
                    },
                    {
                        title: '模板名称',
                        key: 'name',
                        minWidth: 100
                    },
                    {
                        title: '计费方式',
                        key: 'type',
                        minWidth: 120
                    },
                    {
                        title: '指定包邮',
                        key: 'appoint',
                        minWidth: 120
                    },
                    {
                        title: '排序',
                        key: 'sort',
                        minWidth: 120
                    },
                    {
                        title: '添加时间',
                        key: 'add_time',
                        minWidth: 120
                    },
                    {
                        title: '操作',
                        slot: 'action',
                        fixed: 'right',
                        minWidth: 120
                    }
                ],
                levelFrom: {
                    name: '',
                    page: 1,
                    limit: 15
                },
                levelLists: [],
                total: 0,
                FromData: null
            }
        },
        created () {
            this.getList();
        },
        computed: {
            ...mapState('admin/layout', [
                'isMobile'
            ]),
            labelWidth () {
                return this.isMobile ? undefined : 75;
            },
            labelPosition () {
                return this.isMobile ? 'top' : 'right';
            }
        },
        methods: {
            // 添加运费模板
            freight () {
                this.$refs.template.id = 0;
                this.$refs.template.isTemplate = true;
            },
            // 删除
            del (row, tit, num) {
                let delfromData = {
                    title: tit,
                    num: num,
                    url: `setting/shipping_templates/del/${row.id}`,
                    method: 'DELETE',
                    ids: ''
                };
                this.$modalSure(delfromData).then((res) => {
                    this.$Message.success(res.msg);
                    this.levelLists.splice(num, 1);
                }).catch(res => {
                    this.$Message.error(res.msg);
                });
            },
            // 运费模板列表
            getList () {
                this.loading = true;
                templatesApi(this.levelFrom).then(async res => {
                    let data = res.data;
                    this.levelLists = data.data;
                    this.total = data.count;
                    this.loading = false;
                }).catch(res => {
                    this.loading = false;
                    this.$Message.error(res.msg);
                })
            },
            pageChange (index) {
                this.levelFrom.page = index;
                this.getList();
            },
            // 编辑
            edit (id) {
                this.$refs.template.isTemplate = true;
                this.$refs.template.editFrom(id);
            },
            // 表格搜索
            userSearchs () {
                this.levelFrom.page = 1;
                this.getList();
            }
        }
    }
</script>
