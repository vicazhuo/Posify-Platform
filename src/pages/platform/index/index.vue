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
				  <RadioGroup
				    v-model="formValidate.data"
				    type="button"
				    @on-change="selectChange(formValidate.data)"
					class="time"
				  >
				    <Radio
				      :label="item.val"
				      v-for="(item, i) in fromList.fromTxt"
				      :key="i"
				      >{{ item.text }}</Radio
				    >
				  </RadioGroup>
				  <DatePicker
				    :editable="false"
				    @on-change="onchangeTime"
				    :value="timeVal"
				    format="yyyy/MM/dd"
				    type="daterange"
				    placement="bottom-end"
				    placeholder="自定义时间"
				    style="width: 200px"
				  ></DatePicker>
				</FormItem>
			</Form>
		</Card>
		<cards-data :cardLists="cardLists"></cards-data>
		<Card :bordered="false" dis-hover>
			<div class="ivu-pl-8 fonts">营业趋势</div>
			<echarts-from ref="visitChart" :series="series"  :echartsTitle="inlie" :infoList="infoList" v-if="infoList" :yAxisData="yAxisData"></echarts-from>
		</Card>
		<Row :gutter="24"  class="ivu-mt">
		    <Col :xl="16" :lg="12" :md="24" :sm="24" :xs="24" class="ivu-mb dashboard-console-visit">
		        <Card :bordered="false" dis-hover class="tablebox" >
					<!-- <Avatar icon="ios-analytics" size="small" v-color="'#1890ff'" v-bg-color="'#e6f7ff'" /> -->
					<div class="ivu-pl-8 fonts">交易数据</div>
					<Table ref="selection" :columns="columns4" :data="tabList" :loading="loading"
					       no-data-text="暂无数据" highlight-row
					       no-filtered-data-text="暂无筛选结果">
					</Table>
		        </Card>
		    </Col>
		    <Col :xl="8" :lg="12" :md="24" :sm="24" :xs="24">
		       <Card :bordered="false" dis-hover class="dashboard-console-visit">
		            <div slot="title">
						<div class="ivu-pl-8 fonts">购买用户统计</div>
		            </div>
		            <echarts-from ref="visitChart" :infoList="infoLists" :echartsTitle="circle"></echarts-from>
		        </Card>
		    </Col>
		</Row>
		<Card :bordered="false" dis-hover class="ivu-mt box">
			<div class="fonts">店员业绩</div>
			<Table ref="selection" :columns="columns4" :data="tabList" :loading="loading"
			       no-data-text="暂无数据" highlight-row
			       no-filtered-data-text="暂无筛选结果">
			</Table>
		</Card>
	</div>
      
</template>

<script>
	import { orderApi, userApi } from '@/api/index'
	import echartsFrom from '@/components/echarts/index'
	import cardsData from "@/components/cards/cards";
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
				  data: "",
				  page: 1,
				  limit: 20,
				},
				timeVal: [],
				fromList: {
				  title: "选择时间",
				  custom: true,
				  fromTxt: [
				    { text: "全部", val: "" },
				    { text: "近一周", val: "week" },
				    { text: "近30天", val: "month" },
				    { text: "近半年", val: "year" },
				  ],
				},
				cardLists: [],
				extractStatistics: {
					price: '11',
					brokerage_count: '23',
					priced: '34'
					
				},
				series: [],
				yAxisData: [],
				infoList: {},
				infoLists: {},
				circle: 'circle1',
				inlie: 'inlie',
				columns4: [
				    {
				        title: '店员名称',
				        key: 'name',
				        minWidth: 80
				    },
				    {
				        title: '推广用户数',
				        width: 180,
				        key: 'store_name'
				    },
				    {
				        title: '推广用户消费金额',
				        key: 'visit',
				        minWidth: 100
				    },
				    {
				        title: '推广付费会员数',
				        key: 'user',
				        minWidth: 100
				    },
				    {
				        title: '推广付费会员金额',
				        key: 'cart',
				        minWidth: 100
				    },
				],
				tabList: [
					{name:"小四",store_name:"12",pay:"122",cart:"22",user:'55',visit:'22'},
					{name:"李三",store_name:"145",pay:"645",cart:"22",user:'224',visit:'2'},
					{name:"王五",store_name:"43",pay:"353",cart:"22",user:'75',visit:'98'},
					{name:"王五",store_name:"43",pay:"353",cart:"22",user:'75',visit:'98'},
					{name:"王五",store_name:"43",pay:"353",cart:"22",user:'75',visit:'98'}
				],
			}
		},
		computed: {
		  labelWidth() {
		    return this.isMobile ? undefined : 80;
		  },
		  labelPosition() {
		    return this.isMobile ? "top" : "left";
		  },
		},
		created(){
			this.cardList()
			this.getStatistics()
		},
		methods:{
			// 统计
			getStatistics () {
			    let data = {
			        cycle: this.visitDate
			    }
			    orderApi(data).then(async res => {
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
			    }).catch(res => {
			        this.$Message.error(res.msg)
			    })
				userApi().then(async res => {
				    this.infoLists = res.data;
					})
			},
			// 选择时间
			selectChange(tab) {
			  this.formValidate.page = 1;
			  this.formValidate.data = tab;
			  this.timeVal = [];
			  console.log( this.formValidate.data)
			  // this.getList();
			},
			// 具体日期
			onchangeTime(e) {
			  this.timeVal = e;
			  this.formValidate.data = this.timeVal[0] ? this.timeVal.join("-") : "";
			  this.formValidate.page = 1;
			  console.log( this.formValidate.data)
			  // this.getList();
			},
			cardList(){
				this.cardLists = [
				  {
				    col: 6,
				    count: this.extractStatistics.price,
				    name: "门店实际收款",
				    className: "md-basket",
				  },
				  {
				    col: 6,
				    count: this.extractStatistics.brokerage_count,
				    name: "余额消耗金额",
				    className: "md-pricetags",
				  },
				  {
				    col: 6,
				    count: this.extractStatistics.priced,
				    name: "用户充值金额",
				    className: "md-cash",
				  },
				  {
				    col: 6,
				    count: this.extractStatistics.priced,
				    name: "收银订单金额",
				    className: "ios-cash",
				  },
				  {
				    col: 6,
				    count: this.extractStatistics.priced,
				    name: "付费会员金额",
				    className: "md-cash",
				  },
				  {
				    col: 6,
				    count: this.extractStatistics.priced,
				    name: "分配订单金额",
				    className: "ios-cash",
				  },
				  {
				    col: 6,
				    count: this.extractStatistics.priced,
				    name: "核销订单金额",
				    className: "md-cash",
				  },
				  {
				    col: 6,
				    count: this.extractStatistics.priced,
				    name: "门店新增用户数",
				    className: "md-cash",
				  },
				  {
				    col: 6,
				    count: this.extractStatistics.priced,
				    name: "门店成交用户数",
				    className: "md-cash",
				  },
				  {
				    col: 6,
				    count: this.extractStatistics.priced,
				    name: "门店会员卡激活数",
				    className: "md-cash",
				  }
				]
			},
			
			// 监听页面宽度变化，刷新表格
			handleResize () {
			    if (this.infoList) this.$refs.visitChart.handleResize()
			}
		}
	}
</script>

<style scoped lang="less">
	// @media (min-width: 1200px)
	/deep/.ivu-col-span-xl-6 {
	    display: block;
	    flex: 0 0 25%;
	    max-width: 20%;
	}
	.tablebox{height: 386px;overflow: hidden;overflow-y: scroll;}
	.box{padding-bottom: 70px;}
	.fonts{margin-bottom: 10px;font-weight: bold;}
	.ivu-form-item{margin-bottom: 0px !important;}
	.time{margin-right: 20px;}
</style>
