<template>
  <div>
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/client/group' }">
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
        <FormItem label="名称" prop="name">
          <Input
            v-model="formItem.name"
            style="width: 460px"
            placeholder="请输入名称"
          />
        </FormItem>
        <FormItem label="选择群发账号" required>
          <Button @click="addUser()">选择成员</Button>
          <div style="margin-top: 10px">
            <Tag
              closable
              @on-close="handleDel"
              :name="item.name"
              size="medium"
              v-for="(item, index) in formItem.userids"
              :key="index"
              >{{ item.name }}</Tag
            >
          </div>
        </FormItem>
        <FormItem label="选择客户" prop="client_type">
          <RadioGroup v-model="formItem.client_type">
            <Radio label="0">全部客户</Radio>
            <Radio label="1">筛选客户</Radio>
          </RadioGroup>
          <div class="desc">预计群发
            <span class="client_count">[{{clientCount}}]</span>人。
            <span v-show="formItem.client_type == 1">将消息发送给符合条件的客户</span>
          </div>
        </FormItem>
        <template v-if="formItem.client_type == 1">
          <FormItem label="添加时间">
            <DatePicker
              :editable="false"
              @on-change="onchangeTime"
              :value="timeVal"
              format="yyyy/MM/dd"
              type="datetimerange"
              placement="bottom-start"
              placeholder="自定义时间"
              style="width: 460px"
              :options="options"
            ></DatePicker>
          </FormItem>
          <FormItem label="标签">
            <TreeSelect
              v-model="formItem.where_label"
              :data="labelList"
              v-width="460"
              multiple
            />
          </FormItem>
          <FormItem label="排除标签">
            <TreeSelect
              v-model="formItem.where_not_label"
              :data="newLabelList"
              v-width="460"
              multiple
            />
          <div class="desc">
            可根据标签选择客户，群发时将不会发送给该标签内的客户
          </div>
          </FormItem>
        </template>
        <FormItem label="群发内容">
          <Input
            v-model="formItem.welcome_words.text.content"
            type="textarea"
            :rows="4"
            style="width: 460px"
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
      <Form :model="formItem" :label-width="110">
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
import { mapState, mapMutations } from "vuex";
import uploadPictures from "@/components/uploadPictures";
import department from "@/components/department/index.vue";
import { workLabel, workGroupTemplateSave,workClientCount } from "@/api/work";
import timeOptions from "@/utils/timeOptions";
import { getNewFormBuildRuleApi } from "@/api/setting";
export default {
  data() {
    return {
      formItem: {
        template_type: "0", //0=立即发送，1=定时发送
        name: "",
        type: "0", //客户群发
        client_type: "0", //0=全部客户，1=所选客户
        where_time: "", //客户筛选时间
        where_label: [], //标签
        userids: [], //所选群发账号
        send_time: "", //定时发送账号
        welcome_words: {
          text: {
            content: "",
          },
          attachments: [],
        }, //欢迎语
      },
      ruleValidate:{
        name: [
          { required: true, message: '名称不能为空', trigger: 'blur' }
        ],
        client_type:[
          { required: true, message: '请选择客户类型', trigger: 'change' }
        ],
        template_type:[
          { required: true, message: '请选择发送类型', trigger: 'change' }
        ]
      },
      options: timeOptions,
      timeVal: [],
      //客户标签列表
      labelList: [],
      newLabelList:[],
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
      picTit: "",
      modalPic: false,
      modalRoutine: false,
      isChoice: "单选",
      activeDepartment: {},
      isSite: true,
      onlyDepartment: false,
      openType: "",
      userList: [],
      clientCount:0
    };
  },
  components: { uploadPictures, department },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 80;
    },
    labelPosition() {
      return this.isMobile ? "top" : "left";
    },
  },
  watch:{
    "formItem.where_label"(val,oldVal){
      if(val !== oldVal){
        this.getClientCount();
      }
    },
    "formItem.where_not_label"(val,oldVal){
      if(val !== oldVal){
        this.getClientCount();
      }
    },
    "formItem.where_time"(val,oldVal){
      if(val !== oldVal){
        this.getClientCount();
      }
    },
    "formItem.userids"(val,oldVal){
      if(val !== oldVal){
        this.getClientCount();
      }
    },
  },
  mounted() {
    this.setCopyrightShow({ value: false });
    this.getWorkLabel();
    this.getClientCount();
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
    //获取客户标签
    getWorkLabel() {
      workLabel().then((res) => {
        this.labelList = res.data.map((org) => this.mapTree(org));
        this.newLabelList = this.deepClone(this.labelList);
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
      this.userList = this.formItem.userids;
      this.$refs.department.memberStatus = true;
    },
    //tag标签删除成员
    handleDel(e, name) {
      let index = this.formItem.userids.indexOf(name);
      this.formItem.userids.splice(index, 1);
      this.getClientCount();
    },
    //欢迎语tag删除
    wordsDel(name) {
      let index = this.formItem.welcome_words.attachments.indexOf(name);
      this.formItem.welcome_words.attachments.splice(index, 1);
    },
    changeMastart(arr, type) {
      this.formItem.userids = arr.map((item) => {
        return {
          userid: item.userid,
          name: item.name,
        };
      });
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
      this.formItem.welcome_words.text.content =
        this.formItem.welcome_words.text.content.concat("##客户名称##");
    },
    routineConfirm() {
      const routine = this.deepClone(this.rontineObj);
      this.formItem.welcome_words.attachments.push(routine);
    },
    submit() {
      if(!this.formItem.userids.length) return this.$Message.error("请选择成员")
      const formData = this.deepClone(this.formItem);
      formData.userid = formData.userids.map((item) => {
        return item.userid;
      });
      this.$refs.formItem.validate((valid) => {
          if (valid) {
            workGroupTemplateSave(formData).then((res) => {
              this.$Message.success(res.msg);
              this.$router.push("/admin/work/client/group");
            })
            .catch((err) => {
              this.$Message.error(err.msg);
            });
          }
        })
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
    getClientCount(){
      workClientCount({
        is_all: this.formItem.client_type == 1 ? 0 : 1,
        label:this.formItem.where_label,
        notLabel:this.formItem.where_not_label,
        time: this.formItem.where_time,
        userid: this.formItem.userids.map((item) => {
          return item.userid;
        })
      }).then(res=>{
        this.clientCount = res.data.sum_count;
      })
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
.client_count{
  color:#F5222D;
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
</style>