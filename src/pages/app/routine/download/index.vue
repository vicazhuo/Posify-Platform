<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <div class="flex-wrapper">
        <!-- :src="iframeUrl" -->
        <div>
          <iframe
            class="iframe-box"
            :src="iframeUrl"
            frameborder="0"
            ref="iframe"
          ></iframe>
          <div class="mask"></div>
        </div>

        <div class="right">
          <div class="content">
            <div class="content-box title">
              <div class="line"></div>
              <div class="right title">基础配置</div>
            </div>
            <div class="content-box">
              <div class="left">appid：</div>
              <div class="right">
                <Input v-model="formData.routine_appId" placeholder="请输入appid" />
              </div>
            </div>
            <div class="content-box">
              <div class="left">AppSecret：</div>
              <div class="right">
                <Input v-model="formData.routine_appsecret" placeholder="请输入AppSecret" />
              </div>
            </div>
            <div class="content-box">
              <div class="left">小程序名称：</div>
              <div class="right">
                <Input v-model="formData.routine_name" placeholder="请输入小程序名称" />
              </div>
            </div>
            <div class="content-box">
              <div class="left">小程序客服类型：</div>
              <div class="right">
                <RadioGroup v-model="formData.routine_contact_type">
                  <Radio :label="1">
                    <span>小程序客服</span>
                  </Radio>
                  <Radio :label="0">
                    <span>内置客服系统</span>
                  </Radio>
                </RadioGroup>
              </div>
            </div>
          </div>
          <div class="content" style="margin-bottom: 60px;">
            <div class="content-box title">
              <div class="line"></div>
              <div class="right title">相关下载</div>
            </div>
            <Alert v-if="!pageData.appId && !pageData.code" closable>
              <template slot="desc">
                您尚未配置小程序信息，请<router-link
                  :to="{ path: '/admin/setting/system_config?from=download' }"
                  >立即设置</router-link
                ></template
              >
            </Alert>
           <!-- <div class="content-box">
              <div class="left">小程序标题：</div>
              <div class="right">{{ pageData.routine_name || "未命名" }}</div>
            </div> -->
            <div class="content-box">
              <div class="left">小程序码：</div>
              <div class="right">
                <Button type="primary" @click="downLoadCode(pageData.code)"
                  >下载小程序码</Button
                >
              </div>
            </div>
            <div class="content-box">
              <div class="left">小程序源码包：</div>
              <div class="right">
                <!-- <span>是否已开通小程序直播</span> -->
                <RadioGroup size="large" v-model="is_live">
                  <Radio :label="0">未开通直播</Radio>
                  <Radio :label="1">已开通直播</Radio>
                </RadioGroup>
              </div>
            </div>
            <div class="content-box last">
              <div class="left"></div>
              <div class="right">
                <div class="tip">
                  请谨慎选择是否有开通小程序直播功能，否则将影响小程序的发布
                  可前往
                  <a :href="pageData.help" target="_blank">帮助文档</a>
                  查看如何开通直播功能
                </div>

                <Button class="mt10" type="primary" @click="downLoad()"
                  >源码包下载</Button
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="submit-card">
      <Button type="primary" @click="submitForm">提交</Button>
    </Card>
  </div>
</template>

<script>
import { routineDownload, routineInfo } from "@/api/app";
import { saveBasicsApi, getNewFormBuildRuleApi } from "@/api/setting";
import { mapState } from "vuex";
export default {
  name: "routineTemplate",
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      iframeUrl: `${location.origin}/pages/index/index`,
      is_live: 1,
      pageData: {
        code: "",
        routine_name: "",
        help: "",
        appId: "1",
      },
      formData: {
        routine_appId: '',
        routine_appsecret: '',
        routine_name: '',
        routine_contact_type: 1
      }
    };
  },
  created() {
    routineInfo().then((res) => {
      console.log(res);
      this.pageData = res.data;
    });
    getNewFormBuildRuleApi('routine').then(res => {
        let data = res.data;
        this.formData.routine_appId = data.routine_appId.value;
        this.formData.routine_appsecret = data.routine_appsecret.value;
        this.formData.routine_name = data.routine_name.value;
        this.formData.routine_contact_type = data.routine_contact_type.value;
    })
  },
  watch: {
    $route(to, from) {},
  },
  computed: {
    ...mapState("media", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 80;
    },
    labelPosition() {
      return this.isMobile ? "top" : "left";
    },
  },
  methods: {
    downLoad() {
      routineDownload({
        is_live: this.is_live,
      })
        .then((res) => {
          window.open(res.data.url);
        })
        .catch((err) => {
          this.$Message.warning(err.msg);
        });
    },
    downLoadCode(url) {
      if (!url) return this.$Message.warning("暂无小程序码");
      var image = new Image();
      image.src = url;
      // 解决跨域 Canvas 污染问题
      image.setAttribute("crossOrigin", "anonymous");
      image.onload = function () {
        var canvas = document.createElement("canvas");
        canvas.width = image.width;
        canvas.height = image.height;
        var context = canvas.getContext("2d");
        context.drawImage(image, 0, 0, image.width, image.height);
        var url = canvas.toDataURL(); //得到图片的base64编码数据
        var a = document.createElement("a"); // 生成一个a元素
        var event = new MouseEvent("click"); // 创建一个单击事件
        a.download = name || "photo"; // 设置图片名称
        a.href = url; // 将生成的URL设置为a.href属性
        a.dispatchEvent(event); // 触发a的单击事件
      };
    },
    submitForm () {
      saveBasicsApi(this.formData).then(res => {
        this.$Message.success(res.msg);
      }).catch(res => {
        this.$Message.error(res.msg);
      });
    }
  },
};
</script>

<style scoped lang="stylus">
.template_sp_box {
  padding: 5px 0;
  box-sizing: border-box;
}

.template_sp {
  display: block;
  padding: 2px 0;
  box-sizing: border-box;
}

.flex-wrapper {
  display: flex;
  border-radius: 10px;
}

.iframe-box {
  width: 294px;
  height: 523px;
  border-radius: 10px;
  margin-top: 14px;
  margin-left: 24px;
}

.ivu-mt {
  // height: 600px;
}

.content {
  padding: 0 0 0 60px;
  margin-top: 60px;

  &:first-child {
    margin-top: 40px;
  }

  .ivu-radio-wrapper {
    margin-right: 30px;
  }
}

.content > .title {
  padding-bottom: 30px;
  margin-bottom: 0;
  font-size: 15px;
  line-height: 15px;
  color: #000000;
}

.content-box {
  display: flex;
  align-items: center;
  margin-bottom: 26px;
  color: #333;

  .tip {
    width: 348px;
    margin-top: -12px;
    font-size: 12px;
    line-height: 17px;
    color: #999999;
  }

  &.last {
    .ivu-btn-primary {
      margin-top: 14px;
    }
  }
}

.content-box:last-child {
  margin-bottom: 0;
}

.content-box.last {
  margin-top: 0;
  color: #999999;
}

.line {
  width: 3px;
  height: 16px;
  background-color: #1890FF;
  margin-right: 11px;
}

.content-box .title {
  font-size: 16px;
  font-weight: bold;
}

.content-box > span {
  color: #F5222D;
  font-size: 20px;
}

.content-box .left {
  width: 130px;
  text-align: right;
}

.content-box .right {
  width: 460px;
}

.rad {
  margin-left: 20px;
}

.mask {
  position: absolute;
  left: 0;
  top: 0;
  width: 312px;
  height: 550px;
  background-color: rgba(0, 0, 0, 0);
}

.submit-card {
  margin-top: 14px;
  text-align: center;

  .ivu-btn {
    padding: 0 20px;
  }
}
</style>