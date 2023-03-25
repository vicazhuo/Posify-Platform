<template>
    <div>
       <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
            <div class="new_card_pd">
                <Form ref="formValidate" :model="formValidate" inline 
                :label-width="labelWidth" 
                :label-position="labelPosition" @submit.native.prevent>
                    <FormItem label="状态：">
                        <Select v-model="formValidate.status" placeholder="请选择" clearable style="width:250px;">
                            <Option value="1">开启</Option>
                            <Option value="2">禁用</Option>
                        </Select>
                    </FormItem>
                    <FormItem label="搜索：">
                        <Input v-model="formValidate.name" placeholder="请输入账号或者描述" style="width:250px;margin-right:14px"/>
                        <Button type="primary"   @click="userSearchs">查询</Button>
                    </FormItem>
                </Form>
            </div>
       </Card>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <Button type="primary"   @click="add">添加账号</Button>
            <Table :columns="columns1" :data="levelLists" ref="table" class="mt25"
                   :loading="loading"
                   no-userFrom-text="暂无数据"
                   no-filtered-userFrom-text="暂无筛选结果">
                <template slot-scope="{ row }" slot="status">
                    <i-switch v-model="row.status" :value="row.status" :true-value="1" :false-value="2" @on-change="onchangeIsShow(row)" size="large">
                        <span slot="open">开启</span>
                        <span slot="close">禁用</span>
                    </i-switch>
                </template>
                <template slot-scope="{ row, index }" slot="action">
					<a @click="edit(row)">编辑</a>
					<Divider type="vertical" />
					<a @click="del(row, '删除该账户', index)">删除</a>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" :current="formValidate.page" show-elevator show-total @on-change="pageChange"
                      :page-size="formValidate.limit"/>
            </div>
        </Card>
		<Modal v-model="modals" scrollable :title="type == 0?'添加账号':'编辑账号'" :mask-closable="false" :closable="false">
			<Form  ref="modalsdate" :model="modalsdate" :rules="ruleValidate" :label-width="50" label-position="right">
				<FormItem label="账号" prop="appid" >
					<div style="display: flex;">
						<Input type="text" v-model="modalsdate.appid" :disabled='type != 0'></Input>
					</div>
				</FormItem>
				<FormItem label="密码" prop="appsecret">
					<div style="display: flex;">
						<Input type="text" v-model="modalsdate.appsecret" class="input"></Input>
						<Button type="primary" @click="reset" class="reset">重置</Button>
					</div>
				</FormItem>
				<FormItem label="描述">
					<div style="display: flex;">
						<Input type="textarea" v-model="modalsdate.title"></Input>
					</div>
				</FormItem>
			</Form>
			<div slot="footer">
				<Button v-if="modalsid == ''" type="primary" @click="ok('modalsdate')">确定</Button>
				<Button v-else type="primary" @click="oks('modalsdate')">确定</Button>
				<Button @click="cancel">取消</Button>
			</div>
		</Modal>
    </div>
</template>
<script>
    import { mapState } from 'vuex';
    import { outListApi, outStatusApi, outSaveApi, outSavesApi } from '@/api/setting';
    export default {
        name: 'user_level',
        data () {
            return {
                grid: {
                    xl: 7,
                    lg: 7,
                    md: 12,
                    sm: 24,
                    xs: 24
                },
				modalsid: '',
                loading: false,
                columns1: [
                    {
                        title: 'ID',
                        key: 'id',
                        width: 50
                    },
                    {
                        title: '账号',
                        key: 'appid',
                        minWidth: 100
                    },
                    {
                        title: '描述',
                        key: 'title',
                        minWidth: 120
                    },
					{
					    title: '添加时间',
					    key: 'add_time',
					    minWidth: 100
					},
					{
					    title: '最后登录时间',
					    key: 'last_time',
					    minWidth: 100
					},
					{
					    title: '登录',
					    key: 'ip',
					    minWidth: 100
					},
                    {
                        title: '状态',
                        slot: 'status',
                        minWidth: 120
                    },
                    {
                        title: '操作',
                        slot: 'action',
                        fixed: 'right',
                        minWidth: 80
                    }
                ],
                formValidate: {
                    name: '',
					status: "",
                    page: 1,
                    limit: 15
                },
				modalsdate:{
					appid: "",
					appsecret: '',
					title: ''
				},
				 ruleValidate: {
					appid: [
						{ required: true, message: '请输入正确的账号 (不少于8位数)', trigger: 'blur', min:8}
					],
					appsecret: [
						{ required: true, message: '请输入正确的密码 (不少于32位数)', trigger: 'blur', min:32 }
					]
				},
                levelLists: [],
                total: 0,
				modals: false,
				type: 0,
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
                return this.isMobile ? undefined : 96;
            },
            labelPosition () {
                return this.isMobile ? 'top' : 'right';
            }
        },
        methods: {
            // 删除
            del (row, tit, num) {
                let delfromData = {
                    title: tit,
                    num: num,
                    url: `setting/system_out/delete/${row.id}`,
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
            // 修改是否显示
            onchangeIsShow (row) {
                outStatusApi(row.id,row.status).then(res => {
                    this.$Message.success(res.msg);
                }).catch(res => {
                    this.$Message.error(res.msg);
                })
            },
            // 等级列表
            getList () {
                this.loading = true;
                outListApi(this.formValidate).then(res => {
                    let data = res.data
                    this.levelLists = data.list;
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
            // 添加
			add() {
			    this.modals = true
				this.type = 0
			},
            // 编辑
            edit (row) {
				this.modals = true
				this.modalsdate.appid = row.appid
				this.modalsdate.title = row.title
				this.modalsid = row.id
				this.type = 1
            },
			ok(name){
				this.$refs[name].validate((valid) => {
				  if (valid) {
					  outSaveApi(this.modalsdate).then(res=>{
						  this.modalsdate = {
						  	appid: "",
						  	appsecret: '',
						  	title: ''
						  }
						  this.modals = false,
						  this.$Message.success(res.msg);
						  this.modalsid = ''
						  this.getList()
					  }).catch(err=>{
						  this.$Message.error(err.msg);
					  })
				  } else {
				    this.$Message.warning("请完善数据");
				  }
				});
			},
			oks(name){
				this.$refs[name].validate((valid) => {
				  if (valid) {
					  outSavesApi(this.modalsid,this.modalsdate).then(res=>{
						  this.modalsdate = {
						  	appid: "",
						  	appsecret: '',
						  	title: ''
						  }
						  this.modals = false,
						  this.$Message.success(res.msg);
						  this.modalsid = ''
						  this.getList()
					  }).catch(err=>{
						  this.$Message.error(err.msg);
					  })
				  } else {
				    this.$Message.warning("请完善数据");
				  }
				});
			},
			cancel(){
				this.modalsid = ''
				this.modalsdate = {
					appid: "",
					appsecret: '',
					title: ''
				}
				this.modals = false
			},
			reset(){
				  let len = 32
				  let chars =
					'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678' 
				  let maxPos = chars.length
				  let pwd = ''
				  for (let i = 0; i < len; i++) {
					pwd += chars.charAt(Math.floor(Math.random() * maxPos))
				  }
				  this.modalsdate.appsecret =  pwd
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
	.reset{
			margin-left: 10px;
		}
    
        
</style>
