<template>
  <div>
    <Card :bordered="false" dis-hover class="mt15 ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          ref="orderData"
          :model="orderData"
          :label-width="labelWidth"
          :label-position="labelPosition"
          class="tabform"
          inline
          @submit.native.prevent
        >
         <FormItem label="选择门店：">
            <Select
              v-model="orderData.store_id"
              clearable
              filterable
              @on-change="userSearchs"
              style="width: 250px"
            >
              <Option v-for="item in staffData" :value="item.id" :key="item.id"
                >{{ item.name }}
              </Option>
            </Select>
          </FormItem>
          <FormItem label="创建时间：">
            <DatePicker
              :editable="false"
              @on-change="onchangeTime"
              :value="timeVal"
              format="yyyy/MM/dd"
              type="datetimerange"
              placement="bottom-start"
              placeholder="自定义时间"
              style="width: 250px"
              :options="options"
            ></DatePicker>
          </FormItem>
          <FormItem label="会员类型：">
            <Select
              v-model="vipData.member_type"
              clearable
              @on-change="userSearchs"
              style="width: 250px"
            >
              <Option
                v-for="item in treeSelect"
                :value="item.id"
                :key="item.id"
                >{{ item.label }}</Option
              >
            </Select>
          </FormItem>
          
           <FormItem label="订单状态：">
            <Select v-model="orderData.status" style="width: 250px">
              <Option value="">全部</Option>
              <Option value="0">未支付</Option>
              <Option value="1">待配送</Option>
              <Option value="2">配送中</Option>
              <Option value="3">待评价</Option>
              <Option value="5">待核销</Option>
              <Option value="6">已核销</Option>
              <Option value="4">已完成</Option>
              <Option value="-4">已删除</Option>
            </Select>
          </FormItem>
          <FormItem
            label="订单号："
            label-for="real_name"
            v-if="currentTab == 6 || currentTab == 7 || currentTab == 5"
          >
            <Input
              placeholder="订单号/商品名称"
              v-model="orderData.real_name"
              style="width: 250px;"
            />
          </FormItem>
          <FormItem
            label="用户搜索："
            label-for="nickname"
            v-if="currentTab == 3 || currentTab == 4"
          >
            <Input
              placeholder="请输入用户名"
              v-model="rechargeData.nickname"
              style="width: 250px; "
            />
          </FormItem>
          <FormItem>
            <Button type="primary" @click="orderSearch()" style="margin-left:-75px;">查询</Button>
          </FormItem>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="mt15 ivu-mt">
      <div class="new_tab">
         <Tabs v-model="currentTab" @on-click="onClickTab">
          <TabPane label="收银台订单" name="6" />
          <TabPane label="配送订单" name="7" />
          <TabPane label="核销订单" name="5" />
          <TabPane label="充值订单" name="3" />
          <TabPane label="付费会员订单" name="4" />
        </Tabs>
      </div>
      <Table
        :columns="
          currentTab == 6 || currentTab == 7 || currentTab == 5
            ? columns
            : currentTab == 3
            ? rechargeColumns
            : vipColumns
        "
        :data="tableList"
        ref="table"
        :loading="loading"
        highlight-row
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
      >
        <template slot-scope="{ row }" slot="order_id">
          <span v-text="row.order_id" style="display: block"></span>
          <span v-show="row.is_del === 1" style="color: #ed4014; display: block"
            >用户已删除</span
          >
        </template>
        <template slot-scope="{ row }" slot="userInfo">
          <div v-if="currentTab == 6 || currentTab == 7 || currentTab == 5">
            <a @click="showUserInfo(row)" v-if="row.uid">
              {{ row.nickname }}
              <span style="margin-left: 5px">/{{ row.uid }}</span>
            </a>
            <span v-else
              >游客<span style="margin-left: 5px">/{{ row.uid }}</span></span
            >
          </div>
          <div class="tabBox" v-if="currentTab == 3">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="row.avatar" />
            </div>
            <span class="tabBox_tit">{{ row.nickname }}</span>
          </div>
          <div class="tabBox" v-if="currentTab == 4 && row.user">
            <div class="tabBox_img" v-viewer>
              <img v-lazy="row.user.avatar" />
            </div>
            <span class="tabBox_tit">{{ row.user.nickname }}</span>
          </div>
        </template>
        <template slot-scope="{ row }" slot="info">
          <Tooltip theme="dark" max-width="300" :delay="600">
            <div class="tabBox" v-for="(val, i) in row._info" :key="i">
              <div class="tabBox_img" v-viewer>
                <img
                  v-lazy="
                    val.cart_info.productInfo.attrInfo
                      ? val.cart_info.productInfo.attrInfo.image
                      : val.cart_info.productInfo.image
                  "
                />
              </div>
              <span class="info_title line1">
                <span class="font-color-red" v-if="val.cart_info.is_gift" >赠品</span>
                {{ val.cart_info.productInfo.store_name + " | " }}
                {{val.cart_info.productInfo.attrInfo ? val.cart_info.productInfo.attrInfo.suk : ""}}
              </span>
              <!-- <span class="tabBox_pice"> {{ "￥" + val.cart_info.truePrice + " x " + val.cart_info.cart_num}}</span> -->
            </div>
            <div slot="content">
              <div v-for="(val, i) in row._info" :key="i">
                <p class="font-color-red" v-if="val.cart_info.is_gift">赠品</p>
                <p>{{ val.cart_info.productInfo.store_name }}</p>
                <p> {{val.cart_info.productInfo.attrInfo ? val.cart_info.productInfo.attrInfo.suk: ""}}</p>
                <p class="tabBox_pice">{{ "￥" + val.cart_info.truePrice + " x " + val.cart_info.cart_num }}</p>
              </div>
            </div>
          </Tooltip>
        </template>
        <template slot-scope="{ row }" slot="statusName">
          <Tag color="red" size="medium" v-if="row.status_name && row.status == 0">{{row.status_name.status_name}}</Tag>
          <Tag color="green" size="medium" v-if="row.status_name && row.status !== 0">{{row.status_name.status_name}}</Tag><br/>
          <Tag color="orange" size="medium" v-if="!row.is_all_refund && row.refund.length">部分退款中</Tag>
          <Tag color="orange" size="medium" v-if="row.is_all_refund && row.refund.length && row.refund_type != 6">退款中</Tag>
          <!-- <div class="pictrue-box" v-if="row.status_name">
            <div
              v-viewer
              v-for="(item, index) in row.status_name.pics || []"
              :key="index"
            >
              <img class="pictrue mr10" v-lazy="item" :src="item" />
            </div>
          </div> -->
        </template>
        <template slot-scope="{ row }" slot="pay_price">
          {{ row.paid > 0 ? row.pay_price : 0 }}
        </template>
        <template slot-scope="{ row }" slot="action">
          <span v-if="currentTab == 6">
            <a @click="detail(row)">订单详情</a>
          </span>
          <span v-if="currentTab == 7">
            <a :disabled="openErp" v-if="row._status == 2" @click="distribution(row)">分配</a>
            <Divider v-if="row._status == 2" type="vertical" />
            <a @click="detail(row)">详情</a>
            <!-- <Divider type="vertical" /> -->
          </span>
          <span v-if="currentTab == 5">
            <a :disabled="openErp" v-if="row._status == 11" @click="distribution(row)">分配</a>
            <Divider v-if="row._status == 11" type="vertical" />
            <a @click="detail(row)">详情</a>
          </span>
          <a @click="remark(row)" v-if="currentTab == 3 || currentTab == 4"
            >备注</a
          >
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="orderData.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="orderData.limit"
        />
      </div>
    </Card>
    <Distribution ref="distshow"></Distribution>
    <!-- 用户详情-->
    <user-details ref="userDetails" fromType="order"></user-details>
    <!-- 编辑 配送信息表单数据 退款 退积分 不退款-->
    <edit-from
      ref="edits"
      :FromData="FromData"
      @submitFail="submitFail"
    ></edit-from>
    <!-- 详情 -->
    <details-from
      ref="detailss"
      :orderDatalist="orderDatalist"
      :orderId="orderId"
      :row-active="rowActive"
      :currentTab="currentTab"
	  :openErp="openErp"
    ></details-from>
    <!-- 备注 -->
    <order-remark
      ref="remarks"
      :orderId="orderId"
      @submitFail="submitFail"
    ></order-remark>
    <!-- 记录 -->
    <order-record ref="record"></order-record>
    <!-- 发送货 -->
    <order-send
      ref="send"
      :orderId="orderId"
      :status="status"
      :pay_type="pay_type"
      @submitFail="submitFail"
    >
    </order-send>
  </div>
</template>

<script>
import { mapState } from "vuex";
import Distribution from "./components/distribution.vue";
import expandRow from "./components/tableExpand.vue";
import userDetails from "@/pages/user/list/handle/userDetails";
import editFrom from "@/components/from/from";
import orderSend from "./components/orderSend";
import detailsFrom from "./components/orderDetails";
import orderRecord from "./components/orderRecord";
import orderRemark from "./components/orderRemark";
import {
  orderList,
  orderChart,
  orderHeader,
  orderRecharge,
  orderVip,
  getOrdeDatas, //编辑表单数据
  getDistribution,
  writeUpdate,
  getDataInfo,
  orderExport,
  staffListInfo,
} from "@/api/store";
import { erpConfig } from "@/api/erp";
import timeOptions from "@/utils/timeOptions";
// import {
// 	staffListInfo
// } from '@/api/staff';
export default {
  name: "index",
  components: {
    userDetails,
    editFrom,
    detailsFrom,
    orderRecord,
    orderRemark,
    orderSend,
    expandRow,
    Distribution,
  },
  data() {
    return {
	  openErp:false,
      distshow: false, //分配的弹窗
      delfromData: {},
      pay_type: "",
      status: 0, //发货状态判断
      FromData: null,
      orderDatalist: null,
      orderId: 0,
      treeSelect: [
        {
          id: "free",
          label: "试用",
        },
        {
          id: "month",
          label: "月卡",
        },
        {
          id: "quarter",
          label: "季卡",
        },
        {
          id: "year",
          label: "年卡",
        },
        {
          id: "ever",
          label: "永久",
        },
        {
          id: "card",
          label: "卡密激活",
        },
      ],
      staffData: [],
      tablists: {},
      orderChartType: {},
      options: timeOptions,
      timeVal: [],
      // 订单列表
      orderData: {
        page: 1,
        limit: 10,
        type: 6,
        status: "",
        time: "",
        real_name: "",
        store_id: "",
      },
      // 充值订单搜索
      rechargeData: {
        nickname: "",
        limit: 15,
      },
      // 会员订单搜索
      vipData: {
        member_type: "",
        limit: 15,
      },
      currentTab: "6",
      tableList: [],
      total: 0,
      loading: false,
      rechargeColumns: [
        {
          title: "ID",
          key: "id",
          width: 60,
        },
        {
          title: "订单号",
          key: "order_id",
          minWidth: 180,
        },
        {
          title: "用户信息",
          slot: "userInfo",
          minWidth: 160,
        },
        {
          title: "支付金额",
          key: "price",
          minWidth: 110,
        },
        {
          title: "充值类型",
          key: "_recharge_type",
          minWidth: 100,
        },
        {
          title: "支付时间",
          key: "_pay_time",
          minWidth: 130,
        },
        {
          title: "关联店员",
          key: "_recharge_type",
          minWidth: 100,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 200,
          align: "center",
        },
      ],
      vipColumns: [
        {
          title: "订单号",
          key: "order_id",
          minWidth: 180,
        },
        {
          title: "用户信息",
          slot: "userInfo",
          minWidth: 180,
        },
        {
          title: "会员类型",
          key: "member_type",
          minWidth: 100,
        },
        {
          title: "有效期限（天）",
          minWidth: 100,
          render: (h, params) => {
            return h(
              "span",
              params.row.vip_day === -1 ? "永久" : params.row.vip_day
            );
          },
        },
        {
          title: "支付金额",
          key: "pay_price",
          minWidth: 100,
        },
        {
          title: "支付方式",
          key: "pay_type",
          minWidth: 100,
        },
        {
          title: "购买时间",
          key: "pay_time",
          minWidth: 130,
        },
        {
          title: "关联店员",
          key: "pay_time",
          minWidth: 130,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 200,
          align: "center",
        },
      ],
      columns: [
        {
          type: "expand",
          width: 30,
          render: (h, params) => {
            return h(expandRow, {
              props: {
                row: params.row,
              },
            });
          },
        },
        {
          title: "订单号",
          slot: "order_id",
          minWidth: 160,
        },
        {
          title: "用户信息",
          slot: "userInfo",
          minWidth: 120,
        },
        {
          title: "商品信息",
          slot: "info",
          minWidth: 350,
        },
        {
          title: "实际支付",
          slot: "pay_price",
          minWidth: 120,
        },
        {
          title: "支付方式",
          key: "pay_type_name",
          minWidth: 120,
        },
        {
          title: "收银店员",
          key: "clerk_name",
          minWidth: 120,
        },
        {
          title: "下单时间",
          key: "add_time",
          minWidth: 150,
        },
        {
          title: "订单状态",
          slot: "statusName",
          minWidth: 120,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 200,
          align: "center",
        },
      ],
      rowActive: {},
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
	this.getErpConfig();
    this.getTabs();
    this.getChart();
    this.staffList();
    this.getList();
  },
  mounted() {},
  methods: {
	getErpConfig(){
		erpConfig().then(res=>{
			this.openErp = res.data.open_erp;
		}).catch(err=>{
			this.$Message.error(err.msg);
		})
	},  
    remark(row) {
      this.orderId = row.id;
      this.$refs.remarks.modals = true;
      switch (this.currentTab) {
        case "3":
        case "4":
          this.$refs.remarks.currentTab(this.currentTab, row.remarks);
          break;
        default:
          this.$refs.remarks.currentTab(this.currentTab, row.remark);
      }
    },
    detail(row) {
      this.orderId = row.id;
      this.getData(row.id);
      this.rowActive = row;
    },
    distribution(row) {
      this.$refs.distshow.modals = true;
      this.$refs.distshow.getList(row.id);
    },
    record(row) {
      this.orderId = row.id;
      this.$refs.record.modals = true;
      this.$refs.record.getList(row.id);
    },
    // 查看子订单
    splitOrderDetail(row) {
      this.$router.push({
        path: "split_list",
        query: {
          id: row.id,
        },
      });
    },
    // 删除单条订单
    delOrder(row, data) {
      if (row.is_del === 1) {
        this.$modalSure(data)
          .then((res) => {
            this.$Message.success(res.msg);
            this.getList();
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
    },
    // 获取详情表单数据
    getData(id, type) {
      getDataInfo(id)
        .then(async (res) => {
          if (!type) {
            this.$refs.detailss.modals = true;
          }
          this.$refs.detailss.activeName = "detail";
          this.orderDatalist = res.data;
          if (this.orderDatalist.orderInfo.refund_reason_wap_img) {
            try {
              this.orderDatalist.orderInfo.refund_reason_wap_img = JSON.parse(
                this.orderDatalist.orderInfo.refund_reason_wap_img
              );
            } catch (e) {
              this.orderDatalist.orderInfo.refund_reason_wap_img = [];
            }
          }
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 立即核销
    bindWrite(row) {
      let self = this;
      this.$Modal.confirm({
        title: "提示",
        content: "确定要核销该订单吗？",
        cancelText: "取消",
        closable: true,
        maskClosable: true,
        onOk: function () {
          writeUpdate(row.order_id).then((res) => {
            self.$Message.success(res.msg);
            self.getList();
          });
        },
        onCancel: () => {},
      });
    },
    // 配送信息表单数据
    delivery(row) {
      getDistribution(row.id)
        .then(async (res) => {
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 编辑
    edit(row) {
      this.getOrderData(row.id);
    },
    // 获取编辑表单数据
    getOrderData(id) {
      getOrdeDatas(id)
        .then(async (res) => {
          if (res.data.status === false) {
            return this.$authLapse(res.data);
          }
          this.$authLapse(res.data);
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 修改成功(编辑只有未支付时出现)
    submitFail() {
      this.status = 0;
      if (this.currentTab == 3) {
        this.getRecharge();
      } else if (this.currentTab == 4) {
        this.getVipOrder();
      } else {
        this.getList();
        this.getData(this.orderId, 1);
      }
    },
    // 发送货
    sendOrder(row) {
      this.$store.commit("admin/order/setSplitOrder", row.total_num);
      this.$refs.send.modals = true;
      this.orderId = row.id;
      this.status = row._status;
      this.pay_type = row.pay_type;
      this.$refs.send.getList();
      this.$refs.send.getDeliveryList();
      this.$nextTick((e) => {
        this.$refs.send.getCartInfo(row._status, row.id);
      });
    },
    // 详情
    showUserInfo(row) {
      this.$refs.userDetails.modals = true;
      this.$refs.userDetails.activeName = "info";
      this.$refs.userDetails.getDetails(row.uid);
    },
    // 初始化数据
    clearData() {
      this.orderData = {
        page: 1,
        limit: 15,
        type: 6,
        status: "",
        time: "",
        real_name: "",
        store_id: "",
      };
      this.rechargeData = {
        nickname: "",
        limit: 15,
      };
      this.vipData = {
        member_type: "",
        limit: 15,
      };
    },
    // 店员列表
    staffList() {
      let data = {
        page: 0,
        limit: 0,
      };
      staffListInfo()
        .then((res) => {
          this.staffData = res.data;
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    // 获取门店订单头部统计
    getTabs() {
      orderHeader()
        .then((res) => {
          this.tablists = res.data;
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    // 订单头部数据
    getChart() {
      orderChart({ type: this.currentTab })
        .then((res) => {
          this.orderChartType = res.data;
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    // 编辑
    edit(row) {
      this.getOrderData(row.id);
    },
    //点击切换头部
    onClickTab() {
      this.clearData();
      this.orderData.type = this.currentTab;
      this.orderData.page = 1;
      this.timeVal = [];
      this.getChart();
      this.searchList();
    },
    // 不同类型订单调用列表；
    searchList() {
      if (this.currentTab == 3) {
        this.getRecharge();
      } else if (this.currentTab == 4) {
        this.getVipOrder();
      } else {
        this.getList();
      }
    },
    // 搜索店员或店员选择
    userSearchs() {
      this.orderData.page = 1;
      this.searchList();
    },
    // 切换订单状态
    selectStatus() {
      this.orderData.page = 1;
      this.getList();
    },
    // 付费会员订单列表
    getVipOrder() {
      this.loading = true;
      this.tableList = [];
      this.vipData.name = this.rechargeData.nickname;
      this.vipData.add_time = this.orderData.time;
      this.vipData.store_id = this.orderData.store_id;
      this.vipData.page = this.orderData.page;
      orderVip(this.vipData)
        .then((res) => {
          let data = res.data;
          this.tableList = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((err) => {
          this.loading = false;
          this.$Message.error(err.msg);
        });
    },
    // 充值订单
    getRecharge() {
      this.loading = true;
      this.tableList = [];
      this.rechargeData.data = this.orderData.time;
      this.rechargeData.store_id = this.orderData.store_id;
      this.rechargeData.page = this.orderData.page;
      orderRecharge(this.rechargeData)
        .then((res) => {
          let data = res.data;
          this.tableList = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((err) => {
          this.loading = false;
          this.$Message.error(err.msg);
        });
    },
    // 一般订单
    getList() {
      this.loading = true;
      this.tableList = [];
      orderList(this.orderData)
        .then((res) => {
          let data = res.data;
          data.data.forEach((item) => {
            if (item.id == this.orderId) {
              this.rowActive = item;
            }
          });
          this.tableList = data.data;
          this.total = data.count;
          this.loading = false;
        })
        .catch((err) => {
          this.loading = false;
          this.$Message.error(err.msg);
        });
    },
    pageChange(index) {
      this.orderData.page = index;
      this.searchList();
    },
    // 搜索
    orderSearch() {
      this.orderData.page = 1;
      this.searchList();
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.orderData.time = this.timeVal.join("-");
      this.orderData.page = 1;
      if (!e[0]) {
        this.orderData.time = "";
      }
      this.searchList();
    },
    // 选择时间
    selectChange() {
      this.orderData.page = 1;
      this.timeVal = [];
      this.searchList();
    },
    getExcelData(excelData) {
      return new Promise((resolve, reject) => {
        let type = 1;
        if (
          this.currentTab == 6 ||
          this.currentTab == 7 ||
          this.currentTab == 5
        ) {
          type = 1;
        } else if (this.currentTab == 3) {
          type = 2;
        } else {
          type = 3;
        }
        orderExport(excelData, type).then((res) => {
          return resolve(res.data);
        });
      });
    },
  },
};
</script>

<style lang="stylus" scoped>
.cardCon {
  /deep/.ivu-card-body {
    padding: 14px 20px 0 0px !important;
  }

  /deep/.ivu-tabs-bar {
    margin-bottom: 0 !important;
    border-bottom: 1px solid #E9E9E9 !important;
  }
}

.pictrue-box {
  display: flex;
  align-item: center;
}

.pictrue {
  width: 25px;
  height: 25px;
}

img {
  height: 36px;
  display: block;
}

.tabBox {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;

  .tabBox_img {
    width: 30px;
    height: 30px;

    img {
      width: 100%;
      height: 100%;
    }
  }

  .tabBox_tit {
    width: 60%;
    height: 46px;
    line-height: 23px;
    font-size: 12px !important;
    margin: 0 2px 0 10px;
    letter-spacing: 1px;
    padding: 5px 0;
    box-sizing: border-box;
  }
  .info_title{
    width:290px;
    height:30px;
    line-height:30px;
    font-size: 12px !important;
    margin: 0 2px 0 10px;
    letter-spacing: 1px;
    box-sizing: border-box;
  }
}
.tabBox +.tabBox{
  margin-top:5px;
}
.trip {
  color: orange;
}
.new_tab {
  >>>.ivu-tabs-nav .ivu-tabs-tab{
      padding:4px 16px 20px !important;
      font-weight: 500;
  }
}
</style>
