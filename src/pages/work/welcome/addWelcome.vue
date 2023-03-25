<template>
  <div>
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/welcome' }">
            <div class="font-sm after-line">
              <span class="iconfont iconfanhui"></span>
              <span class="pl10">返回</span>
            </div>
              <!-- <Button icon="ios-arrow-back" size="small" class="mr20">返回</Button> -->
            </router-link>
          </div>
          <span class="mr20 ml16">新建好友欢迎语</span>
        </div>
      </PageHeader>
    </div>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Form :model="formItem" :label-width="110" :label-colon="true">
        <FormItem label="类型" prop="type">
          <RadioGroup v-model="formItem.type">
            <Radio label="0">通用</Radio>
            <Radio label="1">指定企业成员</Radio>
          </RadioGroup>
          <div class="desc">
            一个成员如果被设置了多个欢迎语，将会使用最新设置或修改的欢迎语
          </div>
        </FormItem>
        <template v-if="formItem.type == '1'">
            <FormItem label="添加成员">
                <Button @click="addUser()">添加成员</Button>
                <div style="margin-top:10px;">
                    <Tag closable @on-close="handleDel" :name="item.name" size="large" v-for="(item,index) in formItem.userids" :key="index">{{item.name}}</Tag>
                </div>
            </FormItem>
          </template>
        <FormItem label="欢迎语内容">
          <Input
            v-model="formItem.content"
            type="textarea"
            :rows="4"
            style="width: 460px"
          />
        </FormItem>
        <FormItem>
          <Button type="primary" ghost @click="insertName()"
            >插入客户名称</Button
          >
          <div class="desc">
            可以插入客户名称，在发送给客户之前系统会自动解析成客户的名称
          </div>
        </FormItem>
        <FormItem>
          <Poptip trigger="hover">
            <template slot="content">
              <div class="poptip_content">
                <div @click="modalPicTap('image')">
                  <div class="add_img acea-row row-center-wrapper">
                    <span class="iconfont icontupian4"></span>
                  </div>
                  <span class="tip_tit">图片</span>
                </div>
                <div @click="addRoutine()">
                  <div class="add_routine acea-row row-center-wrapper">
                    <span class="iconfont iconxiaochengxu"></span>
                  </div>
                  <span class="tip_tit">小程序</span>
                </div>
              </div>
            </template>
            <Button type="success">添加图片/小程序</Button>
          </Poptip>
        </FormItem>
        <FormItem>
          <div
            v-for="(item, index) in formItem.attachments"
            :key="index"
          >
            <div
              class="
                ivu-tag
                ivu-tag-dot
                ivu-tag-size-default
                ivu-tag-closable
                ivu-tag-checked
              "
            >
              <span
                v-if="item.msgtype == 'image'"
                class="iconfont icontupian4 tag_icon"
              ></span>
              <span
                v-if="item.msgtype == 'miniprogram'"
                class="iconfont iconxiaochengxu tag_icon"
              ></span>
              <span v-if="item.msgtype == 'image'" class="ivu-tag-text">{{
                item.image.pic_url
              }}</span>
              <span v-if="item.msgtype == 'miniprogram'" class="ivu-tag-text">{{
                item.miniprogram.title
              }}</span>
              <i
                v-if="item.msgtype == 'image'"
                class="ivu-icon ivu-icon-ios-close"
                @click="wordsDel(item.image.pic_url)"
              ></i>
              <i
                v-if="item.msgtype == 'miniprogram'"
                class="ivu-icon ivu-icon-ios-close"
                @click="wordsDel(item.miniprogram.title)"
              ></i>
            </div>
          </div>
        </FormItem>
        <FormItem label="排序">
            <InputNumber v-model="formItem.sort" controls-outside :min="0" :max="999"></InputNumber>
            <div class="desc">
              数字越⼤排序越靠前 
            </div>
        </FormItem>
      </Form>
    </Card>
    <Card :bordered="false" dis-hover class="fixed-card" :style="{left: `${!menuCollapse?'200px':isMobile?'0':'80px'}`}">
      <div class="acea-row row-center-wrapper">
        <Button type="primary" @click="submit()" class="step_btn">提交</Button>
      </div>
    </Card>
    <Modal
      v-model="modalPic"
      width="960px"
      scrollable
      footer-hide
      closable
      title="logo"
      :mask-closable="false"
      :z-index="9"
    >
      <uploadPictures
        :isChoice="isChoice"
        @getPic="getPic"
        :gridBtn="gridBtn"
        :gridPic="gridPic"
        v-if="modalPic"
      ></uploadPictures>
    </Modal>
    <Modal
      v-model="modalRoutine"
      title="添加小程序消息"
      @on-ok="routineConfirm"
      :z-index="1"
    >
      <Form :model="formItem" :label-width="110" >
        <FormItem label="封面图:">
          <div class="picBox" @click="modalPicTap('routine')">
            <div class="pictrue" v-if="rontineObj.miniprogram.pic_url">
              <img v-lazy="rontineObj.miniprogram.pic_url" />
            </div>
            <div class="upLoad" v-else>
              <div class="iconfont">+</div>
            </div>
          </div>
        </FormItem>
        <FormItem label="小程序消息标题:">
          <Input v-model="rontineObj.miniprogram.title"></Input>
        </FormItem>
        <FormItem label="小程序Appid:">
          <Input v-model="rontineObj.miniprogram.appid"></Input>
        </FormItem>
        <FormItem label="小程序功能页:">
          <Input v-model="rontineObj.miniprogram.page"></Input>
        </FormItem>
      </Form>
    </Modal>
    <department
        ref="department"
        :active-department="activeDepartment"
        :is-site="isSite"
        :userList="userList"
        :only-department="onlyDepartment"
        @changeMastart="changeMastart"
      />
  </div>
</template>
<script>
import { mapState } from "vuex";
import uploadPictures from "@/components/uploadPictures";
import { getNewFormBuildRuleApi } from "@/api/setting";
import department from "@/components/department/index.vue";
import {workWelcomeSave,getWelcomeInfo,welcomeUpdate} from "@/api/work"
export default {
  data() {
    return {
      formItem: {
        sort:0,
        type:"0",
        userids:[],
        content: "",
        attachments: [],
      },
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
      rontineObj:{
        msgtype: "miniprogram",
        miniprogram:{
          pic_url:"",
          pic_media_id:"",
          title:"",
          appid:"",
          page:""
        }
      },
      imageObj:{
        msgtype: "image",
        image:{
          media_id:"",
          pic_url:""
        }
      },
      picTit:"",
      modalPic: false,
      modalRoutine: false,
      isChoice: "单选",
      activeDepartment: {},
      isSite: true,
      onlyDepartment: false,
      openType:'',
      userList:[]
    };
  },
    components: { uploadPictures, department },
  computed: {
    ...mapState("admin/layout", ["isMobile","menuCollapse"]),
    labelWidth() {
      return this.isMobile ? undefined : 80;
    },
    labelPosition() {
      return this.isMobile ? "top" : "left";
    },
  },
  mounted() {
    if (this.$route.params.id !== "0" && this.$route.params.id) {
      this.getInfo();
    } else if (!this.$route.params.id){
      
    }
  },
  methods: {
    modalPicTap(picTit){
      this.modalPic = true;
      this.picTit = picTit;
    },
    addRoutine() {
      getNewFormBuildRuleApi('routine').then(res => {
          let data = res.data;
          this.rontineObj.miniprogram.pic_url = '';
          this.rontineObj.miniprogram.title = data.routine_name.value;
          this.rontineObj.miniprogram.appid = data.routine_appId.value;
          this.rontineObj.miniprogram.page = '/pages/index/index';
      })
      this.modalRoutine = true;
    },
    addUser(){
      this.userList = this.formItem.userids;
      this.$refs.department.memberStatus = true;
    },
    //tag标签删除成员
    handleDel(e,name){
        // let index = this.formItem.userids.indexOf(name);
        // this.formItem.userids.splice(index, 1);
        let i = this.formItem.userids.findIndex((item) => item.name == name);
        this.formItem.userids.splice(i,1);
    },
    //欢迎语tag删除
    wordsDel(name){
      let index = this.formItem.attachments.indexOf(name);
      this.formItem.attachments.splice(index,1);
    },
    changeMastart(arr,type){
        this.formItem.userids = arr.map(item=>{
            return {
              userid:item.userid, 
              name:item.name,
            }
        })
    },
    // 选中图片
    getPic(pc) {
      switch (this.picTit) {
        case "image":
          this.imageObj.image.pic_url = pc.att_dir;
          this.formItem.attachments.push(this.imageObj)
          break;
        case "routine":
          this.rontineObj.miniprogram.pic_url = pc.att_dir;
          break;
      }
      this.modalPic = false;
    },
    insertName(){
      this.formItem.content = this.formItem.content.concat('##客户名称##');
    },
    routineConfirm() {
      const routine = this.deepClone(this.rontineObj);
      this.formItem.attachments.push(routine)
    },
    getInfo(){
        getWelcomeInfo(this.$route.params.id).then(res=>{
            this.formItem.type = res.data.type.toString();
            this.formItem.attachments = res.data.attachments;
            this.formItem.content = res.data.content;
            this.formItem.sort = res.data.sort;
            if(res.data.userList.length){
              this.formItem.userids = res.data.userList.map(item=>{
                return {
                    userid:item.userid,
                    welcome_id:item.welcome_id,
                    name:item.member.name
                }
              })
            }else{
              this.$set(this.formItem,'userids',[]);
            }
            
        }).catch(err=>{
            this.$Message.error(err.msg);
        })
    },
    submit(){
        const formData = this.deepClone(this.formItem)
        if(formData.type == 1){
            formData.userids = formData.userids.map(item=>{
                return item.userid
            })
        }
        if(formData.type == 0 && formData.userids.length){
            formData.userids = formData.userids.map(item=>{
                return item.userid
            })
            delete formData.userList
          }
        formData.type = Number(formData.type);
        if (this.$route.params.id !== "0" && this.$route.params.id) {
            welcomeUpdate(this.$route.params.id,formData).then(res=>{
                this.$Message.success("欢迎语修改成功");
                this.$router.push('/admin/work/welcome')
            }).catch(err=>{
                this.$Message.error(err.msg)
            })
        } else if (!this.$route.params.id){
            workWelcomeSave(formData).then(res=>{
                this.$Message.success("欢迎语新建成功");
                this.$router.push('/admin/work/welcome')
            }).catch(err=>{
                this.$Message.error(err.msg)
            })
        }
    },
    //深克隆
    deepClone(obj) {
        let newObj = Array.isArray(obj) ? [] : {}
        if (obj && typeof obj === "object") {
            for (let key in obj) {
                if (obj.hasOwnProperty(key)) {
                    newObj[key] = (obj && typeof obj[key] === 'object') ? this.deepClone(obj[key]) : obj[key];
                }
            }
        } 
        return newObj
    },
  },
};
</script>
<style scoped lang="stylus">
.desc {
  color: #999;
  font-size: 12px;
  line-height: 17px;
  padding-top: 6px;
}
/deep/.ivu-input-number-input{
    text-align: center;
}
.poptip_content {
  width: 138px;
  height: 94px;
  box-sizing: border-box;
  padding: 17px;
  display: flex;
  justify-content: space-between;
  cursor: pointer;
}

.add_img {
  width: 40px;
  height: 40px;
  background: rgba(24, 144, 255, 0.1);
  border-radius: 2px;
}

.add_routine {
  width: 40px;
  height: 40px;
  background: rgba(27, 190, 107, 0.1);
  border-radius: 2px;
}

.icontupian4 {
  color: #1890FF;
  font-size: 20px;
}

.iconxiaochengxu {
  color: #1BBE6B;
  font-size: 20px;
}

.tip_tit {
  display: block;
  font-size: 12px;
  text-align: center;
  padding-top: 6px;
}
.tag_icon{
  font-size:14px;
  display:inline-block;
  margin-right:8px;
}
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