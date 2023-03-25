<template>
    <div>
        <Card :bordered="false" dis-hover class="ivu-mt" :padding= "0">
        <div class="new_card_pd">
            <div class="acea-row">
            <Alert type="warning" show-icon style="width: 590px;margin-bottom:20px">必须前往微信小程序官方后台开通直播权限，关注<span style="color: red;cursor: pointer;" @click="codeImg">【小程序直播】</span>须知直播状态</Alert>
            </div>
        </div>
        </Card>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <div class="acea-row">
                <Button v-auth="['admin-user-label_add']" type="primary" style="margin-right: 20px;" @click="add">添加主播</Button>
                <!-- <Alert type="warning" show-icon style="width: 590px;">必须前往微信小程序官方后台开通直播权限，关注<span style="color: red;cursor: pointer;" @click="codeImg">【小程序直播】</span>须知直播状态</Alert> -->
            </div>
            <Table :columns="columns1" :data="labelLists" ref="table" class="ivu-mt"
                   :loading="loading" highlight-row
                   no-userFrom-text="暂无数据"
                   no-filtered-userFrom-text="暂无筛选结果">
                <template slot-scope="{ row, index }" slot="icons">
                    <viewer>
                        <div class="tabBox_img">
                            <img v-lazy="row.icon">
                        </div>
                    </viewer>
                </template>
                <template slot-scope="{ row, index }" slot="action">
                    <a @click="edit(row.id)">修改</a>
                    <Divider type="vertical" />
                    <a @click="del(row,'删除分组',index)">删除</a>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" show-elevator show-total @on-change="pageChange"
                      :page-size="labelFrom.limit"/>
            </div>
        </Card>
        <Modal v-model="modal3" title="二维码" @on-cancel="cancel" footer-hide>
            <div class="acea-row row-around">
                <viewer class="QRpic">
                    <img src="https://res.wx.qq.com/op_res/9rSix1dhHfK4rR049JL0PHJ7TpOvkuZ3mE0z7Ou_Etvjf-w1J_jVX0rZqeStLfwh" />
                </viewer>
            </div>
        </Modal>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import { liveAuchorList,liveAuchorAdd,liveAuchorDel } from '@/api/live'
    export default {
        name: "anchor",
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
                        minWidth: 120
                    },
                    {
                        title: '名称',
                        key: 'name',
                        minWidth: 300
                    },
                    {
                        title: '电话',
                        key: 'phone',
                        minWidth: 300
                    },
                    {
                        title: '微信号',
                        key: 'wechat',
                        minWidth: 300
                    },
                    {
                        title: '操作',
                        slot: 'action',
                        fixed: 'right',
                        minWidth: 120
                    }
                ],
                labelFrom: {
                    kerword:'',
                    page: 1,
                    limit: 15
                },
                labelLists: [],
                total: 0,
								modal3:false
            }
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
        created () {
            this.getList();
        },
        methods:{
						codeImg () {
								this.modal3 = true
						},
						cancel () {
						    this.modal3 = false
						},
            // 添加
            add () {
                this.$modalForm(liveAuchorAdd(0)).then((res) => {
                    this.getList();
                    if(res.data.auth == true){
                        this.modal3 = true
                    }      
                }
                )
            },
            // 修改
            edit(id){
                this.$modalForm(liveAuchorAdd(id)).then(() => this.getList());
            },
            // 删除
            del(row, tit, num){
                let delfromData = {
                    title: tit,
                    num: num,
                    url: `live/anchor/del/${row.id}`,
                    method: 'DELETE',
                    ids: ''
                };
                this.$modalSure(delfromData).then((res) => {
                    this.$Message.success(res.msg);
                    this.labelLists.splice(num, 1);

                    this.getList();
                }).catch(res => {
                    this.$Message.error(res.msg);
                });
            },
            // 分组列表
            getList () {
                this.loading = true;
                liveAuchorList(this.labelFrom).then(async res => {
                    let data = res.data;
                    this.labelLists = data.list;
                    this.total = data.count;
                    this.loading = false;
                }).catch(res => {
                    this.loading = false;
                    this.$Message.error(res.msg);
                })
            },
            pageChange (index) {
                this.labelFrom.page = index;
                this.getList();
            },
        }
    }
</script>

<style scoped lang="stylus">
	.QRpic {
	    width: 180px;
	    height: 180px;
	    img {
	        width: 100%;
	        height: 100%;
	    }
	}
</style>
