<template>
    <div class="order-details pos-order-details" v-if="orderInfo">
        <div class="header acea-row row-middle">
            <div class="state">{{ title }}</div>
            <div class="data">
                <div class="order-num">订单：{{ orderInfo.order_id }}</div>
                <div>
                    <span class="time">{{ orderInfo._add_time }}</span>
                </div>
            </div>
        </div>
        <div
                class="remarks acea-row"
                v-if="$route.params.goname != 'looks'"
        >
            <span class="iconfontYI icon-zhinengkefu- mr10"></span>
            <span
                    class="line1"
                    style="text-align: left;width: 6.2rem;"
                    v-text="
          orderInfo.remark ? orderInfo.remark : '订单未备注，点击添加备注信息'
        "
                    @click="modify(1)"
            />
        </div>
        <div class="orderingUser acea-row row-middle" v-if="orderInfo.real_name">
            <span class="iconfontYI icon-yonghu2"></span>{{ orderInfo.real_name }}
        </div>
        <div class="address" v-if="orderInfo.product_type == 0 && orderInfo.delivery_type !='fictitious'">
            <div class="name">
                {{ orderInfo.real_name
                }}<span class="phone">{{ orderInfo.user_phone }}</span>
            </div>
            <div>{{ orderInfo.user_address }}</div>
        </div>
        <div class="line"><img src="../../../../assets/images/line.jpg"/></div>
        <div class="pos-order-goods">
            <div
                    class="goods acea-row row-between-wrapper"
                    v-for="(item, index) in orderInfo.cartInfo"
                    :key="index"
            >
                <div class="picTxt acea-row row-between-wrapper">
                    <div class="pictrue">
                        <img :src="item.productInfo.image"/>
                    </div>
                    <div class="text">
                        <div class="info line2">
													<span class="font-color-red" v-if="item.is_gift">[赠品]</span>
                            {{ item.productInfo.store_name }}
                        </div>
                        <div class="attr line1">{{ item.productInfo.attrInfo.suk }}</div>
                    </div>
                </div>
                <div class="money">
                    <div class="x-money">￥{{ item.productInfo.attrInfo.price }}</div>
                    <div class="num">x{{ item.cart_num }}</div>
                    <div class="y-money">￥{{ item.productInfo.ot_price }}</div>
                </div>
            </div>
			<div class="giveGoods">
				<div class="item acea-row row-between-wrapper" v-for="(item,index) in giveCartInfo" :key="index">
					<div class="picTxt acea-row row-middle">
						<div class="pictrue">
							<img :src="item.productInfo.attrInfo.image" v-if="item.productInfo.attrInfo" />
							<img :src="item.productInfo.image" v-else />
						</div>
						<div class="texts">
							<div class="name line1">[赠品]{{item.productInfo.store_name}}</div>
							<div class="limit line1" v-if="item.productInfo.attrInfo">{{item.productInfo.attrInfo.suk}}</div>
						</div>
					</div>
					<div class="num">x{{item.cart_num}}</div>
				</div>
				<div class="item acea-row row-between-wrapper" v-for="(item,index) in giveData.give_coupon" :key="index" v-if="giveData.give_coupon.length">
					<div class="picTxt acea-row row-middle">
						<div class="pictrue acea-row row-center-wrapper">
							<span class="iconfont iconpc-youhuiquan"></span>
						</div>
						<div class="texts">
							<div class="line1">[赠品]{{item.coupon_title}}</div>
						</div>
					</div>
				</div>
				<div class="item acea-row row-between-wrapper" v-if="giveData.give_integral>0">
					<div class="picTxt acea-row row-middle">
						<div class="pictrue acea-row row-center-wrapper">
							<span class="iconfont iconpc-jifen"></span>
						</div>
						<div class="texts">
							<div class="line1">[赠品]{{giveData.give_integral}}积分</div>
						</div>
					</div>
				</div>
			</div>
        </div>
        <div class="public-total">
            共{{ orderInfo.cart_num }}件商品，应支付
            <span class="money">￥{{ orderInfo.pay_price }}</span> ( 邮费 ¥{{
            orderInfo.pay_postage
            }}
            )
        </div>
        <div class='wrapper' v-if='orderInfo.delivery_type=="fictitious" && orderInfo.product_type!=1'>
            <div class='item acea-row row-between' v-if="orderInfo.fictitious_content">
                <div>虚拟备注：</div>
                <div class='conter'>{{orderInfo.fictitious_content}}</div>
            </div>
        </div>
        <div class='wrapper' v-if="orderInfo.virtual_info && orderInfo.product_type==1">
            <div class='item acea-row row-between'>
                <div>卡密发货：</div>
                <div class='conter'>{{orderInfo.virtual_info}}</div>
            </div>
        </div>
		<div class='wrapper' style="border-bottom: 1px solid #eee;" v-if="orderInfo.custom_form && orderInfo.custom_form.length">
			<div class='item acea-row row-between' v-for="(item,index) in orderInfo.custom_form" :key="index" v-if="item.value">
				<div>{{item.title}}：</div>
				<div v-if="item.label == 'img'" class='conter'>
					<div class='pictrue' v-for="(img,indexn) in item.value" :key="indexn">
						<img :src='img'/>
					</div>
				</div>
				<div v-if="item.label != 'img'" class='conter'>{{item.value}}</div>
			</div>
		</div>
        <div class="wrapper">
            <div class="item acea-row row-between">
                <div>订单编号：</div>
                <div class="conter acea-row row-middle row-right">
                    {{ orderInfo.order_id
                    }}
                    <span
                            class="copy copy-data"
                            :data-clipboard-text="orderInfo.order_id"
                    >复制</span
                    >
                </div>
            </div>
            <div class="item acea-row row-between">
                <div>支付时间：</div>
                <div class="conter">{{ orderInfo._pay_time }}</div>
            </div>
            <div class="item acea-row row-between">
                <div>支付状态：</div>
                <div class="conter">
                    {{ title }}
                </div>
            </div>
            <div class="item acea-row row-between">
                <div>支付方式：</div>
                <div class="conter">{{ orderInfo._status?orderInfo._status._payType:'' }}</div>
            </div>
			<div class="item acea-row row-between" v-if="orderInfo.mark">
				<div v-if="isReturen == 1">退款留言：</div>
				<div v-else>买家留言：</div>
				<div class="conter">{{ orderInfo.mark }}</div>
			</div>
			<div class="item acea-row row-between" v-if="orderInfo.refund_goods_explain">
				<div>退货留言：</div>
				<div class='conter'>{{orderInfo.refund_goods_explain}}</div>
			</div>
			<div class="item acea-row row-between" v-if="orderInfo.refund_img && orderInfo.refund_img.length">
				<div>退款凭证：</div>
				<div class="conter">
					<div class="pictrue" v-for="(item,index) in orderInfo.refund_img">
					   <img :src="item" mode="aspectFill"/>
					</div>
				</div>
			</div>
			<div class="item acea-row row-between" v-if="orderInfo.refund_goods_img && orderInfo.refund_goods_img.length">
				<div>退货凭证：</div>
				<div class="conter">
					<div class="pictrue" v-for="(item,index) in orderInfo.refund_goods_img">
					   <img :src="item" mode="aspectFill"/>
					</div>
				</div>
			</div>
        </div>
        <div class="wrapper">
            <div class="item acea-row row-between">
                <div>商品总价：</div>
                <div class="conter">￥{{(parseFloat(orderInfo.total_price)-parseFloat(orderInfo.vip_true_price)).toFixed(2)}}</div>
            </div>
			<div v-if="orderInfo.vip_true_price > 0" class='item acea-row row-between'>
				<div>会员商品优惠：</div>
				<div class='conter'>-￥{{parseFloat(orderInfo.vip_true_price).toFixed(2)}}</div>
			</div>
            <div class="item acea-row row-between">
                <div>优惠券抵扣：</div>
                <div class="conter">-￥{{ orderInfo.coupon_price }}</div>
            </div>
            <div class="item acea-row row-between">
                <div>运费：</div>
                <div class="conter">￥{{ orderInfo.pay_postage }}</div>
            </div>
			<div class='item acea-row row-between' v-for="(item,index) in orderInfo.promotions_detail" :key="index" v-if="parseFloat(item.promotions_price)">
				<div>{{item.title}}：</div>
				<div class='conter'>-￥{{parseFloat(item.promotions_price).toFixed(2)}}</div>
			</div>
            <div class="actualPay acea-row row-right">
                实付款：<span class="money font-color-red"
            >￥{{ orderInfo.pay_price }}</span
            >
            </div>
        </div>
        <div
                class="wrapper"
                v-if="
        orderInfo.deliveryType === 'express'"
        >
            <div class="item acea-row row-between">
                <div>配送方式：</div>
                <div class="conter" v-if="orderInfo.delivery_type === 'express'">
                    快递
                </div>
                <div class="conter" v-if="orderInfo.delivery_type === 'send'">送货</div>
            </div>
            <div class="item acea-row row-between">
                <div v-if="orderInfo.delivery_type === 'express'">快递公司：</div>
                <div v-if="orderInfo.delivery_type === 'send'">送货人：</div>
                <div class="conter">{{ orderInfo.deliveryName }}</div>
            </div>
            <div class="item acea-row row-between">
                <div v-if="orderInfo.delivery_type === 'express'">快递单号：</div>
                <div v-if="orderInfo.delivery_type === 'send'">送货人电话：</div>
                <div class="conter">
                    {{ orderInfo.delivery_id
                    }}<span
                        class="copy copy-data"
                        :data-clipboard-text="orderInfo.delivery_id"
                >复制</span
                >
                </div>
            </div>
        </div>
        <div style="height:1.2rem;"></div>
        <div
                class="footer acea-row row-right row-middle"
                v-if="$route.params.goname != 'looks'"
        >
            <div class="more"></div>
            <div class="bnt cancel" :class="openErp?'on':''" @click="modify(0)" v-if="types === 0">
                一键改价
            </div>
            <div class="bnt cancel" :class="openErp?'on':''" @click="modify(2)" v-if="types === -1">
                立即退款
            </div>
            <div class="bnt cancel" @click="modify(1)">订单备注</div>
            <div
                    class="bnt cancel"
					:class="openErp?'on':''"
                    v-if="orderInfo.pay_type === 'offline' && orderInfo.paid === 0"
                    @click="offlinePay"
            >
                确认付款
            </div>
            <router-link
                    class="bnt delivery"
					:class="openErp?'on':''"
					:disabled="openErp"
                    v-if="types === 1&& orderInfo.shipping_type !== 2 && (orderInfo.pinkStatus === null || orderInfo.pinkStatus === 2)"
                    :to="{path:`/kefu/orderDelivery/${orderInfo.id}/${orderInfo.order_id}`,query: {productType: orderInfo.product_type}}"
            >去发货
            </router-link
            >
        </div>
        <PriceChange
                :change="change"
                :orderInfo="orderInfo"
                v-on:closechange="changeclose($event)"
                @closeChange="closeChange($event)"
                :status="status"
        ></PriceChange>
    </div>
</template>
<script>
    import PriceChange from "../../components/PriceChange";
    import ClipboardJS from "clipboard";
    import {orderInfo,refundInfo} from '@/api/kefu';
	import { erpConfig } from "@/api/erp";
    import {required, num} from "@/utils/validate";
    import {validatorDefaultCatch} from "@/libs/dialog";

    export default {
        name: "AdminOrder",
        components: {
            PriceChange
        },
        props: {},
        data: function () {
            return {
				openErp:false,
				giveData:{
					give_integral:0,
					give_coupon:[]
				},
				giveCartInfo:[],
                order: false,
                change: false,
                orderId: '',
                orderInfo: {},
                status: 0,
                title: "",
                payType: "",
                types: "",
				isReturen:0
            };
        },
        watch: {
            "$route": function (newVal) {
                let that = this;
                if (newVal != undefined) {
                    that.orderId = newVal;
                    that.getIndex();
                }
            }
        },
        mounted: function () {
			this.getErpConfig();
            // this.orderId = this.$route.params.id;
            this.getIndex();
            this.$nextTick(function () {
                var copybtn = document.getElementsByClassName("copy-data");
                const clipboard = new ClipboardJS(copybtn);
                clipboard.on("success", () => {
                    this.$dialog.success("复制成功");
                });
            });
        },
        methods: {
			getErpConfig(){
				erpConfig().then(res=>{
					this.openErp = res.data.open_erp;
				}).catch(err=>{
					this.$Message.error(err.msg);
				})
			},
            more: function () {
                this.order = !this.order;
            },
            modify: function (status) {
				if(status !=1 && openErp) return
                this.change = true;
                this.status = status;
            },
            changeclose: function (msg) {
                this.change = msg;
                this.getIndex()
            },
            closeChange(msg) {
                this.change = msg;
            },
            getIndex: function () {
                let that = this;
								let isReturen = this.$route.params.goname || 0;
								let obj = '';
								this.isReturen = isReturen;
								if(isReturen == 1) {
									obj = refundInfo(this.$route.params.id);
								}else{
									obj = orderInfo(this.$route.params.id);
								}
                obj.then(
                    res => {
                        that.types = res.data.orderInfo._status._type;
                        that.title = res.data.orderInfo._status._title;
						that.giveData.give_coupon = res.data.orderInfo.give_coupon;
						that.giveData.give_integral = res.data.orderInfo.give_integral;
						let cartObj = [],giftObj = [];
						res.data.orderInfo.cartInfo.forEach((item, index) => {
							if(item.is_gift == 1){
								giftObj.push(item)
							}else{
								cartObj.push(item)
							}
						});
						res.data.orderInfo.cartInfo = cartObj;
						that.$set(that, 'giveCartInfo', giftObj);
						that.orderInfo = res.data.orderInfo;
                        this.$nextTick(function () {
                            let copybtn = document.getElementsByClassName("copy-data");
                            const clipboard = new ClipboardJS(copybtn);
                            clipboard.on("success", () => {
                                this.$dialog.success("复制成功");
                            });
                        });
                    },
                    err => {
                        that.$dialog.error(err.msg);
                    }
                );
            },
            offlinePay: function () {
				if(openErp) return
                setOfflinePay({orderId: this.orderInfo.orderId}).then(
                    res => {
                        this.$dialog.success(res.msg);
                        this.getIndex();
                    },
                    err => {
                        this.$dialog.error(err.msg);
                    }
                );
            }
        }
    };
</script>
<style scoped lang="less">
	.giveGoods{
		.item{
			padding: 0.14rem 0;
			border-top: 1px solid #eee;
			.picTxt{
				.pictrue{
					width: 0.76rem;
					height: 0.76rem;
					border-radius: 0.06rem;
					background-color: #F5F5F5;
					color: #2a7efb;
					.iconfont{
						font-size: 0.34rem;
					}
					img{
						width: 100%;
						height: 100%;
						border-radius: 0.06rem;
					}
					margin-right: 0.16rem;
				}
				.texts{
					width: 3.6rem;
					color: #999999;
					font-size: 0.2rem;
					.name{
						color: #333;
					}
					.limit{
						font-size: 0.2rem;
						margin-top: 0.04rem;
					}
				}
			}
			.num{
				color: #999999;
				font-size: 0.2rem;
			}
		}
	}
    input {
        display: block;
        height: 100%;
        background: none;
        color: inherit;
        opacity: 1;
        -webkit-text-fill-color: currentcolor;
        font: inherit;
        line-height: inherit;
        letter-spacing: inherit;
        text-align: inherit;
        text-indent: inherit;
        text-transform: inherit;
        text-shadow: inherit;
    }

    .pos-order-goods {
        padding: 0 0.3rem;
        background-color: #fff;
    }

    .pos-order-goods .goods {
        height: 1.85rem;
    }

    .pos-order-goods .goods ~ .goods {
        border-top: 1px dashed #e5e5e5;
    }

    .pos-order-goods .goods .picTxt {
        width: 5.15rem;
    }

    .pos-order-goods .goods .picTxt .pictrue {
        width: 1.3rem;
        height: 1.3rem;
    }

    .pos-order-goods .goods .picTxt .pictrue img {
        width: 100%;
        height: 100%;
        border-radius: 0.06rem;
    }

    .pos-order-goods .goods .picTxt .text {
        width: 3.65rem;
        height: 1.3rem;
    }

    .pos-order-goods .goods .picTxt .text .info {
        font-size: 0.28rem;
        color: #282828;
    }

    .pos-order-goods .goods .picTxt .text .attr {
        font-size: 0.2rem;
        color: #999;
        height: 0.8rem;
        line-height: 0.8rem;
				width: 3.5rem;
    }

    .pos-order-goods .goods .money {
        width: 1.64rem;
        text-align: right;
        font-size: 0.28rem;
    }

    .pos-order-goods .goods .money .x-money {
        color: #282828;
    }

    .pos-order-goods .goods .money .num {
        color: #ff9600;
        margin: 0.05rem 0;
    }

    .pos-order-goods .goods .money .y-money {
        color: #999;
        text-decoration: line-through;
    }

    .order-details .header {
        padding: 0 0.3rem;
        height: 1.5rem;
    }

    .order-details .header.on {
        background-color: #666 !important;
    }

    .order-details .header .pictrue {
        width: 1.1rem;
        height: 1.1rem;
    }

    .order-details .header .pictrue img {
        width: 100%;
        height: 100%;
    }

    .order-details .header .data {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.24rem;
        margin-left: 0.27rem;
    }

    .order-details .header.on .data {
        margin-left: 0;
    }

    .order-details .header .data .state {
        font-size: 0.3rem;
        font-weight: bold;
        color: #fff;
        margin-bottom: 0.07rem;
    }

    .order-details .nav {
        background-color: #fff;
        font-size: 0.26rem;
        color: #282828;
        padding: 0.25rem 0;
    }

    .order-details .nav .navCon {
        padding: 0 0.4rem;
    }

    .order-details .nav .navCon .on {
        font-weight: bold;
        color: #e93323;
    }

    .order-details .nav .progress {
        padding: 0 0.65rem;
        margin-top: 0.1rem;
    }

    .order-details .nav .progress .line {
        width: 1rem;
        height: 0.02rem;
        background-color: #939390;
    }

    .order-details .nav .progress .iconfontYI {
        font-size: 0.25rem;
        color: #939390;
        margin-top: -0.02rem;
        width: 0.3rem;
        height: 0.3rem;
        line-height: 0.33rem;
        text-align: center;
        margin-right: 0 !important;
    }

    .order-details .address {
        font-size: 0.26rem;
        color: #868686;
        background-color: #fff;
        padding: 0.25rem 0.3rem 0.3rem 0.3rem;
    }

    .order-details .address .name {
        font-size: 0.3rem;
        color: #282828;
        margin-bottom: 0.1rem;
    }

    .order-details .address .name .phone {
        margin-left: 0.4rem;
    }

    .order-details .line {
        width: 100%;
        height: 0.03rem;
    }

    .order-details .line img {
        width: 100%;
        height: 100%;
        display: block;
    }

    .order-details .wrapper {
        background-color: #fff;
        margin-top: 0.12rem;
        padding: 0.3rem;
    }

    .order-details .wrapper .item {
        font-size: 0.28rem;
        color: #282828;
    }

    .order-details .wrapper .item ~ .item {
        margin-top: 0.2rem;
    }

    .order-details .wrapper .item .conter {
        color: #868686;
        width: 4.5rem;
		display: flex;
		flex-wrap: nowrap;
		justify-content: flex-end;
		text-align: right;
		.pictrue{
			width: 0.8rem;
			height: 0.8rem;
			margin-left: 0.06rem;
			img{
				width: 100%;
				height: 0.8rem;
				border-radius: 0.06rpx;
			}
		}
    }

    .order-details .wrapper .item .conter .copy {
        font-size: 0.2rem;
        color: #333;
        border-radius: 0.03rem;
        border: 1px solid #666;
        padding: 0.03rem 0.15rem;
        margin-left: 0.24rem;
    }

    .order-details .wrapper .actualPay {
        border-top: 0.01rem solid #eee;
        margin-top: 0.3rem;
        padding-top: 0.3rem;
    }

    .order-details .wrapper .actualPay .money {
        font-weight: bold;
        font-size: 0.3rem;
    }

    .order-details .footer {
        width: 100%;
        height: 1rem;
        position: fixed;
        bottom: 0;
        left: 0;
        background-color: #fff;
        padding: 0 0.3rem;
        border-top: 1px solid #eee;
    }

    .order-details .footer .bnt {
        width: auto;
        height: 0.6rem;
        text-align: center;
        line-height: 0.6rem;
        border-radius: 0.5rem;
        color: #fff;
        font-size: 0.27rem;
        padding: 0 3%;
    }

    .order-details .footer .bnt.cancel {
        color: #aaa;
        border: 1px solid #ddd;
    }
	
	.order-details .footer .bnt.on{
		color: #c5c8ce!important;
		background: #f7f7f7!important;
		border: 1px solid #dcdee2!important;
	}

    .order-details .footer .bnt.default {
        color: #444;
        border: 1px solid #444;
    }

    .order-details .footer .bnt ~ .bnt {
        margin-left: 0.18rem;
    }

    .pos-order-details .header {
        background: linear-gradient(to right, #2291f8 0%, #1cd1dc 100%);
        background: -webkit-linear-gradient(to right, #2291f8 0%, #1cd1dc 100%);
        background: -moz-linear-gradient(to right, #2291f8 0%, #1cd1dc 100%);
    }

    .pos-order-details .header .state {
        font-size: 0.3rem;
        color: #fff;
    }

    .pos-order-details .header .data {
        font-size: 0.2rem;
    }

    .pos-order-details .header .data .order-num {
        font-size: 0.27rem;
        margin-bottom: 0.08rem;
    }

    .pos-order-details .remarks {
        width: 100%;
        height: 0.86rem;
        background-color: #fff;
        padding: 0 0.3rem;
        line-height: 0.86rem
    }

    .pos-order-details .remarks .iconfontYI {
        font-size: 0.4rem;
        color: #2a7efb;
    }

    .pos-order-details .remarks input {
        width: 6.3rem;
        height: 100%;
        font-size: 0.3rem;
    }

    .pos-order-details .remarks input::placeholder {
        color: #666;
    }

    .pos-order-details .orderingUser {
        font-size: 0.26rem;
        color: #282828;
        padding: 0 0.3rem;
        height: 0.67rem;
        background-color: #fff;
        margin-top: 0.16rem;
        border-bottom: 1px solid #f5f5f5;
    }

    .pos-order-details .orderingUser .iconfontYI {
        font-size: 0.4rem;
        color: #2a7efb;
        margin-right: 0.15rem;
    }

    .pos-order-details .address {
        margin-top: 0;
    }

    .pos-order-details .pos-order-goods {
        margin-top: 0.17rem;
    }

    .pos-order-details .footer .more {
        font-size: 0.27rem;
        color: #aaa;
        width: 1rem;
        height: 0.64rem;
        text-align: center;
        line-height: 0.64rem;
        margin-right: 0.25rem;
        position: relative;
    }

    .pos-order-details .footer .delivery {
        background: linear-gradient(to right, #2291f8 0%, #1cd1dc 100%);
        background: -webkit-linear-gradient(to right, #2291f8 0%, #1cd1dc 100%);
        background: -moz-linear-gradient(to right, #2291f8 0%, #1cd1dc 100%);
    }

    .pos-order-details .footer .more .order .arrow {
        width: 0;
        height: 0;
        border-left: 0.11rem solid transparent;
        border-right: 0.11rem solid transparent;
        border-top: 0.2rem solid #e5e5e5;
        position: absolute;
        left: 0.15rem;
        bottom: -0.18rem;
    }

    .pos-order-details .footer .more .order .arrow:before {
        content: '';
        width: 0;
        height: 0;
        border-left: 0.09rem solid transparent;
        border-right: 0.09rem solid transparent;
        border-top: 0.19rem solid #fff;
        position: absolute;
        left: -0.1rem;
        bottom: 0;
    }

    .pos-order-details .footer .more .order {
        width: 2rem;
        background-color: #fff;
        border: 1px solid #eee;
        border-radius: 0.1rem;
        position: absolute;
        top: -2rem;
        z-index: 9;
    }

    .pos-order-details .footer .more .order .item {
        height: 0.77rem;
        line-height: 0.77rem;
    }

    .pos-order-details .footer .more .order .item ~ .item {
        border-top: 1px solid #f5f5f5;
    }

    .pos-order-details .footer .more .moreName {
        width: 100%;
        height: 100%;
    }

    .public-total {
        font-size: 0.28rem;
        color: #282828;
        border-top: 1px solid #eee;
        height: 0.92rem;
        line-height: 0.92rem;
        text-align: right;
        padding: 0 0.3rem;
        background-color: #fff;
    }

    .public-total .money {
        color: #ff4c3c;
    }
</style>
