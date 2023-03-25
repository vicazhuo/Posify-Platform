<template>
	<div>
		<Drawer :closable="false" width="1000" class-name="order_box" v-model="modals" :styles="{ padding: 0 }">
		    <div v-if="orderDatalist">
		        <div class="head">
		            <div class="full">
		                <Icon custom="iconfont icondingdan" size="60"/>
		                <div class="text">
		                    <div class="title">积分订单</div>
		                    <div>订单编号：{{ orderDatalist.orderInfo.order_id }}</div>
		                </div>
		                <div>
		                    <Button :class="openErp?'on':''" :disabled="openErp" v-if="orderData.status === 1" @click="sendOrder">发送货</Button>
		                    <Button :class="openErp?'on':''" :disabled="openErp" v-if="orderData.status === 2" @click="delivery">配送信息</Button>
		                    <Button v-if="orderData.status >= 1" @click="changeMenu('10')">小票打印</Button>
							<Button :class="openErp?'on':''" :disabled="openErp" v-if="orderData.status >= 1 && orderData.express_dump" @click="changeMenu('11')">电子面单打印</Button>
		                    <Button :class="openErp?'on':''" :disabled="openErp" v-if="orderData.is_del == 1" @click="changeMenu('9')">删除订单</Button>
		                    <Dropdown @on-click="changeMenu" v-if="orderData.status !== 4">
		                        <Button icon="ios-more"></Button>
		                        <DropdownMenu slot="list">
		                            <DropdownItem v-if="orderData.status !== 4" name="4">订单备注</DropdownItem>
		                            <DropdownItem :disabled="openErp" v-if="orderData.status === 2" name="8">已收货</DropdownItem>
		                        </DropdownMenu>
		                    </Dropdown>
		                </div>
		            </div>
		            <ul class="list">
		                <li class="item">
		                    <div class="title">订单状态</div>
												<div class="value1">{{orderDatalist.orderInfo.status_name || '-'}}</div>
		                </li>
		                <li class="item">
		                    <div class="title">兑换积分</div>
		                    <div>¥{{ orderDatalist.orderInfo.price || '-'}}</div>
		                </li>
		                <!-- <li class="item">
		                    <div class="title">支付方式</div>
		                    <div>{{ orderDatalist.orderInfo._status._payType }}</div>
		                </li> -->
		                <li class="item">
		                    <div class="title">兑换时间</div>
		                    <div>{{ orderDatalist.orderInfo.add_time || '-' }}</div>
		                </li>
		            </ul>
		        </div>
		        <Tabs v-model="activeName">
		            <TabPane label="订单信息" name="detail">
		                <div class="section">
		                    <div class="title">用户信息</div>
		                    <ul class="list">
		                        <li class="item">
		                            <div>用户昵称：</div>
		                            <div class="value">{{orderDatalist.userInfo.uid?orderDatalist.userInfo.nickname:'游客'}}</div>
		                        </li>
		                        <li class="item">
		                            <div>绑定电话：</div>
		                            <div class="value">{{ orderDatalist.userInfo.phone || '-'}}</div>
		                        </li>
		                    </ul>
		                </div>
		                <div class="section">
		                    <div class="title">收货信息</div>
		                    <ul class="list">
		                        <!-- <li class="item">
		                            <div>用户昵称：</div>
		                            <div class="value">{{orderDatalist.userInfo.uid?orderDatalist.userInfo.nickname:'游客'}}</div>
		                        </li> -->
		                        <li class="item">
		                            <div>收货人：</div>
		                            <div class="value">{{ orderDatalist.orderInfo.real_name || '-'}}</div>
		                        </li>
		                        <li class="item">
		                            <div>收货电话：</div>
		                            <div class="value">{{ orderDatalist.orderInfo.user_phone || '-'}}</div>
		                        </li>
		                        <li class="item">
		                            <div>收货地址：</div>
		                            <div class="value">{{ orderDatalist.orderInfo.user_address || '-'}}</div>
		                        </li>
		                    </ul>
		                </div>
		                <div class="section" v-if="orderDatalist.orderInfo.fictitious_content && orderDatalist.orderInfo.product_type !=1">
		                    <div class="title">虚拟发货</div>
		                    <ul class="list">
		                        <li class="item">
		                            <div class="value">{{ orderDatalist.orderInfo.fictitious_content }}</div>
		                        </li>
		                    </ul>
		                </div>
		                <div class="section" v-if="orderDatalist.orderInfo.product_type ==1">
		                    <div class="title">卡密发货</div>
							<div v-if='orderDatalist.orderInfo.virtual.length'>
								<div class="list" v-for="(item,index) in orderDatalist.orderInfo.virtual" :key='index'>
								    <div class="item">
								        <div>卡号{{index+1}}：</div>
								        <div class="value">{{item.card_no}}</div>
								    </div>
									<div class="item">
									    <div>密码{{index+1}}：</div>
									    <div class="value">{{item.card_pwd}}</div>
									</div>
								</div>
							</div>
							<ul class="list" v-else>
							    <li class="item">
							        <div class="value">{{ orderDatalist.orderInfo.virtual_info || '-'}}</div>
							    </li>
							</ul>
		                </div>
		                <div class="section">
		                    <div class="title">订单信息</div>
		                    <ul class="list">
		                        <li class="item">
		                            <div>创建时间：</div>
		                            <div class="value">{{ orderDatalist.orderInfo._add_time || '-'}}</div>
		                        </li>
		                        <li class="item">
		                            <div>商品总数：</div>
		                            <div class="value">{{ orderDatalist.orderInfo.total_num || '-'}}</div>
		                        </li>
		                        <li class="item">
		                            <div>商品总积分：</div>
		                            <div class="value">{{ orderDatalist.orderInfo.total_price || '-'}}</div>
		                        </li>
		                    </ul>
		                </div>
						<div class="section" v-if="orderDatalist.orderInfo.delivery_type==='express'">
						    <div class="title">物流信息</div>
						    <ul class="list">
						        <li class="item">
						            <div>快递公司：</div>
						            <div class="value">{{orderDatalist.orderInfo.delivery_name || '-'}}</div>
						        </li>
								<li class="item">
								    <div>快递单号：</div>
								    <div class="value">{{orderDatalist.orderInfo.delivery_id}}<span class="logisticsLook" @click="openLogistics">查询</span></div>
								</li>
						    </ul>
						</div>
						<div class="section" v-if="orderDatalist.orderInfo.delivery_type==='send'">
						    <div class="title">配送信息</div>
						    <ul class="list">
						        <li class="item">
						            <div>送货人姓名：</div>
						            <div class="value">{{orderDatalist.orderInfo.delivery_name || '-'}}</div>
						        </li>
								<li class="item">
								    <div>送货人电话：</div>
								    <div class="value">{{orderDatalist.orderInfo.delivery_id || '-'}}</div>
								</li>
						    </ul>
						</div>
		                <div v-if="orderDatalist.orderInfo.custom_form.length" class="section">
		                    <div class="title">自定义留言</div>
		                    <ul class="list">
		                        <li v-for="(item, index) in orderDatalist.orderInfo.custom_form" :key="index" class="item" v-if="item.value">
		                            <div>{{ item.title }}：</div>
		                            <div v-if="item.label === 'img'" class="value">
		                                <div v-for="(img, i) in item.value" :key="i" class="image" v-viewer>
		                                    <img v-lazy="img">
		                                </div>
		                            </div>
		                            <div v-else class="value">{{ item.value || '-' }}</div>
		                        </li>
		                    </ul>
		                </div>
		                <div class="section" v-if="orderDatalist.orderInfo.mark">
		                    <div class="title">买家留言</div>
		                    <ul class="list">
		                        <li class="item">
		                            <div class="value">{{orderDatalist.orderInfo.mark}}</div>
		                        </li>
		                    </ul>
		                </div>
		                <div class="section" v-if="orderDatalist.orderInfo.remark">
		                    <div class="title">订单备注</div>
		                    <ul class="list">
		                        <li class="item">
		                            <div>备注：</div>
		                            <div class="value">{{ orderDatalist.orderInfo.remark || '-' }}</div>
		                        </li>
		                    </ul>
		                </div>
		            </TabPane>
		            <TabPane label="商品信息" name="product">
		                <Table :columns="columns1" :data="orderDatalist.orderInfo.cart_info" border highlight-row>
		                    <template slot-scope="{ row }" slot="product">
		                        <div class="product">
		                            <div class="image" v-viewer>
		                                <img v-lazy="row.attrInfo ? row.attrInfo.image : row.image">
		                            </div>
		                            <div class="title">{{ row.title }} | {{ row.attrInfo ? row.attrInfo.suk : '' }}</div>
		                        </div>
		                    </template>
		                </Table>
		            </TabPane>
		            <TabPane label="订单记录" name="record">
		                <Table :columns="columns2" :data="recordData" border :loading="loading" no-data-text="暂无数据" highlight-row   no-filtered-data-text="暂无筛选结果"></Table>
		            </TabPane>
		        </Tabs>
		    </div>
		</Drawer>
		<Modal v-model="modal2" scrollable title="物流查询"  width="350" class="order_box2">
		    <div class="logistics acea-row row-top" v-if="orderDatalist">
		        <div class="logistics_img"><img src="../../../../assets/images/expressi.jpg"></div>
		        <div class="logistics_cent">
		            <span>物流公司：{{orderDatalist.orderInfo.delivery_name}}</span>
		            <span>物流单号：{{orderDatalist.orderInfo.delivery_id}}</span>
		        </div>
		    </div>
		    <div class="acea-row row-column-around trees-coadd">
		        <div class="scollhide">
		            <Timeline>
		                <TimelineItem v-for="(item,i) in result" :key="i">
		                    <p class="time" v-text="item.time"></p>
		                    <p class="content" v-text="item.status"></p>
		                </TimelineItem>
		            </Timeline>
		        </div>
		    </div>
		</Modal>
	</div>
</template>
<!-- <template>
    <div  v-if="orderDatalist">
        <Modal v-model="modals"  scrollable title="订单信息" width="700" class="order_box" footer-hide>
            <Card :bordered="false" dis-hover class="i-table-no-border">
              <div class="ivu-description-list-title">收货信息</div>
              <Row class="mb10">
                <Col span="12">用户昵称：{{orderDatalist.userInfo.nickname}}</Col>
                <Col span="12">收货人：{{orderDatalist.orderInfo.real_name}}</Col>
              </Row>
              <Row class="mb10">
                <Col span="12">联系电话：{{orderDatalist.orderInfo.user_phone}}</Col>
                <Col span="12">收货地址：{{orderDatalist.orderInfo.user_address}}</Col>
              </Row>
              <Divider style="margin: 20px 0 !important;"/>
              <div class="ivu-description-list-title">订单信息</div>
              <Row class="mb10">
                <Col span="12">订单ID：{{orderDatalist.orderInfo.order_id}}</Col>
                <Col span="12" class="fontColor1">订单状态：{{orderDatalist.orderInfo.status_name}}</Col>
              </Row>
              <Row class="mb10">
                <Col span="12">商品名称：{{ orderDatalist.orderInfo.store_name + " | "}}{{orderDatalist.orderInfo.suk ? orderDatalist.orderInfo.suk : ""}}</Col>
              </Row>
              <Row class="mb10">
                <Col span="12">商品总数：{{orderDatalist.orderInfo.total_num}}</Col>
                <Col span="12">商品总积分：{{orderDatalist.orderInfo.total_price}}</Col>
              </Row>
              <Row class="mb10">
                <Col span="12" class="mb10">创建时间：{{orderDatalist.orderInfo.add_time}}</Col>
                <Col span="12" class="mb10" v-if="orderDatalist.orderInfo.remark">商家备注：{{orderDatalist.orderInfo.remark}}</Col>
                <Col span="12" class="mb10" v-if="orderDatalist.orderInfo.fictitious_content">虚拟发货备注：{{orderDatalist.orderInfo.fictitious_content}}</Col>
              </Row>
              <div v-if="orderDatalist.orderInfo.delivery_type==='express'">
                <Divider style="margin: 20px 0 !important;"/>
                <div class="ivu-description-list-title">物流信息</div>
                <Row class="mb10">
                  <Col span="12">快递公司：{{orderDatalist.orderInfo.delivery_name}}</Col>
                  <Col span="12">快递单号：{{orderDatalist.orderInfo.delivery_id}} <Button type="info" size="small" @click="openLogistics">物流查询</Button></Col>
                </Row>
              </div>
              <div v-if="orderDatalist.orderInfo.delivery_type==='send'">
                <Divider style="margin: 20px 0 !important;"/>
                <div class="ivu-description-list-title">配送信息</div>
                <Row class="mb10">
                  <Col span="12">送货人姓名：{{orderDatalist.orderInfo.delivery_name}}</Col>
                  <Col span="12">送货人电话：{{orderDatalist.orderInfo.delivery_id}}</Col>
                </Row>
              </div>
              <div v-if="orderDatalist.orderInfo.mark">
                <Divider style="margin: 20px 0 !important;" v-if="orderDatalist.orderInfo.mark"/>
                <div class="ivu-description-list-title" v-if="orderDatalist.orderInfo.mark">备注信息</div>
                <Row class="mb10">
                  <Col span="12" class="fontColor2">{{orderDatalist.orderInfo.mark}}</Col>
                </Row>
              </div>
            </Card>
        </Modal>
        <Modal v-model="modal2" scrollable title="物流查询"  width="350" class="order_box2">
            <div class="logistics acea-row row-top">
                <div class="logistics_img"><img src="../../../../assets/images/expressi.jpg"></div>
                <div class="logistics_cent">
                    <span>物流公司：{{orderDatalist.orderInfo.delivery_name}}</span>
                    <span>物流单号：{{orderDatalist.orderInfo.delivery_id}}</span>
                </div>
            </div>
            <div class="acea-row row-column-around trees-coadd">
                <div class="scollhide">
                    <Timeline>
                        <TimelineItem v-for="(item,i) in result" :key="i">
                            <p class="time" v-text="item.time"></p>
                            <p class="content" v-text="item.status"></p>
                        </TimelineItem>
                    </Timeline>
                </div>
            </div>
        </Modal>
    </div>
</template> -->

<script>
    import { getExpress, getIntegralOrderRecord } from '@/api/marketing';
    export default {
        name: 'orderDetails',
        data () {
            return {
                modal2: false,
                modals: false,
                grid: {
                    xl: 8,
                    lg: 8,
                    md: 12,
                    sm: 24,
                    xs: 24
                },
                result: [],
								columns1: [
								        {
								            title: '商品信息',
								            slot: 'product',
								            align: 'center',
								            minWidth: 400
								        },
								        {
								            title: '积分兑换',
								            key: 'price',
								            align: 'center'
								        },
								        {
								            title: '数量',
								            key: 'cart_num',
								            align: 'center'
								        },
								        {
								            title: '小计',
								            align: 'center',
								            render: (h, params) => {
									return h('div', params.row.price * params.row.cart_num);
								}
								        }
								    ],
								    columns2: [
								        {
								            title: '订单ID',
								            key: 'oid',
								            align: 'center',
								            minWidth: 40
								        },
								        {
								            title: '操作记录',
								            key: 'change_message',
								            align: 'center',
								            minWidth: 280
								        },
								        {
								            title: '操作时间',
								            key: 'change_time',
								            align: 'center',
								            minWidth: 100
								        }
								    ],
								    recordData: [],
										activeName:'detail',
										orderData:{}
            }
        },
        props: {
            orderDatalist: Object,
            orderId: Number,
			rowActive: Object,
			openErp:{
				type:Boolean,
				default:false,
			}
        },
				watch: {
					orderDatalist (value) {
						this.orderData = value.orderInfo;
						this.getList(value.orderInfo.id);
					}
				},
        methods: {
            openLogistics () {
                this.getOrderData()
                this.modal2 = true;
            },
            // 获取订单物流信息
            getOrderData () {
                getExpress(this.orderId).then(async res => {
                    this.result = res.data.result;
                }).catch(res => {
                    this.$Message.error(res.msg);
                })
            },
						getList (id) {
						    let data = {
						        id: id,
						        datas: this.page
						    }
						    this.loading = true;
						    getIntegralOrderRecord(data).then(async res => {
						        this.recordData = res.data;
						        this.loading = false;
						    }).catch(res => {
						        this.loading = false;
						        this.$Message.error(res.msg);
						    })
						},
						changeMenu (value) {
						    this.$parent.changeMenu(this.rowActive, value);
						},
						sendOrder () {
						    this.$parent.sendOrder(this.rowActive);
						},
						delivery () {
						    this.$parent.delivery(this.rowActive);
						}
        },
        computed: {
        }
    }
</script>

<style scoped lang="stylus">
	.order_box .head .full .ivu-btn.on{
		color: #c5c8ce!important;
		background-color: #f7f7f7!important;
		border-color: #dcdee2!important;
	}
</style>
<style scoped lang="stylus">
    .ivu-description-list-title {
        margin-bottom: 16px;
        color: #17233d;
        font-weight: 500;
        font-size: 14px;
    }
	.logisticsLook{
		font-size 13px;
		margin-left 10px;
		color #1890FF;
		cursor pointer;
	}
	.value{
		word-break:break-all
	}
	/deep/.ivu-icon-ios-more {
		font-size 20px;
	}
    .logistics
        align-items: center
        padding: 10px 0px
        .logistics_img
            width 45px
            height 45px
            margin-right: 12px
            img
             width 100%
             height 100%
        .logistics_cent
            span
              display block
              font-size 12px
    .trees-coadd
        width: 100%;
        height: 400px;
        border-radius: 4px;
        overflow: hidden;
        .scollhide
            width: 100%;
            height: 100%;
            overflow: auto;
            margin-left: 18px;
            padding: 10px 0 10px 0;
            box-sizing: border-box;
            .content
              font-size 12px
            .time
              font-size 12px
              color: #2d8cf0
.order_box2
   position absolute
   z-index 999999999
.order_box >>> .ivu-modal-header
   padding 30x 16px !important
.order_box >>> .ivu-card
    font-size 12px !important
.fontColor1 >>> .ivu-description-term
    color red !important
.fontColor1 >>> .ivu-description-detail
    color red !important
    padding-bottom 14px !important
.fontColor2 >>> .ivu-description-detail
    color #733AF9 !important
.order_box >>> .ivu-description-term
    padding-bottom 10px !important
.order_box >>> .ivu-description-detail
    padding-bottom 10px !important
.order_box >>> .ivu-modal-body
    padding: 0 !important
.fontColor3 >>> .ivu-description-term
    color #f1a417 !important
.fontColor3 >>> .ivu-description-detail
    color #f1a417 !important

.tabBoxPic
    width 50px
    height 50px
    display inline-block
    vertical-align top
    margin-right 6px
.tabBox_img
    width 100%
    height 100%
    border-radius:4px
    cursor pointer
    img
       width 100%
       height 100%
       padding 2px

>>> .order_box

    .head
        padding 30px 35px 25px

        .full
            display flex

            .iconfont
                color #1890FF

                &.sale-after {
                    color #90ADD5
                }

            .text
                align-self center
                flex 1
                min-width 0
                padding-left 12px
                border 0
                font-size 13px
                line-height 13px
                color #606266

                .title
                    margin-bottom 10px
                    font-weight 500
                    font-size 16px
                    line-height 16px
                    color rgba(0, 0, 0, 0.85)

            .ivu-btn
                margin-left 12px

                &:first-child
                    display inline-block
                    border-color #1890FF
                    margin-left 0
                    background-color #1890FF
                    color #FFFFFF
                
                &:nth-child(2)
                    display inline-block
                    border-color #19BE6B
                    background-color #19BE6B
                    color #FFFFFF

                &:nth-child(3)
                    display inline-block

                &:focus
                    box-shadow none
            
            .ivu-dropdown
                margin-left 12px

                &:nth-child(n+5)
                    display inline-block

                .ivu-btn
                    border-color #DCDEE2
                    background-color #FFFFFF
                    color #515A6E

        .list
            display flex
            margin-top 20px
            overflow hidden
            list-style none

            .item
                flex none
                width 200px
                font-size 14px
                line-height 14px
                color rgba(0, 0, 0, 0.85)

                .title
                    margin-bottom 12px
                    font-size 13px
                    line-height 13px
                    color #666666

                .value1
                    color #F56022

                .value2
                    color #1BBE6B

                .value3
                    color #1890FF

                .value4
                    color #6A7B9D

                .value5
                    color #F5222D

    .section
        padding 25px 0
        border-bottom 1px dashed #EEEEEE

        .title
            padding-left 10px
            border-left 3px solid #1890FF
            font-size 15px
            line-height 15px
            color #303133

        .list
            display flex
            flex-wrap wrap
            list-style none

        .item
            flex 0 0 calc(100% / 3)
            display flex
            margin-top 16px
            font-size 13px
			color #606266

            &:nth-child(3n+1)
                padding-right 20px

            &:nth-child(3n+2)
                padding-right 10px
                padding-left 10px

            &:nth-child(3n+3)
                padding-left 20px

        .value
            flex 1

            .image
                display inline-block
                width 40px
                height 40px
                margin 0 12px 12px 0
                vertical-align middle

            img
                width 100%
                height 100%

    .product
        display flex

        .image
            width 50px
            height 50px

        img
            width 100%
            height 100%
            border-radius 4px
        
        .title
            flex 1
            padding-left 13px
            text-align left

>>> .ivu-tabs
    color rgba(0, 0, 0, 0.85)

    .ivu-tabs-bar
        border-bottom 0
        margin-bottom 0
        background-color #F5F7FA

    .ivu-tabs-nav-container
        font-size 13px

    .ivu-tabs-nav-wrap
        margin-bottom 0

    .ivu-tabs-ink-bar
        display none

    .ivu-tabs-tab
        padding 7px 19px
        margin-right 0
        line-height 26px

    .ivu-tabs-tab-active
        background-color #FFFFFF
        color rgba(0, 0, 0, 0.85)

        &::before
            content ""
            position absolute
            top 0
            left 0
            width 100%
            height 2px
            background-color #1890FF

    .ivu-tabs-tabpane
        padding 25px 35px

        &:first-child
            padding 0 35px

>>> .ivu-table

	.ivu-table-header

		table
			border-top 0 !important

		th
			background-color #F7F7F7 !important
</style>
