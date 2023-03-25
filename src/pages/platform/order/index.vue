<template>
	<div>
		<Card :bordered="false" dis-hover>
			<PageHeader class="product_tabs" :title="$route.meta.title" hidden-breadcrumb>
			    <div slot="content">
			       <div class="new_tab">
					    <Tabs @on-click="onClickTab">
							<TabPane label="收银台订单" name=" "/>
							<TabPane label="充值订单" name="1"/>
							<TabPane label="配送订单" name="2"/>
							<TabPane label="核销订单" name="3"/>
							<TabPane label="付费会员订单" name="4"/>
						</Tabs>
				   </div>
			    </div>
			</PageHeader>
		</Card>
		
		<Card :bordered="false" dis-hover class="ivu-mt box">
			<Form
			  ref="formValidate"
			  :model="formValidate"
			  :label-width="labelWidth"
			  :label-position="labelPosition"
			  @submit.native.prevent
			>
				<FormItem label="创建时间：">
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
				    style="width: 200px"
				  ></DatePicker>
				</FormItem>
				<Row type="flex" :gutter="24">
					<Col v-bind="grid">
						<FormItem label="订单状态："  label-for="status1">
							<Select v-model="formValidate.is_show" placeholder="请选择" clearable element-id="status1" @on-change="searchs">
								<Option value="1">显示</Option>
								<Option value="0">不显示</Option>
							</Select>
						</FormItem>
					</Col>
					<Col v-bind="grid">
						<FormItem label="选择门店："  label-for="status1">
							<Select v-model="formValidate.is_show" placeholder="请选择" clearable element-id="status1" @on-change="searchs">
								<Option value="1">显示</Option>
								<Option value="0">不显示</Option>
							</Select>
						</FormItem>
					</Col>
					<Col v-bind="grids">
						<FormItem label="订单搜索：" label-for="nickname">
						  <Input
						    v-model="formValidate.nickname"
						    placeholder="请输入"
						    element-id="nickname"
						    clearable
						  >
						    <Select
						      v-model="formValidate.nickname"
						      slot="prepend"
						      style="width: 80px"
						    >
						      <Option value="all">全部</Option>
						      <Option value="uid">UID</Option>
						      <Option value="phone">手机号</Option>
						      <Option value="nickname">用户昵称</Option>
						    </Select>
						  </Input>
						</FormItem>
					</Col>
					<Col>
						<div class="search">搜索</div>
					</Col>
					<Col>
						<div class="reset">重置</div>
					</Col>
				</Row>
				
			</Form>
		</Card>
		
		<Card :bordered="false" dis-hover class="ive-mt tablebox">
			<div class="btnbox"></div>
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
				        <a>订单详情</a>
				        <Divider type="vertical"/>
				        <a>记录</a>
				        <Divider type="vertical"/>
				        <a>备注</a>
				    </template>
				</Table>
			</div>
		</Card>
			
	</div>
</template>

<script>
	export default {
	    name: 'order',
	    data () {
			return{
				grid: {
				    xl: 6,
				    lg: 7,
				    md: 12,
				    sm: 24,
				    xs: 24
				},
				grids: {
				    xl: 7,
				    lg: 7,
				    md: 12,
				    sm: 24,
				    xs: 24
				},
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
				],
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
				    { text: "昨天", val: "yesterday" },
				    { text: "今天", val: "today" },
				    { text: "本周", val: "week" },
				    { text: "本月", val: "month" },
				    { text: "本季度", val: "quarter" },
				    { text: "本年", val: "year" },
				  ],
				},
				userFrom: {
				  label_id: "",
				  user_type: "",
				  status: "",
				  sex: "",
				  is_promoter: "",
				  country: "",
				  isMember: "",
				  pay_count: "",
				  user_time_type: "",
				  user_time: "",
				  nickname: "",
				  province: "",
				  city: "",
				  page: 1,
				  limit: 15,
				  level: "",
				  group_id: "",
				  field_key: "",
				},
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
	    mounted () {},
	    methods: {
			searchs(){},
	        onClickTab (e) {
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
	    },
	}
</script>

<style scoped lang="less">
	/deep/.ivu-page-header,/deep/.ivu-tabs-bar{border-bottom: 1px solid #ffffff;}
	/deep/.ivu-card-body{padding: 0;}
	/deep/.ivu-tabs-nav{height: 45px;}
	.box{padding: 20px;padding-bottom: 0px;}
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
	.new_tab {
		.ivu-tabs-nav .ivu-tabs-tab{
			padding:4px 16px 20px !important;
			font-weight: 500;
		}
	}
</style>
