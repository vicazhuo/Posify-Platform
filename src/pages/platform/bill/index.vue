<template>
	<div>
		<Card :bordered="false" dis-hover>
			<PageHeader class="product_tabs" :title="$route.meta.title" hidden-breadcrumb>
			    <div slot="content">
			        <div class="new_tab">
						<Tabs @on-click="onClickTab">
							<TabPane label="日账单" name=" "/>
							<TabPane label="周账单" name="1"/>
							<TabPane label="月账单" name="2"/>
						</Tabs>
					</div>
			    </div>
			</PageHeader>
		</Card>
		
		<Card :bordered="false" dis-hover class="searchbox">
			<Form
			  ref="formValidate"
			  :model="formValidate"
			  :label-width="labelWidth"
			  :label-position="labelPosition"
			  @submit.native.prevent
			>
				<FormItem label="订单时间：">
					<RadioGroup
						v-model="formValidate.data"
						type="button"
						@on-change="selectChange(formValidate.data)"
						style="margin-right: 20px"
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
						style="margin-right: 20px;"
						>
					</DatePicker>
				</FormItem>
			</form>
		</Card>
		
		<Card :bordered="false" dis-hover>
			<Table :columns="columns" :data="orderList" ref="table" class="mt25"
			       :loading="loading" highlight-row
			       no-userFrom-text="暂无数据"
			       no-filtered-userFrom-text="暂无筛选结果">
					<template slot-scope="{ row, index }" slot="money">
					    <span style="color: red;">￥{{row.money}}</span>
					</template>
					<template slot-scope="{ row, index }" slot="action">
					    <a>账单详情</a>
					    <Divider type="vertical"/>
					    <a>下载</a>
					</template>
			</Table>
		</Card>
	</div>
</template>

<script>
	export default{
		name: 'bill',
		computed: {
		  labelWidth() {
		    return this.isMobile ? undefined : 80;
		  },
		  labelPosition() {
		    return this.isMobile ? "top" : "left";
		  },
		},
		data() {
			return{
				grid: {
				    xl: 6,
				    lg: 7,
				    md: 5,
				    sm: 5,
				    xs: 5
				},
				formValidate: {
				  status: 1,
				  extract_type: "",
				  nireid: "",
				  data: "",
				  page: 1,
				  limit: 20,
				},
				fromList: {
				  title: "选择时间",
				  custom: true,
				  fromTxt: [
				    { text: "全部", val: "" },
				    { text: "昨天", val: "yesterday" },
				    { text: "今天", val: "today" },
				    { text: "本周", val: "week" },
				    { text: "本月", val: "month" },
				    { text: "本季度", val: "quarter" },
				    { text: "本年", val: "year" },
				  ],
				},
				timeVal: [],
				loading: false,
				columns: [
					{
						title: 'ID',
						key: 'id',
						minWidth: 50
					},
					{
						title: '日期',
						key: 'id',
						minWidth: 80
					},
					{
						title: '收入金额',
						slot: 'money',
						minWidth: 80
					},
					{
						title: '支出金额',
						key: 'id',
						minWidth: 80
					},
					{
						title: '平台应入账金额',
						key: 'id',
						minWidth: 80
					},
					{
					    title: '操作',
					    slot: 'action',
					    fixed: 'right',
					    minWidth: 100,
					    align: 'center'
					}
				],
				orderList:[
					{id:"1",money:"12",pay_price:"12",status:1,phone:'13000000000',address:'陕西省西安市莲湖区大兴西路啊餐厨'}
				]
			}
		},
		methods:{
			onClickTab(e){
				console.log(e)
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
		}
	}
</script>

<style scoped lang="less">
	/deep/.ivu-page-header,/deep/.ivu-tabs-bar{border-bottom: 1px solid #ffffff;}
	/deep/.ivu-card-body{padding: 0;}
	/deep/.ivu-tabs-nav{height: 45px;}
	.searchbox{margin-top: 15px;padding: 20px;padding-bottom: 1px;}
	.new_tab {
		.ivu-tabs-nav .ivu-tabs-tab{
			padding:4px 16px 20px !important;
			font-weight: 500;
		}
	}
</style>
