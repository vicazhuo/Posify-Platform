<template>
	<div>
		<Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
			<div class="new_card_pd">
				<Form
					  ref="formValidate"
					  inline
					  :model="formValidate"
					  :label-width="labelWidth"
					  :label-position="labelPosition"
					  @submit.native.prevent
					>
          <FormItem label="选择门店：">
									<Select v-model="formValidate.store_id" placeholder="请选择" 
									clearable
                  filterable
									style="width:250px">
										<Option :value ="item.id" v-for="(item,index) in staff" :key="index">{{item.name}}</Option>
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
						<FormItem label="审核状态：">
							 <Select v-model="formValidate.status" style="width:250px">
								<Option  v-for="(item, index) in fromLists.status" :value="item.val" :key="index">{{ item.text }}</Option>
							</Select>
						</FormItem>
						<FormItem label="收款方式："  label-for="status1">
									<Select v-model="formValidate.extract_type" placeholder="请选择" 
									clearable element-id="status1"
									style="width:250px">
										<Option value="bank">银行卡</Option>
										<Option value="weixin">微信</Option>
										<Option value="alipay">支付宝</Option>
									</Select>
								</FormItem>
								<FormItem label="转账状态："  label-for="status1">
									<Select v-model="formValidate.pay_status" placeholder="请选择" 
									clearable element-id="status1"
									style="width:250px;margin-right:14px;">
										<Option value="0">未转账</Option>
										<Option value="1">已转账</Option>
									</Select>
                  <Button type="primary" @click="searchs()" >查询</Button>
								</FormItem>
								
								<FormItem>
									
								</FormItem>
					</Form>
			</div>
		</Card>
				<cards-data :cardLists="cardLists" v-if="cardLists.length >= 0"></cards-data>
				<Card :bordered="false" dis-hover class="ive-mt">
					<div class="table">
						<Table :columns="columns" :data="orderList" ref="table"
						       :loading="loading" highlight-row
						       no-userFrom-text="暂无数据"
						       no-filtered-userFrom-text="暂无筛选结果">
						    <template slot-scope="{ row }" slot="action">
								<!-- 审核：status （-1 未通过 0 审核中 1 已提现 )
								提现：pay_status（0 未转账 1 已转账 ） -->
								<a @click="examine(row)" v-if="row.status == 0">审核</a>
								<Divider type="vertical" v-if="row.status == 0"/>
								<a @click="paying(row)" v-if="row.status == 1 && row.pay_status == 0">转账</a>
								<Divider type="vertical" v-if="row.status == 1 && row.pay_status == 0"/>
						    	<a @click="remark(row)">备注</a>
						    </template>
                <template slot-scope="{ row }" slot="admin_id">
                  <div class="tabBox_img">
									  <img v-lazy="row.store.image" />
								  </div>
                </template>
							<template slot-scope="{ row }" slot="store">
								<div>门店名称：{{row.store.name}}</div>
								<viewer>
								</viewer>
							</template>
							<template slot-scope="{ row }" slot="extract_type">
								<span v-if="row.extract_type == 'bank'">银行卡</span>
								<span v-if="row.extract_type == 'weixin'">微信</span>
								<span v-if="row.extract_type == 'alipay'">支付宝</span>
							</template>
							<template slot-scope="{ row }" slot="type">
								<div v-if="row.extract_type == 'bank'">
									<div>开户地址：{{row.bank_address}}</div>
									<div>银行卡：{{row.bank_code}}</div>
								</div>
								<div v-if="row.extract_type == 'weixin'">
									<div>微信号：{{row.wechat}}</div>
									<viewer>
									  <div class="tabBox_img">
										  <img v-lazy="row.qrcode_url" />
									  </div>
									</viewer>
								</div>
								<div v-if="row.extract_type == 'alipay'">
									<div>支付宝账号：{{row.alipay_account}}</div>
									<viewer>
									  <div class="tabBox_img">
									    <img v-lazy="row.qrcode_url" />
									  </div>
									</viewer>
								</div>
							</template>
							<template slot-scope="{ row }" slot="status">
                <Tag color="orange" size="medium" v-if="row.status == 0">未审核</Tag>
                <Tag color="green"  size="medium"v-if="row.status == 1">已通过</Tag>
                <Tooltip placement="top" :content="row.fail_msg" :delay="500">
                  <Tag color="red" size="medium" v-if="row.status == -1">未通过</Tag>
              </Tooltip>
							</template>
							<template slot-scope="{ row }" slot="pay_status" style="display:flex;">
                <Tag color="green" v-if="row.pay_status == 1">已转账</Tag>
                <Tooltip placement="top" :delay="500">
                  <template slot="content">
                      <div>转账说明：{{row.voucher_title}}</div>
                      <viewer v-if="row.voucher_image">
                        <div class="tabBox_img">
                          <img v-lazy="row.voucher_image" />
                        </div>
                      </viewer>
                  </template>
                  <Tag color="red" size="medium" v-if="row.pay_status == 0">未转账</Tag>
                </Tooltip>
							</template>
						</Table>
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
					</div>
				</Card>
				<!-- 备注 -->
				<Modal v-model="modalmark"  scrollable title="请修改内容"  class="order_box" :closable="false" :mask-closable="false">
				    <Form ref="remarks" :model="remarks" :label-width="80" @submit.native.prevent>
				        <FormItem label="备注：">
				            <Input v-model="remarks.mark" maxlength="200" show-word-limit type="textarea" placeholder="请填写备注~" style="width: 100%" />
				        </FormItem>
				    </Form>
				    <div slot="footer">
				        <Button  type="primary"  @click="putRemark()">提交</Button>
				        <Button   @click="cancel()">取消</Button>
				    </div>
				</Modal>
				<!-- 审核 -->
				<Modal title="审核" v-model="modal" :mask-closable="false">
					<Form :model="formItem" :label-width="80">
						<FormItem label="审核状态：">
							<RadioGroup v-model="formItem.type">
								<Radio label="1">通过</Radio>
								<Radio label="2">拒绝</Radio>
							</RadioGroup>
						</FormItem>
						<FormItem label="拒绝原因：" v-if="formItem.type == 2">
							<Input v-model="formItem.message" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="请输入拒绝原因"></Input>
						</FormItem>
					</Form>
					<div slot="footer">
					    <Button type="primary"  @click="ok">提交</Button>
					    <Button @click="cancel">取消</Button>
					</div>
				</Modal>
			</div>
		</template>
		
	<script>
import { mapState } from "vuex";
import {
  storeExtractInfo,
  storeExtractMarkApi,
  staffListInfo,
  storeExtractVerifyApi,
  storepaying,
} from "@/api/store";
import cardsData from "../../../components/cards/cards.vue";
import timeOptions from "@/utils/timeOptions";
export default {
  name: "bill",
  components: {
    cardsData,
  },
  data() {
    return {
      id: 0,
      modal: false,
      modalmark: false,
      formItem: {
        type: "1",
        message: "",
      },
      remarkId: 0,
      remarks: {
        mark: "",
      },
      total: 0,
      cardLists: [],
      extractStatistics: [],
      staff: [],
      loading: false,
      columns: [
        {
          title: "ID",
          key: "id",
          width: 60,
        },
        {
          title: "图片",
          slot: "admin_id",
          width: 60,
        },
        {
          title: "门店",
          slot: "store",
          minWidth: 120,
        },
        {
          title: "申请金额",
          key: "extract_price",
          minWidth: 80,
        },
        {
          title: "申请时间",
          key: "add_time",
          minWidth: 150,
        },
        {
          title: "收款方式",
          slot: "extract_type",
          minWidth: 80,
        },
        {
          title: "收款信息",
          slot: "type",
          minWidth: 200,
        },
        {
          title: "审核状态",
          slot: "status",
          minWidth: 150,
        },
        {
          title: "转账状态",
          slot: "pay_status",
          minWidth: 180,
        },
        {
          title: "管理员",
          key: "admin_name",
          minWidth: 80,
        },
        {
          title: "备注",
          key: "store_mark",
          minWidth: 150,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 120,
          align: "center",
        },
      ],
      orderList: [],
      formValidate: {
        pay_status: "",
        extract_type: "",
        data: "",
        status: "",
        store_id: "",
        page: 1,
        limit: 15,
      },
      timeVal: [],
      options: timeOptions,
      fromLists: {
        custom: true,
        status: [
          { text: "全部", val: "" },
          { text: "待审核", val: "0" },
          { text: "已通过", val: "1" },
          { text: "未通过", val: "-1" },
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
    this.staffApi();
    this.getList();
  },
  methods: {
    getList() {
      this.loading = true;
      storeExtractInfo(this.formValidate).then((res) => {
        this.orderList = res.data.list.list;
        this.total = res.data.list.count;
        this.extractStatistics = res.data.extract_statistics;
        this.cardLists = [
          {
            col: 6,
            count: this.extractStatistics.unPayPrice,
            name: "待转账金额",
            className: "md-basket",
          },
          {
            col: 6,
            count: this.extractStatistics.price,
            name: "待审核金额",
            className: "md-cash",
          },
          {
            col: 6,
            count: this.extractStatistics.price_not,
            name: "可提现金额",
            className: "ios-cash",
          },
          {
            col: 6,
            count: this.extractStatistics.paidPrice,
            name: "累计提现金额",
            className: "ios-cash",
          },
        ];
        this.loading = false;
      });
    },
    staffApi() {
      staffListInfo().then((res) => {
        this.staff = res.data;
      });
    },
    searchs() {
      this.formValidate.page = 1;
      this.getList();
    },
    // 选择时间
    selectChange(tab) {
      this.formValidate.page = 1;
      this.formValidate.data = tab;
      this.timeVal = [];
      this.getList();
    },
    //审核状态
    payStatus(tab) {
      this.formValidate.page = 1;
      this.formValidate.status = tab;
      this.getList();
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.data = this.timeVal[0] ? this.timeVal.join("-") : "";
      this.formValidate.page = 1;
      this.getList();
    },
    //分页
    pageChange(status) {
      this.formValidate.page = status;
      this.getList();
    },
    remark(data) {
      this.remarkId = data.id;
      this.remarks.mark = data.store_mark;
      this.modalmark = true;
    },
    //审核
    examine(row) {
      this.modal = true;
      this.id = row.id;
    },
    //转账
    paying(row) {
      this.$modalForm(storepaying(row.id)).then(() => this.getList());
    },
    //审核提交
    ok() {
      if (this.formItem.type == 2) {
        if (this.formItem.message == "") {
          this.$Message.error("请填写拒绝原因");
          return;
        }
      }
      storeExtractVerifyApi(this.id, this.formItem)
        .then((res) => {
          this.$Message.success(res.msg);
          this.formItem = {
            type: "1",
            message: "",
          };
          this.modal = false;
          this.getList();
        })
        .catch((err) => {
          this.$Message.error(err.msg);
          this.modal = false;
        });
    },
    // 取消按钮
    cancel() {
      this.formItem = {
        type: "1",
        message: "",
      };
      this.remarks.mark = "";
      this.modal = false;
      this.modalmark = false;
    },
    //备注的提交
    putRemark() {
      storeExtractMarkApi(this.remarkId, this.remarks)
        .then((res) => {
          this.$Message.success(res.msg);
          this.modalmark = false;
          this.getList();
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
  },
};
</script>
		
<style scoped lang="stylus">
/deep/.ivu-tabs-nav {
  height: 45px;
}

// /deep/.ivu-col-span-xl-6{max-width: 20%;}
/deep/.ivu-input-number {
  width: 150px !important;
}

/deep/.ivu-modal-body {
  padding: 16px 50px;
}

.topbox {
  padding: 20px;
  font-size: 18px;
  font-weight: 500;
}

.box {
  padding: 20px;
  padding-bottom: 1px;
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
  padding: 0px 0 15px 10px;
}

.tabBox_img {
  width: 40px;
  height: 40px;
  border-radius: 4px;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}
</style>
		