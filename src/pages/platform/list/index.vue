<template>
	<div>
		<Card :bordered="false" dis-hover>
			<PageHeader class="product_tabs" :title="$route.meta.title" hidden-breadcrumb>
			    <div slot="content">
			        <Tabs @on-click="onClickTab">
			            <TabPane label="全部" name=" "/>
			            <TabPane label="营业中" name="1"/>
			            <TabPane label="休息中" name="2"/>
			            <TabPane label="已停业" name="3"/>
			        </Tabs>
			    </div>
			</PageHeader>
		</Card>
		
		<Card :bordered="false" dis-hover class="ive-mt tablebox">
			<div class="btnbox">
				<div class="btns">添加门店</div>
			</div>
			<div class="table">
				<Table :columns="columns" :data="orderList" ref="table" class="mt25"
				       :loading="loading" highlight-row
				       no-userFrom-text="暂无数据"
				       no-filtered-userFrom-text="暂无筛选结果">
				    <template slot-scope="{ row, index }" slot="status">
				        <template>
				            <div v-if="row.status===0">未发货</div>
				            <div v-else-if="row.status===1">待收货</div>
				            <div v-else-if="row.status===2">待评价</div>
				            <div v-else-if="row.status===3">已完成</div>
				        </template>
				    </template>
				    <template slot-scope="{ row, index }" slot="action">
				        <a>进入门店</a>
				        <Divider type="vertical"/>
				        <a>停业</a>
				        <Divider type="vertical"/>
				        <a>编辑</a>
				        <Divider type="vertical"/>
				        <a>删除</a>
				    </template>
				</Table>
			</div>
		</Card>
			
	</div>
</template>

<script>
	export default {
	    name: 'store',
	    data () {
			return{
				loading: false,
				columns: [
					{
						title: 'ID',
						key: 'id',
						minWidth: 50
					},
					{
						title: '门店图片',
						key: 'id',
						minWidth: 80
					},
					{
						title: '门店名称',
						key: 'id',
						minWidth: 80
					},
					{
						title: '联系电话',
						key: 'phone',
						minWidth: 80
					},
					{
						title: '门店地址',
						key: 'address',
						ellipsis: true,  
						minWidth: 150
					},
					{
						title: '营业时间',
						key: 'id',
						minWidth: 120
					},
				    {
				        title: '营业状态',
				        slot: 'status',
				        minWidth: 80
				    },
				    {
				        title: '操作',
				        slot: 'action',
				        fixed: 'right',
				        minWidth: 180,
				        align: 'center'
				    }
				],
				orderList:[
					{id:"1",order_id:"12",pay_price:"12",status:1,phone:'13000000000',address:'陕西省西安市莲湖区大兴西路啊餐厨'}
				]
			}
		},
	    mounted () {},
	    methods: {
			searchs(){},
	        onClickTab (e) {
				console.log(e)
	        },
	    },
	}
</script>

<style scoped lang="less">
	/deep/.ivu-page-header,/deep/.ivu-tabs-bar{border-bottom: 1px solid #ffffff;}
	/deep/.ivu-card-body{padding: 0;}
	/deep/.ivu-tabs-nav{height: 45px;}
	.tablebox{margin-top: 15px;}
	.btnbox{
		padding: 20px 0px 0px 30px;
		.btns{width: 99px;height: 32px;background: #1890FF;border-radius: 4px;text-align: center;line-height: 32px;color: #FFFFFF;cursor: pointer;}
	}
	.table{padding: 0px 30px 15px 30px;}
	.search{
		width: 86px;
		height: 32px;
		background: #1890FF;
		border-radius: 4px;
		text-align: center;
		line-height: 32px;
		font-size: 13px;
		font-family: PingFangSC-Regular, PingFang SC;
		font-weight: 400;
		color: #FFFFFF;
		cursor: pointer;
	}
	.reset{
		width: 86px;
		height: 32px;
		border-radius: 4px;
		border: 1px solid rgba(151, 151, 151, 0.36);
		text-align: center;
		line-height: 32px;
		font-size: 13px;
		font-family: PingFangSC-Regular, PingFang SC;
		font-weight: 400;
		color: rgba(0, 0, 0, 0.85);
		cursor: pointer;
	}

</style>
