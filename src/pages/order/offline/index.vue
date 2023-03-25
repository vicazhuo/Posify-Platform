<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          ref="pagination"
          :model="pagination"
          :label-width="labelWidth"
          inline
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <FormItem label="订单号：" label-for="title">
            <Input
              v-model="pagination.order_id"
              placeholder="请输入订单号"
              style="width: 250px"
            />
          </FormItem>
          <FormItem label="用户名：" label-for="title">
            <Input
              v-model="pagination.name"
              placeholder="请输入用户名"
              style="width: 250px"
            />
          </FormItem>
          <FormItem label="创建时间：">
            <DatePicker
              @on-change="onchangeTime"
              :value="timeVal"
              format="yyyy/MM/dd"
              type="datetimerange"
              placement="bottom-start"
              placeholder="自定义时间"
              style="width: 250px;margin-right:14px"
              class="mr20"
              :options="options"
            ></DatePicker>
            <Button type="primary" @click="search()">查询</Button>
          </FormItem>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Button type="primary" @click="qrcodeShow">查看二维码</Button>
      <Table
        :columns="thead"
        :data="tbody"
        ref="table"
        :loading="loading"
        highlight-row
        class="ivu-mt"
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
      >
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="pagination.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="pagination.limit"
        />
      </div>
    </Card>
    <Modal v-model="modal" title="收款码" footer-hide class-name="vertical-center-modal">
      <div>
        <div v-viewer class="acea-row row-around code">
          <Spin fix v-if="spin"></Spin>
          <div class="acea-row row-column-around row-between-wrapper">
            <div class="QRpic">
              <img v-lazy="qrcode && qrcode.wechat" />
            </div>
            <span class="mt10">{{
              animal ? "公众号收款码" : "公众号二维码"
            }}</span>
          </div>
          <div class="acea-row row-column-around row-between-wrapper">
            <div class="QRpic">
              <img v-lazy="qrcode && qrcode.routine" />
            </div>
            <span class="mt10">{{
              animal ? "小程序收款码" : "小程序二维码"
            }}</span>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import { mapState } from "vuex";
import { orderScanList, orderOfflineScan } from "@/api/order";
import timeOptions from "@/utils/timeOptions";
export default {
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      thead: [
        {
          title: "订单号",
          key: "order_id",
        },
        {
          title: "用户信息",
          key: "nickname",
        },
        {
          title: "实际支付",
          key: "pay_price",
        },
        {
          title: "优惠价格",
          key: "true_price",
        },
        {
          title: "支付时间",
          key: "pay_time",
        },
      ],
      tbody: [],
      loading: false,
      total: 0,
      animal: 1,
      pagination: {
        page: 1,
        limit: 30,
        order_id: "",
        add_time: "",
      },
      options: timeOptions,
      timeVal: [],
      modal: false,
      qrcode: null,
      name: "",
      spin: false,
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
  created() {
    this.getOrderList();
  },
  methods: {
    onchangeCode(e) {
      this.animal = e;
      this.qrcodeShow();
    },
    // 具体日期搜索()；
    onchangeTime(e) {
      this.pagination.page = 1;
      this.timeVal = e;
      this.pagination.add_time = this.timeVal[0] ? this.timeVal.join("-") : "";
    },
    // 订单列表
    getOrderList() {
      this.loading = true;
      orderScanList(this.pagination)
        .then((res) => {
          this.loading = false;
          const { count, list } = res.data;
          this.total = count;
          this.tbody = list;
        })
        .catch((err) => {
          this.loading = false;
          this.$Message.error(err.msg);
        });
    },
    // 分页
    pageChange(index) {
      this.pagination.page = index;
      this.getOrderList();
    },
    search() {
      this.pagination.page = 1;
      this.getOrderList();
    },
    // 查看二维码
    qrcodeShow() {
      this.spin = true;
      orderOfflineScan({ type: this.animal })
        .then((res) => {
          this.spin = false;
          this.qrcode = res.data;
          this.modal = true;
        })
        .catch((err) => {
          this.spin = false;
          this.$Message.error(err.msg);
        });
    },
  },
};
</script>

<style lang="less" scoped>
.code {
  position: relative;
}

.QRpic {
  width: 180px;
  height: 259px;

  img {
    width: 100%;
    height: 100%;
  }
}
</style>
