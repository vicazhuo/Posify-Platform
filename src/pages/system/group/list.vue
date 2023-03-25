<template>
    <div>
         <div class="i-layout-page-header"> 
            <PageHeader class="product_tabs"  hidden-breadcrumb>
                <div slot="title" class="acea-row row-middle">
                    <router-link :to="{ path: '/admin/system/config/system_group/index' }">
                        <div class="font-sm after-line">
                            <span class="iconfont iconfanhui"></span>
                            <span class="pl10">返回</span>
                        </div>
                    </router-link>
                <span class="mr20 ml16">数据列表</span>
                </div>
            </PageHeader>
        </div>
        <Card :bordered="false" dis-hover class="ivu-mt" :padding= "0">
            <div class="new_card_pd">
                <Form ref="formValidate" :model="formValidate"  :label-width="labelWidth" :label-position="labelPosition" @submit.native.prevent>
                    <FormItem label="是否显示：">
                        <Select v-model="formValidate.status" placeholder="请选择" clearable  @on-change="userSearchs"
                        style="width: 250px;margin-bottom:20px">
                            <Option value="1">显示</Option>
                            <Option value="0">不显示</Option>
                        </Select>
                    </FormItem>
                </Form>
            </div>
        </Card>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <Menu :theme="theme3" :active-name="sortName" width="auto" >
                <MenuGroup>
                    <MenuItem :name="item.id" class="menu-item" v-for="(item,index) in groupAll" :key="index" @click.native="bindMenuItem(item,index)">
                        {{item.name}}
                    </MenuItem>
                </MenuGroup>
            </Menu>
        <Card :bordered="false" dis-hover :padding="0">
            <Form ref="formValidate" :model="formValidate"  :label-width="labelWidth" :label-position="labelPosition" @submit.native.prevent>
                <Button type="primary" @click="groupAdd('添加数据')" class="mr20">添加数据</Button>
            </Form>
            <Table :columns="columns1" :data="tabList" ref="table" class="ivu-mt"
                    :loading="loading" highlight-row
                    no-userFrom-text="暂无数据"
                    no-filtered-userFrom-text="暂无筛选结果">
                <template slot-scope="{ row, index }" slot="status">
                    <i-switch v-model="row.status" :value="row.status" :true-value="1" :false-value="0" @on-change="onchangeIsShow(row)" size="large">
                        <span slot="open">显示</span>
                        <span slot="close">隐藏</span>
                    </i-switch>
                </template>
                <template slot-scope="{ row, index }" slot="action">
                    <a @click="edit(row, '编辑')">编辑</a>
                    <Divider type="vertical"/>
                    <a @click="del(row,'删除这条信息',index)">删除</a>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" :current="formValidate.page" show-elevator show-total @on-change="pageChange"
                        :page-size="formValidate.limit"/>
            </div>
        </Card>
    </Card>
        <!-- <Card :bordered="false" dis-hover class="ivu-mt">
            <Form ref="formValidate" :model="formValidate"  :label-width="labelWidth" :label-position="labelPosition" @submit.native.prevent>
                <Row type="flex" :gutter="24">
                    <Col v-bind="grid">
                        <FormItem label="是否显示：">
                            <Select v-model="formValidate.status" placeholder="请选择" clearable  @on-change="userSearchs">
                                <Option value="1">显示</Option>
                                <Option value="0">不显示</Option>
                            </Select>
                        </FormItem>
                    </Col>
                </Row>
                <Row type="flex">
                    <Col v-bind="grid">
                        <Button type="primary" icon="md-add" @click="groupAdd('添加数据')" class="mr20">添加数据</Button>
                    </Col>
                </Row>
            </Form>
            <Table :columns="columns1" :data="tabList" ref="table" class="mt25"
                   :loading="loading" highlight-row
                   no-userFrom-text="暂无数据"
                   no-filtered-userFrom-text="暂无筛选结果">
                <template slot-scope="{ row, index }" slot="status">
                    <i-switch v-model="row.status" :value="row.status" :true-value="1" :false-value="0" @on-change="onchangeIsShow(row)" size="large">
                        <span slot="open">显示</span>
                        <span slot="close">隐藏</span>
                    </i-switch>
                </template>
                <template slot-scope="{ row, index }" slot="action">
                    <a @click="edit(row, '编辑')">编辑</a>
                    <Divider type="vertical"/>
                    <a @click="del(row,'删除这条信息',index)">删除</a>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" :current="formValidate.page" show-elevator show-total @on-change="pageChange"
                      :page-size="formValidate.limit"/>
            </div>
        </Card> -->
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    import editFrom from '@/components/from/from'
    import { groupDataListApi, groupDataAddApi, groupDataEditApi, groupDataHeaderApi, groupDataSetApi, groupAllApi } from '@/api/system'
    export default {
        name: 'list',
        components: { editFrom },
        data () {
            return {
                grid: {
                    xl: 7,
                    lg: 7,
                    md: 12,
                    sm: 24,
                    xs: 24
                },
                formValidate: {
                    status: '',
                    page: 1,
                    limit: 20,
                    gid: 0
                },
                total: 0,
                tabList: [],
                columns1: [],
                FromData: null,
                loading: false,
                titleType: 'group',
                groupAll: [],
                theme3: 'light',
                labelSort: [],
                sortName: null,
                current: 0
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
        watch: {
            $route (to, from) {
                if (this.$route.params.id) {
                    this.getList();
                    this.getListHeader()
                } else {
                    this.getGroupAll();
                }
                // this.getListHeader();
                // this.getList();
            }
        },
        mounted () {
            if (this.$route.params.id) {
                this.getList();
                this.getListHeader()
            } else {
                this.getGroupAll();
            }
            // this.getListHeader();
            // this.getList();
        },
        methods: {
            bindMenuItem (name, index) {
                this.current = index;
                this.formValidate.gid = name.id
                this.getListHeader();
                this.getList();
            },
            getGroupAll () {
                groupAllApi().then(async res => {
                    this.groupAll = res.data;
                    this.sortName = res.data[0].id
                    this.formValidate.gid = res.data[0].id
                    this.getListHeader()
                    this.getList()
                }).catch(res => {
                    this.$Message.error(res.msg)
                })
            },
            // 返回
            back () {
                this.$router.push({ path: '/admin/system/config/system_group/index' });
            },
            getUrl (type) {
                let url = 'setting/group_data' + type;
                if (this.$route.params.id) {
                    let arr = {
                        'setting_groupDataSign': 'setting/sign_data' + type,
                        'setting_groupDataOrder': 'setting/order_data' + type,
                        'setting_groupDataUser': 'setting/usermenu_data' + type,
                        'setting_groupDataPoster': 'setting/poster_data' + type,
                        'marketing_storeSeckillData': 'setting/seckill_data' + type
                    };
                    if (arr[this.$route.name] === undefined) return url
                    return arr[this.$route.name]
                } else {
                    return url
                }
            },
            // 列表
            getList () {
                this.loading = true
                this.formValidate.gid = this.$route.params.id ? this.$route.params.id : this.formValidate.gid
                this.formValidate.status = this.formValidate.status || ''
                console.log(this.getUrl())
                groupDataListApi(this.formValidate, this.getUrl('')).then(async res => {
                    let data = res.data
                    this.tabList = data.list
                    this.total = data.count
                    this.loading = false
                }).catch(res => {
                    console.log(res)
                    this.loading = false
                    this.$Message.error(res.msg)
                })
                // this.loading = true;
                // this.formValidate.gid = this.$route.params.id;
                // this.formValidate.status = this.formValidate.status || '';
                // groupDataListApi(this.formValidate).then(async res => {
                //     let data = res.data;
                //     this.tabList = data.list;
                //     this.total = data.count;
                //     this.loading = false;
                // }).catch(res => {
                //     this.loading = false;
                //     this.$Message.error(res.msg);
                // })
            },
            // 表格头部
            getListHeader () {
                this.loading = true
                let data = {
                    gid: this.$route.params.id ? this.$route.params.id : this.formValidate.gid
                }
                groupDataHeaderApi(data, this.getUrl('/header')).then(async res => {
                    let data = res.data
                    let header = data.header
                    let index = []
                    header.forEach(function (item, i) {
                        if (item.type === 'img') {
                            index.push(i)
                        }
                    })
                    index.forEach(function (item) {
                        header[item].render = (h, params) => {
                            let arr = params.row[header[item].key]
                            let newArr = []
                            if (arr !== undefined && arr.length) {
                                arr.forEach(function (e, i) {
                                    newArr.push(h('div', {
                                        style: {
                                            width: '36px',
                                            height: '36px',
                                            'border-radius': '4px',
                                            cursor: 'pointer',
                                            display: 'inline-block'
                                        }
                                    }, [
                                        h('img', {
                                            attrs: {
                                                src: params.row[header[item].key][i]
                                            },
                                            style: {
                                                width: '100%',
                                                height: '100%'
                                            }
                                        })
                                    ]))
                                })
                            }
                            return h('viewer', newArr)
                        }
                    })
                    this.columns1 = header
                    this.loading = false
                }).catch(res => {
                    this.loading = false
                    this.$Message.error(res.msg)
                })
            },
            pageChange (index) {
                this.formValidate.page = index
                this.getList()
            },
            // 表格搜索
            userSearchs () {
                this.formValidate.page = 1
                this.getList()
            },
            // 添加表单
            groupAdd () {
                this.$modalForm(groupDataAddApi({ gid: this.$route.params.id ? this.$route.params.id : this.formValidate.gid }, this.getUrl('/create'))).then(() => this.getList())
            },
            // 修改是否显示
            onchangeIsShow (row) {
                groupDataSetApi(this.getUrl(`/set_status/${row.id}/${row.status}`)).then(async res => {
                    this.$Message.success(res.msg)
                    this.getList()
                }).catch(res => {
                    this.$Message.error(res.msg)
                })
                // let data = {
                //     id: row.id,
                //     status: row.status
                // }
                // groupDataSetApi(data).then(async res => {
                //     this.$Message.success(res.msg);
                // }).catch(res => {
                //     this.$Message.error(res.msg);
                // })
            },
            // 编辑
            edit (row) {
                let data = {
                    gid: row.gid
                }
                this.$modalForm(groupDataEditApi(data, this.getUrl(`/${row.id}/edit`))).then(() => this.getList())
            },
            // 删除
            del (row, tit, num) {
                let delfromData = {
                    title: tit,
                    num: num,
                    url: this.getUrl(`/${row.id}`),
                    method: 'DELETE',
                    ids: ''
                }
                this.$modalSure(delfromData).then((res) => {
                    this.$Message.success(res.msg)
                    this.tabList.splice(num, 1)
                }).catch(res => {
                    this.$Message.error(res.msg)
                })
                // let delfromData = {
                //     title: tit,
                //     num: num,
                //     url: `setting/group_data/${row.id}`,
                //     method: 'DELETE',
                //     ids: ''
                // }
                // this.$modalSure(delfromData).then((res) => {
                //     this.$Message.success(res.msg);
                //     this.tabList.splice(num, 1);
                // }).catch(res => {
                //     this.$Message.error(res.msg);
                // });
            }
        }
    }
</script>

<style scoped lang="stylus">
/deep/ .ivu-menu-vertical .ivu-menu-item-group-title{
        display: none;
    }
    /deep/ .ivu-menu-vertical.ivu-menu-light:after{
        display none
    }

    .left-wrapper
        height 904px
        background #fff
        border-right 1px solid #dcdee2
    .menu-item
        // z-index 50
        position: relative;
        display flex
        justify-content space-between
        word-break break-all
        .icon-box
            z-index 3
            position absolute
            right 20px
            top 50%
            transform translateY(-50%)
            display none
        &:hover .icon-box
            display block
        .right-menu
            z-index 10
            position absolute
            right: -106px;
            top: -11px;
            width auto
            min-width: 121px;
    .tabBox_img
        width 36px
        height 36px
        border-radius:4px
        cursor pointer
        img
            width 100%
            height 100%
    .ivu-menu
        z-index auto
</style>
