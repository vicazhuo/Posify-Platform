<template>
  <div>
      <Form
        ref="orderData"
        inline
        :model="orderData"
        :label-width="labelWidth"
        :label-position="labelPosition"
        class="tabform"
        @submit.native.prevent
      >
        <Row>
          <Col v-bind="grid">
            <FormItem label="订单状态：">
              <Select
                  v-model="orderData.status"
                  style="width: 300px"
                  clearable
                  @on-change="selectChange2"
                  placeholder="全部"
              >
                <Option value="" >全部</Option>
                <Option value="0">未支付</Option>
                <Option value="1">未发货</Option>
                <Option value="2">待收货</Option>
                <Option value="3">待评价</Option>
                <Option value="4">交易完成</Option>
                <Option value="-4">已删除</Option>
              </Select>
            </FormItem>
          </Col>
          <Col v-bind="grid">
            <FormItem label="支付方式：">
              <Select v-model="orderData.pay_type" clearable  style="width: 300px" @on-change="userSearchs" placeholder="全部" >
                <Option v-for="item in payList" :value="item.val" :key="item.id">{{
                    item.label
                  }}</Option>
              </Select>
            </FormItem>
          </Col>
          <Col v-bind="grid">
            <FormItem label="创建时间：">
            <DatePicker
                :editable="false"
                :clearable="true"
                @on-change="onchangeTime"
                :value="timeVal"
                format="yyyy/MM/dd HH:mm:ss"
                type="datetimerange"
                placement="bottom-start"
                placeholder="自定义时间"
                style="width: 300px"
                class="mr20"
                :options="options"
            ></DatePicker>
          </FormItem>
          </Col>
        </Row>
        <Row>
          <Col span="24">
            <FormItem label="订单搜索：" prop="real_name" label-for="real_name">
              <Input
                  v-model="orderData.real_name"
                  placeholder="请输入"
                  element-id="name"
                  clearable
                  style="width:300px;"
									maxlength="20"
              >
                <Select
                    v-model="orderData.field_key"
                    slot="prepend"
                    style="width:80px;"
                    default-label="全部"
                >
                  <Option value="all">全部</Option>
                  <Option value="order_id">订单号</Option>
                  <Option value="uid">用户UID</Option>
                  <Option value="real_name">用户姓名</Option>
                  <Option value="user_phone">用户电话</Option>
                  <Option value="title">商品名称</Option>
                  <Option value="total_num">商品件数</Option>
                </Select>
              </Input>
            </FormItem>
            <FormItem>
              <Button type="primary" @click="orderSearch(orderData.real_name)" style="margin-left:-90px;">查询</Button>
            </FormItem>
          </Col>
        </Row>
      </Form>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";
import {
  putWrite,
  storeOrderApi,
  handBatchDelivery,
  otherBatchDelivery,
  exportExpressList,
} from "@/api/order";
import autoSend from "../handle/autoSend";
import queueList from "../handle/queueList";
import Setting from "@/setting";
import util from "@/libs/util";
import timeOptions from "@/utils/timeOptions";
// import XLSX from 'xlsx';
// const make_cols = refstr => Array(XLSX.utils.decode_range(refstr).e.c + 1).fill(0).map((x,i) => ({name:XLSX.utils.encode_col(i), key:i}));
export default {
  name: "table_from",
  components: {
    autoSend,
    queueList,
  },
  props: ["formSelection", "isAll"],
  data() {
    const codeNum = (rule, value, callback) => {
      if (!value) {
        return callback(new Error("请填写核销码"));
      }
      // 模拟异步验证效果
      if (!Number.isInteger(value)) {
        callback(new Error("请填写12位数字"));
      } else {
        // const reg = /[0-9]{12}/;
        const reg = /\b\d{12}\b/;
        if (!reg.test(value)) {
          callback(new Error("请填写12位数字"));
        } else {
          callback();
        }
      }
    };
    return {
      currentTab: "",
      grid: {
        xl: 8,
        lg: 12,
        md: 24,
        sm: 24,
        xs: 24,
      },
      // 搜索条件
      orderData: {
        status: "",
        data: "",
        real_name: "",
        field_key: "all",
        pay_type: "",
      },
      modalTitleSs: "",
      statusType: "",
      time: "",
      value2: [],
      isDelIdList: [],
      writeOffRules: {
        code: [{ validator: codeNum, trigger: "blur", required: true }],
      },
      writeOffFrom: {
        code: "",
        confirm: 0,
      },
      modals2: false,
      timeVal: [],
      options: timeOptions,
      payList: [
        { label: "全部", val: "" },
        { label: "微信支付", val: "1" },
        { label: "支付宝支付", val: "4" },
        { label: "余额支付", val: "2" },
        { label: "线下支付", val: "3" },
      ],
      manualModal: false,
      uploadAction: `${Setting.apiBaseURL}/file/upload/1`,
      uploadHeaders: {},
      file: "",
      autoModal: false,
      isShow: false,
      recordModal: false,
      sendOutValue: "",
      exportListOn: 0,
      fileList: [],
      // modal5: false,
      // data5: [],
      // cols5: []
      // orderStatus: false,
      // orderInfo:''
    };
  },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    ...mapState("admin/order", ["orderChartType", "isDels", "delIdList"]),
    labelWidth() {
      return this.isMobile ? undefined : 96;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
    today() {
      const end = new Date();
      const start = new Date();
      var datetimeStart =
        start.getFullYear() +
        "/" +
        (start.getMonth() + 1) +
        "/" +
        start.getDate();
      var datetimeEnd =
        end.getFullYear() + "/" + (end.getMonth() + 1) + "/" + end.getDate();
      return [datetimeStart, datetimeEnd];
    },
  },
  watch: {
    $route() {
      if (this.$route.fullPath === "/admin/order/list?status=1") {
        this.getPath();
      }
    },
  },
  created() {
    // this.timeVal = this.today;
    // this.orderData.data = this.timeVal.join('-');
    if (this.$route.fullPath === "/admin/order/list?status=1") {
      this.getPath();
    }
    this.$parent.$emit("add");
  },
  methods: {
    ...mapMutations("admin/order", [
      "getOrderStatus",
      "getOrderType",
      "getOrderTime",
      "getOrderNum",
      "getfieldKey",
    ]),
    getPath() {
      this.orderData.status = this.$route.query.status.toString();
      this.getOrderStatus(this.orderData.status);
      this.$emit("getList", 1);
      this.$emit("order-data", this.orderData);
    },
    // 具体日期
    onchangeTime(e) {
      if (e[1].slice(-8) === "00:00:00") {
        e[1] = e[1].slice(0, -8) + "23:59:59";
        this.timeVal = e;
      } else {
        this.timeVal = e;
      }
      this.orderData.data = this.timeVal[0] ? this.timeVal.join("-") : "";
      // this.$store.dispatch("admin/order/getOrderTabs", {
      //   data: this.orderData.data,
      // });
      this.getOrderTime(this.orderData.data);
      this.$emit("getList", 1);
      this.$emit("order-data", this.orderData);
    },
    // 选择时间
    selectChange(tab) {
      this.$store.dispatch("admin/order/getOrderTabs", { data: tab });
      this.orderData.data = tab;
      this.getOrderTime(this.orderData.data);
      this.timeVal = [];
      this.$emit("getList");
      this.$emit("order-data", this.orderData);
    },
    // 订单选择状态
    selectChange2(tab) {
      this.getOrderStatus(tab);
      this.$emit("getList", 1);
    },
    userSearchs(type) {
      this.getOrderType(type);
      this.$emit("getList", 1);
    },
    // 时间状态
    timeChange(time) {
      this.getOrderTime(time);
      this.$emit("getList");
    },
    // 订单号搜索
    orderSearch(num) {
      this.getOrderNum(num);
      this.getfieldKey(this.orderData.field_key);
      this.$emit("getList", 1);
    },
    // 点击订单类型
    onClickTab() {
      this.$emit("onChangeType", this.currentTab);
    },
    // 批量删除
    delAll() {
      if (this.delIdList.length === 0) {
        this.$Message.error("请先选择删除的订单！");
      } else {
        if (this.isDels) {
          this.delIdList.filter((item) => {
            this.isDelIdList.push(item.id);
          });
          let idss = {
            ids: this.isDelIdList,
            all: this.isAll,
            where: this.orderData,
          };
          let delfromData = {
            title: "删除订单",
            url: `/order/dels`,
            method: "post",
            ids: idss,
          };
          this.$modalSure(delfromData)
            .then((res) => {
              this.$Message.success(res.msg);
              this.tabList();
            })
            .catch((res) => {
              this.$Message.error(res.msg);
            });
        } else {
          const title = "错误！";
          const content =
            "<p>您选择的的订单存在用户未删除的订单，无法删除用户未删除的订单！</p>";
          this.$Modal.error({
            title: title,
            content: content,
          });
        }
      }
    },
    handleSubmit() {
      this.$emit("on-submit", this.data);
    },
    // 刷新
    Refresh() {
      this.$emit("getList");
    },
    //
    handleReset() {
      this.$refs.form.resetFields();
      this.$emit("on-reset");
    },
    queuemModal() {
      this.$refs.queue.modal = true;
    },
  },
};
</script>

<style scoped lang="stylus">
.tab_data >>> .ivu-form-item-content {
  margin-left: 0 !important;
}

.table_box >>> .ivu-divider-horizontal {
  margin-top: 0px !important;
}

.tabform {
  margin-bottom: 10px;
}

.Refresh {
  font-size: 12px;
  color: #1890FF;
  cursor: pointer;
}

.order-wrapper {
  margin-top: 10px;
  padding: 10px;
  border: 1px solid #ddd;

  .title {
    font-size: 16px;
  }

  .order-box {
    margin-top: 10px;
    border: 1px solid #ddd;

    .item {
      display: flex;
      align-items: center;
      border-bottom: 1px solid #ddd;

      &:last-child {
        border-bottom: 0;
      }

      .label {
        width: 100px;
        padding: 10px 0 10px 10px;
        border-right: 1px solid #ddd;
      }

      .con {
        flex: 1;
        padding: 10px 0 10px 10px;
      }
    }
  }
}

.manual-modal {
  display: flex;
  align-items: center;
}
@media screen and (max-width: 1100px) {
    .caozuo{
      margin-top:20px;
    }
}
</style>
