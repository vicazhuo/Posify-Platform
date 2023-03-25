<template>
    <div v-resize="handleResize">
		<Card :bordered="false" dis-hover class="ivu-mt">
			<Form
			  ref="formValidate"
			  :model="formValidate"
			  :label-width="labelWidth"
			  :label-position="labelPosition"
			  @submit.native.prevent
			>
				<FormItem label="时间筛选：">
				  <DatePicker
				    :editable="false"
					:clearable="true"
				    @on-change="onchangeTime"
				    :value="timeVal"
				    format="yyyy/MM/dd"
				    type="datetimerange"
				    placement="bottom-start"
				    placeholder="自定义时间"
				    style="width: 250px"
					:options="options"></DatePicker>
				</FormItem>
			</Form>
		</Card>
		<cards-data :cardLists="cardLists"></cards-data>
		<Card :bordered="false" dis-hover>
			<div class="ivu-pl-8 fonts">营业趋势</div>
			<echarts-from ref="visitChart" :series="series"  :echartsTitle="inlie" :infoList="infoList" v-if="infoList" :yAxisData="yAxisData"></echarts-from>
		</Card>
		<Row :gutter="24"  class="ivu-mt">
		    <Col :xl="15" :lg="12" :md="24" :sm="24" :xs="24" class="ivu-mb dashboard-console-visit">
		        <Card :bordered="false" dis-hover class="tablebox" >
					<!-- <Avatar icon="ios-analytics" size="small" v-color="'#1890ff'" v-bg-color="'#e6f7ff'" /> -->
					<div class="ivu-pl-8 fonts">交易数据</div>
					<Table ref="selection" :columns="columns2" :data="tabList2" :loading="loading"  height="300" 
					       no-data-text="暂无数据" highlight-row
					       no-filtered-data-text="暂无筛选结果">
						   <template slot-scope="{ row, index }" slot="avatar">
						   	<img v-if="row.uid" :src="row.avatar" alt="">
								<img v-else src="@/assets/images/yonghu.png"/>
						   </template>
							 <template slot-scope="{ row, index }" slot="nickname">
								 <div>{{row.uid?row.nickname:'游客'}}</div>
							 </template>
						   <template slot-scope="{ row, index }" slot="pay_time">
						   	<div>{{$moment(row.pay_time * 1000).format('YYYY-MM-DD H:mm:ss')}}</div>
						   </template>
						   <template slot-scope="{ row, index }" slot="pay_price">
						   	<span class="colorred">￥ {{row.pay_price}}</span>
						   </template>
					</Table>
		        </Card>
		    </Col>
		    <Col :xl="9" :lg="12" :md="24" :sm="24" :xs="24">
		       <Card :bordered="false" dis-hover class="dashboard-console-visit">
		            <div slot="title">
						<div class="ivu-pl-8 fonts">交易类型</div>
		            </div>
		            <echarts-from ref="visitChart" :infoList="infoLists" :echartsTitle="circle"></echarts-from>
		        </Card>
		    </Col>
		</Row>
		<Card :bordered="false" dis-hover class="ivu-mt box">
			<div class="fonts">门店业绩</div>
			<Table ref="selection" :columns="columns" :data="tabList" :loading="loading"
			       no-data-text="暂无数据" highlight-row
			       no-filtered-data-text="暂无筛选结果">
			</Table>
		</Card>
	</div>
      
</template>

<script>
	import { mapState } from 'vuex'
	import { headerApi, orderCharts, storeApi, operateApi } from '@/api/store'
	import echartsFrom from '@/components/echarts/index'
	import cardsData from "@/components/cards/cards";
	import timeOptions from "@/utils/timeOptions"
	export default {
		name: "home",
		components: { cardsData, echartsFrom },
		data() {
			return{
				loading:false,
				optionData:{},
				formValidate: {
				  status: "",
				  extract_type: "",
				  nireid: "",
				  data: "thirtyday",
				  page: 1,
				  limit: 20,
				},
				timeVal: [],
				options:timeOptions,
				cardLists: [],
				extractStatistics: {},
				series: [],
				yAxisData: [],
				infoList: {},
				infoLists: {},
				circle: 'circle1',
				inlie: 'inlie',
				columns: [
				    {
				        title: '门店名称',
				        key: 'name',
				        minWidth: 80
				    },
				    {
				        title: '门店实际收款',
				        width: 180,
				        key: 'store_price'
				    },
				    {
				        title: '门店商品销售额',
				        key: 'store_order_price',
				        minWidth: 100
				    },
				    {
				        title: '门店商品销量',
				        key: 'store_product_count',
				        minWidth: 100
				    },
				    {
				        title: '门店新增用户数',
				        key: 'store_user_count',
				        minWidth: 100
				    },
				],
				columns2: [
				    {
				        title: '头像',
				        slot: 'avatar',
				        minWidth: 50
				    },
				    {
				        title: '用户名称',
				        minWidth: 100,
				        slot: 'nickname'
				    },
				    {
				        title: '订单号',
				        key: 'order_id',
				        minWidth: 100
				    },
				    {
				        title: '交易金额',
				        slot: 'pay_price',
				        minWidth: 100
				    },
				    {
				        title: '成交时间',
				        slot: 'pay_time',
				        minWidth: 100
				    },
				],
				tabList: [],
				tabList2: [],
			}
		},
		computed: {
			...mapState('admin/layout', [
			    'isMobile'
			]),
		  labelWidth() {
		    return this.isMobile ? undefined : 80;
		  },
		  labelPosition() {
		    return this.isMobile ? "top" : "right";
		  },
		},
		created(){
			this.cardList()
			this.orderchart()
			this.staff()
			this.trends()
		},
		methods:{
			int(){
				this.cardList()
				this.orderchart()
				this.trends()
				this.staff()
			},
			trends(){
				operateApi({data:this.formValidate.data}).then(res=>{
					this.infoList = res.data || {}
					this.series = this.infoList.series || [],
					this.yAxisData = [
						{
							type: 'value',
							name: '',
							axisLine: {
								show: false
							},
							axisTick: {
								show: false
							},
							axisLabel: {
								textStyle: {
									color: '#7F8B9C'
								}
							},
							splitLine: {
								show: true,
								lineStyle: {
									color: '#F5F7F9'
								}
							}
						},
						{
							type: 'value',
							name: '',
							axisLine: {
								show: false
							},
							axisTick: {
								show: false
							},
							axisLabel: {
								textStyle: {
									color: '#7F8B9C'
								}
							},
							splitLine: {
								show: true,
								lineStyle: {
									color: '#F5F7F9'
								}
							}
						}
					]
													
				})
			},
			orderchart(){
				orderCharts({data:this.formValidate.data}).then(res=>{
					this.tabList2 = res.data.order_list
					this.infoLists = res.data
					
				})
			},
			staff(){
				storeApi({data:this.formValidate.data}).then(res=>{
					this.tabList = res.data
				})
			},
			// 选择时间
			selectChange(tab) {
			  this.formValidate.page = 1;
			  this.formValidate.data = tab;
			  this.timeVal = [];
			  this.int()
			},
			// 具体日期
			onchangeTime(e) {
			  this.timeVal = e;
			  this.formValidate.data = this.timeVal[0] ? this.timeVal.join("-") : "";
			  if(e[0]==''){
				this.formValidate.data = 'thirtyday';
			  }
			  this.formValidate.page = 1;
			  this.int()
			},
			cardList(){
				headerApi({data:this.formValidate.data}).then(res=>{
					this.extractStatistics = res.data
					this.cardLists = [
						  {
						    col: 6,
						    count: this.extractStatistics.store_income,
						    name: "门店订单金额",
						    className: "iconmendiandingdanjine",
								type:1
						  },
						  {
						    col: 6,
						    count: this.extractStatistics.store_use_yue,
						    name: "余额消耗金额",
						    className: "iconyuexiaohaojine",
								type:1
						  },
						  {
						    col: 6,
						    count: this.extractStatistics.recharge_price,
						    name: "用户充值金额",
						    className: "iconmendianxinzengyonghushu",
								type:1
						  },
						  {
						    col: 6,
						    count: this.extractStatistics.cashier_order_price,
						    name: "收银订单金额",
						    className: "iconshouyindingdanjine",
								type:1
						  },
						  {
						    col: 6,
						    count: this.extractStatistics.vip_price,
						    name: "付费会员金额",
						    className: "iconfufeihuiyuanjine",
								type:1
						  },
						  {
						    col: 6,
						    count: this.extractStatistics.store_order_price,
						    name: "分配订单金额",
						    className: "iconfenpeidingdanjine",
								type:1
						  },
						  {
						    col: 6,
						    count: this.extractStatistics.store_writeoff_order_price,
						    name: "核销订单金额",
						    className: "iconhexiaodingdanjine",
								type:1
						  },
						  {
						    col: 6,
						    count: this.extractStatistics.store_user_count,
						    name: "门店新增用户数",
						    className: "iconxinzengyonghushu1",
								type:1
						  },
						  {
						    col: 6,
						    count: this.extractStatistics.store_pay_user_count,
						    name: "门店成交用户数",
						    className: "iconmendianchengjiaoyonghushu",
								type:1
						  },
						  {
						    col: 6,
						    count: this.extractStatistics.card_count,
						    name: "微信会员卡激活数",
						    className: "iconhuiyuankajihuoshu",
								type:1
						  }
						]
					
				})
				},
			
			// 监听页面宽度变化，刷新表格
			handleResize () {
			    if (this.infoList) this.$refs.visitChart.handleResize()
			}
		}
	}
</script>

<style scoped lang="less">
	
	.tablebox::-webkit-scrollbar {
	    display: none;
		background-color:none !important;
	}
	// @media (min-width: 1200px)
	/deep/.ivu-col-span-xl-6 {
	    display: block;
	    flex: 0 0 25%;
	    max-width: 20%;
	}
	img{width: 36px; height: 36px;border-radius: 50%;}
	.colorred{color: #F5222D;}
	.tablebox{height: 386px;overflow: hidden;overflow-y: scroll;}
	.box{padding-bottom: 70px;}
	.fonts{margin-bottom: 10px;font-weight: bold;}
	.ivu-form-item{margin-bottom: 0px !important;}
	.time{margin-right: 20px;}
</style>
