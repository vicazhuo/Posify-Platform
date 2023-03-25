<template>
    <div>
        <Modal v-model="isTemplate" scrollable  footer-hide closable :title="title"  :z-index="1" width="700" @on-cancel="cancel">
            <div class="article-manager">
                <Card :bordered="false" dis-hover>
                    <Form ref="formItem" :model="formItem" :label-width="labelWidth" :label-position="labelPosition" :rules="ruleValidate" @submit.native.prevent>
                        <Row type="flex" :gutter="24">
							<Col span="24" v-if="openErp">
								<Col v-bind="grid">
									<FormItem label="erp门店：" prop="erp_shop_id">
										<Button @click="tapErp">{{formItem.erp_shop_id?formItem.erp_shop_id:"请选择erp门店"}}</Button>
									</FormItem>
								</Col>
							</Col>
							<Col span="24">
							    <Col v-bind="grid">
							        <FormItem label="门店照片：" prop="image">
							            <div class="picBox" @click="modalPicTap('单选')">
							                <div class="pictrue" v-if="formItem.image"><img v-lazy="formItem.image"></div>
							                <div class="upLoad" v-else>
												<div class="iconfont">+</div>
							                </div>
							            </div>
							        </FormItem>
							    </Col>
							</Col>
							<Col span="24" v-if="formItem.id == 0">
							    <Col v-bind="grid" >
							        <FormItem label="管理员账号：" prop="store_account" label-for="store_account">
							            <Input v-model="formItem.store_account"  placeholder="请输入管理员账号"/>
							        </FormItem>
							    </Col>
							</Col>
							<Col span="24"  v-if="formItem.id == 0">
							    <Col v-bind="grid">
							        <FormItem label="管理员密码：" prop="store_password" label-for="store_password">
							            <Input type="password" v-model="formItem.store_password"  placeholder="请输入管理员密码"/>
							        </FormItem>
							    </Col>
							</Col>
                            <Col span="24">
                                <Col v-bind="grid">
                                    <FormItem label="门店名称：" prop="name" label-for="name">
                                        <Input v-model="formItem.name"  maxlength="20" show-word-limit  placeholder="请输入门店名称"/>
                                    </FormItem>
                                </Col>
                            </Col>
                            <Col span="24">
                            	<Col v-bind="grid">
                            		<FormItem label="门店简介：" label-for="introduction">
                            			<Input v-model="formItem.introduction"  maxlength="100" show-word-limit :rows="4" :autosize="{maxRows:4,minRows: 4}" type="textarea"   placeholder="请输入门店简介"s/>
                            		</FormItem>
                            	</Col>
                            </Col>
                            <Col span="24">
                                <Col v-bind="grid">
                                    <FormItem label="门店手机号：" label-for="phone" prop="phone">
                                        <Input v-model="formItem.phone"  placeholder="请输入门店手机号"/>
                                    </FormItem>
                                </Col>
                            </Col>
							<Col span="24">
								<Col v-bind="grid">
									<FormItem label="营业状态：" label-for="is_show" prop="is_show">
										<Switch size="large" v-model="formItem.is_show" :false-value="0" :true-value="1">
											<span slot="open" :true-value="1">开启</span>
											<span slot="close" :false-value="0">关闭</span>
										</Switch>
									</FormItem>
								</Col>
							</Col>
							<Col span="24">
								<Col v-bind="grid" v-if="formItem.is_show == 1">
									<FormItem label="营业时间：" label-for="day_time"  prop="day_time">
										<TimePicker type="timerange" @on-change="onchangeTime" v-model="formItem.day_time"  format="HH:mm:ss" :value="formItem.day_time" placement="bottom-end" placeholder="请选择营业时间" class="inputW" ></TimePicker>
									</FormItem>
								</Col>
							</Col>
							<Col span="24">
								<Col v-bind="grid">
									<FormItem label="到店自提：" label-for="mention" prop="mention">
										<Switch size="large" v-model="formItem.is_store" :false-value="0" :true-value="1">
											<span slot="open" :true-value="1">开启</span>
											<span slot="close" :false-value="0">关闭</span>
										</Switch>
									</FormItem>
								</Col>
							</Col>
                            <Col span="24">
                            	<Col v-bind="grid">
                            		<FormItem label="门店地址：" label-for="address" prop="address">
                            			<Cascader :data="addresData" :load-data="loadData" v-model="formItem.addressSelect" @on-change="addchack" class="inputW"></Cascader>
                            		</FormItem>
                            	</Col>
                            </Col>
							<Col span="24">
								<Col v-bind="grid">
									<FormItem required label="配送范围(半径)：" label-for="valid_range" prop="valid_range">
										<InputNumber :min="0.01" :max="100000" v-model="formItem.valid_range" :formatter="value => `${formItem.valid_range}`" :parser="value => value.replace('%', '')" style="width: 90px;"></InputNumber><span style="margin-left: 10px;">km</span>
									</FormItem>
								</Col>
							</Col>
                            <Col span="24">
                            	<Col v-bind="grid">
                            		<FormItem label="门店详细地址：" label-for="detailed_address" prop="detailed_address">
                            			<Input search enter-button="查找位置" v-model="formItem.detailed_address"  placeholder="输入地址(包含城市名称,否则会影响搜索精度)" class="inputW" @on-search="onSearch" />
                            		  <!-- 提示：定位地址后，手动补充完详细地址，禁止再次点击查找 -->
									</FormItem>
                            	</Col>
                            </Col>
							<Col span="24" v-if="isApi || add">
								<Maps v-if="mapKey" ref="mapChild" class="map-sty" :mapKey="mapKey" :lat="Number(formItem.latitude || 34.34127)" :lon="Number(formItem.longitude || 108.93984)" :address="formItem.detailed_address" @getCoordinates="getCoordinates" />
							</Col>
                        </Row>
                        <Row style="justify-content: space-around;">
                            <Col>
                                <Button type="primary" class="btn" @click="handleSubmit('formItem')">{{formItem.id!=0?'修改':'提交'}}</Button>
                            </Col>
                        </Row>
                        <Spin size="large" fix v-if="spinShow"></Spin>
                    </Form>
                </Card>

                <Modal v-model="modalPic" width="960px" scrollable  footer-hide closable title='上传提货点logo' :mask-closable="false" :z-index="1">
                    <uploadPictures :isChoice="isChoice" @getPic="getPic" :gridBtn="gridBtn" :gridPic="gridPic" v-if="modalPic"></uploadPictures>
                </Modal>
				<Modal v-model="modalErp" width="700px" scrollable  footer-hide closable title='erp门店' :mask-closable="false" :z-index="1">
					<erpList ref="refErp" @getProductId="getProductId"></erpList>
				</Modal>
            </div>
        </Modal>
    </div>
</template>

<script>
	// import { keyApi} from '@/api/setting';
	import { keyApi, storeGetInfoApi, cityApi, storeUpdateApi } from '@/api/store';
	import { erpConfig } from "@/api/erp";
	// import { keyApi, storeUpdateApi, storeGetInfoApi, cityApi } from '@/api/setting';
	import { mapState } from 'vuex';
	import uploadPictures from '@/components/uploadPictures';
	import erpList from './erpList.vue';
	import Maps from '@/components/map/map.vue'
	export default {
		name: 'systemStore',
		components: { uploadPictures,Maps,erpList },
		props: { },
		data () {
			let validatePhone = (rule, value, callback) => {
				if (!value) {
					return callback(new Error('请填写手机号'));
				} else if (!/^1[3456789]\d{9}$/.test(value)) {
					callback(new Error('手机号格式不正确!'));
				} else {
					callback();
				}
			};
			let validateUpload = (rule, value, callback) => {
				console.log('111111111',this.formItem.image)
				if (!this.formItem.image) {
					callback(new Error('请上传门店照片'))
				} else {
					callback()
				}
			};
			let validateErp = (rule, value, callback) => {
				console.log('2222222222',this.formItem.erp_shop_id)
				if (this.formItem.erp_shop_id == 0) {
					callback(new Error('请选择erp门店'))
				} else {
					callback()
				}
			};
			return {
				modalErp:false,
				openErp:false,
				isTemplate: false,
				title: '',
				formItem: {
					id: 0,
					erp_shop_id: 0,
					store_account: '',
					store_password: '',
					image: '',
					name: '',
					introduction: '',
					phone: '',
					is_show: 1,
					day_time: [],
					is_store: 0,
					address: '',
					detailed_address: '',
					latitude:'',
					longitude:'',
					province:0,
					city:0,
					area:0,
					street:0,
					addressSelect:[],
					valid_range:0
				},
				spinShow: false,
				addresData: [],
				ruleValidate: {
					name: [
						{ required: true, message: '请输入门店名称', trigger: 'blur' }
					],
					erp_shop_id: [
						{ required: true, validator: validateErp, trigger: 'change' }
					],
					store_account: [
						{ required: true, message: '请输入管理员账号', trigger: 'blur' }
					],
					store_password: [
						{ required: true, message: '请输入管理员密码', trigger: 'blur' }
					],
					address: [
						{ required: true, message: '请选择门店地址', trigger: 'change' }
					],
					day_time: [ 
						{required: true,type: "array", message: "请选择营业时间",trigger: "change"},
						{validator(rule, value, callback, source, options) 
							{
								if (value[0] === "") {
								callback("时间不能为空");
								}
							 callback();//这个一定要有。不然无法验证通过
							}
						}
				    ],//TimePicker-timerange，自定义的

					phone: [
						{ required: true, validator: validatePhone, trigger: 'blur' }
					],
					detailed_address: [
						{ required: true, message: '请输入详细地址', trigger: 'blur' }
					],
					image: [
						{ required: true, validator: validateUpload, trigger: 'change' }
					]
				},
				mapKey: '',
				grid: {
					xl: 20,
					lg: 20,
					md: 20,
					sm: 24,
					xs: 24
				},
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
				modalPic: false,
				isChoice: '单选',
				pid:0,
				isApi:0,
				add:0
			}
		},
		created () {
			this.getErpConfig();
			this.getKey();
			// this.getInfo();
			let data = {pid:0}
			this.cityInfo(data);
		},
		computed: {
			...mapState('admin/layout', [
				'isMobile'
			]),
			labelWidth () {
				return this.isMobile ? undefined : 120;
			},
			labelPosition () {
				return this.isMobile ? 'top' : 'right';
			}
		},
		mounted: function () {},
		methods: {
			getProductId(id){
				this.formItem.erp_shop_id = id;
				this.modalErp = false;
				this.$refs.formItem.validateField("erp_shop_id");
			},
			tapErp(){
				this.$refs.refErp.currentid = this.formItem.erp_shop_id;
				this.modalErp = true;
				this.$refs.formItem.validateField("erp_shop_id");
			},
			getErpConfig(){
				erpConfig().then(res=>{
					this.openErp = res.data.open_erp;
				}).catch(err=>{
					this.$Message.error(err.msg);
				})
			},
			clearFrom () {
			    this.formItem = {
					//id: 0,
					store_account: '',
					store_password: "",
					image: '',
					erp_shop_id:0,
					name: '',
					introduction: '',
					phone: '',
					is_show: 1,
					day_time: [],
					is_store: 0,
					address: '',
					detailed_address: '',
					latitude:'',
					longitude:'',
					province:0,
					city:0,
					area:0,
					street:0,
					addressSelect:[],
					valid_range:0
				}
				this.add = 0;
				this.isApi = 0;
			},
			cancel () {
				this.isTemplate = false;
			    this.$refs['formItem'].resetFields();
			    this.clearFrom();
			},
			addchack(e,selectedData){
				e.forEach((i,index)=>{
					if(index==0){
						this.formItem.province = i
					}else if(index==1){
						this.formItem.city = i
					}else if(index==2){
						this.formItem.area = i
					}else {
						this.formItem.street = i
					}
				})
				this.formItem.address = (selectedData.map(o => o.label)).join("/");
			},
			cityInfo(data){
				cityApi(data).then(res=>{
					this.addresData = res.data
				})
			},
			loadData(item, callback) {
				item.loading = true;
				cityApi({pid:item.value}).then(res=>{
					item.children = res.data;
					item.loading = false;
					callback();
				});
			},
			// 地图信息获取
			getCoordinates(data) {
				this.formItem.latitude = data.location.lat || 34.34127
				this.formItem.longitude = data.location.lng || 108.93984
				if(this.isApi){
					// // this.formItem.detailed_address = data.address
					let components = data.addressComponents;
					if(this.formItem.detailed_address.indexOf(components.street) == -1){
						this.formItem.detailed_address = data.address+(components.town?components.town:'');
					}
				}
			},
			// 查找位置
			onSearch() {
				if(this.$refs.mapChild){
					this.$refs.mapChild.searchKeyword(this.formItem.detailed_address)
				}
			},
			// key值
			getKey () {
				keyApi().then(res => {
					this.mapKey = res.data.key
				}).catch(res => {
					this.$Message.error(res.msg)
				})
			},
			// 详情
			getInfo (id) {
			    let that = this;
			    that.formItem.id = id;
			    that.spinShow = true;
			    storeGetInfoApi(id).then(res => {
					this.isApi = 1;
					let addressSelect = [];
					this.formItem = res.data.info;
					this.formItem.erp_shop_id = res.data.info.erp_shop_id || 0;
					let a = []
					a.push(res.data.info.day_start)
					a.push(res.data.info.day_end)
					this.formItem.day_time = a
					if(res.data.info.province){
						addressSelect.push(res.data.info.province)
					}
					if(res.data.info.city){
						addressSelect.push(res.data.info.city)
					}
					if(res.data.info.area){
						addressSelect.push(res.data.info.area)
					}
					if(res.data.info.street){
						addressSelect.push(res.data.info.street)
					}
					this.$set(this.formItem,'valid_range',(this.formItem.valid_range)/1000)
					this.formItem.addressSelect = addressSelect;
					this.onSearch();
					that.spinShow = false;
				}).catch(function (res) {
					that.spinShow = false;
					that.$Message.error(res.msg);
				})
			},
			// 选择图片
			modalPicTap () {
				this.modalPic = true;
				this.$refs.formItem.validateField("image")
			},
			// 选中图片
			getPic (pc) {
				this.formItem.image = pc.att_dir;
				this.modalPic = false;
				this.$refs.formItem.validateField("image")
			},
			// 营业时间
			onchangeTime (e) {
				this.formItem.day_time = e;
			},
			// 提交
			handleSubmit (name) {
				this.$refs[name].validate((valid) => {
					if (valid) {
						if(this.formItem.day_time[0] == ''){
							this.formItem.day_time = ['00:00:00', '23:59:59']
						}
						
						if(this.formItem.valid_range == ''||this.formItem.valid_range<0){
							return this.$Message.error('请输入有效的门店范围');
						}
						// this.formItem.valid_range = this.formItem.valid_range/1000 
						storeUpdateApi(this.formItem.id,this.formItem).then(async res => {
							this.$Message.success(res.msg);
							this.isTemplate = false;
							this.$parent.getList();
							this.$refs[name].resetFields();
							this.clearFrom();
						}).catch(res => {
							this.$Message.error(res.msg);
						})
					} else {
						return false;
					}
				})
				
			}
		}
	}
</script>

<style scoped lang="stylus">
	.map-sty {
		width: 90%;
		text-align: right;
		margin: 0 0 0 10%;
	}
	/deep/.ivu-card-body{
		padding 16px 0 0 0!important;
	}
	.footer{
		width 100%;
		height 50px;
		box-shadow: 0px -2px 4px 0px rgba(0, 0, 0, 0.05);
		margin-top 50px;
	}
	/deep/.ivu-btn-primary{
		width 86px;
	}
	.btn{
		margin-top: 20px;
	}
	.inputW{
		width 400px;
	}
	.ivu-mt{
		min-width 580px;
	}
	.picBox
		display: inline-block;
		cursor: pointer;
		.upLoad
			width: 58px;
			height: 58px;
			line-height: 58px;
			border: 1px dotted rgba(0, 0, 0, 0.1);
			border-radius: 4px;
			background: rgba(0, 0, 0, 0.02);
		.pictrue
			width: 60px;
			height: 60px;
			border: 1px dotted rgba(0, 0, 0, 0.1);
			margin-right: 10px;
			img
				width: 100%;
				height: 100%;
		.iconfont
			color: #CCCCCC;
			font-size 26px;
			text-align center
			
</style>
