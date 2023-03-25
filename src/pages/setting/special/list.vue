<template>
    <div>
        <div class="i-layout-page-header">
            <PageHeader class="product_tabs" :title="$route.meta.title" hidden-breadcrumb></PageHeader>
        </div>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <div class="acea-row row-between-wrapper">
                <Row type="flex">
                    <Col v-bind="grid">
                        <div class="button acea-row row-middle">
                            <Button type="primary"  icon="md-add" @click="add">添加</Button>
                        </div>
                    </Col>
                </Row>
            </div>
            <Table :columns="columns1" :data="list" ref="table" class="mt25"
                   :loading="loading" highlight-row
                   no-userFrom-text="暂无数据"
                   no-filtered-userFrom-text="暂无筛选结果">
                <template slot-scope="{ row, index }" slot="link">
                    <div>/pages/annex/special/index?id={{row.id}}</div>
                </template>
                <template slot-scope="{ row, index }" slot="action">
                    <a @click="edit(row)">编辑</a>
                    <div  v-if="row.id !=1" style="display: inline-block">
                        <Divider type="vertical" />
                        <a @click="del(row,index)">删除</a>
                    </div>
                    <div style="display: inline-block">
                        <Divider type="vertical"/>
                        <a class="copy-data" :data-clipboard-text="BaseURL+'pages/annex/special/index?id='+row.id">预览</a>
                    </div>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" :current="page" show-elevator show-total @on-change="pageChange"
                      :page-size="limit"/>
            </div>
        </Card>
    </div>
</template>

<script>
    import Setting from '@/setting';
    import ClipboardJS from "clipboard";
    import { diyList, diyDel, setStatus } from '@/api/diy'
    import { mapState } from 'vuex';
    export default {
        name: 'devise_list',
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
                        title: '页面ID',
                        key: 'id',
                        minWidth: 120
                    },
                    {
                        title: '专题页名称',
                        key: 'title',
                        minWidth: 120
                    },
                    {
                        title: '专题链接',
                        slot: 'link',
                        minWidth: 300
                    },
                    {
                        title: '添加时间',
                        key: 'add_time',
                        minWidth: 120
                    },
                    {
                        title: '更新时间',
                        key: 'update_time',
                        minWidth: 120
                    },
                    {
                        title: '操作',
                        slot: 'action',
                        fixed: 'right',
                        minWidth: 300
                    }
                ],
                list: [],
                total: 0,
                page: 1,
                limit: 15,
                BaseURL: Setting.apiBaseURL.replace(/adminapi/, "")
            }
        },
        created () {
            this.getList()
        },
        mounted: function() {
            this.$nextTick(function() {
                const clipboard = new ClipboardJS('.copy-data');
                clipboard.on("success", () => {
                    this.$Message.success('复制预览链接成功');
                });
            });
        },
        methods: {
            // 获取列表
            getList () {
                this.loading = true
                diyList({
                    type:2,
                    page: this.page,
                    limit: this.limit
                }).then(res => {
                    this.loading = false
                    this.list = res.data.list
                    this.total = res.data.count
                })
            },
            // 编辑
            edit (row) {
                this.$router.push({ path: '/admin/pages/special/diy', query: { id: row.id, name: row.name, type: row.type } });
            },
            // 添加
            add () {
                this.$router.push({ path: '/admin/pages/special/diy', query: { id: 0, name: '首页', type: 2 } });
            },
            // 删除
            del(row) {
                let delfromData = {
                    title: "删除",
                    num: 2000,
                    url: "diy/del/" + row.id,
                    method: "DELETE",
                    data: {
                        type: 2,
                    },
                };
                this.$modalSure(delfromData)
                    .then((res) => {
                        this.getList();
                    })
                    .catch((res) => {
                        this.$Message.error(res.msg);
                    });
            },
            // 使用模板
            setStatus (row) {
                setStatus(row.id).then(res => {
                    this.$Message.success(res.msg);
                    this.getList()
                })
            },
            pageChange (index) {
                this.page = index
                this.getList()
            }
        }
    }
</script>

<style scoped>

</style>
