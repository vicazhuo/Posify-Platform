<template>
    <div>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <Row class="ivu-mt box-wrapper">
				<Col class="iframe" :bordered="false">
					<div class="iframe-box">
						<img v-if="statusColor == 1" src="../../../assets/images/bluesgin.png">
						<img v-if="statusColor == 2" src="../../../assets/images/greesgin.png">
						<img v-if="statusColor == 3" src="../../../assets/images/redsgin.png">
						<img v-if="statusColor == 4" src="../../../assets/images/pinksgin.png">
						<img v-if="statusColor == 5" src="../../../assets/images/oragesgin.png">
					</div>
				</Col>
				<Col :xs="24" :sm="24" :md="8" :lg="15" style="margin-left: 20px;">
					<div class="table_box">
						<Row type="flex">
							<Col v-bind="grid">
								<div class="title">签到天数设置</div>
								<Button type="primary"
								 @click="groupAdd('添加数据')" style="margin-left: 14px;margin-top: 30px;">添加数据</Button>
							</Col>
						</Row>
						<div class="table">
							<Table :columns="columns" :data="signList" ref="table" class="mt25"
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
						</div>
					</div>
				</Col>
            </Row>
        </Card>
    </div>
</template>

<script>
	import { getColorChange } from  '@/api/diy'
    import { mapState } from 'vuex'
    import { groupAllApi, groupDataListApi, groupDataAddApi, groupDataHeaderApi, groupDataEditApi, groupDataSetApi} from '@/api/system'
    export default {
        name: 'list',
		computed:{
			labelWidth() {
			  return this.isMobile ? undefined : 120;
			},
			labelPosition() {
			  return this.isMobile ? "top" : "right";
			},
			...mapState('admin/layout', [
				'menuCollapse'
			])
		},
        data () {
            return {
				gridPic: {
				    xl: 6,
				    lg: 8,
				    md: 12,
				    sm: 12,
				    xs: 12
				},
				gridBtn: {
				    xl: 4,
				    lg: 8,
				    md: 8,
				    sm: 8,
				    xs: 8
				},
				grid: {
						xl: 7,
						lg: 7,
						md: 12,
						sm: 24,
						xs: 24
					},
				name:'sign_day_num',
				pageId:0,
				columns:[],
				loading: false,
				signList:[],
				statusColor:1
            }
        },
		created() {
			this.color()
        },
		mounted() {
			this.getListHeader();
			this.getGroupAll();
			this.info()
		},
        methods: {
			info(){
			  groupDataListApi({config_name:this.name}, 'setting/group_data').then(async res => {
				this.signList = res.data.list
				}).catch(res => {
					this.loading = false
					this.$Message.error(res.msg)
				})
			},
			// 添加表单
			groupAdd () {
				this.$modalForm(groupDataAddApi({ gid: this.signList[0].gid,config_name:this.name}, 'setting/group_data/create')).then(() => {
					this.info()
				})
			},
			// 编辑
			edit (row) {
				this.$modalForm(groupDataEditApi({gid:row.gid,config_name:this.name}, 'setting/group_data/'+row.id+'/edit')).then(() => {
					this.info()
				})
			},
			getGroupAll () {
			    groupAllApi().then(async res => {
			        let groupAll = res.data;
					groupAll.forEach(item=>{
						if(item.config_name == this.name){
							this.pageId = item.id;
						}
					})
			    }).catch(res => {
			        this.$Message.error(res.msg)
			    })
			},
			// 删除
			del (row, tit, num) {
				let delfromData = {
					title: tit,
					num: num,
					url:'setting/group_data/'+row.id,
					method: 'DELETE',
					ids: ''
				}
				this.$modalSure(delfromData).then((res) => {
					this.info()
					this.$Message.success(res.msg)
				}).catch(res => {
					this.$Message.error(res.msg)
				})
			},
			// 修改是否显示
			onchangeIsShow (row) {
				groupDataSetApi('setting/group_data/set_status/'+row.id+'/'+row.status).then(async res => {
					this.$Message.success(res.msg)
					this.info()
				}).catch(res => {
					this.$Message.error(res.msg)
				})
			},
			getListHeader(){
				this.loading = true
				groupDataHeaderApi({config_name:this.name},'setting/sign_data/header').then(res=>{
					this.columns = res.data.header
					this.loading = false
				}).catch(res => {
					this.loading = false
					this.$Message.error(res.msg)
				})
			},
			color(){
				getColorChange('color_change').then(res=>{
					this.statusColor = res.data.status;
				})
			}
        }
    }
</script>

<style scoped lang="stylus">
	.title{
		padding: 0 0 13px 0
		font-weight: bold;
		font-size: 15px;
		border-left: 2px solid #1890FF;
		height: 23px;
		padding-left: 10px;
	}
	.iframe{
		margin-left 20px
		position: relative
		width: 310px;
		height: 550px;
		background: #FFFFFF;
		border: 1px solid #EEEEEE;
		opacity: 1;
		border-radius: 10px;
	}

	/deep/.ivu-form-item-content{
		margin-left 0px !important
	}
	.table{
		color: #515a6e;
		font-size: 14px;
		background-color: #fff;
		box-sizing: border-box;
		margin: 0 auto;
		margin-left: 20px;

	}
	.iframe-box {
	    width: 310px;
	    height: 550px;
	    border-radius: 10px;
		// margin: 30px 15px 0px 15px
		background: rgba(0, 0, 0, 0);
		border: 1px solid #EEEEEE;
		opacity: 1;
		img{
			width 100%
			height 100%
		}
	}
	</style>
