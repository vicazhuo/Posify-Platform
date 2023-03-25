<template>
	<div>
		<!-- <div class="i-layout-page-header">
			<PageHeader class="product_tabs" title="子订单列表" hidden-breadcrumb>
			</PageHeader>
		</div> -->
		<Table :columns="columns" :data="orderList" ref="table" :loading="loading" highlight-row no-data-text="暂无数据"
			no-filtered-data-text="暂无筛选结果" class="orderData mt25">
			<template slot-scope="{ row, index }" slot="order_id">
				<span v-text="row.order_id" style="display: block"></span>
				<span v-show="row.is_del === 1" style="color: #ed4014; display: block">用户已删除</span>
			</template>
			<template slot-scope="{ row, index }" slot="nickname">
				<a @click="showUserInfo(row)">{{row.nickname}}<span style="margin-left: 5px;">/{{row.uid}}</span></a>
			</template>
			<template slot-scope="{ row, index }" slot="info">
				<div class="tabBox" v-for="(val, i) in row._info" :key="i">
					<div class="tabBox_img" v-viewer>
						<img v-lazy="
                val.cart_info.productInfo.attrInfo
                  ? val.cart_info.productInfo.attrInfo.image
                  : val.cart_info.productInfo.image
              " />
					</div>
					<span class="tabBox_tit">
						{{ val.cart_info.productInfo.store_name + " | "
            }}{{
              val.cart_info.productInfo.attrInfo
                ? val.cart_info.productInfo.attrInfo.suk
                : ""
            }}
					</span>
					<span class="tabBox_pice">{{
            "￥" + val.cart_info.truePrice + " x " + val.cart_info.cart_num
          }}</span>
				</div>
			</template>
			<template slot-scope="{ row, index }" slot="statusName">
				<div v-html="row.status_name.status_name" class="pt5"></div>
				<div class="pictrue-box">
					<div v-viewer v-if="row.status_name.pics" v-for="(item, index) in row.status_name.pics || []"
						:key="index">
						<img class="pictrue mr10" v-lazy="item" :src="item" />
					</div>
				</div>
			</template>
			<template slot-scope="{ row, index }" slot="action">
				<a @click="edit(row)" v-if="row._status === 1">编辑</a>
				<a @click="sendOrder(row)" v-if="
            (row._status === 2 || row._status === 8) &&
            row.shipping_type === 1 &&
            (row.pinkStatus === null || row.pinkStatus === 2)
          ">发送货</a>
				<a @click="delivery(row)" v-if="row._status === 4">配送信息</a>
				<a @click="bindWrite(row)" v-if="
            row.shipping_type == 2 &&
            row.status == 0 &&
            row.paid == 1 &&
            row.refund_status === 0
          ">立即核销</a>
				<Divider type="vertical" v-if="
            row._status === 1 ||
            ((row._status === 2 || row._status === 8) &&
              (row.pinkStatus === null || row.pinkStatus === 2)) ||
            row._status === 4 ||
            (row.shipping_type == 2 &&
              row.status == 0 &&
              row.paid == 1 &&
              row.refund_status === 0)
          " />
				<template>
					<Dropdown @on-click="changeMenu(row, $event)">
						<a href="javascript:void(0)">
							更多
							<Icon type="ios-arrow-down"></Icon>
						</a>
						<DropdownMenu slot="list">
							<DropdownItem name="1" ref="ones" v-show="
                  row._status === 1 &&
                  row.paid === 0 &&
                  row.pay_type === 'offline'
                ">立即支付</DropdownItem>
							<DropdownItem name="2">订单详情</DropdownItem>
							<DropdownItem name="3">订单记录</DropdownItem>
							<DropdownItem name="11" v-show="row._status >= 3 && row.express_dump">电子面单打印</DropdownItem>
							<DropdownItem name="10" v-show="row._status >= 2">小票打印</DropdownItem>
							<DropdownItem name="4" v-show="
                  row._status !== 1 ||
                  (row._status === 3 &&
                    row.use_integral > 0 &&
                    row.use_integral >= row.back_integral)
                ">订单备注</DropdownItem>
							<DropdownItem name="8" v-show="row._status === 4">已收货</DropdownItem>
							<DropdownItem name="9" v-if="row.is_del == 1">删除订单</DropdownItem>
						</DropdownMenu>
					</Dropdown>
				</template>
			</template>
		</Table>
		<!-- 编辑 退款 退积分 不退款-->
		<edit-from ref="edits" :FromData="FromData" @submitFail="submitFail"></edit-from>
		<!-- 用户详情-->
		<user-details ref="userDetails"></user-details>
		<!-- 详情 -->
		<details-from ref="detailss" :orderDatalist="orderDatalist" :orderId="orderId"></details-from>
		<!-- 备注 -->
		<order-remark ref="remarks" :orderId="orderId" @submitFail="submitFail"></order-remark>
		<!-- 记录 -->
		<order-record ref="record"></order-record>
		<!-- 发送货 -->
		<order-send ref="send" :orderId="orderId" :status="status" @submitFail="submitFail"></order-send>
	</div>
</template>

<script>
	import expandRow from "./components/tableExpand.vue";
	import {
		orderList,
		getOrdeDatas,
		getDataInfo,
		getDistribution,
		writeUpdate,
		splitOrderList,
	} from "@/api/order";
	import editFrom from "@/components/from/from";
	import detailsFrom from "./components/orderDetails";
	import orderRemark from "./components/orderRemark";
	import orderRecord from "./components/orderRecord";
	import orderSend from "./components/orderSend";
	import userDetails from "@/pages/user/components/userDetails";

	export default {
		name: "table_list",
		components: {
			expandRow,
			editFrom,
			detailsFrom,
			orderRemark,
			orderRecord,
			orderSend,
			userDetails,
		},
		props: ["where", "isAll"],
		data() {
			return {
				columns: [{
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
						align: "center",
						slot: "order_id",
						minWidth: 150,
					},
					{
						title: "订单类型",
						key: "pink_name",
						minWidth: 120,
					},
					{
						title: "用户信息",
						slot: "nickname",
						minWidth: 100,
					},
					{
						title: "商品信息",
						slot: "info",
						minWidth: 330,
					},
					{
						title: "实际支付",
						key: "pay_price",
						minWidth: 70,
					},
					{
						title: "支付时间",
						key: "_pay_time",
						minWidth: 100,
					},
					{
						title: "支付状态",
						key: "pay_type_name",
						minWidth: 80,
					},
					{
						title: "订单状态",
						key: "statusName",
						slot: "statusName",
						minWidth: 120,
					},
					{
						title: "操作",
						slot: "action",
						// fixed: "right",
						minWidth: 150,
						align: "center",
					}
				],
				orderList: [],
				FromData: null,
				orderId: 0,
				status: 0, //发货状态判断
				orderDatalist: null,
				delfromData: {},
				loading: false,
				modal: false
			};
		},
		mounted() {},
		created() {
			this.getList();
		},
		watch: {},
		methods: {
			// 订单列表
			getList() {
				this.loading = true;
				splitOrderList(this.$route.query.id)
					.then(async (res) => {
						this.orderList = res.data;
						this.loading = false;
					})
					.catch((res) => {
						this.loading = false;
						this.$Message.error(res.msg);
					});
			},
			showUserInfo(row) {
				this.$refs.userDetails.modals = true;
				this.$refs.userDetails.getDetails(row.uid);
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
			// 发送货
			sendOrder(row) {
				this.$refs.send.modals = true;
				this.orderId = row.id;
				this.status = row._status;
				this.$refs.send.getList();
				this.$refs.send.getDeliveryList();
				this.$nextTick((e) => {
					this.$refs.send.getCartInfo(row._status, row.id);
				});
			},
			// 核销订单
			bindWrite(row) {
				let self = this;
				this.$Modal.confirm({
					title: "提示",
					content: "确定要核销该订单吗？",
					cancelText: "取消",
					closable: true,
					maskClosable: true,
					onOk: function() {
						writeUpdate(row.order_id).then((res) => {
							self.$Message.success(res.msg);
							self.getList();
						});
					},
					onCancel: () => {},
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
								this.getList();
							})
							.catch((res) => {
								this.$Message.error(res.msg);
							});
						break;
					case "2":
						this.getData(row.id);
						break;
					case "3":
						this.$refs.record.modals = true;
						this.$refs.record.getList(row.id);
						break;
					case "4":
						this.$refs.remarks.modals = true;
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
								this.getList();
							})
							.catch((res) => {
								this.$Message.error(res.msg);
							});
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
								this.getList();
							})
							.catch((res) => {
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
								this.getList();
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
						this.delOrder(row, this.delfromData);
				}
			},
			// 获取详情表单数据
			getData(id) {
				getDataInfo(id)
					.then(async (res) => {
						this.$refs.detailss.modals = true;
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
			// 修改成功
			submitFail() {
				this.status = 0;
				this.getList();
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
			}
		},
	};
</script>

<style scoped lang="stylus">
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
			width: 36px;
			height: 36px;

			img {
				width: 100%;
				height: 100%;
			}
		}

		.tabBox_tit {
			width: 60%;
			font-size: 12px !important;
			margin: 0 2px 0 10px;
			letter-spacing: 1px;
			padding: 5px 0;
			box-sizing: border-box;
		}
	}

	.orderData>>>.ivu-table-cell {
		padding-left: 0 !important;
	}

	.vertical-center-modal {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.orderData .ivu-table {
		overflow: visible !important;
	}

	.orderData .ivu-table th {
		overflow: visible !important;
	}

	.orderData .ivu-table-header {
		overflow: visible !important;
	}

	/deep/.ivu-table-header {
		// overflow: visible;
	}

	/deep/.ivu-table th {
		overflow: visible;
	}

	/deep/.select-item:hover {
		background-color: #f3f3f3;
	}

	/deep/.select-on {
		display: block;
	}

	/deep/.select-item.on {
		background: #f3f3f3;
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
