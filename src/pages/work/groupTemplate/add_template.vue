<template>
  <div>
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/group/template' }">
              <div class="font-sm after-line">
                <span class="iconfont iconfanhui"></span>
                <span class="pl10">返回</span>
              </div>
            </router-link>
          </div>
          <span class="mr20 ml16">新建群发</span>
        </div>
      </PageHeader>
    </div>

    <Card :bordered="false" dis-hover class="ivu-mt mb100">
      <Form :model="formItem" ref="formItem" :rules="ruleValidate" :label-width="110" :label-colon="true">
        <FormItem label="群发名称" prop="name">
          <Input
            v-model="formItem.name"
            style="width: 460px"
            placeholder="群发名称仅内部可见"
          />
        </FormItem>
        <FormItem label="选择群主" required>
          <Button @click="addUser()">选择群主</Button>
          <div style="margin-top: 10px">
            <Tag
              closable
              @on-close="handleDel"
              :name="item.name"
              size="medium"
              v-for="(item, index) in formItem.ownerInfo"
              :key="index"
              >{{ item.name }}</Tag
            >
            <!-- <div class=" ivu-tag ivu-tag-dot ivu-tag-size-default ivu-tag-closable ivu-tag-checked"
             v-for="(item, index) in formItem.ownerInfo"
              :key="index">{{ item.name }}
              <i
                class="ivu-icon ivu-icon-ios-close"
                @click="handleDel(index)"
              ></i>
              </div> -->
          </div>
          <div class="tips">群主收到群发任务后，可在企业微信上选择群聊发送群发消息</div>
        </FormItem>
        <FormItem label="群发内容" required>
          <Input
            v-model="formItem.welcome_words.text.content"
            type="textarea"
            :rows="4"
            style="width: 460px"
            placeholder="限制1000字"
          />
        </FormItem>
        <!-- <FormItem>
          <Button type="primary" ghost @click="insertName()"
            >插入客户名称</Button
          >
        </FormItem> -->
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
            v-for="(item, index) in formItem.welcome_words.attachments"
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
        <FormItem label="发送类型" prop="template_type">
          <RadioGroup v-model="formItem.template_type">
            <Radio label="0">立即发送</Radio>
            <Radio label="1">定时发送</Radio>
          </RadioGroup>
        </FormItem>
        <FormItem label="定时发送时间" v-if="formItem.template_type == 1">
          <DatePicker
            type="datetime"
            v-model="formItem.send_time"
            @on-change="snedChangeTime"
            placeholder="请选择发送时间"
            style="width: 460px"
          ></DatePicker>
        </FormItem>
      </Form>
    </Card>
    <Card :bordered="false" dis-hover class="fixed-card">
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
    <!-- <department
      ref="department"
      :active-department="activeDepartment"
      :is-site="isSite"
      :userList="userList"
      :only-department="onlyDepartment"
      @changeMastart="changeMastart"
    /> -->
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
        <template slot-scope="{ row }" slot="notice">
            <Tooltip max-width="200" placement="bottom">
              <span class="line2">{{ row.notice }}</span>
              <p slot="content">{{ row.notice }}</p>
            </Tooltip>
        </template>
        <template slot-scope="{ row }" slot="ownerInfo">
          <span>{{row.ownerInfo.name}}</span>
        </template>
        <!-- admin_user_list -->
        <template slot-scope="{ row }" slot="admin_user_list">
          <span v-for="(item,index) in row.admin_user_list" :key="index">{{item.name}}</span>
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
  </div>
</template>
<script>
import { mapState, mapMutations } from "vuex";
import uploadPictures from "@/components/uploadPictures";
// import department from "@/components/department/index.vue";
import { workGroupChat, workGroupTemplateChatSave } from "@/api/work";
import timeOptions from "@/utils/timeOptions";
import { getNewFormBuildRuleApi } from "@/api/setting";
export default {
  data() {
    return {
      userLoading: false,
      groupStatus:false,
      formItem: {
        template_type: "0", //0=立即发送，1=定时发送
        name: "",
        type: "1", //客户群发
        client_type: "0", //0=全部客户，1=所选客户
        where_time: "", //客户筛选时间
        where_label: [], //标签
        ownerInfo: [], //所选群发账号
        send_time: "", //定时发送账号
        welcome_words: {
          text: {
            content: "",
          },
          attachments: [],
        }, 
      },
      ruleValidate:{
        name: [
          { required: true, message: '群发名称不能为空', trigger: 'blur' }
        ],
        template_type:[
          { required: true, message: '请选择发送类型', trigger: 'change' }
        ]
      },
      options: timeOptions,
      timeVal: [],
      //客户标签列表
      labelList: [],
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
      picTit: "",
      modalPic: false,
      modalRoutine: false,
      isChoice: "单选",
      activeDepartment: {},
      isSite: true,
      onlyDepartment: false,
      openType: "",
      userList: [],
    };
  },
  components: { uploadPictures },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 80;
    },
    labelPosition() {
      return this.isMobile ? "top" : "left";
    },
  },
  mounted() {
    this.setCopyrightShow({ value: false });
    this.getWorkGroupChat();
  },
  destroyed() {
    this.setCopyrightShow({ value: true });
  },
  methods: {
    ...mapMutations("admin/layout", ["setCopyrightShow"]),
    onchangeTime(e) {
      this.timeVal = e;
      this.formItem.where_time = this.timeVal.join("-");
    },
    snedChangeTime(val) {
      this.formItem.send_time = val;
    },
    modalPicTap(picTit) {
      this.modalPic = true;
      this.picTit = picTit;
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
      // this.formItem.userids = this.selectGroup.map(item=>{
      //   return {
      //     userid:item.ownerInfo.userid,
      //     name:item.ownerInfo.name
      //   }
      // })
      this.formItem.ownerInfo = this.selectGroup.map(item=>{
          return {
            userid:item.ownerInfo.userid,
            name:item.ownerInfo.name
          }
        })
    },
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
    addUser() {
      this.groupStatus = true;
    },
    //tag标签删除成员
    handleDel(e,name) {
      let index = this.formItem.ownerInfo.indexOf(name)
      this.formItem.ownerInfo.splice(index, 1);
    },
    //群发内容tag删除
    wordsDel(name) {
      let index = this.formItem.welcome_words.attachments.indexOf(name);
      this.formItem.welcome_words.attachments.splice(index, 1);
    },
    groupChange(index){
      this.groupForm.page =index;
      this.getWorkGroupChat();
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
    // insertName() {
    //   this.formItem.welcome_words.text.content =
    //     this.formItem.welcome_words.text.content.concat("##客户名称##");
    // },
    routineConfirm() {
      const routine = this.deepClone(this.rontineObj);
      this.formItem.welcome_words.attachments.push(routine);
    },
    submit() {
      const formData = this.deepClone(this.formItem);
      formData.userids = formData.ownerInfo.map((item) => {
        return item.userid;
      });
      this.$refs.formItem.validate((valid) => {
          if (valid) {
            workGroupTemplateChatSave(formData).then((res) => {
              this.$Message.success(res.msg);
              this.$router.push("/admin/work/group/template");
            })
            .catch((err) => {
              this.$Message.error(err.msg);
            });
          }
        })
    },
    //分页
    ownerChange(index){
      this.clientForm.page = index;
      this.getownerList();
    },
    //深克隆
    deepClone(obj) {
      let newObj = Array.isArray(obj) ? [] : {};
      if (obj && typeof obj === "object") {
        for (let key in obj) {
          if (obj.hasOwnProperty(key)) {
            newObj[key] =
              obj && typeof obj[key] === "object"
                ? this.deepClone(obj[key])
                : obj[key];
          }
        }
      }
      return newObj;
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

/deep/.ivu-input-number-input {
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

.tag_icon {
  font-size: 14px;
  display: inline-block;
  margin-right: 8px;
}

.picBox {
  display: inline-block;
  cursor: pointer;

  .upLoad {
    width: 58px;
    height: 58px;
    line-height: 58px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.02);
  }

  .pictrue {
    width: 60px;
    height: 60px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    margin-right: 10px;

    img {
      width: 100%;
      height: 100%;
    }
  }

  .iconfont {
    color: #CCCCCC;
    font-size: 26px;
    text-align: center;
  }
}

.mb100 {
  margin-bottom: 100px;
}

.fixed-card {
  position: fixed;
  right: 0;
  bottom: 0;
  left: 200px;
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

.tips {
	  display: inline-bolck;
	  font-size: 12px;
	  font-weight: 400;
	  color: #999999;
	  margin-top: 10px;
	  line-height: initial;
	}
</style>