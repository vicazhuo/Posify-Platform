<template>
	<div>
		<Card :bordered="false" dis-hover class="ivu-mt box">
			<Form
			  ref="formValidate"
			  inline
			  :model="formValidate"
			  :label-width="labelWidth"
			  :label-position="labelPosition"
			  @submit.native.prevent
			>
				<FormItem label="创建时间：">
				  <DatePicker
				    :editable="false"
				    @on-change="onchangeTime"
				    :value="timeVal"
				    format="yyyy/MM/dd"
				    type="datetimerange"
					placement="bottom-start"
					placeholder="自定义时间"
					style="width: 250px"
					:options="options"
				  ></DatePicker>
				</FormItem>
				<FormItem label="选择门店：">
					<Select v-model="formValidate.store_id" placeholder="请选择" clearable @on-change="search" style="width: 250px">
						<Option :value ="item.id" v-for="(item,index) in staff">{{item.name}}</Option>
					</Select>
				</FormItem>
				<FormItem label="订单搜索："  label-for="status1">
					<Input  v-model="formValidate.keyword" placeholder="请输入交易单号/交易人" style="width: 250px;" />
				</FormItem>
				<FormItem>
					<Button type="primary" @click="search()" style="margin-left:-75px;margin-right:14px;">查询</Button>
					<Button @click="exports">导出</Button>
				</FormItem>
			</Form>
		</Card>
		
		<Card :bordered="false" dis-hover class="ive-mt tablebox" :padding="20">
			<div class="table">
				<Table :columns="columns" :data="orderList" ref="table"
				       :loading="loading" highlight-row
				       no-userFrom-text="暂无数据"
				       no-filtered-userFrom-text="暂无筛选结果">
					<template slot-scope="{ row, index }" slot="number">
						<!-- <span class="color">{{row.number}}</span> -->
						<span v-if="row.pm == 0" class="colorgreen">- {{row.number}}</span>
						<span v-if="row.pm == 1" class="colorred">+ {{row.number}}</span>
				    </template>
						<template slot-scope="{ row, index }" slot="user_nickname">
						   <span>{{row.uid?row.user_nickname:'游客'}}</span>
						</template>
				    <template slot-scope="{ row, index }" slot="action">
				        <a @click="remark(row)">备注</a>
				    </template>
				</Table>
			</div>
			<div class="acea-row row-right page">
			  <Page
			    :total="total"
			    :current="formValidate.page"
			    show-elevator
			    show-total
			    @on-change="pageChange"
			    :page-size="formValidate.limit"
			  />
			</div>
		</Card>
		<!-- 备注 -->
		<Modal v-model="modalmark"  scrollable title="请修改内容"  class="order_box" :closable="false" :mask-closable="false">
		    <Form ref="remarks" :model="remarks" :label-width="80" @submit.native.prevent>
		        <FormItem label="备注：">
		            <Input v-model="remarks.mark" maxlength="200" show-word-limit type="textarea" placeholder="请填写备注~" style="width: 100%" />
		        </FormItem>
		    </Form>
		    <div slot="footer">
		        <Button  type="primary"  @click="putRemark()">提交</Button>
		        <Button   @click="cancel()">取消</Button>
		    </div>
		</Modal>
	</div>
</template>

<script>
	import { mapState } from 'vuex';
	import {storeFinanceInfo, storeFinanceMarkApi, staffListInfo,exportTableList } from "@/api/store"
	import exportExcel from "@/utils/newToExcel.js";
	import timeOptions from "@/utils/timeOptions"
	export default {
	    name: 'order',
	    data () {
			return{
				modalmark: false,
				remarks: {
					mark: ''
				},
				staff: [],
				total: 0,
				grid: {
				    xl: 7,
				    lg: 7,
				    md: 12,
				    sm: 24,
				    xs: 24
				},
				loading: false,
				columns: [
					{
						title: '交易单号',
						key: 'order_id',
						minWidth: 180
					},
					{
						title: '关联订单',
						key: 'link_id',
						minWidth: 180
					},
					{
						title: '交易时间',
						key: 'trade_time',
						minWidth: 150
					},
					{
						title: '交易金额',
						slot: 'number',
						minWidth: 80
					},
					{
						title: '交易人',
						slot: 'user_nickname',
						ellipsis: true,  
						minWidth: 80
					},
					{
						title: '关联店员',
						key: 'staff_name',
						minWidth: 80
					},
				    {
				        title: '交易类型',
				        key: 'type_name',
				        minWidth: 80
				    },
					{
					    title: '支付方式',
					    key: 'pay_type_name',
					    minWidth: 80
					},
					{
						title: '备注',
						key: 'remark',
						minWidth: 120
					},
				    {
				        title: '操作',
				        slot: 'action',
				        fixed: 'right',
				        minWidth: 80,
				        align: 'center'
				    }
				],
				orderList:[],
				formValidate: {
				  store_id: "",
				  keyword: "",
				  data: "",
				  page: 1,
				  limit: 20,
				},
				timeVal: [],
				options:timeOptions,
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
		    return this.isMobile ? "top" : "left";
		  },
		},
	    mounted () {
			this.getList()
			this.staffApi()
		},
	    methods: {
			getList(){
				this.loading = true
				storeFinanceInfo(this.formValidate).then(res=>{
					this.orderList = res.data.list
					this.total = res.data.count
					this.loading = false
				})
			},
			staffApi(){
				staffListInfo().then(res=>{
					this.staff = res.data
				})
			},
			search(){
				this.formValidate.page = 1
				this.getList()
			},
			reset(){
				this.formValidate = {
				  store_id: "",
				  keyword: "",
				  data: "",
				  page: 1,
				  limit: 20,
				}
				this.timeVal = [];
				this.getList()
			},
			// 选择时间
			selectChange(tab) {
			  this.formValidate.page = 1;
			  this.formValidate.data = tab;
			  this.timeVal = [];
			  this.getList();
			},
			// 具体日期
			onchangeTime(e) {
			  this.timeVal = e;
			  this.formValidate.data = this.timeVal[0] ? this.timeVal.join("-") : "";
			  this.formValidate.page = 1;
			  this.getList();
			},
			//分页
			pageChange(status) {
			  this.formValidate.page = status;
			  this.getList()
			},
			remark(e){
				this.remarkId = e.id
				this.modalmark = true;
				this.remarks.mark = e.remark
			},
			//备注的提交
			putRemark(){
				this.modalmark = false
				storeFinanceMarkApi(this.remarkId,this.remarks).then(res=>{
					this.$Message.success(res.msg)
					this.remarks = {mark: ''}
					this.modalmark = false
					this.getList()
				}).catch(err=>{
					this.$Message.error(err.msg)
					this.modalmark = false
				})
			},
			// 取消备注按钮
			cancel(){
				this.remarks = {mark: ''}
				this.modalmark = false
			},
			//导出
			async exports() {
				let [th, filekey, data, fileName] = [[], [], [], ""];
				let id = this.formValidate.store_id,
				keyword = this.formValidate.keyword,
				date = this.formValidate.data;
				let lebData = await this.getExcelData(id,keyword,date); 
				if (!fileName) fileName = lebData.filename;
				filekey = lebData.filekey;
				if (!th.length) th = lebData.header; //表头
				data = data.concat(lebData.export);
				// console.log(th, filekey, fileName, data);
          		exportExcel(th, filekey, fileName, data);
			},
			getExcelData(id,keyword,data) {
				return new Promise((resolve, reject) => {
					exportTableList(id,keyword,data).then((res) => {
						return resolve(res.data);
					});
				});
			},
	    },
	}
</script>

<style scoped lang="less">
	/deep/.ivu-page-header,/deep/.ivu-tabs-bar{border-bottom: 1px solid #ffffff;}
	/deep/.ivu-card-body{padding: 0;}
	/deep/.ivu-tabs-nav{height: 45px;}
	/deep/.ivu-form-label-left .ivu-form-item-label{text-align: right;}
	.topbox{padding: 20px;font-size: 18px;font-weight: 500;}
	.page{padding-right: 30px;padding-bottom: 10px;}
	.box{padding: 20px;padding-bottom: 0px;}
	.tablebox{margin-top: 15px;padding-bottom: 10px;}
	.table{padding: 0px 0 15px 0;}
	.colorred{color: #ff5722;}
	.colorgreen{color: #009688;}
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
