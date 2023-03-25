<template>
  <div>
    <!-- <div class="i-layout-page-header">
      <PageHeader
        class="product_tabs"
        :title="$route.meta.title"
        hidden-breadcrumb
      ></PageHeader>
    </div> -->
    <Card :bordered="false" dis-hover class="ivu-mt">
      <div slot="title" class="header_title">{{$route.meta.title}}</div>
      <Form
        ref="formValidate"
        :model="formValidate"
        :label-width="60"
        label-position="right"
        :label-colon="true"
      >
        <FormItem label="头像" prop="head_pic">
          <div class="picBox" @click="modalPicTap('单选')">
            <div class="pictrue" v-if="formValidate.head_pic">
              <img v-lazy="formValidate.head_pic" /> 
            </div>
            <div class="upLoad acea-row row-center-wrapper" v-else>
              <Icon type="md-add" size="26" class="upload_icon" />
            </div>
          </div>
          <span class="text_btn" v-if="formValidate.head_pic" @click="modalPicTap('单选')">更换头像</span>
        </FormItem>
        <FormItem label="账号">
          <Input
            type="text"
            v-model="account"
            :disabled="true"
            class="input"
          ></Input>
        </FormItem>
        <FormItem label="姓名">
          <Input
            type="text"
            :disabled="true"
            v-model="formValidate.real_name"
            class="input"
          ></Input>  
          <span class="text_btn" @click="showModal('updateName')">修改姓名</span>
        </FormItem>
        <FormItem label="手机号">
          <Input type="text" v-model="formValidate.phone" :disabled="true" class="input"></Input>
          <span class="text_btn" @click="showModal('updatePhone')">更换手机号</span>
        </FormItem>
        <FormItem label="密码">
          <Input
            type="password"
            v-model="formValidate.pwd"
            :disabled="true"
            class="input"
          ></Input>
          <span class="text_btn" @click="showModal('updatePassword')">更换密码</span>
        </FormItem>
      </Form>
    </Card>
    <Modal
      v-model="updatePhone"
      title="修改手机号"
      closable
      :mask-closable="false"
      :z-index="1"
    >
      <Form
        ref="formPhone"
        :model="formPhone"
        :rules="rulesPhone"
        :label-width="80"
        label-position="right"
        :label-colon="true"
      >
        <FormItem label="新手机号" prop="phone">
          <Input v-model="formPhone.phone"></Input>
        </FormItem>
        <FormItem label="验证码" prop="code">
          <Input
            v-model="formPhone.code"
            style="width: 294px"
          ></Input>
          <Button
            :disabled="!this.canClick"
            @click="cutDown"
            type="primary"
            style="margin-left: 20px;width:92px;"
            >{{ cutNUm }}</Button
          >
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="text" @click="handleCancle('updatePhone')">取消</Button>
        <Button type="primary" @click="phoneUpdate">确定</Button>
      </div>
    </Modal>
    <Modal
      v-model="updatePassword"
      title="修改密码"
      closable
      :mask-closable="false"
      :z-index="1"
    >
      <Form
        ref="formPwd"
        :model="formPwd"
        :rules="rulesPwd"
        :label-width="80"
        label-position="right"
        :label-colon="true"
      >
        <FormItem label="旧密码" prop="pwd">
          <Input type="text" v-model="formPwd.pwd"></Input>
        </FormItem>
        <FormItem label="新密码" prop="new_pwd">
          <Input type="text" v-model="formPwd.new_pwd"></Input>
        </FormItem>
        <FormItem label="确认密码" prop="conf_pwd">
          <Input type="text" v-model="formPwd.conf_pwd"></Input>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="text" @click="handleCancle('updatePassword')">取消</Button>
        <Button type="primary" @click="pwdUpdate">确定</Button>
      </div>
    </Modal>
    <Modal
      v-model="updateName"
      title="修改姓名"
      closable
      :mask-closable="false"
      :z-index="1"
    >
      <Form
        ref="formName"
        :model="formName"
        :rules="rulesName"
        :label-width="80"
        label-position="right"
        :label-colon="true"
      >
        <FormItem label="姓名" prop="real_name">
          <Input type="text" v-model="formName.real_name"></Input>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="text" @click="handleCancle('updateName')">取消</Button>
        <Button type="primary" @click="nameUpdate">确定</Button>
      </div>
    </Modal>
    <Modal
      v-model="modalPic"
      width="960px"
      scrollable
      footer-hide
      closable
      title="上传用户头像"
      :mask-closable="false"
      :z-index="1"
    >
      <uploadPictures
        :isChoice="isChoice"
        @getPic="getPic"
        :gridBtn="gridBtn"
        :gridPic="gridPic"
        v-if="modalPic"
      ></uploadPictures>
    </Modal>
    <vcode
      :show="isShow"
      @success="onSuccess()"
      @close="onClose()"
      successText="验证通过,短信已发送"
    />
  </div>
</template>

<script>
import Vcode from "vue-puzzle-vcode";
import { updtaeAdmin, setInfo } from "@/api/user";
import uploadPictures from "@/components/uploadPictures";
import { mapState } from "vuex";
import Setting from "@/setting";
import axios from "axios";
import util from "@/libs/util";
export default {
  name: "setting_user",
  components: { uploadPictures,Vcode },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    ...mapState("admin/userLevel", ["categoryId"]),
    labelWidth() {
      return this.isMobile ? undefined : 75;
    },
    labelPosition() {
      return this.isMobile ? "top" : "left";
    },
  },
  data() {
    return {
      modalPic: false,
      isChoice: "单选",
      account: "",
      formValidate: {
        head_pic: "",
        real_name: "",
        pwd: "",
        new_pwd: "",
        conf_pwd: "",
        phone:""
      },
      formPhone: {
        phone: "",
        code: "",
      },
      rulesPhone: {
        phone: [{ required: true, message: "手机号不能为空", trigger: "blur" }],
        code: [{ required: true, message: "验证码不能为空", trigger: "blur" }],
      },
      formPwd:{
          pwd:"",
          new_pwd:"",
          conf_pwd:""
      },
      rulesPwd:{
          pwd: [{ required: true, message: "原密码不能为空", trigger: "blur" }],
          new_pwd: [{ required: true, message: "新密码不能为空", trigger: "blur" }],
          conf_pwd: [{ required: true, message: "请确认密码", trigger: "blur" }],
      },
      formName:{
          real_name:""
      },
      rulesName:{
          real_name: [{ required: true, message: "姓名不能为空", trigger: "blur" }],
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
      updatePhone: false,
      updatePassword:false,
      updateName:false,
      cutNUm: "获取验证码",
      canClick: true,
      isShow: false, // 验证码模态框是否出现
    };
  },
  mounted() {
    // const db = await this.$store.dispatch('admin/db/database', {
    //     user: true
    // });
    // this.account = db.get('user_info').value().account;
    this.getInfo();
  },
  methods: {
    // 选择图片
    modalPicTap() {
      this.modalPic = true;
    },
    // 选中图片
    getPic(pc) {
      this.formValidate.head_pic = pc.att_dir;
      updtaeAdmin({head_pic:pc.att_dir})
        .then((res) => {
            this.$Message.success(res.msg);
            this.modalPic = false;
        }).catch((res) => {
            this.$Message.error(res.msg);
        });
    },
    getInfo() {
      setInfo()
        .then((res) => {
          let data = res.data;
          this.account = data.account;
          this.formValidate.head_pic = data.head_pic;
          this.formValidate.real_name = data.real_name;
          this.formValidate.phone = data.phone;
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          updtaeAdmin(this.formValidate)
            .then((res) => {
              this.$Message.success(res.msg);
            })
            .catch((res) => {
              this.$Message.error(res.msg);
            });
        } else {
          if (this.formValidate.new_pwd !== this.formValidate.conf_pwd) {
            this.$Message.error("您输入的新密码与旧密码不一致");
          }
        }
      });
    },
    showModal(name) {
      this[name] = true;
    },
    phoneUpdate() {
      this.$refs.formPhone.validate((valid) => {
        if (valid) {
          updtaeAdmin(this.formPhone)
            .then((res) => {
              this.$Message.success(res.msg);
              this.$refs.formPhone.resetFields()
              this.updatePhone = false;
              this.getInfo();
          }).catch((res) => {
              this.$Message.error(res.msg);
          });
        }
      });
    },
    pwdUpdate(){
        if(this.formPwd.new_pwd !== this.formPwd.conf_pwd ) return this.$Message.warning("新密码与确认密码不一致");
        this.$refs.formPwd.validate((valid) => {
        if (valid) {
          updtaeAdmin(this.formPwd)
            .then((res) => {
              this.$Message.success(res.msg);
              this.$refs.formPwd.resetFields()
              this.updatePassword = false;
          }).catch((res) => {
              this.$Message.error(res.msg);
          });
        }
      });
    },
    nameUpdate(){
      this.$refs.formName.validate((valid) => {
        if (valid) {
          updtaeAdmin(this.formName)
            .then((res) => {
              this.$Message.success(res.msg);
              this.$refs.formName.resetFields()
              this.updateName = false;
              this.getInfo();
          }).catch((res) => {
              this.$Message.error(res.msg);
          });
        }
      });
    },
    handleCancle(name) {
        this[name] = false;
    },
    // 短信验证码
    cutDown() {
      if (this.formPhone.phone) {
        if(!this.formPhone.phone) return this.$Message.warning("请输入手机号码");
        if(!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(this.formPhone.phone)) return this.$Message.warning("请输入正确的手机号码");
        this.isShow = true;
      } else {
        this.$Message.warning("请填写手机号!");
      }
    },
    onSuccess() {
      this.isShow = false; // 通过验证后，需要手动隐藏模态框
      if (!this.canClick) return;
      this.canClick = false;
      this.cutNUm = 60;
      let verification = Setting.apiBaseURL.replace(/adminapi/, "api");
      axios.get(verification + "/verify_code").then((res) => {
        let codeData = {
          type: "login",
          phone: this.formPhone.phone,
          key: res.data.data.key,
          code: "",
        };
        fetch(verification + "/register/verify", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(codeData),
        })
          .then((response) => {
            let time = setInterval(() => {
              this.cutNUm--;
              if (this.cutNUm === 0) {
                this.cutNUm = "获取验证码";
                this.canClick = true;
                clearInterval(time);
              }
            }, 1000);
          })
          .catch((err) => {
            this.$Message.error(err.msg);
          });
      });
    },
    // 用户点击遮罩层，应该关闭模态框
    onClose() {
      this.isShow = false;
    },
  },
};
</script>

<style scoped lang="stylus">
.input {
  width: 460px;
}

.text_btn {
  font-size: 13px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #1890FF;
  padding-left: 14px;
  cursor: pointer;
}

.picBox {
  display: inline-block;
  cursor: pointer;

  .upLoad {
    width: 58px;
    height: 58px;
    line-height: 58px;
    border: 1px dashed rgba(0, 0, 0, 0.1);
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

  .upload_icon {
    color: #898989;
  }
}
.header_title{
  color:#17233d;
  font-weight:500;
  font-size:18px;
  padding-left:20px;
}
</style>
