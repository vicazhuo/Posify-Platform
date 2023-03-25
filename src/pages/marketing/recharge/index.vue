<template>
    <div  :style="bgcolors">
		<!-- <div class="i-layout-page-header">
		  <PageHeader
		    class="product_tabs"
		    :title="$route.meta.title"
		    hidden-breadcrumb
		  ></PageHeader>
		</div> -->
        <Card :bordered="false" dis-hover class="ivu-mt">
            <Row class="ivu-mt box-wrapper">
				<Col class="iframe">
					<div class="iframe-boxs">
						<div class="moneyBox">
							<div class="box1">
								<div class="font1">我的余额</div>
								<div>￥ <i class="font2">0.00</i></div>
							</div>
							<div class="moneyBox_content">
								<div class="box2">
									<div>账户充值</div>
									<div>佣金导入</div>
								</div>
								<div class="box3">
										<div v-if="item.status != 0" class="box3_box" v-for="(item,index) in recharge">
											<div>{{item.price}}<i class="font">元</i></div>
											<div  class="font">赠送:{{item.give_money}}元</div>
										</div>
									<div class="box3_box">
										<div class="other">其他</div>
									</div>

								</div>
								<div class="box4">
									<div class="tips">注意事项：</div>
									<div class="tips-samll">
										<p>充值后帐户的金额不能提现，可用于商城消费使用。</p>
										<p>佣金导入账户之后不能再次导出、不可提现。</p>
										<p>账户充值出现问题可联系商城客服，也可拨打商城客服热线：40088888889。</p>
									</div>
								</div>
								<div class="box5">立即充值</div>
							</div>
						</div>
					</div>
				</Col>
				<Col :xs="24" :sm="24" :md="15" :lg="15" style="margin-left: 20px;">
					<div class="table_box">
						<Row type="flex">
							<Col v-bind="grid">
								<div class="title">充值金额设置</div>
								<Button type="primary"  @click="groupAdd('添加数据')" style="margin-left: 14px;margin-top: 30px;">添加数据</Button>
							</Col>
						</Row>
						<div class="table">
							<Table :columns="columns" :data="recharge" ref="table" class="mt25"
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
			bgcolors(){
				return {
					'--color-theme':this.bgCol
				}
			},
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
				name:'user_recharge_quota',
				pageId:0,
				columns:[],
				bgCol:'',
				loading: false,
				recharge:[]
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
				this.recharge = res.data.list
				}).catch(res => {
					this.loading = false
					this.$Message.error(res.msg)
				})
			},
			// 添加表单
			groupAdd () {
				this.$modalForm(groupDataAddApi({ gid: this.recharge[0].gid,config_name:this.name}, 'setting/group_data/create')).then(() => {
					this.info()
				})
			},
			// 编辑
			edit (row) {
                console.log(row);
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
					switch(res.data.status){
						case 1:
							this.bgCol = '#1DB0FC'
						break;
						case 2:
							this.bgCol = '#42CA4D'
						break;
						case 3:
							this.bgCol = '#e93323'
						break;
						case 4:
							this.bgCol = '#FF448F'
						break;
						case 5:
							this.bgCol = '#FE5C2D'
						break;
					}
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
	.iframe-boxs::-webkit-scrollbar {
		display: none;
	}

	.iframe-boxs {
		    width: 310px;
		    height: 550px;
		    border-radius: 10px;
			background: rgba(0, 0, 0, 0);
			border: 1px solid #EEEEEE;
			opacity: 1;
			overflow: auto;
			.moneyBox{
				background-color var(--color-theme)
				height 414px
				border-radius: 10px;
				.box1{
					text-align center
					color #FFFFFF
					padding-bottom 15px
					.font1{
						padding-top 20px
						// padding-bottom 15px
						font-size 12px
						opacity: 0.6;
					}
					.font2{
						font-size 30px
						font-style normal
						opacity: 0.9;
					}
				}
				.moneyBox_content{
					background-color #FFFFFF
					height 317px
					border-radius: 4px;
					.box2{
						display flex
						justify-content space-around
						height 35px
						line-height 35px
						margin-bottom 10px
						div:nth-child(1){
							font-weight bold
							border-bottom 2px solid var(--color-theme)
						}
					}
					.box3{
						padding 0px 10px
						display flex
						justify-content left
						flex-wrap wrap
						.box3_box{
							width 90px
							height 55px
							border-radius 9px
							background-color #f4f4f4
							color #888
							margin-bottom 10px
							text-align: center;
							padding-top: 3px;
							font-size 19px
							margin-right: 3px;
							margin-left: 3px;
							.font{
								font-size 11px
								font-style normal
							}
						}
						.box3_box:nth-child(1){
							width 90px
							height 55px
							border-radius 9px
							background-color  var(--color-theme)
							color #FFFFFF
							text-align: center;
							padding-top: 3px;
							margin-right: 3px;
							margin-left: 3px;
						}
						.other{
							line-height 55px
						}

					}
					.box4{
						padding 0px 10px
						.tips {
							font-size: 14px;
							color: #333333;
							font-weight: 800;
							margin-bottom: 7px;
							margin-top: 10px;
						}
						.tips-samll {
							font-size: 12px;
							color: #333333;
							margin-bottom: 7px;
							p{
								margin 2px 0px
							}
						}
					}
					.box5{
						font-size: 15px;
						width: 225px;
						height: 40px;
						border-radius: 25px;
						margin: 23px auto 0 auto;
						line-height: 40px;
						text-align: center;
						background-color: var(--color-theme);
						color #FFFFFF
					}
				}

			}
		}
	</style>
