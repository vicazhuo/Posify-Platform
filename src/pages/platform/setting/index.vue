<template>
	<div>
		<Card :bordered="false" dis-hover>
			<PageHeader class="product_tabs" :title="$route.meta.title" hidden-breadcrumb></PageHeader>
		</Card>
		<Card :bordered="false" dis-hover class="tapbox">
			<Tabs @on-click="onClickTab">
			    <TabPane label="手续费设置" name="1"/>
			    <TabPane label="提现设置" name="2"/>
			</Tabs>
		</Card>
		
		<div class="box" v-if="name == 1">
			<Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="140" >
				<FormItem label="收银台订单手续费：" prop="name">
					<div class="ls">
						<Input v-model="formValidate.name" type="number" placeholder="0" width="200px"></Input>
						<div class="l">%</div>
					</div>
				</FormItem>
				<FormItem label="分配订单手续费：" prop="name">
					<div class="ls">
						<Input v-model="formValidate.name" type="number" placeholder="0" width="200px"></Input>
						<div class="l">%</div>
					</div>
				</FormItem>
				<FormItem label="核销订单手续费：" prop="name">
					<div class="ls">
						<Input v-model="formValidate.name" type="number" placeholder="0" width="200px"></Input>
						<div class="l">%</div>
					</div>
				</FormItem>
				<FormItem label="充值订单返点：" prop="name">
					<div class="ls">
						<Input v-model="formValidate.name" type="number" placeholder="0" width="200px"></Input>
						<div class="l">%</div>
					</div>
				</FormItem>
				<FormItem label="购买付费会员返点：" prop="name">
					<div class="ls">
						<Input v-model="formValidate.name" type="number" placeholder="0" width="200px"></Input>
						<div class="l">%</div>
					</div>
				</FormItem>
			</Form>
		</div>
		
		<div class="box" v-if="name == 2">
			<Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="140" >
				<FormItem label="最低提现金额：" prop="name">
					<div class="ls">
						<Input v-model="formValidate.name" type="number" placeholder="0" width="200px"></Input>
						<div class="l">%</div>
					</div>
				</FormItem>
				<FormItem label="最高提现金额：" prop="name">
					<div class="ls">
						<Input v-model="formValidate.name" type="number" placeholder="0" width="200px"></Input>
						<div class="l">%</div>
					</div>
				</FormItem>
			</Form>
		</div>
		
		<Card :bordered="false" dis-hover class="save">
			<div class="savebox" @click="save">保存</div>
		</Card>
		
	</div>
</template>

<script>
	export default {
	    name: 'order',
	    data () {
			return{
				name: 1,
				formValidate: {
					name: '',
				},
				ruleValidate: {
					name: [
						{ required: true, message: '请输入额度', trigger: 'blur' }
					],
				}
			}
		},
		methods:{
			save(){
				console.log(this.formValidate)
			},
			onClickTab(e){
				console.log(e)
				this.name = e
			}
		}
	}
</script>

<style scoped lang="stylus">
	/deep/.ivu-page-header,/deep/.ivu-tabs-bar{border-bottom: 1px solid #ffffff;}
	/deep/.ivu-card-body{padding: 0;}
	/deep/.ivu-tabs-nav{height: 45px;}
	/deep/input::-webkit-outer-spin-button,
	/deep/input::-webkit-inner-spin-button {
		-webkit-appearance: none !important;
		margin: 0;
	}
	.tapbox{margin-top: 15px;padding: 20px;padding-bottom: 1px;}
	.height{
		line-height: 70px;
	}
	.ls{display: flex;}
	.l{margin-left: 10px;}
	.box{
		// height: 670px;
		background: #FFFFFF;
		padding: 15px 14px 26px;
		text-align: center;
		display: flex;
		justify-content: center;
		padding-top: 70px;
		font-size: 13px;
		font-family: PingFangSC-Regular, PingFang SC;
		font-weight: bold;
		color: rgba(0, 0, 0, 0.85);
		.input{
			width: 200px;
			font-size: 13px;
			font-weight: 400;
			color: rgba(0, 0, 0, 0.25);
		}
	}
	.save{
		border-top: 1px solid  #FFFFFF;
		box-shadow: 0px -2px 4px 0px rgba(0, 0, 0, 0.05);
		padding-bottom: 10px;
		.savebox{
			width: 86px;
			height: 30px;
			background: #1890FF;
			border-radius: 2px;
			text-align: center;
			cursor: pointer;
			line-height: 30px;
			color: #FFFFFF;
			margin: 0 auto;
			margin-top: 10px;
				
		}
	}
</style>
