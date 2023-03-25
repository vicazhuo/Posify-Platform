<template>
  <div class="message">
    
    <div class="table-box">
      <Card :bordered="false" :padding="20" dis-hover>
         <div class="new_tab">
           <Tabs v-model="currentTab"  @on-click="changeTab">
              <TabPane
                :label="item.label"
                :name="item.value.toString()"
                v-for="(item, index) in headerList"
                :key="index"
              />
          </Tabs>
         </div>
        <div  class="table-box explain">
          <Card dis-hover>
            <h3>使用说明</h3>
            <template v-if="currentTab == 3">
              <p>阿里云oss开通方法：<a target="_blank"  href="https://doc.crmeb.com/pro/crmebprov2/1213">点击查看</a></p>
            </template>
            <template v-if="currentTab == 4">
              <p>腾讯云oss开通方法：<a target="_blank"  href="https://doc.crmeb.com/pro/crmebprov2/1214">点击查看</a></p>
            </template>
            <template v-if="currentTab == 2">
              <p>七牛云开通方法：<a target="_blank"  href="https://doc.crmeb.com/pro/crmebprov2/1215">点击查看</a></p>
            </template>
            <!-- <template v-if="currentTab == 1">
              <p>本地存储不支持视频上传，只支持图片上传；关闭本地存储需要打开其他云存储状态，否则将关闭上传功能</p>
            </template> -->
            <template v-if="currentTab == 1" >
              <p>上传图片时会生成缩略图</p>
              <p>未设置按照系统默认生成，系统默认：大图800 * 800px，中图300 * 300px，小图150 * 150px</p>
              <p>水印只在上传图片时生成，原图，大中小缩略图上都按照比例存在。</p>
              <p>若上传图片时未开启水印，则该图在开启水印之后依旧无水印效果。</p>
            </template>
            <template v-else-if="currentTab == 5" >
              <p>上传图片时会生成缩略图</p>
              <p>未设置按照系统默认生成，系统默认：大图800 * 800px，中图300 * 300px，小图150 * 150px</p>
              <p>水印只在上传图片时生成，原图，大中小缩略图上都按照比例存在。</p>
              <p>若上传图片时未开启水印，则该图在开启水印之后依旧无水印效果。</p>
            </template>
            <template v-else>
              <p>第一步： 添加【存储空间】（空间名称不能重复）</p>
              <p>第二步： 开启【使用状态】</p>
              <template v-if="currentTab == 2">
                <p>第三步（必选）： 选择云存储空间列表上的修改【空间域名操作】</p>
                <p>
                  第四步（必选）：
                  选择云存储空间列表上的修改【CNAME配置】，打开后复制记录值到对应的平台解析
                </p>
              </template>
              <template v-else>
                <p>第三步（可选）： 选择云存储空间列表上的修改【空间域名操作】</p>
                <p>
                  第四步（可选）：
                  选择云存储空间列表上的修改【CNAME配置】，打开后复制记录值到对应的平台解析
                </p>
              </template>
            </template>
          </Card>
        </div>
        <div class="table-box" v-if="currentTab == 1">
          <Card :bordered="false" :padding="0" dis-hover>
            <Form ref="formValidate" :model="formData" :rules="rules" :label-width="123">
                <FormItem label="存储类型：" :label-width="90">
                    <RadioGroup v-model="formData.upload_type">
                        <Radio :label="1">本地存储</Radio>
                        <Radio :label="3">阿里云存储</Radio>
                        <Radio :label="4">腾讯云存储</Radio>
                        <Radio :label="2">七牛云存储</Radio>
                    </RadioGroup>
                </FormItem>
                <div>缩略图设置：</div>
                <div class="thumb-list">
                    <div class="item">
                        <div class="image">
                            <img src="@/assets/images/thumb1.png">
                            <div>缩略大图</div>
                        </div>
                        <div>
                            <FormItem label="宽：" :label-width="63" prop="thumb_big_width">
                                <Input v-model="formData.thumb_big_width" placeholder="请输入宽">
                                    <span slot="append">px</span>
                                </Input>
                            </FormItem>
                            <FormItem label="高：" :label-width="63" prop="thumb_big_height">
                                <Input v-model="formData.thumb_big_height" placeholder="请输入高">
                                    <span slot="append">px</span>
                                </Input>
                            </FormItem>
                        </div>
                    </div>
                    <div class="item">
                        <div class="image">
                            <img src="@/assets/images/thumb2.png">
                            <div>缩略中图</div>
                        </div>
                        <div>
                            <FormItem label="宽：" :label-width="63" prop="thumb_mid_width">
                                <Input v-model="formData.thumb_mid_width" placeholder="请输入宽">
                                    <span slot="append">px</span>
                                </Input>
                            </FormItem>
                            <FormItem label="高：" :label-width="63" prop="thumb_mid_height">
                                <Input v-model="formData.thumb_mid_height" placeholder="请输入高">
                                    <span slot="append">px</span>
                                </Input>
                            </FormItem>
                        </div>
                    </div>
                    <div class="item">
                        <div class="image">
                            <img src="@/assets/images/thumb2.png">
                            <div>缩略小图</div>
                        </div>
                        <div>
                            <FormItem label="宽：" :label-width="63" prop="thumb_small_width">
                                <Input v-model="formData.thumb_small_width" placeholder="请输入宽">
                                    <span slot="append">px</span>
                                </Input>
                            </FormItem>
                            <FormItem label="高：" :label-width="63" prop="thumb_small_height">
                                <Input v-model="formData.thumb_small_height" placeholder="请输入高">
                                    <span slot="append">px</span>
                                </Input>
                            </FormItem>
                        </div>
                    </div>
                </div>
                <FormItem label="是否开启水印：">
                    <Switch v-model="formData.image_watermark_status" :true-value="1" :false-value="0" size="large">
                        <span slot="open">开启</span>
                        <span slot="close">关闭</span>
                    </Switch>
                </FormItem>
                <FormItem v-show="formData.image_watermark_status" label="水印类型：">
                    <RadioGroup v-model="formData.watermark_type">
                        <Radio :label="1">图片</Radio>
                        <Radio :label="2">文字</Radio>
                    </RadioGroup>
                </FormItem>
                <div v-show="formData.image_watermark_status" class="form-item-list">
                    <FormItem v-if="formData.watermark_type === 1" key="watermark_image" label="水印图片：">
                        <div v-if="formData.watermark_image" class="upload-list">
                            <img v-lazy="formData.watermark_image">
                            <div class="upload-list-cover">
                                <Icon type="ios-eye-outline" @click.native="handleView"></Icon>
                                <Icon type="ios-trash-outline" @click.native="handleRemove"></Icon>
                            </div>
                        </div>
                        <div v-else class="upload" @click="modalPicTap('dan')">
                            <Icon type="ios-add" size="20" />
                        </div>
                    </FormItem>
                    <FormItem v-if="formData.watermark_type === 2" key="watermark_text" label="水印文字：">
                        <Input v-model="formData.watermark_text" placeholder="请输入水印文字"></Input>
                    </FormItem>
                    <FormItem v-if="formData.watermark_type === 2" key="watermark_text_size" label="水印文字大小：" prop="watermark_text_size" :rules="{
                        validator (rule, value, callback) {
                            if (!value || /(^[1-9]\d*$)/.test(value)) {
                                callback();
                            } else {
                                callback(false);
                            }
                        },
                        message: '必须为正整数',
                        trigger: 'blur'
                    }">
                        <Input v-model="formData.watermark_text_size" placeholder="请输入水印文字大小">
                            <span slot="append">px</span>
                        </Input>
                    </FormItem>
                    <FormItem v-if="formData.watermark_type === 2" key="watermark_text_color" label="水印字体颜色：">
                        <ColorPicker v-model="formData.watermark_text_color" />
                    </FormItem>
                    <FormItem label="水印位置：">
                        <RadioGroup v-model="formData.watermark_position" type="button">
                            <Radio v-for="item in 9" :key="item" :label="item"></Radio>
                        </RadioGroup>
                        <span class="tip">{{ positionText }}</span>
                    </FormItem>
                    <FormItem v-if="formData.watermark_type === 1" key="watermark_opacity" label="水印透明度：" prop="watermark_opacity" :rules="{
                        validator: (rule, value, callback) => {
                            if (!value || /^([0-9]{1,2}|100)$/.test(value)) {
                                callback();
                            } else {
                                callback(false);
                            }
                        },
                        message: '必须为0-100的整数',
                        trigger: 'blur'
                    }">
                        <Input v-model="formData.watermark_opacity" placeholder="请输入水印透明度"></Input>
                    </FormItem>
                    <FormItem v-if="formData.watermark_type === 1" key="watermark_rotate" label="水印倾斜度：" prop="watermark_rotate"
                        :rules="{
                            validator (rule, value, callback) {
                                if (!value || /^([0-9]{1,2}|360)$/.test(value)) {
                                    callback();
                                } else {
                                    callback(false);
                                }
                            },
                            message: '必须为0-360的整数',
                            trigger: 'blur'
                        }"
                    >
                        <Input v-model="formData.watermark_rotate" placeholder="请输入水印倾斜度"></Input>
                    </FormItem>
                    <FormItem v-if="formData.watermark_type === 2" key="watermark_text_angle" label="水印字体旋转角度：">
                        <Input v-model="formData.watermark_text_angle" placeholder="请输入水印字体旋转角度"></Input>
                    </FormItem>
                    <FormItem label="水印横坐标偏移量：" prop="watermark_x">
                        <Input v-model="formData.watermark_x" placeholder="请输入横坐标偏移量">
                            <span slot="append">px</span>
                        </Input>
                    </FormItem>
                    <FormItem label="水印纵坐标偏移量：" prop="watermark_y">
                        <Input v-model="formData.watermark_y" placeholder="请输入纵坐标偏移量">
                            <span slot="append">px</span>
                        </Input>
                    </FormItem>
                </div>
                <FormItem>
                    <Button type="primary" @click="handleSubmit('formValidate')">保存</Button>
                </FormItem>
            </Form>
          </Card>
        </div>
        <div class="table-box" v-else>
          <Card :bordered="false" dis-hover class="ivu-mt">
            <Row type="flex" class="mb20">
              <Col span="24">
                <Button type="primary" @click="addStorageBtn">添加存储空间</Button>
                <Button type="success" @click="synchro" style="margin-left: 20px"
                  >同步存储空间</Button
                >
                <Button @click="addConfigBtn" style="float: right"
                  >修改配置信息</Button
                >
              </Col>
            </Row>
            <Table
              :columns="columns"
              :data="levelLists"
              ref="table"
              class="mt25"
              :loading="loading"
              highlight-row
              no-userFrom-text="暂无数据"
              no-filtered-userFrom-text="暂无筛选结果"
            >
              <template slot-scope="{ row, index }" slot="status">
                <!-- {{row}}{{index}} -->
                <i-switch
                  v-model="row.status"
                  :value="row.status"
                  :true-value="1"
                  :false-value="0"
                  @on-change="changeSwitch(row, index)"
                  size="large"
                >
                  <span slot="open">开启</span>
                  <span slot="close">关闭</span>
                </i-switch>
              </template>
              <template slot-scope="{ row, index }" slot="action">
                <template v-if="row.domain && row.domain != row.cname">
                  <span class="btn" @click="config(row)">CNAME配置</span>
                  <Divider type="vertical" />
                </template>
                <span class="btn" @click="edit(row)">修改空间域名</span>
                <Divider type="vertical" />
                <span class="btn" @click="del(row, '删除该数据', index)">删除</span>
              </template>
            </Table>
            <div class="acea-row row-right page">
              <Page
                :total="total"
                :current="list.page"
                show-elevator
                show-total
                @on-change="pageChange"
                :page-size="list.limit"
              />
            </div>
          </Card>
        </div>
      </Card>
    </div>
    <Modal v-model="configuModal" title="CNAME配置">
      <div>
        <div class="confignv">
          <span class="configtit">主机记录：</span>{{ configData.domain }}
        </div>
        <div class="confignv">
          <span class="configtit">记录类型：</span>CNAME
        </div>
        <div class="confignv">
          <span class="configtit">记录值：</span>{{ configData.cname }}
          <span class="copy copy-data" :data-clipboard-text="configData.cname"
            >复制</span
          >
        </div>
      </div>
      <div slot="footer"></div>
    </Modal>
    <Modal
      v-model="modalPic"
      width="960px"
      scrollable
      footer-hide
      closable
      title="上传商品图"
      :mask-closable="false"
      :z-index="888"
    >
      <uploadPictures
        :isChoice="isChoice"
        @getPic="getPic"
        :gridBtn="gridBtn"
        :gridPic="gridPic"
        v-if="modalPic"
      ></uploadPictures>
    </Modal>
    <Modal v-model="visible">
        <img :src="formData.watermark_image" v-if="visible" style="width: 100%">
    </Modal>
  </div>
</template>

<script>
import ClipboardJS from "clipboard";
import uploadPictures from "@/components/uploadPictures";

import {
  addConfigApi,
  addStorageApi,
  storageListApi,
  storageSynchApi,
  storageStatusApi,
  editStorageApi,
  getConfigImageApi,
  saveBasicsApi
} from "@/api/setting";
export default {
  name: 'setting_storage',
  components: { uploadPictures },
  data() {
      let isInteger = (rule, value, callback) => {
          if (!value || /(^[1-9]\d*$)/.test(value)) {
              callback();
          } else {
              callback(new Error('必须为正整数'));
          }
      };
    return {
      modalPic: false,
      isChoice: "单选",
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
      configuModal: false,
      configData: "",
      headerList: [
        { label: "基础配置", value: "1" },
        { label: "阿里云储存", value: "3" },
        { label: "腾讯云储存", value: "4" },
        { label: "七牛云储存", value: "2" }
      ],
      columns: [
        {
          title: "储存空间名称",
          key: "name",
          align: "center",
          minWidth: 200,
        },
        {
          title: "区域",
          key: "_region",
          align: "center",
          minWidth: 100,
        },
        {
          title: "空间域名",
          key: "domain",
          align: "center",
          minWidth: 200,
        },
        {
          title: "使用状态",
          slot: "status",
          align: "center",
          width: 90,
        },
        {
          title: "创建时间",
          key: "_add_time",
          align: "center",
          minWidth: 150,
        },
        {
          title: "更新时间",
          key: "_update_time",
          align: "center",
          minWidth: 150,
        },
        {
          title: "操作",
          slot: "action",
          width: 210,
          align: "center",
        },
      ],
      total: 0,
      list: {
        page: 1,
        limit: 15,
        type: "1",
      },
      levelLists: [],
      currentTab: "1",
      loading: false,
      addData: {
        input: "",
        select: "",
        jurisdiction: "1",
        type: "1",
      },
      confData: {
        AccessKeyId: "",
        AccessKeySecret: "",
      },
      formData: {
        upload_type: 1,
        image_watermark_status: 0,
        thumb_big_width: '',
        thumb_big_height: '',
        thumb_mid_width: '',
        thumb_mid_height: '',
        thumb_small_width: '',
        thumb_small_height: '',
        watermark_type: 1,
        watermark_text: '',
        watermark_text_angle: '',
        watermark_text_color: '',
        watermark_text_size: '',
        watermark_position: 1,
        watermark_image: '',
        watermark_opacity: '',
        watermark_rotate: '',
        watermark_x: '',
        watermark_y: ''
      },
      rules: {
          thumb_big_width: [
              {
                  validator: isInteger,
                  trigger: 'blur'
              }
          ],
          thumb_big_height: [
              {
                  validator: isInteger,
                  trigger: 'blur'
              }
          ],
          thumb_mid_width: [
              {
                  validator: isInteger,
                  trigger: 'blur'
              }
          ],
          thumb_mid_height: [
              {
                  validator: isInteger,
                  trigger: 'blur'
              }
          ],
          thumb_small_width: [
              {
                  validator: isInteger,
                  trigger: 'blur'
              }
          ],
          thumb_small_height: [
              {
                  validator: isInteger,
                  trigger: 'blur'
              }
          ],
          watermark_x: [
              {
                  validator: isInteger,
                  trigger: 'blur'
              }
          ],
          watermark_y: [
              {
                  validator: isInteger,
                  trigger: 'blur'
              }
          ]
      },
      visible: false
    };
  },
  computed: {
    positionText () {
        return ['左上', '中上', '右上', '左中', '中', '右中', '左下', '中下', '右下'][this.formData.watermark_position - 1];
    }
  },
  created() {
    this.getConfigImage();
  },
  mounted: function () {
    this.$nextTick(function () {
      const clipboard = new ClipboardJS(".copy-data");
      clipboard.on("success", () => {
        this.$Message.success("复制成功");
      });
    });
  },
  methods: {
    //   获取缩略图配置
      getConfigImage() {
          getConfigImageApi().then(res => {
                let data = res.data;
                this.formData.upload_type = data.upload_type.value;
                this.formData.image_watermark_status = data.image_watermark_status.value;
                this.formData.thumb_big_width = data.thumb_big_width.value;
                this.formData.thumb_big_height = data.thumb_big_height.value;
                this.formData.thumb_mid_width = data.thumb_mid_width.value;
                this.formData.thumb_mid_height = data.thumb_mid_height.value;
                this.formData.thumb_small_width = data.thumb_small_width.value;
                this.formData.thumb_small_height = data.thumb_small_height.value;
                this.formData.watermark_type = data.watermark_type.value;
                this.formData.watermark_text = data.watermark_text.value;
                this.formData.watermark_text_angle = data.watermark_text_angle.value;
                this.formData.watermark_text_color = data.watermark_text_color.value;
                this.formData.watermark_text_size = data.watermark_text_size.value;
                this.formData.watermark_position = data.watermark_position.value;
                this.formData.watermark_image = data.watermark_image.value;
                this.formData.watermark_opacity = data.watermark_opacity.value;
                this.formData.watermark_rotate = data.watermark_rotate.value;
                this.formData.watermark_x = data.watermark_x.value;
                this.formData.watermark_y = data.watermark_y.value;
          });
      },
    //   基础配置保存
    handleSubmit(name) {
        this.$refs[name].validate(valid => {
            if (valid) {
                saveBasicsApi(this.formData).then(res => {
                    this.$Message.success(res.msg);
                }).catch(res => {
                    this.$Message.error(res.msg);
                });
            }
        });
    },
    // 选择图片
    modalPicTap() {
      this.modalPic = true;
    },
    // 选中图片
    getPic(pc) {
      this.formData.watermark_image = pc.att_dir;
      this.modalPic = false;
    },
    // CNAME配置
    config(row) {
      this.configuModal = true;
      this.configData = row;
    },
    //同步储存空间
    synchro() {
      storageSynchApi(this.currentTab)
        .then((res) => {
          this.$Message.success(res.msg);
          this.getlist();
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    // 添加存储空间
    addStorageBtn() {
      this.$modalForm(addStorageApi(this.currentTab)).then(() => {
        this.getlist();
      });
    },
    // 修改配置信息
    addConfigBtn() {
      this.$modalForm(addConfigApi(this.currentTab)).then(() => {
        this.getlist();
      });
    },
    //修改空间域名
    edit(row) {
      this.$modalForm(editStorageApi(row.id)).then(() => {
        this.getlist();
      });
    },
    // 使用状态
    changeSwitch(row, item) {
      return new Promise((resolve) => {
        this.$Modal.confirm({
          title: "切换状态",
          content: "您确认要切换使用状态吗？",
          onOk: () => {
            // resolve();
            storageStatusApi(row.id)
              .then((res) => {
                this.$Message.success(res.msg);
                this.getlist();
              })
              .catch((err) => {
                this.$Message.error(err.msg);
              });
          },
          onCancel: () => {
            this.$Message.info("已取消");
            this.getlist();
          },
        });
      });
    },
    // 云存储列表
    getlist() {
      this.loading = true;
      storageListApi(this.list).then((res) => {
        this.total = res.data.count;
        this.levelLists = res.data.list;
        this.loading = false;
      });
    },
    // 切换选项卡
    changeTab(data) {
      this.currentTab = data;
      this.list.type = data;
      this.list.page = 1;
      if (data == 1) {
          this.getConfigImage();
      } else {
        this.getlist();
      }
    },
    // 删除
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `setting/config/storage/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.getlist();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    pageChange(index) {
      this.list.page = index;
      this.getlist();
    },
    handleView () {
        this.visible = true;
    },
    handleRemove () {
        this.formData.watermark_image = '';
    }
  },
};
</script>
<style scoped lang="stylus">
.ivu-input-group > .ivu-input:last-child,
/deep/.ivu-input-group-append {
  background: none;
  color: #999999;
}
/deep/.ivu-input-group .ivu-input {
  border-right: 0px !important;
}
.content /deep/.ivu-form .ivu-form-item-label {
  width: 133px;
}
.topIput {
  width: 186px;
  background: #ffffff;
  border-right: 0px !important;
}
.new_tab {
  >>>.ivu-tabs-nav .ivu-tabs-tab{
      padding:4px 16px 20px !important;
      font-weight: 500;
  }
}
.abbreviation {
  .top {
    display: flex;
    justify-content: flex-start;
    .topBox {
      display: flex;
      .topRight {
        width: 254px;
        margin-left: 36px;
      }
      .topLeft {
        width: 94px;
        height: 94px;

        text-align: center;
        font-size: 13px;
        font-weight: 400;
        color: #000000;
        .img {
          // width: 84px;
          height: 67px;
          background: #f7fbff;
          border-radius: 4px;
          margin-bottom: 9px;
          .imgs {
            width: 70px;
            height: 51px;
            display: inline-block;
            text-align: center;
            margin-top: 8px;
          }
        }
      }
    }
  }
  .content {
    /deep/.ivu-form-item-label {
      width: 120px;
    }
    .flex {
      display: flex;
      justify-content: flex-start;
      // width: 400px;

      .contentIput {
        width: 400px;
      }
      .conents {
        display: flex;
        .title {
          width: 30px;
          margin-top: 70px;
          margin-left: 6px;
        }
        .positionBox {
          display: flex;
          flex-wrap: wrap;
          width: 94px;
          height: 90px;
          border-radius: 4px;
          border-right: 1px solid #dddddd;
          .box {
            width: 31px;
            height: 31px;
            // border-radius: 4px 0px 0px 0px;
            border: 1px solid #dddddd;
            cursor: pointer;
          }
          .on {
            background: rgba(24, 144, 255, 0.1);
          }
        }
      }
    }
  }
}

/deep/ .ivu-form-item-label {
    padding-right: 6px;
    font-size: 13px !important;
    color: rgba(0, 0, 0, 0.85);
}

/deep/ .ivu-input {
    font-size: 13px !important;
}

/deep/ .ivu-input-group-with-append .ivu-input {
    border-right: 0;
}

/deep/ .ivu-input-group-append {
    padding-right: 14px;
    background-color: transparent;
    font-size: 13px;
    color: #999999;
}

/deep/ .ivu-radio-wrapper {
    margin-right: 30px;
    font-size: 13px !important;
    color: #666666;

    &.ivu-radio-wrapper-checked {
        color: #2D8CF0;
    }
}

.ivu-form {
    > .ivu-form-item {
        &:first-child {
            margin: 8px 0 28px;
        }

        &:last-child {
            margin-bottom: 0;
        }
    }

    .ivu-divider {
        margin-bottom: 16px;
    }
}

.thumb-list {
    display: flex;
    margin-top: 20px;
    margin-bottom: 59px;

    .item {
		flex: 0 0 calc((100% - 120px) / 3);
        display: flex;
        align-items: center;
        margin-left: 60px;

        &:first-child {
            margin-left: 0;
        }
    }

    .image {
        padding: 0 5px;
        font-size: 13px;
        line-height: 18px;
        text-align: center;
        color: #000000;
    }

    img {
        width: 84px;
        height: 67px;
        margin-bottom: 9px;
    }

    .ivu-form-item {
        &:last-child {
            margin-bottom: 0;
        }
    }
}

.form-item-list {
    display: flex;
    flex-wrap: wrap;
	width: calc(100% / 3 * 2);

    .ivu-form-item {
		flex: 0 0 calc((100% - 60px) / 2);
        margin-left: 60px;

        &:nth-child(2n+1) {
            margin-left: 0;
        }
    }

    .ivu-radio-group {
        display: inline-flex;
        flex-wrap: wrap;
        width: 90px;
    }

    .ivu-radio-wrapper {
        width: 30px;
        height: 30px;
        padding: 0;
        margin-right: 0;
        font-size: 0 !important;

        &:first-child {
            border-radius: 4px 0 0 0;
        }

        &:last-child {
            border-radius: 0 0 4px 0;
        }

        &:nth-child(3) {
            border-radius: 0 4px 0 0;
        }

        &:nth-child(7) {
            border-radius: 0 0 0 4px;
        }

        &::before {
            left: 0;
        }

        &::after {
            top: 0;
            left: 0;
        }
    }

    .ivu-radio-wrapper-checked {
        background: rgba(24, 144, 255, 0.1);
    }
}

.tip {
    display: inline-block;
    margin-left: 6px;
    vertical-align: bottom;
    font-size: 12px;
    line-height: 18px;
    color: #999999;
}

.upload, .upload-list {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 60px;
    border: 1px solid #DDDDDD;
    border-radius: 4px;
    background-color: rgba(0, 0, 0, 0.02);
    vertical-align: middle;
    text-align: center;
    line-height: 60px;

    img {
        width: 100%;
        height: 100%;
    }

    .ivu-icon {
        color: #898989;
    }
}

.upload-list-cover {
    display: none;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,.6);
}

.upload-list:hover .upload-list-cover{
    display: block;
}
.upload-list-cover i{
    color: #fff !important;
    font-size: 20px;
    cursor: pointer;
    margin: 0 2px;
}
</style>
<style scoped>
.message /deep/ .ivu-table-header thead tr th {
  padding: 8px 16px;
}
.message /deep/ .ivu-tabs-tab {
  border-radius: 0 !important;
}
.table-box {
  /* padding: 20px; */
}
.table-box.explain .ivu-card {
    border-color: #D6E7FC;
    background-color: #EAF4FE;
}
.ivu-card-bordered.info {
  border-color: #D6E7FC;
  background: #EAF4FE;
}
.is-table {
  display: flex;
  /* justify-content: space-around; */
  justify-content: center;
}
.btn {
  cursor: pointer;
  color: #2d8cf0;
  font-size: 10px;
}
.is-switch-close {
  background-color: #504444;
}
.is-switch {
  background-color: #eb5252;
}
.notice-list {
  background-color: #308cf5;
  margin: 0 15px;
}
.table {
  padding: 0 18px;
}
.confignv {
  margin: 10px 0px;
}
.configtit {
  display: inline-block;
  width: 80px;
  text-align: right;
}
.copy {
  padding: 3px 5px;
  border: 1px solid #cccccc;
  border-radius: 5px;
  color: #333;
  cursor: pointer;
  margin-left: 5px;
}
.copy:hover {
  border-color: #2d8cf0;
  color: #2d8cf0;
}
.picBox {
  display: inline-block;
  cursor: pointer;
}
.picBox .pictrue {
  width: 60px;
  height: 60px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  margin-right: 10px;
}

.picBox .pictrue img {
  width: 100%;
  height: 100%;
}
.picBox .upLoad {
  width: 58px;
  height: 58px;
  line-height: 58px;
  border: 1px dotted rgba(0, 0, 0, 0.1);
  border-radius: 4px;
  background: rgba(0, 0, 0, 0.02);
}


</style>
