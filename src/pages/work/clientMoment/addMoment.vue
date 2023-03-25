<template>
  <div>
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/client/moment' }">
              <div class="font-sm after-line">
                <span class="iconfont iconfanhui"></span>
                <span class="pl10">返回</span>
              </div>
            </router-link>
          </div>
          <span class="mr20 ml16">发布朋友圈</span>
        </div>
      </PageHeader>
    </div>
    <Card :bordered="false" dis-hover class="ivu-mt mb100">
      <Form
        :model="formItem"
        ref="formItem" 
        :label-width="110"
        :label-colon="true"
        :rules="ruleValidate"
      >
        <FormItem label="任务名称" prop="name">
          <Input
            v-model="formItem.name"
            placeholder="请输入任务名称"
            style="width: 460px"
          ></Input>
        </FormItem>
        <FormItem label="成员类型" required>
          <RadioGroup v-model="formItem.type">
            <Radio label="0">全部成员</Radio>
            <Radio label="1">选定成员</Radio>
          </RadioGroup>
          <!-- <Alert>
            提示：管理员可创建客户朋友圈发表任务，成员确认内容后即可发布到朋友圈中，每位客户的朋友圈每个月最多展示
            <span>{{ formItem.type == 0 ? "4条企业" : "3条个人" }}</span>
            发表的内容。电脑上暂不支持发送企微朋友圈。
          </Alert> -->
        </FormItem>
        <FormItem label="发表成员" v-if="formItem.type == 1">
          <Button @click="addUser()">选择成员</Button>
          <div style="margin-top: 10px">
            <Tag
              closable
              @on-close="handleDel"
              :name="item.name"
              size="large"
              v-for="(item, index) in formItem.user_ids"
              :key="index"
              >{{ item.name }}</Tag
            >
          </div>
        </FormItem>
        <FormItem label="可见的客户" required>
          <RadioGroup v-model="formItem.client_type">
            <Radio label="0">全部客户</Radio>
            <Radio label="1">筛选客户</Radio>
          </RadioGroup>
          <div class="desc">预估将发送到
            <span class="client_count">[{{clientCount}}]</span>个客户朋友圈中。
            <span v-show="formItem.client_type == 1">将消息发送给符合条件的客户</span>
          </div>
        </FormItem>
        <FormItem label="标签" v-if="formItem.client_type == 1">
          <TreeSelect
            v-model="formItem.client_tag_list"
            :data="labelList"
            v-width="460"
            multiple
          />
        </FormItem>
        <FormItem label="消息内容1" required>
          <Input
            v-model="formItem.welcome_words.text.content"
            type="textarea"
            :rows="4"
            show-word-limit
            placeholder="请输入消息内容"
            maxlength="1000"
            style="width: 460px"
          />
        </FormItem>
        <FormItem label="消息内容2">
          <RadioGroup v-model="descType">
            <Radio label="0">图片</Radio>
            <Radio label="1">视频</Radio>
            <Radio label="2">图文链接</Radio>
          </RadioGroup>
        </FormItem>
        <FormItem v-if="descType == 0">
          <div class="acea-row">
            <div
              class="pictrue"
              v-for="(item, index) in formItem.welcome_words.attachments"
              :key="index"
            >
              <img v-if="item.msgtype == 'image'" v-lazy="item.image.pic_url" />
              <Button
                shape="circle"
                icon="md-close"
                @click.native="handleRemove(index)"
                class="btndel"
              ></Button>
            </div>
            <div
              v-if="formItem.welcome_words.attachments.length < 9"
              class="upLoad acea-row row-center-wrapper"
              @click="modalPicTap('duo')"
            >
              <Icon type="ios-camera-outline" size="26" />
            </div>
          </div>
          <div class="tips">
            因企业微信限制，图片长宽建议不超过1440x1080，大小不超过10M
          </div>
        </FormItem>
        <FormItem v-if="descType == 1">
          <!-- <div class="upLoad acea-row row-center-wrapper">
            <Icon type="ios-videocam-outline" size="26" />
          </div> -->
          <Upload
          v-if="upload_type"
				  :show-upload-list="false"
				  :action="fileUrl2"
				  :before-upload="videoSaveToUrl"
				  :data="uploadData"
				  :headers="header"
				  :multiple="true"
				  style="display: inline-block">
				    <div class="videbox">
				      +
				    </div>
				</Upload>
        <Input style="width:460px;" v-model="videoObj.video.url" v-else></Input>
			  <div class="tips">建议时长：9～30秒，大小10M以内</div>
        </FormItem>
        <FormItem v-if="descType == 2" label="图文链接">
          <Input
            v-model="linkObj.link.url"
            placeholder="链接地址请以http或https开头"
            style="width: 460px"
            @on-blur="linkBlur()"
          ></Input>
        </FormItem>
        <FormItem v-if="descType == 2" label="链接标题">
          <Input
            v-model="linkObj.link.title"
            placeholder="请输入链接标题"
            style="width: 460px"
            @on-blur="titleBlur()"
          ></Input>
        </FormItem>
        <FormItem label="发送方式" required>
          <RadioGroup v-model="formItem.send_type">
            <Radio label="0">立即发送</Radio>
            <Radio label="1">定时发送</Radio>
          </RadioGroup>
        </FormItem>
        <FormItem label="定时发送时间" prop="send_time" v-if="formItem.send_type == 1">
          <DatePicker
            type="datetime"
            v-model="formItem.send_time"
            @on-change="snedChangeTime"
            placeholder="Select date"
            style="width: 460px"
          ></DatePicker>
        </FormItem>
      </Form>
    </Card>
    <Card :bordered="false" dis-hover class="fixed-card">
      <div class="acea-row row-center-wrapper">
        <Button type="primary" @click="submit()">提交</Button>
      </div>
    </Card>
    <Modal
      v-model="modalPic"
      width="960px"
      scrollable
      footer-hide
      closable
      title="上传商品图"
      :mask-closable="false"
      :z-index="1"
    >
      <uploadPictures
        :isChoice="isChoice"
        @getPicD="getPicD"
        :gridBtn="gridBtn"
        :gridPic="gridPic"
        v-if="modalPic"
      ></uploadPictures>
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
import { workLabel, workMomentSave,workClientCount } from "@/api/work";
import uploadPictures from "@/components/uploadPictures";
import department from "@/components/department/index.vue";
import Setting from "@/setting";
import util from "@/libs/util";
import { uploadByPieces } from "@/utils/upload"; 
export default {
  data() {
    return {
      formItem: {
        name: "",
        type: "0",
        client_tag_list: [],
        client_type: "0",
        user_ids: [],
        welcome_words: {
          text: {
            content: "",
          },
          attachments: [],
        },
        // images:[],
        send_type: "0",
        send_time: "", //定时发送
      },
      descType: "0",
      modalPic: false,
      picTit: "",
      tableIndex: 0,
      isChoice: "",
      labelList: [],
      ruleValidate: {
        name: [
          { required: true, message: '名称不能为空', trigger: 'blur' }
        ],
        send_time: [
          { required: true, message: '发送时间不能为空', trigger: 'change' }
        ],
      },
      gridPic: {
        xl: 6,
        lg: 8,
        md: 12,
        sm: 12,
        xs: 12,
      },
      gridBtn: {
        xl: 4,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 8,
      },
      imageObj: {
        msgtype: "image",
        image: {
          media_id: "",
          pic_url: "",
        },
      },
      linkObj: {
        msgtype: "link",
        link: {
          media_id: "",
          title: "",
          url: "",
        },
      },
      videoObj: {
        msgtype: "video",
        video: {
          media_id: "",
          url: "",
        },
      },
      activeDepartment: {},
      isSite: true,
      onlyDepartment: false,
      openType: "",
      userList: [],
      fileUrl2: Setting.apiBaseURL + "/file/video_upload",
      upload_type: "", //视频上传类型 1 本地上传 2 3 4 OSS上传
      uploadData: {}, // 上传参数
      header: {},
      progress: 0,
      upload_type:true,
      clientCount:0
    };
  },
  components: {
    uploadPictures,
    department,
  },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 96;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  watch:{
    "formItem.client_tag_list"(val,oldVal){
      if(val !== oldVal){
        this.getClientCount();
      }
    },
  },
  mounted() {
    this.setCopyrightShow({ value: false });
    this.getWorkLabel();
    this.getToken();
    this.getClientCount();
  },
  destroyed() {
    this.setCopyrightShow({ value: true });
  },
  methods: {
    ...mapMutations("admin/layout", ["setCopyrightShow"]),
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
    // 点击商品图
    modalPicTap(tit, picTit, index) {
      this.modalPic = true;
      this.isChoice = tit === "dan" ? "单选" : "多选";
      this.picTit = picTit;
      this.tableIndex = index;
    },
    snedChangeTime(val) {
      this.formItem.send_time = val;
    },
    handleRemove(i) {
      this.formItem.welcome_words.attachments.splice(i, 1);
    },
    // 获取多张图信息
    getPicD(pc) {
      let images = [];
      images = pc.map((item) => {
        return item.att_dir;
      });
      images.forEach((item) => {
        this.imageObj.image.pic_url = item;
        this.formItem.welcome_words.attachments.push(this.imageObj);
      });
      this.modalPic = false;
    },
    addUser() {
      this.userList = this.formItem.user_ids;
      this.$refs.department.memberStatus = true;
    },
    changeMastart(arr, type) {
      this.formItem.user_ids = arr.map((item) => {
        return {
          userid: item.userid,
          name: item.name,
        };
      });
    },
    //tag标签删除成员
    handleDel(e, name) {
      let index = this.formItem.user_ids.indexOf(name);
      this.formItem.user_ids.splice(index, 1);
    },
    linkBlur() {
      if (!this.linkObj.link.url) return this.$Message.warning("请输入链接");
    },
    titleBlur() {
      if (!this.linkObj.link.title)
        return this.$Message.warning("请输入链接标题");
    },
    submit() {
      if(!this.formItem.welcome_words.text.content.length) return this.$Message.error("请填写消息内容1")
      if (this.descType == 2) {
        this.formItem.welcome_words.attachments.push(this.linkObj);
      }else if(this.descType == 1){
        this.formItem.welcome_words.attachments.push(this.videoObj);
      }
      const formData = this.deepClone(this.formItem);
      formData.user_ids = formData.user_ids.map((item) => {
        return item.userid;
      });
      this.$refs.formItem.validate((valid) => {
        if (valid) {
          workMomentSave(formData).then((res) => {
            this.$Message.success(res.msg);
            this.$router.push("/admin/work/client/moment");
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
    videoSaveToUrl(file) {
      uploadByPieces({
        randoms: "", // 随机数，这里作为给后端处理分片的标识 根据项目看情况 是否要加
        file: file, // 视频实体
        pieceSize: 3, // 分片大小
        success: (data) => {
          this.upload_type = false;
          this.videoObj.video.url = Setting.apiBaseURL + data.file_path;
          this.$Spin.hide();
        },
        error: (e) => {
          this.$Message.error(e.msg);
          this.$Spin.hide();
        },
        uploading: (chunk, allChunk) => {
          this.$Spin.show();
        },
      });
      return false;
    },
    
    // 上传头部token
    getToken() {
      this.header["Authori-zation"] = "Bearer " + util.cookies.get("token");
    },
    getClientCount(){
      workClientCount({
        is_all: this.formItem.client_type == 1 ? 0 : 1,
        label:this.formItem.client_tag_list,
      }).then(res=>{
        this.clientCount = res.data.sum_count;
      })
    }
  },
};
</script>
<style scoped lang="stylus">
.tips {
  display: inline-bolck;
  font-size: 12px;
  font-weight: 400;
  color: #999999;
}
.desc {
  color: #999;
  font-size: 12px;
  line-height: 17px;
  padding-top: 6px;
}
.client_count{
  color:#F5222D;
}
.pictrue {
  width: 60px;
  height: 60px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  margin-right: 15px;
  margin-bottom: 10px;
  display: inline-block;
  position: relative;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }

  .btndel {
    position: absolute;
    z-index: 1;
    width: 20px !important;
    height: 20px !important;
    left: 46px;
    top: -4px;
  }
}

.upLoad {
  width: 58px;
  height: 58px;
  line-height: 58px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.02);
  cursor: pointer;
}

.tips {
  display: inline-bolck;
  font-size: 12px;
  font-weight: 400;
  color: #999999;
  margin-top: 14px;
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

.mb100 {
  margin-bottom: 100px;
}
.videbox {
  width: 60px;
  height: 60px;
  background: rgba(0, 0, 0, 0.02);
  border-radius: 4px;
  border: 1px dashed #DDDDDD;
  line-height: 60px;
  text-align: center;
  color: #898989;
  font-size: 30px;
  font-weight: 400;
  cursor: pointer;
}
</style>