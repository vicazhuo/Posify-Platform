<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt box">
      <Form
        ref="formValidate"
        inline
        :model="formValidate"
        :label-width="labelWidth"
        :label-position="labelPosition"
        @submit.native.prevent
      >
        <FormItem label="创建时间：">
          <DatePicker
            :editable="false"
            @on-change="onchangeTime"
            :value="timeVal"
            format="yyyy/MM/dd HH:mm"
            type="datetimerange"
            placement="bottom-start"
            placeholder="自定义时间"
            style="width: 250px"
            :options="options"
          ></DatePicker>
        </FormItem>
        <FormItem label="交易类型：">
          <Select
            v-model="formValidate.trading_type"
            placeholder="请选择"
            clearable
            @on-change="search"
            style="width: 250px"
          >
            <Option
              :label="item"
              :value="index"
              v-for="(item, index) in withdrawal"
              :key="index"
              >{{ item }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="订单搜索：" label-for="status1">
          <Input
            @on-search="getList"
            style="width: 250px; margin-right: 14px"
            v-model="formValidate.keywords"
            placeholder="订单号/昵称/电话/用户ID"
            class="input"
          ></Input>
          <Button type="primary" @click="search()">查询</Button>
        </FormItem>
      </Form>
    </Card>

    <Card :bordered="false" dis-hover class="ive-mt tablebox">
      <div class="btnbox"></div>
      <div class="table">
        <Table
          :columns="columns"
          :data="tabList"
          ref="table"
          :loading="loading"
          highlight-row
          no-userFrom-text="暂无数据"
          no-filtered-userFrom-text="暂无筛选结果"
        >
          <template slot-scope="{ row }" slot="price">
            <span :class="Number(row.price) < 0 ? 'colorgreen' : 'colorred'">{{
              row.price
            }}</span>
          </template>
          <template slot-scope="{ row }" slot="user_nickname">
            <span>{{ row.uid ? row.user_nickname : "游客" }}</span>
          </template>
          <template slot-scope="{ row }" slot="action">
            <a @click="remark(row)">备注</a>
          </template>
        </Table>
      </div>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="formValidate.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="formValidate.limit"
        />
      </div>
    </Card>
    <!-- 备注 -->
    <Modal
      v-model="modalmark"
      scrollable
      title="请修改内容"
      class="order_box"
      :closable="false"
      :mask-closable="false"
    >
      <Form
        ref="remarks"
        :model="remarks"
        :label-width="80"
        @submit.native.prevent
      >
        <FormItem label="备注：">
          <Input
            v-model="remarks.mark"
            maxlength="200"
            show-word-limit
            type="textarea"
            placeholder="请填写备注~"
            style="width: 100%"
          />
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="primary" @click="putRemark()">提交</Button>
        <Button @click="cancel()">取消</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
import { mapState } from "vuex";
import { getFlowList, setMarks } from "@/api/statistic";
import timeOptions from "@/utils/timeOptions";
export default {
  name: "order",
  data() {
    return {
      modalmark: false,
      options: timeOptions,
      remarks: {
        mark: "",
      },
      staff: [],
      total: 0,
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      columns: [
        {
          title: "交易单号",
          key: "flow_id",
          minWidth: 180,
        },
        {
          title: "关联订单",
          key: "order_id",
          minWidth: 180,
        },
        {
          title: "交易金额",
          slot: "price",
          minWidth: 80,
        },
        {
          title: "交易用户",
          key: "nickname",
          ellipsis: true,
          minWidth: 80,
        },
        {
          title: "交易时间",
          key: "add_time",
          minWidth: 150,
        },
        {
          title: "交易类型",
          key: "trading_type",
          minWidth: 80,
        },
        {
          title: "支付方式",
          key: "pay_type",
          minWidth: 80,
        },
        {
          title: "备注",
          key: "mark",
          minWidth: 120,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 80,
          align: "center",
        },
      ],
      tabList: [],
      withdrawal: [],
      formValidate: {
        trading_type: '',
        time: "",
        keywords: "",
        page: 1,
        limit: 20,
      },
      timeVal: [],
      fromList: {
        title: "选择时间",
        custom: true,
        fromTxt: [
          { text: "全部", val: "" },
          { text: "昨天", val: "yesterday" },
          { text: "今天", val: "today" },
          { text: "本周", val: "week" },
          { text: "本月", val: "month" },
          { text: "本季度", val: "quarter" },
          { text: "本年", val: "year" },
        ],
      },
    };
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
  mounted() {
    this.getList();
  },
  methods: {
    getList() {
      this.loading = true;
      getFlowList(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.tabList = data.list;
          this.total = data.count;
          this.withdrawal = data.status;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$Message.error(res.msg);
        });
    },
    search() {
      this.formValidate.page = 1;
      // this.formValidate.trading_type = e;
      this.getList();
    },
    reset() {
      this.formValidate = {
        trading_type: 0,
        time: "",
        keywords: "",
        page: 1,
        limit: 20,
      };
      this.timeVal = [];
      this.getList();
    },
    // 选择时间
    selectChange(tab) {
      this.formValidate.page = 1;
      this.formValidate.time = tab;
      this.timeVal = [];
      this.getList();
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.time = this.timeVal[0] ? this.timeVal.join("-") : "";
      this.formValidate.page = 1;
      this.getList();
    },
    //分页
    pageChange(status) {
      this.formValidate.page = status;
      this.getList();
    },
    remark(e) {
      this.remarkId = e.id;
      this.modalmark = true;
      this.remarks.mark = e.mark;
    },
    //备注的提交
    putRemark() {
      this.modalmark = false;
      setMarks(this.remarkId, this.remarks)
        .then((res) => {
          this.$Message.success(res.msg);
          this.modal_loading = false;
          this.modals = false;
          this.getList();
        })
        .catch((err) => {
          this.modal_loading = false;
          this.$Message.error(err.msg);
        });
    },
    // 取消备注按钮
    cancel() {
      this.remarks = { mark: "" };
      this.modalmark = false;
    },
  },
};
</script>

<style scoped lang="less">
/deep/.ivu-page-header,
/deep/.ivu-tabs-bar {
  border-bottom: 1px solid #ffffff;
}
/deep/.ivu-card-body {
  padding: 0;
}
/deep/.ivu-tabs-nav {
  height: 45px;
}
/deep/.ivu-form-label-left .ivu-form-item-label {
  text-align: right;
}
.topbox {
  padding: 20px;
  font-size: 18px;
  font-weight: 500;
}
.page {
  padding-right: 30px;
  padding-bottom: 10px;
}
.box {
  padding: 20px;
  padding-bottom: 0px;
}
.tablebox {
  margin-top: 15px;
  padding-bottom: 10px;
}
.btnbox {
  padding: 20px 0px 0px 30px;
  .btns {
    width: 99px;
    height: 32px;
    background: #1890ff;
    border-radius: 4px;
    text-align: center;
    line-height: 32px;
    color: #ffffff;
    cursor: pointer;
  }
}
.table {
  padding: 0px 30px 15px 30px;
}
.colorred {
  color: #ff5722;
}
.colorgreen {
  color: #009688;
}
.search {
  width: 86px;
  height: 32px;
  background: #1890ff;
  border-radius: 4px;
  text-align: center;
  line-height: 32px;
  font-size: 13px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #ffffff;
  cursor: pointer;
}
.reset {
  width: 86px;
  height: 32px;
  border-radius: 4px;
  border: 1px solid rgba(151, 151, 151, 0.36);
  text-align: center;
  line-height: 32px;
  font-size: 13px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: rgba(0, 0, 0, 0.85);
  cursor: pointer;
}
</style>
