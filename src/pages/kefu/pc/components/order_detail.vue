<template>
    <div class="order_detail" v-if="orderDetail.userInfo">
        <div class="msg-box" v-if="orderDetail.product_type == 0">
            <div class="box-title">收货信息</div>
            <div class="msg-wrapper">
                <div class="msg-item">
                    <div class="item">
                        <span>用户昵称：</span>{{orderDetail.userInfo.nickname}}
                    </div>
                    <div class="item">
                        <span>收货人：</span>{{orderDetail.orderInfo.real_name}}
                    </div>
                </div>
                <div class="msg-item">
                    <div class="item">
                        <span>联系电话：</span>{{orderDetail.orderInfo.user_phone}}
                    </div>
                    <div class="item">
                        <span>收货地址：</span>{{orderDetail.orderInfo.user_address}}
                    </div>
                </div>
            </div>
        </div>
        <div class="msg-box">
            <div class="box-title">订单信息</div>
            <div class="msg-wrapper">
                <div class="msg-item">
                    <div class="item">
                        <span>订单ID：</span>{{orderDetail.orderInfo.order_id}}
                    </div>
                    <div class="item" style="color: red">
                        <span style="color: red">订单状态：</span>{{orderDetail.orderInfo._status._title}}
                    </div>
                </div>
                <div class="msg-item">
                    <div class="item">
                        <span>商品总数：</span>{{orderDetail.orderInfo.total_num}}
                    </div>
                    <div class="item">
                        <span>商品总价：</span>{{(parseFloat(orderDetail.orderInfo.total_price)-parseFloat(orderDetail.orderInfo.vip_true_price)).toFixed(2)}}
                    </div>
                </div>
                <div class="msg-item">
                    <div class="item">
                        <span>交付邮费：</span>{{orderDetail.orderInfo.pay_postage}}
                    </div>
                    <div class="item">
                        <span>优惠券金额：</span>{{orderDetail.orderInfo.coupon_price}}
                    </div>
                </div>
                <div class="msg-item">
                    <div class="item">
                        <span>会员商品优惠：</span>{{orderDetail.orderInfo.vip_true_price||0.00}}
                    </div>
                    <div class="item">
                        <span>积分抵扣：</span>{{orderDetail.orderInfo.deduction_price||0.00}}
                    </div>
                </div>
                <div class="msg-item">
                    <div class="item">
                        <span>实际支付：</span>{{orderDetail.orderInfo.pay_price}}
                    </div>
                    <div class="item">
                        <span>创建时间：</span>{{orderDetail.orderInfo.add_time }}
                    </div>
                </div>
                <div class="msg-item">
                    <div class="item">
                        <span>支付方式：</span>{{orderDetail.orderInfo._status._payType}}
                    </div>
                    <div class="item">
                        <span>推广人：</span>{{orderDetail.userInfo.spread_name}}
                    </div>
                </div>
                <div class="msg-item">
                    <div class="item">
                        <span>商家备注：</span>{{orderDetail.orderInfo.remark}}
                    </div>
					<div class="item acea-row">
					    <span v-if="isReturen">退款留言：</span>
						<span v-else>买家备注：</span>
						<div class="msgCon">{{orderDetail.orderInfo.mark}}</div>
					</div>
                </div>
				<div class="msg-item">
				    <div class="item acea-row" v-if="orderDetail.orderInfo.refund_goods_explain">
				        <span>退货留言：</span>
						<div class="msgCon">{{orderDetail.orderInfo.refund_goods_explain}}</div>
				    </div>
					<div class="item" v-if="orderDetail.orderInfo.refund_img && orderDetail.orderInfo.refund_img.length">
					    <span>退款凭证：</span>
						<div class="pictrue" v-for="(item,index) in orderDetail.orderInfo.refund_img">
							<img :src="item" />
						</div>
					</div>
				</div>
                <div class="msg-item">
                    <div class="item acea-row" v-if="orderDetail.orderInfo.delivery_type=='fictitious' && orderDetail.orderInfo.fictitious_content && orderDetail.orderInfo.product_type!=1">
                        <span>虚拟备注：</span>
                        <div class="msgCon">{{orderDetail.orderInfo.fictitious_content}}</div>
                    </div>
                    <div class="item acea-row" v-if="orderDetail.orderInfo.virtual_info && orderDetail.orderInfo.product_type==1">
                        <span>卡密发货：</span>
                        <div class="msgCon">{{orderDetail.orderInfo.virtual_info}}</div>
                    </div>
                </div>
				<div class="msg-item">
					<div class="item" v-if="orderDetail.orderInfo.refund_goods_img && orderDetail.orderInfo.refund_goods_img.length">
					    <span>退货凭证：</span>
						<div class="pictrue" v-for="(item,index) in orderDetail.orderInfo.refund_goods_img">
							<img :src="item" />
						</div>
					</div>
				</div>
            </div>
        </div>
		<div class="msg-box" v-if="orderDetail.orderInfo.promotions_detail && orderDetail.orderInfo.promotions_detail.length">
		    <div class="box-title">优惠活动</div>
		    <div class="msg-wrapper">
		        <div class="msg-item">
		        	<div class="item" v-for="(item,index) in orderDetail.orderInfo.promotions_detail">
		        		<span>{{item.title}}：</span>-￥{{parseFloat(item.promotions_price).toFixed(2)}}
		        	</div>
		        </div>
		    </div>
		</div>
        <div class="msg-box" style="border: none;" v-if="orderDetail.orderInfo.custom_form && orderDetail.orderInfo.custom_form.length">
            <div class="box-title">自定义留言</div>
            <div class="msg-wrapper">
                <div class="msg-item" v-for="(item,index) in customForm" :key="index">
                    <div class="item acea-row" v-for="(j,indexn) in item.list" :key="indexn">
                        <span>{{j.title}}：</span>
						<div class="msgCon" v-if="j.label !='img'">{{j.value || '暂无'}}</div>
						<div class="pictrue" v-if="j.label =='img'" v-for="(e,indexs) in j.value" :key="indexs">
							<img :src="e" />
						</div>
                    </div>
                </div>
            </div>
        </div>
		<div class="goods-box">
            <Table :columns="columns1" :data="orderList">
                <template slot-scope="{ row, index }" slot="id">
                    {{row.productInfo.id}}
                </template>
                <template slot-scope="{ row, index }" slot="name">
                    <div class="product_info">
                        <img :src="row.productInfo.image" alt="">
                        <p><span class="font-color-red" v-if="row.is_gift">[赠品]</span>{{row.productInfo.store_name}}</p>
                    </div>
                </template>
                <template slot-scope="{ row, index }" slot="className">
                    {{row.class_name}}
                </template>
                <template slot-scope="{ row, index }" slot="price">
                    {{row.productInfo.attrInfo.price}}
                </template>
                <template slot-scope="{ row, index }" slot="total_num">
                    {{row.cart_num}}
                </template>
            </Table>
        </div>
    </div>
</template>

<script>
    import { orderInfo,refundInfo } from '@/api/kefu'
    export default {
        name: "order_detail",
        props:{
            orderId:{
                type:String | Number,
                default:''
            },
			isReturen:{
				type:Boolean,
				default:false
			}
        },
        data(){
            return {
                orderDetail:{},
                orderList:[],
                columns1: [
                    {
                        title: '商品ID',
                        slot: 'id',
                        maxWidth:80
                    },
                    {
                        title: '商品名称',
                        slot: 'name',
                        minWidth: 160
                    },
                    {
                        title: '商品分类',
                        slot: 'className'
                    },
                    {
                        title: '商品售价',
                        slot: 'price'
                    },
                    {
                        title: '商品数量',
                        slot: 'total_num'
                    }
                ],
				customForm:[]
            }
        },
        mounted() {
            this.getOrderInfo()
        },
        methods:{
            getOrderInfo(){
				(this.isReturen? refundInfo(this.orderId):orderInfo(this.orderId))
                .then(res=>{
                    res.data.orderInfo.add_time =  this.$moment(parseInt(res.data.orderInfo.add_time)*1000).format('YYYY-MM-DD')
                    this.orderDetail = res.data
                    this.orderList = res.data.orderInfo.cartInfo
					let customForm = res.data.orderInfo.custom_form
					let count = Math.ceil(customForm.length / 2);
					let customArray = new Array();
					for (let i = 0; i < count; i++) {
						let list = customForm.slice(i * 2, i * 2 + 2);
						if (list.length)
							customArray.push({
								list: list
							});
					}
					this.customForm = customArray;
                })
            }
        }
    }
</script>

<style lang="stylus" scoped>
 .order_detail .msg-box .msg-wrapper .msg-item{
	 flex-wrap wrap;
 }
 .order_detail .msg-box .msg-wrapper .msg-item .item{
	 flex 1 50%!important;
 }
 .order_detail .msg-box .msg-wrapper .msg-item .pictrue{
	 width 40px;
	 height 40px;
	 display inline-block;
	 vertical-align top;
	 margin-right 10px;
 }
 .order_detail .msg-box .msg-wrapper .msg-item .msgCon{
	 width 200px;
 }
 .order_detail .msg-box .msg-wrapper .msg-item .pictrue img{
	 width 100%;
	 height 100%;
	 border-radius 4px;
 }	
.order_detail
    .msg-box
        border-bottom 1px solid #E8EAED
        .box-title
            padding-top 20px
            font-size 16px
            color #333
        .msg-wrapper
            margin-top 15px
            padding-bottom 10px
            .msg-item
                display flex
                .item
                    flex 1
                    margin-bottom 15px
                    span
                        color #333
        &:first-child .box-title
            padding-top 0
    .product_info
        display flex
        align-items center
        img
            width 36px
            height 36px
            border-radius 4px
            margin-right 10px


</style>
