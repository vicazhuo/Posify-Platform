<template>
    <div class="carMywrapper">
		<div class="type-radio">
		  <Form :label-width="80">
		    <FormItem label="卡密类型：">
		      <RadioGroup v-model="cartMyType" size="large">
		        <Radio :label="1">固定卡密</Radio>
		        <Radio :label="2">一次性卡密</Radio>
		      </RadioGroup>
		      <div v-if="cartMyType == 1">
				  <div class="stock-disk">
					  <Input
					    v-model="fixedCar.disk_info"
					    size="large"
					    type="textarea"
					    :rows="4"
					    placeholder="填写卡密信息"
					  />
				  </div>
				  <div class="stock-input">
					  <Input
					    type="number"
					    v-model="fixedCar.stock"
					    size="large"
					    placeholder="填写库存数量"
					  >
					    <span slot="append">件</span>
					  </Input>
				  </div>
		      </div>
		      <div class="scroll-virtual" v-if="cartMyType == 2">
		        <div
		          class="acea-row row-middle mb10"
		          v-for="(item, index) in virtualList"
		          :key="index"
		        >
		          <span class="mr10 virtual-title">卡号{{ index + 1 }}：</span>
		          <Input
		            class="mr10"
		            type="text"
		            v-model.trim="item.key"
		            style="width: 150px"
		            placeholder="请输入卡号(非必填)"
		          ></Input>
		          <span class="mr10 virtual-title">卡密{{ index + 1 }}：</span>
		          <Input
		            class="mr10"
		            type="text"
		            v-model.trim="item.value"
		            style="width: 150px"
		            placeholder="请输入卡密"
		          ></Input>
		          <span class="deteal-btn" @click="removeVirtual(index)"
		            >删除</span
		          >
		        </div>
		      </div>
		      <div class="add-more" v-if="cartMyType == 2">
		        <Button type="primary" @click="handleAdd"
		          >添加卡密</Button
		        >
		        <Upload
				  ref="upload"
		          class="ml10"
		          :action="cardUrl"
		          :before-upload="beforeUpload"
		          :headers="header"
		          :on-success="upFile"
				  :format="['xlsx']"
				  :on-format-error="handleFormatError"
		        >
		          <Button type="success">导入卡密</Button>
		        </Upload>
				<Button class="download" type="default" icon="ios-download-outline" @click="getCarMyList">下载卡密模板</Button>
		      </div>
		    </FormItem>
		  </Form>
		</div>
        <div class="footer">
			<Button type="primary" class="btns" ghost @click="cancel">取消</Button>
            <Button type="primary" class="btns" @click="subBtn">确定</Button>
        </div>
    </div>
</template>
<script>
	import Setting from "@/setting";
	import util from "@/libs/util";
	import exportExcel from "@/utils/newToExcel.js";
	import {
	  importCard,
	  exportProductCard
	} from "@/api/product";
    export default {
        name: "addCarMy",
        props:{
			virtualList: {
			  type: Array,
			  default: function () {
			    return [];
			  }
			}
        },
        data(){
            return {
				cartMyType:1,
				fixedCar:{
					disk_info:'',
					stock:0
				},
				cardUrl: Setting.apiBaseURL + "/file/upload/1",
				header: {} //请求头部信息
            }
        },
		created() {
		    this.getToken();
		},
        mounted() {
        },
        methods:{
			// 下载卡密
			async getCarMyList() {
			  let [th, filekey, data, fileName] = [[], [], [], ""];
			  let lebData = await this.getExcelData();
			  if (!fileName) fileName = lebData.filename;
			  if (!filekey.length) {
			    filekey = lebData.filekey;
			  }
			  if (!th.length) th = lebData.header;
			  data = lebData.export;
			  exportExcel(th, filekey, fileName, data);
			},
			getExcelData() {
			  return new Promise((resolve, reject) => {
			    exportProductCard().then((res) => {
			      return resolve(res.data);
			    });
			  });
			},
			removeVirtual(index) {
			  this.virtualList.splice(index, 1);
			},
			upFile(res) {
			  importCard({ file: res.data.src }).then((res) => {
				this.$emit('changeVirtual',JSON.parse(JSON.stringify(res.data))) 
			   //this.$refs.upload.clearFiles();	
			  }).catch(err=>{
				  return this.$Message.error(err.msg);
			  })
			},
			handleFormatError(file){
				return this.$Message.error('必须上传xlsx格式文件');
			},
			// 上传头部token
			getToken() {
			  this.header["Authori-zation"] = "Bearer " + util.cookies.get("token");
			},
			cancel(){
				this.$emit('closeCarMy')
			},
			handleAdd() {
			  this.virtualList.push({
			    key: "",
			    value: "",
			  });
			},
			beforeUpload() {
			  let promise = new Promise((resolve) => {
			    this.$nextTick(function () {
			      resolve(true);
			    });
			  });
			  return promise;
			},
			subBtn(){
				if(this.cartMyType==1){
					this.fixedCar.cartMyType = 1;
					if(this.fixedCar.disk_info == ''){
						return this.$Message.error("请填写卡密信息");
					}
					if(!this.fixedCar.stock){
						return this.$Message.error("请填写库存数量");
					}
					this.$emit('fixdBtn',JSON.parse(JSON.stringify(this.fixedCar)))
				}else{
					let data = {
						cartMyType:2,
						virtualList:this.virtualList
					}
					for (let i = 0; i < this.virtualList.length; i++) {
					  const element = this.virtualList[i];
					  if (!element.value) {
					    return this.$Message.error("请输入所有卡密");
					  }
					}
					this.$emit('fixdBtn',JSON.parse(JSON.stringify(data)))
				}
			}
        }
    }
</script>

<style lang="stylus" scoped>
	.carMywrapper{
		.download{
			position absolute;
			top:2px;
			left:180px;
		}
		.stock-disk{
			margin 10px 0 15px 0;
		}
		.scroll-virtual {
		  max-height: 320px;
		  overflow-y: auto;
		  margin-top: 10px;
		}
		.virtual-title {
		  width: 50px;
		}
		.deteal-btn {
		  color: #5179ea;
		  cursor pointer;
		}
		.add-more {
		  margin-top: 20px;
		  display: flex;
		  position relative;
		}
		.footer{
				 display flex
				 justify-content flex-end
				 margin-top 40px
				 button{
					  margin-left 10px
				 }
		}
	}
</style>
