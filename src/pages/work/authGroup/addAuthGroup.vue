<template>
  <div>
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/auth_group' }">
              <!-- <Button icon="ios-arrow-back" size="small" class="mr20">返回</Button> -->
              <div class="font-sm after-line">
                <span class="iconfont iconfanhui"></span>
                <span class="pl10">返回</span>
              </div>
            </router-link>
          </div>
          <span class="mr20 ml16">新建自动拉群</span>
        </div>
      </PageHeader>
    </div>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Form :model="formItem" :label-width="110" :label-colon="true" :rules="ruleValidate">
        <FormItem label="二维码名称" prop="name">
          <Input
            v-model="formItem.name"
            placeholder="请输入二维码名称"
            style="width: 460px"
          ></Input>
        </FormItem>
        <FormItem label="选择群聊" required>
          <Button @click="addGroup()">添加群聊</Button>
          <div style="margin-top: 10px">
            <Tag closable @on-close="handleDel" :name="item.name" size="medium" v-for="(item,index) in formItem.chat_id" :key="index">{{item.name}}</Tag>
          </div>
        </FormItem>
        <FormItem label="自动建群">
          <i-switch v-model="formItem.auth_group_chat" size="large" :true-value="1" :false-value="0">
            <span slot="open">开启</span>
            <span slot="close">关闭</span>
          </i-switch>
          <div class="desc">
            开启时，选择群聊群人数达到上限后，将以原群主身份自动创建新群聊
          </div>
        </FormItem>
        <FormItem label="设置群名称" v-if="formItem.auth_group_chat">
          <Input
            v-model="formItem.group_name"
            placeholder="请输入群名称"
            style="width: 460px"
          />
        </FormItem>
        <FormItem label="群序号"  v-if="formItem.auth_group_chat">
          <InputNumber
            v-model="formItem.group_num"
            controls-outside
            :min="0"
            :max="999"
          ></InputNumber>
          <div class="desc">
            群序列号将在自动创建群时自动增加，比如设置的群名为：西安客户群，设置的序列表为：5，那么自动建群的名称为：西安客户群5，西安客户群6，西安客户群7，西安客户群8。
          </div>
        </FormItem>
        <!-- <FormItem label="设置群主">
          <Button @click="addUser('one')">选择群主</Button>
          <div style="margin-top: 10px">
            <Tag closable @on-close="handleDel" :name="item.name" size="large" v-for="(item,index) in formItem.owner" :key="index">{{item.name}}</Tag>
          </div>
        </FormItem>
        <FormItem label="设置群管理员">
          <Button @click="addUser('two')">添加管理员</Button>
           <div style="margin-top: 10px">
            <Tag closable @on-close="handleDel" :name="item.name" size="large" v-for="(item,index) in formItem.admin_user" :key="index">{{item.name}}</Tag>
          </div>
        </FormItem>
        <FormItem label="欢迎语内容">
          <Input
            v-model="formItem.welcome_words.text.content"
            type="textarea"
            :rows="4"
            placeholder="请输入群名称"
            style="width: 460px"
          />
        </FormItem>
        <FormItem>
          <Button type="primary" ghost @click="insertName()"
            >插入客户名称</Button
          >
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
          <div v-for="(item, index) in formItem.welcome_words.attachments" :key="index">
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
        </FormItem> -->
        <FormItem label="客户标签">
          <TreeSelect
              v-model="formItem.label"
              :data="labelList"
              v-width="400"
              multiple 
            />
        </FormItem>
      </Form>
    </Card>
    <Card :bordered="false" dis-hover class="fixed-card" :style="{left: `${!menuCollapse?'200px':isMobile?'0':'80px'}`}">
      <div class="acea-row row-center-wrapper">
        <Button type="primary" @click="submit()" class="step_btn">保存</Button>
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
      <Form :model="formItem" :label-width="110" :label-colon="true">
        <FormItem label="小程序功能页">
          <div class="picBox" @click="modalPicTap('routine')">
            <div class="pictrue" v-if="rontineObj.miniprogram.pic_url">
              <img v-lazy="rontineObj.miniprogram.pic_url" />
            </div>
            <div class="upLoad" v-else>
              <div class="iconfont">+</div>
            </div>
          </div>
        </FormItem>
        <FormItem label="小程序消息标题">
          <Input v-model="rontineObj.miniprogram.title"></Input>
        </FormItem>
        <FormItem label="小程序Appid">
          <Input v-model="rontineObj.miniprogram.appid"></Input>
        </FormItem>
        <FormItem label="小程序功能页">
          <Input v-model="rontineObj.miniprogram.page"></Input>
        </FormItem>
      </Form>
    </Modal>
     <Modal
      v-model="groupStatus"
      title="客户群列表"
      @on-ok="groupConfirm"
      width="900"
      >
      <Table :columns="groupColumn" :data="groupData.list" 
      :loading="userLoading"
      @on-select-all="selectAll"
      @on-select-all-cancel="selectAll"
      @on-selection-change="handleSelectRow">
         <template slot-scope="{ row }" slot="avatar">
          <viewer class="acea-row row-center">
            <div class="tabBox_img">
              <img v-lazy="row.client.avatar" />
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row }" slot="ownerInfo">
          <span>{{row.ownerInfo.name}}</span>
        </template>
        <template slot-scope="{ row }" slot="admin_user_list">
          <span v-for="(item,index) in row.admin_user_list" :key="index">{{item.name}}</span>
        </template>
        <template slot-scope="{ row }" slot="notice">
            <Tooltip max-width="200" placement="bottom">
              <span class="line2">{{ row.notice }}</span>
              <p slot="content">{{ row.notice }}</p>
            </Tooltip>
        </template>
        <template slot-scope="{ row }" slot="name">
          <span>{{row.client.name}}</span>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="groupData.count"
          :current="groupForm.page"
          show-elevator
          show-total
          @on-change="groupChange"
          :page-size="groupForm.limit"
        />
      </div>
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
import department from "@/components/department/index.vue";
import {workGroupChat,workLabel,groupChatAuthSave,getGroupChatInfo,UpdateGroupChat} from "@/api/work"
export default {
  data() {
    return {
      formItem: {
        name: "", //二维码名称
        chat_id:[], //群聊chat_id
        group_name: "", //群名称
        group_num: 0, //群序列号
        //owner:[], //群主userid
        //admin_user:[], //群管理员userid
        label: [], //客户标签
        auth_group_chat: 0, //自动建群0=关闭，1=开启
        //welcome_words:{
        //  text:{
        //    content: "",
        //  },
        //  attachments: [],
        //}
      },
      labelList:[],
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
      groupColumn:[
        {
          type: "selection",
          width: 60,
          align: "center",
        },
        {
          title: "群名称",
          key: "name",
          minWidth: 80,
          align: 'center'
        },
        {
          title: "群主",
          slot: "ownerInfo",
          minWidth: 100,
          align: 'center'
        },
        {
          title: "群公告",
          slot: "notice",
          minWidth: 100,
          align: 'center'
        },
        {
          title: "管理员",
          slot: "admin_user_list",
          minWidth: 80,
          align: 'center'
        },
        {
          title: "创建时间",
          key: "group_create_time",
          minWidth: 110,
          align: 'center'
        },
        {
          title: "群人数",
          key: "member_num",
          minWidth: 80,
          align: 'center'
        },
        {
          title: "退群人数",
          key: "retreat_group_num",
          minWidth: 80,
          align: 'center'
        },
      ],
      groupData:[],
      groupForm:{
        page:1,
        limit:15
      },
      rontineObj: {
        msgtype: "miniprogram",
        miniprogram: {
          pic_url: "",
          pic_media_id: "",
          title: "",
          appid: "",
          page: "",
        },
      },
      imageObj: {
        msgtype: "image",
        image: {
          media_id: "",
          pic_url: "",
        },
      },
      groupStatus:false,
      userLoading:false,
      picTit: "",
      modalPic: false,
      modalRoutine: false,
      isChoice: "单选",
      activeDepartment: {},
      isSite: true,
      onlyDepartment: false,
      openType: "",
      userList: [],
      selectGroup:[],
      ruleValidate:{
        name: [
            { required: true, message: '二维码名称不能为空', trigger: 'blur' }
        ],
      }
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
    }
    this.getWorkGroupChat();
    this.getWorkLabel();
  },
  methods: {
    getWorkGroupChat(){
      this.userLoading = true;
      workGroupChat(this.groupForm).then(res=>{
        this.groupData = res.data;
        this.userLoading = false;
      }).catch(err=>{
        this.$Message.error(err.msg)
        this.userLoading = false;
      })
    },
    addGroup(){
      this.groupStatus = true;
    },
    selectAll(row) {
      if (row.length) {
        this.selectGroup = row; 
      }
    },
    handleSelectRow(row){
      this.selectGroup = row; 
    },
    groupConfirm(){
      this.formItem.chat_id = this.selectGroup.map(item=>{
        return {
          chat_id:item.chat_id,
          name:item.name
        }
      })
    },
    groupChange(index){
      this.groupForm.page =index;
      this.getWorkGroupChat();
    },
    modalPicTap(picTit) {
      this.modalPic = true;
      this.picTit = picTit;
    },
    addRoutine() {
      this.rontineObj.miniprogram.pic_url = "";
      this.rontineObj.miniprogram.title = "";
      this.rontineObj.miniprogram.appid = "";
      this.rontineObj.miniprogram.page = "";
      this.modalRoutine = true;
    },
    addUser(type,index) {
        this.$refs.department.memberStatus = true;
        switch (type) {
          case 'one':
            // this.userList = this.formItem.userids;
            this.$refs.department.openType = 'one';
            break;
          case 'two':
            // this.userList = this.formItem.reserve_userid;
            this.$refs.department.openType = 'two';
            break;
          default:
              break;
        }
    },
    //确认人员
    changeMastart(arr,type){
        if(type == 'one'){
         if(arr.length && arr.length > 1){
           this.$Message.warning("群主只能选择一个");
           let newArr = arr.slice(0,1);
           this.formItem.owner = newArr.map(item=>{
              return {
                userid:item.userid,
                name:item.name
              }
            })
         }else{
            this.formItem.owner = arr.map(item=>{
              return {
                userid:item.userid,
                name:item.name
              }
            })
         }
        }else if(type == 'two'){
          this.formItem.admin_user = arr.map(item=>{
              return {
                userid:item.userid,
                name:item.name
              }
          })
        }
    },
    //tag标签删除成员
    handleDel(e, name) {
      let i = this.formItem.chat_id.findIndex((item) => item.name == name);
      this.formItem.chat_id.splice(i,1);
    },
    //欢迎语tag删除
    wordsDel(name){
      let index = this.formItem.welcome_words.attachments.indexOf(name);
      this.formItem.welcome_words.attachments.splice(index,1);
    },
    // 选中图片
    getPic(pc) {
      switch (this.picTit) {
        case "image":
          this.imageObj.image.pic_url = pc.att_dir;
          this.formItem.welcome_words.attachments.push(this.imageObj);
          break;
        case "routine":
          this.rontineObj.miniprogram.pic_url = pc.att_dir;
          break;
      }
      this.modalPic = false;
    },
    insertName() {
      this.formItem.welcome_words.text.content = "##客户名称##";
    },
    routineConfirm() {
      const routine = this.deepClone(this.rontineObj);
      this.formItem.welcome_words.attachments.push(routine);
    },
    routineCancel() {},
    //获取客户标签
    getWorkLabel() {
      workLabel().then((res) => {
        this.labelList = res.data.map((org) => this.mapTree(org));
      });
    },
     mapTree(org) {
      const haveChildren =
        Array.isArray(org.children) && org.children.length > 0;
      return {
        //分别将我们查询出来的值做出改变他的key
        title: org.label,
        expand: true,
        value: org.value,
        selected: false,
        checked: false,
        children: haveChildren ? org.children.map((i) => this.mapTree(i)) : [],
      };
    },
    getInfo(){
      getGroupChatInfo(this.$route.params.id).then(res=>{
        this.formItem = res.data;
        this.formItem.chat_id = this.formItem.chatList;
      })
    },
    submit(){
      if(!this.formItem.chat_id.length) return this.$Message.error("请添加群聊")
      const formData = this.deepClone(this.formItem);
      formData.chat_id = formData.chat_id.map(item=>{
        return item.chat_id
      })
      // formData.admin_user = formData.admin_user.map(item=>{
      //   return item.userid
      // })
      // formData.owner = formData.owner[0].userid;
      if(this.$route.params.id){
        delete formData.chatList;
        delete formData.labelList;
        UpdateGroupChat(this.$route.params.id,formData).then(res=>{
          this.$Message.success("修改自动拉群成功");
          this.$router.push('/admin/work/auth_group')
        })
      }else{
        groupChatAuthSave(formData).then(res=>{
          this.$Message.success("保存成功");
          this.$router.push('/admin/work/auth_group')
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

.poptip_content {
  width: 138px;
  height: 94px;
  box-sizing: border-box;
  padding: 17px;
  display: flex;
  justify-content: space-between;
  cursor: pointer;
}

/deep/.ivu-input-number-input {
  text-align: center;
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

.add_table {
  width: 500px;
  border-left: 1px dashed rgba(0, 0, 0, 0.15);
  border-right: 1px dashed rgba(0, 0, 0, 0.15);
}

.mt-18 {
  margin-top: 18px;
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