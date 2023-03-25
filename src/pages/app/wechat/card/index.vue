<template>
    <div>
        <Card :bordered="false" dis-hover class="ivu-mt flexs">
			<Row :gutter="24">
				<Col :xl="8" :lg="24" :md="24" :sm="24" :xs="24">
					<div class="card_box">
						<div class="item_box">
							<img src="../../../../assets/images/cardtopbar.png" >
							<div v-if="colorList && formValidate.selet == 1" class="card" :style="{background: `url(${pic})`, backgroundColor: `${colors}`}">
								<div class="top">
									<div class="user">
										<img :src="formValidate.logo_url" alt="">
										<div class="info_box">
											<div class="info">{{formValidate.brand_name}}</div>
											<div class="info_tit">{{formValidate.title}}</div>
										</div>
									</div>
									<img src="../../../../assets/images/qr.png" >
								</div>
								<div class="bottom">
									<div>0268 8888 8888</div>
									<div><Icon style="font-size: 20px;" type="ios-information-circle-outline" /></div>
								</div>
							</div>
							<div v-if="formValidate.selet == 0 && colorList" class="card" :style="{'background-color': colors || `${colorList[0].value}`}">
								<div class="top">
									<div class="user">
										<img :src="formValidate.logo_url" alt="">
										<div class="info_box">
											<div class="info">{{formValidate.brand_name}}</div>
											<div class="info_tit">{{formValidate.title}}</div>
										</div>
									</div>
									<img src="../../../../assets/images/qr.png" >
								</div>
								<div class="bottom">
									<div>0268 8888 8888</div>
									<div><Icon style="font-size: 20px;" type="ios-information-circle-outline" /></div>
								</div>
							</div>
							<!-- <div class="legal">
								<div class="legal_box">
									<div class="legal_top">积分</div>
									<div class="legal_bottom">120</div>
								</div>
								<div class="legal_box" v-for="(item,index) in 2">
									<div class="legal_top">等级</div>
									<div class="legal_bottom">查看</div>
								</div>
							</div> -->
							<div class="entry" v-if="!centerstatus">
								<div class="entry_btn">{{formValidate.center_title || '快速进入'}}</div>
								<p class="entry_tit">{{formValidate.center_sub_title || '添加提示语'}}</p>
							</div>
							<div class="card_link">
								<div class="link">适用门店 <span class="you">></span></div>
								<div class="link">公众号 <span class="you">></span></div>
							</div>
							<div class="custom_card">
								<div class="custom_link" v-if="formValidate.custom_cell != ''">
									<div class="link" v-for="(item,index) in formValidate.custom_cell">{{item.name ||' 自定义入口(选填)'}}<span class="you"><span class="guide">{{item.tips}}</span>></span></div>
								</div>
								<div class="custom_link" v-else>
									<div class="link">自定义入口<span class="tip">(选填)</span><span class="you">></span></div>
								</div>
							</div>
						</div>
					</div>
				</Col>
				<Col :xl="16" :lg="24" :md="24" :sm="24" :xs="24">
					<div class="card_content">
						<div class="content_box">
							<div class="title_bar">基本信息</div>
							<Form ref="formValidate" :model="formValidate" :rules="ruleValidate" :label-width="80">
								<div class="title_text">
									<div class="left">商户头像：</div>
									<div class="right">
										<div class="img-boxs" @click="logoPicTap('dan',0)">
										   <img :src="formValidate.logo_url" alt="" >
										   <div class="img_font"></div>
										   <div class="img_fonts">更换图片</div>
										</div>
									</div>
								</div>
								<div class="title_text">
									<div class="left">商户名称：</div>
									<div class="right">
										<FormItem label="" prop="brand_name" class="nomarb">
                                            <Input v-model="formValidate.brand_name"  maxlength="12" show-word-limit placeholder="商户名称" style="width: 460px" />
                                        </FormItem>
									</div>
								</div>
								<div class="title_text">
									<div class="left">电话：</div>
									<div class="right">
										<FormItem label="" prop="service_phone" class="nomarb">
                                            <Input v-model="formValidate.service_phone" placeholder="" style="width: 460px" />
                                        </FormItem>
									</div>
								</div>
								<div class="title_text">
									<div class="left">卡券封面：</div>
									<div class="right">
										<RadioGroup v-model="formValidate.selet"  @on-change='color'>
											<Radio :label="1">
												<span>图片</span>
											</Radio>
											<Radio :label="0">
												<span>颜色</span>
											</Radio>
										</RadioGroup>
									</div>
								</div>
								<div class="selst">
									<div v-if="formValidate.selet === 1">
										<!-- <div @click="modalPicTap('dan', 0)" class="btn">上传图片</div> -->
										<div
										  class="acea-row row-middle"
                                          @click="modalPicTap('dan', 0)"
										>
										 <div class="pictrue pictrueTab">
											<img v-if="pic" v-lazy="pic" />
                                            <Icon v-else type="ios-add" size="16" />
										  </div>
										</div>
									</div>
									<div v-if="formValidate.selet === 0 && colorList">
										 <div class="color_input" @click="seletcolor">
											 <div class="input_color" :style="{backgroundColor: `${colors}` || `${colorList[0].value}`}"></div><span class="xia">﹀</span>
											 <div class="color_box" v-if="selecolor">
												 <div class="color_link" :style="{backgroundColor: `${item.value}`}"  v-for="(item,index) in colorList" @click.stop="dancolor(item)">
												 </div>
											 </div>  
										 </div>
									</div>
								</div>
								
								<div class="title_text">
									<div class="left">会员卡标题：</div>
									<div class="right">
										<FormItem label="" prop="title">
                                            <Input v-model="formValidate.title" maxlength="9" show-word-limit placeholder="" style="width: 460px" />
                                            <div class="tip">建议会员卡标题包含商户名或服务内容，如腾讯会员黄钻尊享卡</div>
                                        </FormItem>
									</div>
								</div>
								<!-- <div class="title_text marbottom">
									<div class="left">快速进入</div>
									<div class="right">
										<div>
											<div class="btn" @click="addcenter" v-if="centerstatus">添加</div>
											<div class="btn" @click="cancelcenter" v-else>取消</div>
											<div v-if="!centerstatus">
												<div>
													<FormItem label="按钮描述：" prop="center_title">
														<Input v-model="formValidate.center_title" maxlength="18" show-word-limit placeholder="" style="width: 260px" />
													</FormItem>
													<FormItem label="提示语：" prop="center_sub_title">
														<Input v-model="formValidate.center_sub_title" maxlength="24" show-word-limit placeholder="" style="width: 260px" />
													</FormItem>
													<FormItem label="跳转链接：" prop="center_url">
														<Input v-model="formValidate.center_url" placeholder="" style="width: 260px" />
													</FormItem>
												</div>
												
											</div>
										</div>
									</div>
								</div> -->
                                <Divider dashed />
								<div class="title_bar">会员卡详情</div>
								<div class="title_text required">
									<div class="left" style="align-self: flex-start;padding-top: 10px;line-height: 14px;">特权说明：</div>
									<div class="right" style="flex: 1;">
										<FormItem label="" prop="prerogative">
											<Input v-model="formValidate.prerogative" :rows="5" :autosize="{maxRows:5,minRows: 5}" type="textarea" placeholder="注：1、上文设置中，如已经填写“积分优惠”“折扣优惠”的内容，将会自动在用户会员卡详情展示，无需重复填写。2、建议填写其他特权，举例：100积分可兑换精美礼品；会员日可享受折上折优惠等" style="resize: none;border: none;width: 460px;"/>
										</FormItem>
									</div>
								</div>
								<div class="title_text required">
									<div class="left" style="align-self: flex-start;padding-top: 10px;line-height: 14px;">介绍：</div>
									<div class="right" style="flex: 1;">
										<FormItem label="" prop="notice">
											<Input v-model="formValidate.description" maxlength="300" show-word-limit :rows="5" :autosize="{maxRows:5,minRows: 5}" type="textarea" placeholder="注：1、上文设置中，如已经填写“积分优惠”“折扣优惠”的内容，将会自动在用户会员卡详情展示，无需重复填写。2、建议填写其他注意事项，举例：积分不支持兑换现金" style="resize: none;border: none;width: 460px;"/>
										</FormItem>
									</div>
								</div>
                                <Divider dashed />
							<div class="title_bar">商户介绍<span>(选填)</span></div>
							<div v-if=" formValidate.custom_cell && formValidate.custom_cell.length >0" class="addentrance">
								<div v-for="(item,index) in formValidate.custom_cell">
									<div class="add_bar">
										<div v-if="index == 0">入口一</div>
										<div v-if="index == 1">入口二</div>
										<div v-if="index == 2">入口三</div>
										<div class="del" @click="handleRemove(index)">删除</div>
									</div>
									<div class="title_text required">
										<div class="left">入口名称：</div>
										<div class="right">
											<div>
												<FormItem :prop="'custom_cell.' + index + '.name'" :rules="{required: true, message: '请输入入口名称', trigger: 'blur'}">
													<Input v-model="item.name" maxlength="5" show-word-limit placeholder="" style="width: 460px" />
												</FormItem>
											</div>
										</div>
									</div>
									<div class="title_text required">
										<div class="left">跳转链接：</div>
										<div class="right">
											<div>
												<FormItem :prop="'custom_cell.' + index + '.url'" :rules="{required: true, message: '请输入跳转链接', trigger: 'blur'}">
													<Input v-model="item.url" placeholder="" style="width: 460px" />
												</FormItem>
											</div>
										</div>
									</div>
									<div class="title_text">
										<div class="left">引导语：</div>
										<div class="right">
											<div>
												<Input v-model="item.tips" maxlength="6" show-word-limit placeholder="" style="width: 460px" />
											</div>
										</div>
									</div>
								</div>
							</div>
							<div>
								<div v-if="formValidate.custom_cell && formValidate.custom_cell.length <= 2" class="add" @click="add">添加自定义入口</div>
							</div>
							</Form>
						</div>
					</div>
				</Col>
			
			</Row>
		<div class="submit"><div class="btn" @click="submit()">提交</div></div>
        </Card>
		<!-- 上传图片 -->
		<Modal
		  v-model="modalPic"
		  width="960px"
		  scrollable
		  footer-hide
		  closable
		  title="选择图片"
		  :mask-closable="false"
		  class="aaaa"
		>
		  <uploadPictures
		    :isChoice="isChoice"
		    @getPic="getPic"
		    :gridBtn="gridBtn"
		    :gridPic="gridPic"
		    v-if="modalPic"
		  ></uploadPictures>
		</Modal>
		<!-- 上传logo -->
		<Modal
		  v-model="logoPic"
		  width="960px"
		  scrollable
		  footer-hide
		  closable
		  title="选择图片"
		  :mask-closable="false"
		  class="aaaa"
		>
		  <uploadPictures
		    :isChoice="isChoice"
		    @getPic="getlogoPic"
		    :gridBtn="gridBtn"
		    :gridPic="gridPic"
		    v-if="logoPic"
		  ></uploadPictures>
		</Modal>
    </div>
</template>

<script>
	import { wechatCardListApi, wechatCardApi} from '@/api/app'
	import uploadPictures from "@/components/uploadPictures";
    import { mapMutations } from "vuex";
    export default {
        name: 'card',
        components: { uploadPictures },
        data () {
			 var checkPhone = (rule, value, callback) => {
				      if (value === '') {
				        callback(new Error('请输入电话'));
				      } else {
				        let regPone = null;
				        let mobile = /^1(3|4|5|6|7|8|9)\d{9}$/; //最新16手机正则
				        let tel = /^(0[0-9]{2,3}\-)([2-9][0-9]{4,7})+(\-[0-9]{1,4})?$/; //座机
						let tel2 = /^400-[016789]\d{1,3}-\d{1,4}$/; //400电话的正则
				        if (value.charAt(0) == 0) {    // charAt查找第一个字符方法，用来判断输入的是座机还是手机号
				          regPone = tel;         
				        }else if(value.charAt(0) == 4){
							regPone = tel2;
						} else {          
				          regPone = mobile;
				        }
				        if (!regPone.test(value)) {
				          return callback(
				            new Error("请填写正确电话")
				          );
				        }
				        callback();        
				      }
				    };
            return {
				isChoice: "",
				modalPic: false,
				logoPic: false,
				gridBtn: {
				  xl: 4,
				  lg: 8,
				  md: 8,
				  sm: 8,
				  xs: 8,
				},
				gridPic: {
				  xl: 6,
				  lg: 8,
				  md: 12,
				  sm: 12,
				  xs: 12,
				},
				tableIndex: 0,
				colorList: '',
				colors:'',
				colorsname: '',
				formValidate: {
					logo_url: '',
					brand_name: 'CRMEB', // 商户名称
					selet: 0, //卡券封面切换  0--颜色/1--图片
					attrs: [{pic:''}],
					title:'', //会员卡标题
					prerogative: '', //特权说明
					description: '', //介绍
					service_phone: '',
					background_pic_url: '',
					color: '',//卡卷颜色
					custom_cell: [
						// {
						// 	name: '', //入口名称
						// 	tips: '', //引导语
						// 	url: '' //跳转链接
						// }
					],
					center_title: '', //按钮描述
					center_url: '', //跳转链接
					center_sub_title: '', //提示语
					center:[
						// center_title: '', //按钮描述
						// center_url: '', //跳转链接
						// center_sub_title: '', //提示语
					],
				},
				ruleValidate: {
					title: [
						{ required: true, message: '会员卡名称不能为空且长度不超过9个占位符', trigger: 'blur' }
					],
					prerogative: [
						{ required: true, message: '特权说明不能为空', trigger: 'blur' }
					],
					service_phone: [
						 { required: true, validator: checkPhone, trigger: "blur" },
						 // { pattern: /^1[3456789]\d{9}$/, message: "手机号码格式不正确", trigger: "blur" },
					],
					brand_name: [
						{ required: true, message: '商户名称不能为空', trigger: 'blur' }
					],
					center_url:[
						{ required: true, message: '跳转链接不能为空', trigger: 'blur' }
					],
					center_sub_title:[
						{ required: true, message: '提示语不能为空', trigger: 'blur' }
					],
					center_title:[
						{ required: true, message: '按钮描述不能为空', trigger: 'blur' }
					]
				},
				pic: '',
				selecolor: false, //颜色选择框	
				index:0,
				centerstatus: true, //显示按钮添加
				// addentrance: false //自定义入口框	
				
			}
		},
		created() {
		},
		mounted() {
			this.$nextTick(()=>{
				this.getList()
			})
            this.setCopyrightShow({ value: false });
		},
        destroyed() {
            this.setCopyrightShow({ value: true });
        },
        methods: {
            ...mapMutations('admin/layout', [
                'setCopyrightShow'
            ]),
			getList(){
				wechatCardListApi().then(res=>{
					this.colorList = res.data.color
					this.formValidate = res.data.info
					this.$set(this.formValidate,"selet",res.data.selet || 0)
					let it = res.data.info.color || this.colorList[0].name
					this.colors =this.colorList.filter((item) => {
						if(it == item.name){
							return item.value
						}
					})
					if(res.data.info.center_title != ""){
						this.center_title = false
					}
					this.colorsname = this.colors[0].name
					this.colors = this.colors[0].value
					if(this.colors==""){
						this.dancolor(this.colorList[0])
					}
					if(res.data.selet == 1){
						this.pic = res.data.info.background_pic_url
					}
					if(res.data.selet == 0){
						this.pic = res.data.info.color
					}
				}).catch(err=>{
					this.$Message.error(err.msg);
				})
			},
			seletcolor(){
				this.selecolor = !this.selecolor
			},
			dancolor(item){
				this.selecolor = false
				this.colors = item.value
				this.colorsname = item.name
			},
			// 点击上传图片
			modalPicTap(tit, index) {
			  this.modalPic = true;
			  this.isChoice = tit === "dan" ? "单选" : "多选";
			  this.tableIndex = index;
			},
			// 获取单张图片信息
			getPic(pc) {
				this.$nextTick(() => {
					// this.formValidate.selet = 1
					this.pic = pc.att_dir;
					// this.pic = pc.att_dir;
					this.modalPic = false;
				})
			},
			// 点击logo图片
			logoPicTap(tit, index) {
			  this.logoPic = true;
			  this.isChoice = tit === "dan" ? "单选" : "多选";
			  this.tableIndex = index;
			},
			// 获取logo图片信息
			getlogoPic(pc) {
				this.$nextTick(() => {
					this.formValidate.logo_url = pc.att_dir;
					this.logoPic = false;
				})
			},
			color(e){
				this.formValidate.selet = e
			},
			add(){
				this.index ++;
				// this.addentrance = true
				this.formValidate.custom_cell.push({
					name: '',
					tips: '',
					url: ''
				});
			},
			//按钮添加
			addcenter(){
				this.centerstatus = false
			},
			//按钮取消
			cancelcenter(){
				this.formValidate.center_title= ''
				this.formValidate.center_sub_title= ''
				this.formValidate.center_url= ''
				this.centerstatus = true
			},
			handleRemove(index) {
				this.formValidate.custom_cell.splice(index,1);
				this.index --;
			},
			submit() {
                this.$refs.formValidate.validate((valid) => {
                    if (valid) {
						if(this.formValidate.selet == 0){
							this.formValidate.color = this.colorsname
						}else{
							this.formValidate.background_pic_url = this.pic
						}
						wechatCardApi(this.formValidate).then(res=>{
							this.$Message.success(res.msg);
							this.getList()
						}).catch(err=>{
							this.$Message.error(err.msg);
						})
                    } else {
                        this.$Message.error('请完善信息');
                    }
                })
            },
		}
    }
</script>

<style scoped lang="stylus">
    .ivu-divider {
        margin 30px 0;
    }
	/deep/.ivu-form-item{
		flex 1;
        margin-bottom 0;
	}
	/deep/.ivu-form-item-content{
		margin-left 0px !important;
	}
	/deep/.ivu-form-item-label{
		width  auto !important;
	}
	.submit{
        position fixed;
        right 0;
        bottom 0;
        left 200px;
        z-index 99;
		padding 15px;
		width 100%;
		// border-top 1px solid #e7e7eb;
		// margin-top 25px;
        background-color #FFFFFF;
		.btn{
			margin 0 auto;
			// background-color #55bd47;
			color #FFFFFF
		}
	}
	.tip{
		color #9a9a9a;
	}
	.bcl{
		background-color #55bd47 !important;
	}
	.flexs{
		justify-content space-around;
        margin-bottom 79px;
	}
	.marbottom{
		margin-bottom 20px;
	}
	.nomart{
		margin-top 0px !important;
	}
	.nomarb{
		margin-bottom 0px !important;
	}
	.you{
		display inline-block;
		margin-right 10px;
		color #c6c6c6;
		font-size 14px;
		float: right;
		font-family: serif;
		font-weight: bold;
		.guide{
			display inline-block;
			margin-right 10px;
			font-size 14px;
		}
	}
	.selst{
		margin-left 114px;
		margin-top 26px;
		.color_input{
			width 100px;
			height 32px;
			background-color #FFFFFF;
			border 1px solid #d9dadc;
			position relative;
			cursor pointer;
			.xia{
				float: right;
				margin-top: 10px;
				margin-right: 10px;
				font-size: 14px;
			}
			.input_color{
				width 22px;
				height 22px;
				background-color #0000FF;
				margin-top 4px;
				margin-left 10px;
				float left
			}
			.color_box{
				width 140px;
				// height 65px;
				background-color #FFFFFF;
				position absolute;
				padding 5px
				top 35px;
				z-index 1;
				display flex;
				flex-wrap: wrap;
				align-content: space-around;
				justify-content: space-between;
				border 1px solid #d9dadc;
				.color_link{
					width 22px;
					height 22px;
					margin  2px 0px;
					background-color #0000FF;
				}
			}
		}
	}
	.btn{
		width 68px;
		height 36px;
		cursor: pointer;
		background-color #1890FF;
		text-align center;
		line-height 36px;
		// border 1px solid #e7e7eb;
        border-radius 4px;
	}
	.pictrue {
	  width: 85px;
	  height: 85px;
	  // border: 1px dotted rgba(0, 0, 0, 0.1);
	  display: inline-block;
	  position: relative;
	  cursor: pointer;
	
	  img {
	    width: 100%;
	    height: 100%;
	  }
	}
	
	.pictrueTab {
		// margin-top 20px;
	  width: 60px !important;
	  height: 60px !important;
      border 1px dashed #DDDDDD;
      border-radius 4px;
      background-color rgba(0, 0, 0, 0.02);
      line-height 60px;
      text-align center;
	}
	
    .card_box{
		.item_box{
			width 320px;
			// height 536px;
			margin 0 auto;
			margin-bottom 30px;
			overflow hidden;
			// background-color #f6f6f8;
			border 1px solid #e7e7eb;
			img{
				width 320px
				display inline-block
				margin 5px 0px;
				
			}
			.legal{
				width 266px;
				margin 0px auto;
				margin-bottom 20px;
				display flex;
				justify-content space-around;
				text-align center;
				// padding 0px 20px;
				.legal_box{
					cursor pointer;
					.legal_top{
						color #9a9a9a;
						font-size 12px;
					}
					.legal_bottom{
						color #63b359;
						font-size 15px;
					}
				}
			}
			.entry{
				// width 266px;
				margin 20px auto;
				// margin-bottom 20px;
				text-align center
				.entry_btn{
					width: fit-content;
					padding 0px 30px;
					height 40px;
					line-height 40px;
					margin 0px auto;
					border 1px solid #63b359;
					color #63b359;
					font-weight 500;
					font-size 14px;
					margin-bottom 10px;
					border-radius 5px;
					cursor pointer;
				}
				.entry_tit{
					color #9a9a9a;
					font-size 12px;
				}
			}
			.card{
				width 266px;
				height 147px;
				margin 20px auto 10px;
				border-radius 10px;
				padding 20px;
				color #FFFFFF;
				background-repeat no-repeat !important;
				background-size: 100% 100% !important;
				
				.top{
					height 80px;
					display flex;
					justify-content space-between;
					img{
						width 36px;
						height 36px;
						border 1px solid #FFFFFF;
						border-radius 5px
					}
					.user{
						display flex;
						justify-content space-between;
						img{
							width 40px;
							height 40px;
							border 1px solid #FFFFFF;
							border-radius 50%
							overflow hidden;
							background-color #FFFFFF;
						}
						.info_box{
							margin-left 5px
							font-weight bold;
							margin-top 5px
							.info{
								max-width 150px;
								font-size 15px;
								overflow hidden;
								text-overflow ellipsis;
								white-space nowrap;
							}
						}
					}
				}
				.bottom{
					display flex;
					justify-content space-between;
					font-size 18px
				}
			}
			.card_link{
				padding 0px 5px;
				.link{
					height 40px;
					line-height 40px;
					border-bottom 1px solid #e7e7eb;
					
				}
				.link:nth-last-child(1){
					border none
				}
			}
			.custom_card{
				height 222px;
				background-color #f6f6f8;
				padding 15px 0px 50px;
				.custom_link{
					background-color #FFFFFF
					padding 0px 5px;
					.link{
						height 40px;
						line-height 40px
						border-bottom 1px solid #e7e7eb;
					}
					.link:nth-last-child(1){
						border none
					}
				}
				
			}
		}
	}
	.card_content{
		// border 1px solid #e7e7eb;
		// background-color #f6f6f8;
		.add{
			width 138px;
			height 36px;
			// padding 0px 22px;
			// border 1px solid #e6e9eb;
            border-radius 4px;
			background-color #1890FF;
			margin 30px 0px 29px 30px;
			line-height 36px;
            text-align center;
			cursor pointer;
            color #FFFFFF;
		}
		.content_box{
			// height 700px;
			padding 20px 20px;
			.addentrance{
				padding-bottom 20px;
                padding-left 30px;
				// border-bottom 1px solid #e6e9eb;
                .title_text{
                    .left{
                        width 95px;
                    }
                }
			}
			.add_bar{
				margin-top 30px;
				display flex;
				justify-content space-between;
                align-items center;
                height 34px;
                padding 0 20px 0 15px;
                background-color #F5F7FA;
				.del{
					color #F5222D;
					cursor pointer;
				}
			}
			.title_bar{
				// height 30px;
                padding-left 10px;
                border-left 2px solid #1890FF;
                font-weight bold;
				line-height 15px;
                color #000000;
				// border-bottom 1px solid #e6e9eb;
                span{
                    margin-left 6px;
                    font-weight normal;
                    font-size 12px;
                    color #999999;
                }
			}
			.title_text{
				display flex;
				justify-content start;
                align-items center;
				margin-top 26px;
				.left{
					text-align right ;
					width 114px;
                    padding-right 6px;
                    color rgba(0, 0, 0, 0.85);
				}
                &.required{
                    .left::before{
                        content: "*";
                        padding-right 5px;
                        vertical-align middle;
                        color #F56C6C;
                    }
                }
				.right{
					display flex;
					justify-content start;
					.tit{
						display inline-block;
						margin-top 10px;
					}
					.legaladd{
						width 100px;
						height 30px;
						padding 0px 15px;
						text-align center;
						border 1px solid #e6e9eb;
						background-color #FFFFFF;
						line-height 30px;
						cursor pointer;
					}
					img{
						width 80px;
						height 80px;
					}
					.img-boxs{
						position relative
						width: 60px;
						height: 60px;
						background: rgba(0, 0, 0, 0);
						border-radius: 6px;
						overflow hidden
						img{
							width 100%
							height 100%
						}
						.img_font{
							position absolute
							bottom 0
							left 0
							width 100%
							height: 24px;
							background: #000000;
							opacity: 0.4;
							border-radius: 0px 0px 6px 6px;
						}
						.img_fonts{
							position absolute
							bottom 0
							left 0
							width 100%
							height: 24px;
							border-radius: 0px 0px 6px 6px;
							color #FFFFFF
							text-align center
							line-height 24px
							
						}
					}
					   
						
				}
			}
		}
	}
</style>
	