<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          ref="pagination"
          inline
          :model="pagination"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <FormItem label="订单状态：">
            <Select v-model="pagination.refund_type" style="width: 250px">
              <Option
                v-for="(item, index) in num"
                :value="index"
                :key="index"
                >{{ item.name }}</Option
              >
            </Select>
          </FormItem>
          <FormItem label="退款时间：">
            <DatePicker
              :editable="false"
              @on-change="onchangeTime"
              :value="timeVal"
              format="yyyy/MM/dd"
              type="daterange"
              placement="bottom-start"
              placeholder="自定义时间"
              style="width: 250px"
              :options="options"
            ></DatePicker>
          </FormItem>
          <FormItem label="订单号：" label-for="title">
            <Input
              v-model="pagination.order_id"
              placeholder="请输入订单号"
              style="width: 250px;margin-right:14px"
            />
            <Button type="primary" @click="orderSearch()">查询</Button>
          </FormItem>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Table
        :columns="thead"
        :data="tbody"
        ref="table"
        :loading="loading"
        highlight-row
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
      >
        <template slot-scope="{ row }" slot="order_id">
          <span v-text="row.order_id" style="display: block"></span>
          <span v-show="row.is_del === 1 && row.delete_time == null" style="color: #ed4014; display: block"
            >用户已删除</span
          >
        </template>
				<template slot-scope="{ row }" slot="nickname">
				  <div>{{ row.nickname }}<span style="color: #ed4014;" v-if="row.delete_time != null"> (已注销)</span></div>
				</template>
        <template slot-scope="{ row }" slot="user">
          <div>用户名：{{ row.nickname }}</div>
          <div>用户ID：{{ row.uid }}</div>
        </template>
        <template slot-scope="{ row }" slot="refund_type"> 
		  <Tag color="blue" size="medium" v-if="row.refund_type == 1">仅退款</Tag>
		  <Tag color="blue" size="medium" v-if="row.refund_type == 2">退货退款</Tag>
		  <Tag color="red" size="medium" v-if="row.refund_type == 3">拒绝退款</Tag>
		  <Tag color="blue" size="medium" v-if="row.refund_type == 4">商品待退货</Tag>
		  <Tag color="blue" size="medium" v-if="row.refund_type == 5">退货待收货</Tag>
		  <Tag color="green" size="medium" v-if="row.refund_type == 6">已退款</Tag>
        </template>
        <template slot-scope="{ row }" slot="info">
          <!-- <div class="tabBox" v-for="(val, i) in row._info" :key="i">
            <div class="tabBox_img" v-viewer>
              <img
                v-lazy="val.cart_info.productInfo.attrInfo? val.cart_info.productInfo.attrInfo.image : val.cart_info.productInfo.image"/>
            </div>
            <span class="tabBox_tit line2">
              {{ val.cart_info.productInfo.store_name + " | "}}
              {{val.cart_info.productInfo.attrInfo? val.cart_info.productInfo.attrInfo.suk : ""}}
            </span>
            <span class="tabBox_pice">{{ "￥" + val.cart_info.truePrice + " x " + val.cart_info.cart_num}}</span>
          </div> -->
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
            <span class="tabBox_tit line1">
              <span class="font-color-red" v-if="val.cart_info.is_gift">赠品</span>
              {{ val.cart_info.productInfo.store_name + " | "}}
              {{val.cart_info.productInfo.attrInfo ? val.cart_info.productInfo.attrInfo.suk: ""}}
            </span>
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
          <Tooltip theme="dark" max-width="300" :delay="600">
            <div v-html="row.refund_reason" class="pt5"></div>
            <div slot="content">
              <div  class="pt5">退款原因：{{row.refund_explain}}</div>
              <div v-if="row.refund_goods_explain" class="pt5">退货原因：{{row.refund_goods_explain}}</div>
            </div>
          </Tooltip>
          <div class="pictrue-box" v-if="row.refund_img">
            <div
              v-viewer
              v-for="(item, index) in row.refund_img || []"
              :key="index"
            >
              <img class="pictrue mr10" v-lazy="item" :src="item" />
            </div>
          </div>
        </template>
        <!-- <template slot-scope="{ row }" slot="statusGoodName">
          <div v-html="row.refund_goods_explain" class="pt5"></div>
          <div class="pictrue-box" v-if="row.refund_goods_img">
            <div
              v-viewer
              v-for="(item, index) in row.refund_goods_img || []"
              :key="index"
            >
              <img class="pictrue mr10" v-lazy="item" :src="item" />
            </div>
          </div>
        </template> -->
        <template slot-scope="{ row }" slot="action">
          <a
            @click="changeMenu(row, '5')"
			:disabled="openErp"
            v-show="
              [1, 2, 5].includes(row.refund_type) &&
              (parseFloat(row.pay_price) > parseFloat(row.refunded_price) ||
                row.pay_price == 0)
            "
            >{{ row.refund_type == 2 ? "同意退货" : "立即退款" }}</a
          >
          <Divider
            type="vertical"
            v-show="
              [1, 2, 5].includes(row.refund_type) &&
              (parseFloat(row.pay_price) > parseFloat(row.refunded_price) ||
                row.pay_price == 0)
            "
          />
          <a @click="changeMenu(row, '2')">订单详情</a>
          
        </template>
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
    <!-- 编辑 退款 退积分 不退款-->
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
      :rowActive="rowActive"
	  :openErp="openErp"
    ></details-from>
    <!-- 备注 -->
    <order-remark
      ref="remarks"
      remarkType="refund"
      :orderId="orderId"
      @submitFail="submitFail"
    ></order-remark>
    <!-- 记录 -->
    <order-record ref="record"></order-record>
  </div>
</template>

<script>
import { mapState } from "vuex";
import {
  orderRefundList,
  orderList,
  getOrdeDatas,
  getDataInfo,
  getRefundDataInfo,
  getRefundFrom,
  getRefundOrderFrom,
  getnoRefund,
  refundIntegral,
  getDistribution,
  writeUpdate,
} from "@/api/order";
import { erpConfig } from "@/api/erp";
import editFrom from "@/components/from/from";
import detailsFrom from "../orderList/handle/orderDetails";
import orderRemark from "../orderList/handle/orderRemark";
import orderRecord from "../orderList/handle/orderRecord";
import timeOptions from "@/utils/timeOptions";
export default {
  components: { editFrom, detailsFrom, orderRemark, orderRecord },
  data() {
    return {
	  openErp:false,
      thead: [
        {
          title: "订单号",
          align: "center",
          slot: "order_id",
          minWidth: 150,
        },
        {
          title: "用户信息",
          slot: "nickname",
          minWidth: 130,
        },
        {
          title: "商品信息",
          slot: "info",
          minWidth: 300,
        },
        {
          title: "实际支付",
          key: "pay_price",
          minWidth: 70,
        },
        {
          title: "发起退款时间",
          key: "add_time",
          minWidth: 100,
        },
        // {
        //   title: "订单类型",
        //   key: "type",
        //   minWidth: 100,
        // },
        {
          title: "退款状态",
          slot: "refund_type",
          minWidth: 100,
        },
        {
          title: "退款信息",
          slot: "statusName",
          minWidth: 100,
        },
        {
          title: "售后备注",
          key: "remark",
          minWidth: 80,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 150,
          align: "center",
        },
      ],
      tbody: [],
      num: [],
      orderDatalist: null,
      loading: false,
      FromData: null,
      total: 0,
      orderId: 0,
      animal: 1,
      pagination: {
        page: 1,
        limit: 15,
        order_id: "",
        time: "",
        refund_type: 0,
      },
      options: timeOptions,
      timeVal: [],
      modal: false,
      qrcode: null,
      name: "",
      spin: false,
      rowActive: {},
    };
  },
  computed: {
    ...mapState("order", ["orderChartType"]),
    // ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 96;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  created() {
	this.getErpConfig();  
    this.getOrderList();
  },
  methods: {
	//erp配置
	getErpConfig(){
		erpConfig().then(res=>{
			this.openErp = res.data.open_erp;
		}).catch(err=>{
			this.$Message.error(err.msg);
		})
	},  
    onchangeCode(e) {
      this.animal = e;
      this.qrcodeShow();
    },
    // 具体日期搜索()；
    onchangeTime(e) {
      this.pagination.page = 1;
      this.timeVal = e;
      this.pagination.time = this.timeVal[0] ? this.timeVal.join("-") : "";
    },
    // 获取详情表单数据
    getData(id, type) {
      getRefundDataInfo(id)
        .then(async (res) => {
          if (!type) {
            this.$refs.detailss.modals = true;
          }
          this.$refs.detailss.activeName = "detail";
          this.orderDatalist = res.data;
          // if (this.orderDatalist.orderInfo.refund_img) {
          //   try {
          //     this.orderDatalist.orderInfo.refund_img = JSON.parse(
          //       this.orderDatalist.orderInfo.refund_img
          //     );
          //   } catch (e) {
          //     this.orderDatalist.orderInfo.refund_img = [];
          //   }
          // }
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 操作
    changeMenu(row, name) {
      this.orderId = row.id;
      switch (name) {
        case "1":
          this.delfromData = {
            title: "修改立即支付",
            url: `/order/pay_offline/${row.id}`,
            method: "post",
            ids: "",
          };
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$Message.success(res.msg);
              this.getOrderList();
            })
            .catch((res) => {
              console.log(res);
              this.$Message.error(res.msg);
            });
          // this.modalTitleSs = '修改立即支付';
          break;
        case "2":
          this.rowActive = row;
          this.getData(row.id);
          break;
        case "3":
          this.$refs.record.modals = true;
          this.$refs.record.getList(row.store_order_id);
          break;
        case "4":
          this.$refs.remarks.modals = true;
          this.$refs.remarks.formValidate.remark = row.remark;
          break;
        case "5":
          this.getRefundData(row.id, row.refund_type);
          break;
        case "6":
          this.getRefundIntegral(row.id);
          break;
        case "7":
          this.getNoRefundData(row.id);
          break;
        case "8":
          this.delfromData = {
            title: "修改确认收货",
            url: `/order/take/${row.id}`,
            method: "put",
            ids: "",
          };
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$Message.success(res.msg);
              this.getOrderList();
            })
            .catch((res) => {
              this.$Message.error(res.msg);
            });
          // this.modalTitleSs = '修改确认收货';
          break;
        case "10":
          this.delfromData = {
            title: "立即打印订单",
            info: "您确认打印此订单吗?",
            url: `/order/print/${row.id}`,
            method: "get",
            ids: "",
          };
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$Message.success(res.msg);
              this.$emit("changeGetTabs");
              this.getOrderList();
            })
            .catch((res) => {
              console.log(res);
              this.$Message.error(res.msg);
            });
          break;
        case "11":
          this.delfromData = {
            title: "立即打印电子面单",
            info: "您确认打印此电子面单吗?",
            url: `/order/order_dump/${row.id}`,
            method: "get",
            ids: "",
          };
          this.$modalSure(this.delfromData)
            .then((res) => {
              this.$Message.success(res.msg);
              this.getOrderList();
            })
            .catch((res) => {
              this.$Message.error(res.msg);
            });
          break;
        default:
          this.delfromData = {
            title: "删除订单",
            url: `/order/del/${row.id}`,
            method: "DELETE",
            ids: "",
          };
          // this.modalTitleSs = '删除订单';
          this.delOrder(row, this.delfromData);
      }
    },
    // 获取退款表单数据
    getRefundData(id, refund_type) {
      if (refund_type == 2) {
        this.delfromData = {
          title: "是否立即退货",
          url: `/refund/agree/${id}`,
          method: "get",
        };
        this.$modalSure(this.delfromData)
          .then((res) => {
            this.$Message.success(res.msg);
            this.getOrderList();
            this.getData(this.orderId, 1);
          })
          .catch((res) => {
            this.$Message.error(res.msg);
          });
      } else {
        this.$modalForm(getRefundOrderFrom(id)).then(() => {
          this.getOrderList();
          this.getData(this.orderId, 1);
          this.$emit("changeGetTabs");
        });
      }
    },
    // 获取退积分表单数据
    getRefundIntegral(id) {
      refundIntegral(id)
        .then(async (res) => {
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 删除单条订单
    delOrder(row, data) {
      if (row.is_del === 1) {
        this.$modalSure(data)
          .then((res) => {
            this.$Message.success(res.msg);
            this.getOrderList();
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
    // 修改成功
    submitFail() {
      this.getOrderList();
      this.getData(this.orderId, 1);
    },
    // 订单选择状态
    selectChange2(tab) {
      this.pagination.page = 1;
      this.pagination.refund_type = tab;
      this.getOrderList(tab);
    },
    // 不退款表单数据
    getNoRefundData(id) {
      this.$modalForm(getnoRefund(id)).then(() => {
        this.getOrderList();
        this.getData(this.orderId);
        this.$emit("changeGetTabs");
      });
    },
    // 订单列表
    getOrderList() {
      this.loading = true;
      orderRefundList(this.pagination)
        .then((res) => {
          this.loading = false;
          const { count, list, num } = res.data;
          this.total = count;
          this.tbody = list;
          this.num = num;
          list.forEach((item) => {
            if (item.id == this.orderId) {
              this.rowActive = item;
            }
          });
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
    nameSearch() {
      this.pagination.page = 1;
      this.getOrderList();
    },
    // 订单搜索
    orderSearch() {
      this.pagination.page = 1;
      this.getOrderList();
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
  },
};
</script>

<style lang="stylus" scoped>
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

.pictrue-box {
  display: flex;
  align-item: center;
}

.pictrue {
  width: 25px;
  height: 25px;
}
</style>
