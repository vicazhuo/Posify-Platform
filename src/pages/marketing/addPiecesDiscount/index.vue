<template>
	<div class="form-submit">
		<div class="i-layout-page-header">
			<PageHeader class="product_tabs" hidden-breadcrumb>
			  <div slot="title">
			    <router-link :to="{ path: '/admin/marketing/discount/pieces_discount' }"
			      >
						<div class="font-sm after-line">
								<span class="iconfont iconfanhui"></span>
								<span class="pl10">返回</span>
						</div>
						</router-link
			    >
			    <span
			      v-text="$route.params.id !== '0' ? '编辑第N件N折' : '添加第N件N折'"
			      class="mr20 ml16"
			    ></span>
			  </div>
			</PageHeader>
		</div>
		<Card :bordered="false" dis-hover class="ivu-mt" style="margin-bottom: 79px;">
			<Tabs v-model="currentTab">
				<TabPane
				   v-for="(item, index) in headTab"
				   :key="index"
				   :label="item.name"
				   :name="item.type"
				 ></TabPane>
			</Tabs>
			<Form
			  class="formValidate mt20"
			  ref="formValidate"
			  :rules="ruleValidate"
			  :model="formValidate"
			  :label-width="labelWidth"
			  :label-position="labelPosition"
			  @submit.native.prevent
			>
			  <Row :gutter="24" type="flex" v-show="currentTab === '1'">
					<FormItem label="优惠类型：" props="coupon_type">
						<div
							class="productType"
							:class="formValidate.n_piece_n_discount == item.id ? 'on' : ''
							"
							v-for="(item, index) in discountType"
							:key="index"
							@click="discountTypeTap(item)"
						>
							<div class="name" :class="formValidate.n_piece_n_discount == item.id?'on':''">{{ item.name }}</div>
							<div class="title">({{ item.title }})</div>
							<div
								v-if="formValidate.n_piece_n_discount == item.id"
								class="jiao"
							></div>
							<div
								v-if="formValidate.n_piece_n_discount == item.id"
								class="iconfont iconduihao"
							></div>
						</div>
					</FormItem>
					<Col span="24">
					  <FormItem label="活动名称：" prop="name">
					    <Input
					      v-model="formValidate.name"
					      placeholder="请输入活动名称"
					      v-width="'50%'"
					    />
					  </FormItem>
					</Col>
					<Col span="24">
						<FormItem label="活动时间：" prop="section_time">
							<div class="acea-row row-middle">
								<DatePicker :editable="false" type="daterange" format="yyyy-MM-dd" placeholder="请选择活动时间"
								            @on-change="onchangeTime" v-width="'50%'" :value="formValidate.section_time" v-model="formValidate.section_time"></DatePicker>
							</div>
						</FormItem>
					</Col>
					<Col span="24" v-if="formValidate.n_piece_n_discount == 3">
					  <FormItem label="优惠设置：" prop="threshold">
							<InputNumber v-width="'150'" :precision="0" :max="99999999" :min="1" v-model="formValidate.threshold"></InputNumber><span class="ml10 mr10">件</span>
							<InputNumber v-width="'150'" :precision="0" :max="100" :min="1" v-model="formValidate.discount"></InputNumber><span class="ml10">%</span>
					  </FormItem>
					</Col>
					<Col span="24">
					  <FormItem label="关联标签：">
					    <i-switch v-model="formValidate.is_label" :true-value="1" :false-value="0" size="large">
					      <span slot="open">开启</span>
					      <span slot="close">关闭</span>
					    </i-switch>
							<div class="acea-row row-middle mt10" v-if="formValidate.is_label">
								<div class="labelInput acea-row row-between-wrapper" @click="openLabel">
									<div style="width: 90%;">
										<div v-if="dataLabel.length">
											<Tag closable v-for="(item,index) in dataLabel" @on-close="closeLabel(item)">{{item.label_name}}</Tag>
										</div>
										<span class="span" v-else>选择用户关联标签</span>
									</div>
									<div class="iconfont iconxiayi"></div>
								</div>
								<span class="addClass" @click="addLabel">新增标签</span>
							</div>
					  </FormItem>
					</Col>
					<Col span="24">
					  <FormItem label="优惠叠加：" prop="is_overlay">
							<RadioGroup v-model="formValidate.is_overlay" vertical>
								<Radio label="0">
										<Icon type="social-apple"></Icon>
										<span>不叠加其他营销活动</span>
								</Radio>
								<Radio label="1">
										<Icon type="social-android"></Icon>
										<span>叠加其他营销活动</span>
								</Radio>
							</RadioGroup>
							<CheckboxGroup
							  v-if="formValidate.is_overlay == 1"
							  size="small" 
							  v-model="formValidate.overlay"
							  class="checkAlls"
							>
							  <Checkbox :label="item.type" v-for="(item,index) in activityType">{{item.name}}</Checkbox>
							</CheckboxGroup>
					  </FormItem>	
					</Col>
				</Row>
				<div v-show="currentTab === '2'">
					<Row :gutter="24" type="flex">
						<Col span="24">
						  <RadioGroup v-model="formValidate.product_partake_type">
						    <Radio :label="1">全部商品参与</Radio>
						    <Radio :label="2">指定商品参与</Radio>
						    <Radio :label="3">指定商品不参与</Radio>
						  </RadioGroup>
						</Col>
						<Col span="24" class="mt20" v-if="formValidate.product_partake_type !=1">
						   <Button type="primary" @click="addGoods">添加商品</Button>
						</Col>
					</Row>
					<div class="vxeTable" v-if="formValidate.product_partake_type !=1">
						<vxe-table
						         border="inner"
						         ref="xTree"
						         :column-config="{resizable: true}"
						         row-id="id"
						         :tree-config="{children: 'attrValue',reserve:true}"
						         :data="tableData">
						         <vxe-column field="info" title="商品信息" tree-node min-width="300">
									<template v-slot="{ row }">
										<div class="imgPic acea-row row-middle">
										<viewer>
											<div class="pictrue"><img v-lazy="row.image" /></div>
										</viewer>
										<div class="info">
											<Tooltip max-width="200" placement="bottom" transfer>
												<span class="line2">{{ row.store_name }}{{row.suk}}</span>
												<p slot="content">{{ row.store_name }}{{row.suk}}</p>
											</Tooltip>
										</div>
										</div>
									</template>
								</vxe-column>
						         <vxe-column field="cate_name" title="商品分类" min-width="150"></vxe-column>
						         <vxe-column field="store_label" title="商品标签" min-width="150"></vxe-column>
						         <vxe-column field="price" title="售价" min-width="80"></vxe-column>
										 <vxe-column field="cost" title="成本价" min-width="80"></vxe-column>
										 <vxe-column field="stock" title="库存" min-width="80"></vxe-column>
										 <vxe-column field="date" title="操作" min-width="150" fixed="right" align="center">
											 <template v-slot="{ row }">
											     <a @click="del(row)">删除</a>
											 </template>
										 </vxe-column>
						       </vxe-table>			 
					</div>
				</div>
			</Form>
		</Card>
		<Card :bordered="false" dis-hover class="fixed-card">
			<Form>
				<FormItem>
					<Button 
						v-if="currentTab !== '1'" 
						@click="upTab" 
						style="margin-right:10px"
						>上一步</Button>
					<Button
						type="primary"
						class="submission"
						v-if="currentTab !== '2'"
						@click="downTab('formValidate')"
						>下一步</Button
					>
					<Button
					  v-else
						type="primary"
						class="submission"
						@click="handleSubmit('formValidate')"
						>保存并发布</Button
					>
				</FormItem>
			</Form>
		</Card>	
		<!-- 用户标签 -->
		<Modal
		  v-model="labelShow"
		  scrollable
		  title="请选择用户标签"
		  :closable="false"
		  width="320"
		  :footer-hide="true"
			:mask-closable="false"
		>
		  <userLabel ref="userLabel" @activeData="activeData" @close="labelClose"></userLabel>
		</Modal>
		<Modal v-model="modals" title="商品列表" footerHide  class="paymentFooter" scrollable width="900" @on-cancel="cancel">
		    <goods-list ref="goodslist" :ischeckbox="true" :isdiy="true"  @getProductId="getProductId" v-if="modals"></goods-list>
		</Modal>
	</div>
</template>

<script>
	import { mapState,mapMutations } from "vuex";
	import {
		saveDiscount,
		discountInfo
	} from "@/api/marketing";
	import {
		userLabelAddApi
	} from "@/api/user";
	import userLabel from "@/components/labelList";
	import goodsList from '@/components/goodsList'
	export default {
		name: "addPiecesDiscount",
		components: {
			userLabel,
			goodsList
		},
		data(){
			return {
				grid: {
				  xl: 7,
				  lg: 7,
				  md: 12,
				  sm: 24,
				  xs: 24,
				},
				modals: false,
				tableData: [],
				headTab: [
				  {
				    name: "基础设置",
				    type: '1',
				  },
				  {
				    name: "添加商品",
				    type: '2',
				  }
				],
				discountType: [
					{ name: "第2件半价", title: "例：甜筒第2件半价", id: 1 },
					{ name: "买1送1", title: "例：T恤买1送1", id: 2 },
					{ name: "自定义优惠", title: "例：第3件打5折", id: 3 }
				],
				activityType:[
					{
						name:'优惠券',
						type:'5'
					},
					{
						name:'满减满折',
						type:'3'
					},
					{
						name:'限时折扣',
						type:'1'
					},
					// {
					// 	name:'满送活动',
					// 	type:'4'
					// }
				],
				currentTab: '1',
				dataLabel:[],
				labelShow:false,
				formValidate: {
					name:'',
					section_time:[],
					discount: 50,
					threshold: 2,
					is_label:0,
					label_id: [],
					is_overlay:'0',
					overlay:[],
					product_partake_type:1,
					product_id:[],
					n_piece_n_discount:1
				},
				ruleValidate: {
					name: [
					    { required: true, message: '请输入活动名称', trigger: 'blur' }
					],
					section_time: [
					    { required: true, type: 'array', message: '请选择活动时间', trigger: 'change' }
					],
					is_overlay: [
						  { required: true, message: '请设置优惠叠加', trigger: 'change' }
					],
					threshold: [
						  { type: 'number', required: true, message: '请填写优惠设置', trigger: 'blur' }
					]
				}
			}
		},
		computed: {
		  ...mapState("admin/layout", ["isMobile"]),
		  labelWidth() {
		    return this.isMobile ? undefined : 90;
		  },
		  labelPosition() {
		    return this.isMobile ? "top" : "right";
		  },
		  labelBottom() {
		    return this.isMobile ? undefined : 15;
		  },
		},
		mounted(){
			this.setCopyrightShow({ value: false });
			if(this.$route.params.id != 0){
				this.getDiscountInfo();
			}
		},
		destroyed () {
		  this.setCopyrightShow({ value: true });
		},
		methods: {
			...mapMutations('admin/layout', [
			    'setCopyrightShow'
			]),
			discountTypeTap(item){
				this.formValidate.n_piece_n_discount = item.id;
				if(item.id == 1){
					this.formValidate.threshold = 2;
					this.formValidate.discount = 50;
				}else if(item.id == 2){
					this.formValidate.threshold = 2;
					this.formValidate.discount = 0;
				}else{
					this.formValidate.threshold = 1;
					this.formValidate.discount = 100;
				}
			},
			getDiscountInfo(){
				this.formValidate.n_piece_n_discount = 0
				discountInfo(this.$route.params.id).then(res=>{
					this.formValidate = res.data.info;
					this.formValidate.discount = parseFloat(res.data.info.discount)
					this.formValidate.threshold = parseFloat(res.data.info.threshold)
					this.formValidate.is_overlay = this.formValidate.is_overlay.toString();
					this.tableData = res.data.info.products
					this.dataLabel = res.data.info.label_id;
				}).catch(err=>{
					this.$Message.error(err.msg);
				})
			},
			del(row){
				this.tableData.forEach((i,index)=>{
					if(row.id == i.id){
						  return this.tableData.splice(index, 1)
					}else{
						i.attrValue.forEach((j,indexn)=>{
							if(row.id == j.id){
								if(i.attrValue.length == 1){
									return this.tableData.splice(index, 1)
								}else{
									return i.attrValue.splice(indexn, 1)
								}
							}
						})
					}
				})
			},
			addLabel(){
				this.$modalForm(userLabelAddApi(0)).then(() => {});
			},
			addGoods(){
				this.modals = true;
			},
			cancel () {
			    this.modals = false;
			},
			//对象数组去重；
			unique(arr) {
			    const res = new Map();
			    return arr.filter((arr) => !res.has(arr.id) && res.set(arr.id, 1))
			},
			getProductId (data) {
			    this.modals = false;
					let list = this.tableData.concat(data);
					let uni = this.unique(list);
					uni.forEach((i)=>{
						i.attrValue.forEach(j=>{
							j.cate_name = i.cate_name;
							j.store_label = i.store_label;
						})
					})
					this.tableData = uni;
			},
			// 具体日期
			onchangeTime (e) {
			    this.formValidate.section_time = e;
			},
			closeLabel(label){
				let index = this.dataLabel.indexOf(this.dataLabel.filter(d=>d.id == label.id)[0]);
				this.dataLabel.splice(index,1);
			},
			activeData(dataLabel){
				this.labelShow = false;
				this.dataLabel = dataLabel;
			},
			openLabel(row) {
			  this.labelShow = true;
				this.$refs.userLabel.userLabel(JSON.parse(JSON.stringify(this.dataLabel)));
			},
			// 标签弹窗关闭
			labelClose() {
			  this.labelShow = false;
			},
			// 上一页：
			upTab() {
				if(this.currentTab==1&&this.formValidate.product_type!=0){
					this.currentTab = (Number(this.currentTab) - 2).toString();
				}else{
					this.currentTab = (Number(this.currentTab) - 1).toString();
				}
			},
			// 下一页；
			downTab(name) {
				this.$refs[name].validate((valid) => {
					if (valid) {
						if(!this.formValidate.threshold){
							return this.$Message.warning("请填写优惠设置的件数");
						}
						if(this.formValidate.discount == null){
							return this.$Message.warning("请填写优惠设置的折扣");
						}
						if(this.formValidate.is_label && !this.dataLabel.length){
							return this.$Message.warning("请选择用户关联标签");
						}
						if(parseInt(this.formValidate.is_overlay) && !this.formValidate.overlay.length){
							return this.$Message.warning("请选择叠加的营销活动");
						}
						if(this.formValidate.section_time[0] == ''){
							return this.$Message.warning("请选择活动时间");
						}
						this.currentTab = '2'
					}else{
						this.$Message.warning("请完善数据");
					}
				})
			},
			handleSubmit(name){
				this.$refs[name].validate((valid) => {
					if (valid) {
						if(!this.formValidate.threshold){
							return this.$Message.warning("请填写优惠设置");
						}
						if(this.formValidate.is_label && !this.dataLabel.length){
							return this.$Message.warning("请选择用户关联标签");
						}
						if(parseInt(this.formValidate.is_overlay) && !this.formValidate.overlay.length){
							return this.$Message.warning("请选择叠加的营销活动");
						}
						if(this.formValidate.section_time[0] == ''){
							return this.$Message.warning("请选择活动时间");
						}
						// 用户标签
						let activeIds = [];
						this.dataLabel.forEach((item)=>{
							activeIds.push(item.id)
						});
						if(this.formValidate.is_label==0){
							this.formValidate.label_id = [];
						}else{
							this.formValidate.label_id = activeIds
						}
						let product_id = [];
						this.tableData.forEach((item)=>{
							  let obj = {
									  product_id: item.id,
										unique:[]
								}
								if(item.attrValue.length){
									item.attrValue.forEach((j)=>{
										obj.unique.push(j.unique)
									})
								}
								product_id.push(obj)
						})
						if(this.formValidate.product_partake_type !=1 && !product_id.length){
							return this.$Message.error('请添加商品');
						}
						this.formValidate.product_id = product_id
						saveDiscount(2,this.$route.params.id,this.formValidate).then(res=>{
							this.$router.push({ path: "/admin/marketing/discount/pieces_discount" });
							this.$Message.success(res.msg)
						}).catch(err=>{
							this.$Message.error(err.msg);
						})
					}else{
						this.$Message.warning("请完善数据");
					}
				})
			}
		}
	}
</script>

<style scoped lang="stylus">
	.productType {
	  width: 150px;
	  height: 60px;
	  background: #FFFFFF;
	  border-radius: 3px;
	  border: 1px solid #E7E7E7;
	  float: left;
	  text-align: center;
	  padding-top: 8px;
	  position: relative;
	  cursor: pointer;
	  line-height: 23px;
	  margin-right 12px;
	  
	  &.on{
		  border-color #1890FF;
	  }
	
	  .name {
	    font-size: 14px;
	    font-weight: 600;
	    color: rgba(0, 0, 0, 0.85);
			&.on{
			  color #1890FF;
			}
	  }
	
	  .title {
	    font-size: 12px;
	    font-weight: 400;
	    color: #999999;
	  }
	  
	  .jiao {
	    position: absolute;
	    bottom: 0;
	    right: 0;
	    width: 0;
	    height: 0;
	    border-bottom: 26px solid #1890FF;
	    border-left: 26px solid transparent;
	  }
	  
	  .iconfont {
	    position: absolute;
	    bottom: -3px;
	    right: 1px;
	    color: #FFFFFF;
		font-size 12px;
	  }
	  
	}
	.vxeTable{
		border-top:1px dotted #eee;
		margin-top 20px;
	}
	.formValidate{
		.addClass{
			color: #1890FF;
			margin-left 14px;
			padding 9px 0;
			cursor pointer;
		}
	}
	.imgPic{
		.info{
			width: 60%;
			margin-left: 10px;
		}
		.pictrue{
			height: 36px;
			margin: 7px 3px 0 3px;
			img{
				height: 100%;
				display: block;
			}
		}
	}
	.labelInput{
		border: 1px solid #dcdee2;
		width 50%;
		padding 0 5px;
		border-radius 5px;
		min-height 30px;
		cursor pointer;
		.span{
			 color: #c5c8ce;
		}
		.iconxiayi{
			font-size 12px
		}
	}
	.form-submit {
		  /deep/.ivu-card{
		  	border-radius: 0;
		  }
	    margin-bottom: 79px;
	
	    .fixed-card {
	        position: fixed;
	        right: 0;
	        bottom: 0;
	        left: 200px;
	        z-index: 99;
	        box-shadow: 0 -1px 2px rgb(240, 240, 240);
	
	        /deep/ .ivu-card-body {
	            padding: 15px 16px 14px;
	        }
	
	        .ivu-form-item {
	            margin-bottom: 0;
	        }
	
	        /deep/ .ivu-form-item-content {
	            margin-right: 124px;
	            text-align: center;
	        }
	
	        .ivu-btn {
	            height: 36px;
	            padding: 0 20px;
	        }
	    }
	}
</style>