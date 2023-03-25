<template>
  <div>
    <Drawer :closable="false" width="1000" class-name="order_box" v-model="modals" :styles="{ padding: 0 }">
      <div v-if="orderDatalist">
        <div class="head">
          <div class="full">
            <Icon :class="{ 'sale-after': orderDatalist.orderInfo._status._type === -1 }" custom="iconfont icondingdan"
                  size="60"/>
            <div class="text">
              <div class="title">{{ orderData.pink_name || '售后订单' }}</div>
              <div>订单编号：{{ orderDatalist.orderInfo.order_id }}</div>
            </div>
            <div v-if="rowActive.delete_time == null">
              <Button :class="openErp?'on':''" :disabled="openErp"
                      v-if="orderData._status_new === 1 && orderData.paid === 0 && orderData.pay_type === 'offline'"
                      @click="changeMenu('1')">立即支付
              </Button>
              <Button :class="openErp?'on':''" :disabled="openErp" v-if="orderData._status_new == 2 && !rowActive.split.length"
                      @click="distribution">分配
              </Button>
              <Button :class="openErp?'on':''" :disabled="openErp" v-if="orderData._status_new == 1" @click="edit">编辑
              </Button>
              <Button :class="openErp?'on':''" :disabled="openErp"
                      v-if="(orderData._status_new === 2 || orderData._status_new === 8 || orderData.status === 4) && orderData.shipping_type === 1 && (orderData.pinkStatus === null || orderData.pinkStatus === 2)"
                      @click="sendOrder">发送货
              </Button>
              <Button :class="openErp?'on':''" :disabled="openErp" v-if="orderData._status_new === 4&&!rowActive.split.length" @click="delivery">
                配送信息
              </Button>
              <Button :class="openErp?'on':''" :disabled="openErp"
                      v-if="orderData.shipping_type == 2 && orderData.status == 0 && orderData.paid == 1 && orderData.refund_status === 0"
                      @click="bindWrite">立即核销
              </Button>
              <Button v-if="orderData._status_new >= 2" @click="changeMenu('10')">小票打印</Button>
              <Button :class="openErp?'on':''" :disabled="openErp"
                      v-if="orderData._status_new >= 3 && orderData.express_dump" @click="changeMenu('11')">电子面单打印
              </Button>
              <Button :class="openErp?'on':''" :disabled="openErp" v-if="
		                ([1, 2, 5].includes(orderData.refund_type) &&
		                (parseFloat(orderData.pay_price) >
		                  parseFloat(orderData.refunded_price) ||
		                  orderData.pay_price == 0)) && !formType
		              " @click="changeMenu('5')">{{ orderData.refund_type == 2 ? "同意退货" : "立即退款" }}
              </Button>
              <Button :class="openErp?'on':''" :disabled="openErp" v-if="[1, 2].includes(orderData.refund_type)"
                      @click="changeMenu('7')">不退款
              </Button>
              <Button v-if="!formType" @click="changeMenu('4')">售后备注</Button>
              <Button :class="openErp?'on':''" :disabled="openErp" v-if="orderData.is_del == 1"
                      @click="changeMenu('9')">删除订单
              </Button>
              <Dropdown @on-click="changeMenu" v-if="orderData._status_new !== 1 && formType">
                <Button icon="ios-more"></Button>
                <DropdownMenu slot="list">
                  <DropdownItem
                      v-if="orderData._status_new !== 1 || (orderData._status_new === 3 && orderData.use_integral > 0 && orderData.use_integral >= orderData.back_integral)"
                      name="4">订单备注
                  </DropdownItem>
                  <DropdownItem :disabled="openErp"
                                v-if="(orderData.refund_type == 0 || orderData.refund_type == 1 || orderData.refund_type == 5) && orderData.paid == 1 && orderData.refund_status !== 2 && parseFloat(orderData.pay_price) > 0 && (!rowActive.split.length || (rowActive.split.length  &&orderData. _status._type == 1))"
                                name="5">立即退款
                  </DropdownItem>
                  <DropdownItem :disabled="openErp" v-if="orderData._status_new === 4" name="8">已收货</DropdownItem>
                </DropdownMenu>
              </Dropdown>
            </div>
          </div>
          <ul class="list">
            <li class="item">
              <div class="title">订单状态</div>
              <div v-if="!formType">
                <div v-if="orderData.refund_type == 1" class="value1">仅退款</div>
                <div v-else-if="orderData.refund_type == 2" class="value1">退货退款</div>
                <div v-else-if="orderData.refund_type == 3" class="value1">拒绝退款</div>
                <div v-else-if="orderData.refund_type == 4" class="value1">商品待退货</div>
                <div v-else-if="orderData.refund_type == 5" class="value1">退货待收货</div>
                <div v-else-if="orderData.refund_type == 6" class="value2">已退款</div>
              </div>
              <div class="value1" v-else>
                <span v-html="orderData.status_name.status_name"></span>
                <span v-if="!orderData.is_all_refund && orderData.refund.length">,部分退款中</span>
                <span
                    v-if="orderData.is_all_refund && orderData.refund.length && orderData.refund_type != 6">,退款中</span>
              </div>
              <!-- <div v-if="orderDatalist.orderInfo.is_del" class="value4">{{ orderDatalist.orderInfo._status._title }}</div>
              <template v-else>
                  <div v-if="orderDatalist.orderInfo._status._type === -1" class="value5">{{ orderDatalist.orderInfo._status._title }}</div>
                  <div v-else-if="orderDatalist.orderInfo._status._type < 4 || orderDatalist.orderInfo._status._type === 9" class="value1">{{ orderDatalist.orderInfo._status._title }}</div>
                  <div v-else-if="orderDatalist.orderInfo._status._type === 4" class="value2">{{ orderDatalist.orderInfo._status._title }}</div>
                  <div v-else-if="orderDatalist.orderInfo._status._type === 5" class="value3">{{ orderDatalist.orderInfo._status._title }}</div>
              </template> -->
            </li>
            <li class="item">
              <div class="title">实际支付</div>
              <div>¥{{ orderDatalist.orderInfo.paid > 0 ? orderDatalist.orderInfo.pay_price : 0 }}</div>
            </li>
            <li class="item" v-if="!formType">
              <div class="title">退款件数</div>
              <div>{{ orderDatalist.orderInfo.total_num || 0 }}</div>
            </li>
            <li class="item" v-else>
              <div class="title">支付方式</div>
              <div>{{ orderDatalist.orderInfo._status._payType || '-' }}</div>
            </li>
            <li class="item" v-if="!formType">
              <div class="title">退款时间</div>
              <div>{{ orderDatalist.orderInfo._refund_time || '-' }}</div>
            </li>
            <li class="item" v-else>
              <div class="title">支付时间</div>
              <div>{{ orderDatalist.orderInfo._pay_time || '-' }}</div>
            </li>
          </ul>
        </div>
        <Tabs v-model="activeName">
          <TabPane label="订单信息" name="detail">
            <div class="section" v-if="!formType">
              <div class="title">退款信息</div>
              <ul class="list">
                <li class="item">
                  <div>退款原因：</div>
                  <div class="value">{{ orderDatalist.orderInfo.refund_reason || '-' }}</div>
                </li>
                <li class="item" v-if="parseFloat(orderDatalist.orderInfo.refund_price)">
                  <div>退款金额：</div>
                  <div class="value">{{ parseFloat(orderDatalist.orderInfo.refund_price) || 0 }}</div>
                </li>
                <li class="item" v-if="parseFloat(orderDatalist.orderInfo.back_integral)">
                  <div>退回积分：</div>
                  <div class="value">{{ parseFloat(orderDatalist.orderInfo.back_integral) || '-' }}</div>
                </li>
                <li class="item">
                  <div>退款说明：</div>
                  <div class="value">{{ orderDatalist.orderInfo.refund_explain || '-' }}</div>
                </li>
                <li class="item">
                  <div>退款凭证：</div>
                  <div class="value">
                    <div class="image" v-for="(img, i) in orderDatalist.orderInfo.refund_img" :key="i" v-viewer>
                      <img v-lazy="img">
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div class="section" v-if="!formType && orderDatalist.orderInfo.refund_express_name">
              <div class="title">退货物流信息</div>
              <ul class="list">
                <li class="item">
                  <div>物流公司：</div>
                  <div class="value">{{ orderDatalist.orderInfo.refund_express_name || '-'}}</div>
                </li>
                <li class="item">
                  <div>物流单号：</div>
                  <div class="value">{{ orderDatalist.orderInfo.refund_express || '-'}}</div>
                </li>
                <li class="item">
                  <div>联系电话：</div>
                  <div class="value">{{ orderDatalist.orderInfo.refund_phone || '-'}}</div>
                </li>
                <li class="item">
                  <div>退货说明：</div>
                  <div class="value">{{ orderDatalist.orderInfo.refund_goods_explain || '-'}}</div>
                </li>
                <li class="item">
                  <div>退货凭证：</div>
                  <div class="value">
                    <div class="image" v-for="(img, i) in orderDatalist.orderInfo.refund_goods_img" :key="i" v-viewer>
                      <img v-lazy="img">
                    </div>
                  </div>
                </li>
              </ul>
            </div>
            <div class="section">
              <div class="title">用户信息</div>
              <ul class="list">
                <li class="item">
                  <div>用户UID：</div>
                  <div class="value">{{ orderDatalist.userInfo.uid ? orderDatalist.userInfo.uid : '游客' }}</div>
                </li>
                <li class="item">
                  <div>用户昵称：</div>
                  <div class="value">{{ orderDatalist.userInfo.uid ? orderDatalist.userInfo.nickname : '游客' }}</div>
                </li>
                <li class="item">
                  <div>绑定电话：</div>
                  <div class="value">{{ orderDatalist.userInfo.phone || '-'}}</div>
                </li>
              </ul>
            </div>
            <div class="section" v-if="orderDatalist.orderInfo.product_type == 0">
              <div class="title">收货信息</div>
              <ul class="list">
                <!-- <li class="item">
                    <div>用户昵称：</div>
                    <div class="value">{{orderDatalist.userInfo.uid?orderDatalist.userInfo.nickname:'游客'}}</div>
                </li> -->
                <li class="item">
                  <div class="value">收货人：{{ orderDatalist.orderInfo.real_name || '-'}}</div>
                </li>
              </ul>
              <ul class="list">
                <li class="mt10">
                  <div class="value">收货电话：{{ orderDatalist.orderInfo.user_phone || '-'}}</div>
                </li>
              </ul>
              <ul class="list">
                <li class="mt10">
                  <div class="value">收货地址：{{ orderDatalist.orderInfo.user_address || '-'}}</div>
                </li>
              </ul>
            </div>
            <div class="section"
                 v-if="orderDatalist.orderInfo.fictitious_content && orderDatalist.orderInfo.cartInfo[0].product_type !=1">
              <div class="title">虚拟发货</div>
              <ul class="list">
                <li class="item">
                  <div class="value">{{ orderDatalist.orderInfo.fictitious_content }}</div>
                </li>
              </ul>
            </div>
            <div class="section" v-if="orderDatalist.orderInfo.cartInfo[0].product_type ==1">
              <div class="title">卡密发货</div>
              <div v-if='orderDatalist.orderInfo.virtual.length'>
                <div class="list" v-for="(item,index) in orderDatalist.orderInfo.virtual" :key='index'>
                  <div class="item">
                    <div>卡号{{ index + 1 }}：</div>
                    <div class="value">{{ item.card_no }}</div>
                  </div>
                  <div class="item">
                    <div>密码{{ index + 1 }}：</div>
                    <div class="value">{{ item.card_pwd }}</div>
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
                  <div>商品总价：</div>
                  <div class="value">{{ orderDatalist.orderInfo.total_price || '-'}}</div>
                </li>
                <li class="item">
                  <div>优惠券金额：</div>
                  <div class="value">{{ orderDatalist.orderInfo.coupon_price || '-'}}</div>
                </li>
                <li class="item">
                  <div>积分抵扣：</div>
                  <div class="value">{{ orderDatalist.orderInfo.deduction_price || 0.00 }}</div>
                </li>
                <li class="item" v-if="parseFloat(orderDatalist.orderInfo.use_integral)">
                  <div>使用积分：</div>
                  <div class="value">{{ parseFloat(orderDatalist.orderInfo.use_integral) }}</div>
                </li>
                <li class="item">
                  <div>支付邮费：</div>
                  <div class="value">{{ orderDatalist.orderInfo.pay_postage || '-'}}</div>
                </li>
                <li class="item">
                  <div>会员商品优惠：</div>
                  <div class="value">{{ orderDatalist.orderInfo.vip_true_price || 0.00 }}</div>
                </li>
                <li class="item"
                    v-if="orderDatalist.orderInfo.shipping_type === 2 && orderDatalist.orderInfo.refund_status === 0 && orderDatalist.orderInfo.paid === 1">
                  <div>门店名称：</div>
                  <div class="value">{{ orderDatalist.orderInfo._store_name || '-'}}</div>
                </li>
                <li class="item"
                    v-if="orderDatalist.orderInfo.shipping_type === 2 && orderDatalist.orderInfo.refund_status === 0 && orderDatalist.orderInfo.paid === 1">
                  <div>核销码：</div>
                  <div class="value">{{ orderDatalist.orderInfo.verify_code || '-'}}</div>
                </li>
                <li class="item">
                  <div>推广人：</div>
                  <div class="value">{{ orderDatalist.userInfo.spread_name }}/ID:{{
                      orderDatalist.userInfo.spread_uid
                    }}
                  </div>
                </li>
                <li class="item">
                  <div>支付时间：</div>
                  <div class="value">{{ orderDatalist.orderInfo._pay_time || '-'}}</div>
                </li>
                <li class="item">
                  <div>支付方式：</div>
                  <div class="value">{{ orderDatalist.orderInfo._status._payType || '-'}}</div>
                </li>
                <li class="item" v-if="orderDatalist.orderInfo.store_order_sn">
                  <div>原订单号：</div>
                  <div class="value">{{ orderDatalist.orderInfo.store_order_sn }}</div>
                </li>
                <li class="item" v-for="(item,index) in orderDatalist.orderInfo.promotions_detail" :key="index">
                  <div>{{ item.title }}：</div>
                  <div class="value">-￥{{ parseFloat(item.promotions_price).toFixed(2) }}</div>
                </li>
              </ul>
            </div>
            <div class="section" v-if="orderDatalist.orderInfo.delivery_type==='express'">
              <div class="title">物流信息</div>
              <ul class="list">
                <li class="item">
                  <div>快递公司：</div>
                  <div class="value">{{ orderDatalist.orderInfo.delivery_name || '-' }}</div>
                </li>
                <li class="item">
                  <div>快递单号：</div>
                  <div class="value">{{ orderDatalist.orderInfo.delivery_id }}<span class="logisticsLook"
                                                                                    @click="openLogistics">查询</span>
                  </div>
                </li>
              </ul>
            </div>
            <div class="section" v-if="orderDatalist.orderInfo.delivery_type==='send'">
              <div class="title">配送信息</div>
              <ul class="list">
                <li class="item">
                  <div>送货人姓名：</div>
                  <div class="value">{{ orderDatalist.orderInfo.delivery_name || '-' }}</div>
                </li>
                <li class="item">
                  <div>送货人电话：</div>
                  <div class="value">{{ orderDatalist.orderInfo.delivery_id || '-'}}</div>
                </li>
              </ul>
            </div>
            <div v-if="orderDatalist.orderInfo.custom_form.length && isShow" class="section">
              <div class="title">自定义留言</div>
              <ul class="list">
                <li v-for="(item, index) in orderDatalist.orderInfo.custom_form" :key="index" class="item"
                    v-if="item.value">
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
                  <div class="value">{{ orderDatalist.orderInfo.mark || '-'}}</div>
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
            <div class="section" v-if="orderDatalist.orderInfo.refuse_reason">
              <div class="title">拒绝退款原因</div>
              <ul class="list">
                <li class="item">
                  <div class="value">{{ orderDatalist.orderInfo.refuse_reason }}</div>
                </li>
              </ul>
            </div>
            <div v-if="orderDatalist.orderInfo.invoice" class="section">
              <div class="title">发票信息</div>
              <ul class="list">
                <li class="item">
                  <div>发票类型：</div>
                  <div class="value">{{ orderDatalist.orderInfo.invoice.type | invoiceType }}</div>
                </li>
                <li class="item">
                  <div>抬头类型：</div>
                  <div class="value">{{ orderDatalist.orderInfo.invoice.header_type | invoiceHeaderType }}</div>
                </li>
                <li class="item">
                  <div>发票抬头：</div>
                  <div class="value">{{ orderDatalist.orderInfo.invoice.name || '-'}}</div>
                </li>
                <li class="item">
                  <div>税号：</div>
                  <div class="value">{{ orderDatalist.orderInfo.invoice.duty_number || '-'}}</div>
                </li>
                <li class="item">
                  <div>邮箱：</div>
                  <div class="value">{{ orderDatalist.orderInfo.invoice.email || '-'}}</div>
                </li>
                <li class="item">
                  <div>开户银行：</div>
                  <div class="value">{{ orderDatalist.orderInfo.invoice.bank || '-'}}</div>
                </li>
                <li class="item">
                  <div>企业地址：</div>
                  <div class="value">{{ orderDatalist.orderInfo.invoice.address || '-'}}</div>
                </li>
                <li class="item">
                  <div>企业电话：</div>
                  <div class="value">{{ orderDatalist.orderInfo.invoice.drawer_phone || '-'}}</div>
                </li>
              </ul>
            </div>
          </TabPane>
          <TabPane label="商品信息" name="product">
            <Table :columns="columns1" :data="orderDatalist.orderInfo.cartInfo" highlight-row>
              <template slot-scope="{ row }" slot="product">
                <Tooltip theme="dark" max-width="300" :delay="600">
                  <div class="product">
                    <div class="image" v-viewer>
                      <img v-lazy="row.productInfo.attrInfo ? row.productInfo.attrInfo.image : row.productInfo.image">
                    </div>
                    <div class="title">
                      <div class="line2">
                        <span class="font-color-red"
                              v-if="row.is_gift">[赠品]</span>{{ row.productInfo.store_name }} |
                        {{ row.productInfo.attrInfo ? row.productInfo.attrInfo.suk : '' }}
                      </div>
                    </div>
                  </div>
                  <div slot="content">
                    <div>
                      <p class="font-color-red" v-if="row.is_gift">[赠品]</p>
                      <p>{{ row.productInfo.store_name }}</p>
                      <p>{{ row.productInfo.attrInfo ? row.productInfo.attrInfo.suk : '' }}</p>
                    </div>
                  </div>
                </Tooltip>
              </template>
            </Table>
          </TabPane>
          <TabPane label="订单记录" name="record">
            <Table :columns="columns2" :data="recordData" :loading="loading" no-data-text="暂无数据" highlight-row
                   no-filtered-data-text="暂无筛选结果"></Table>
          </TabPane>
          <TabPane label="发货记录" name="recordList" v-if="splitList.length">
            <Table :columns="columnSplit" :data="splitList" :loading="loading" no-data-text="暂无数据" highlight-row
                   no-filtered-data-text="暂无筛选结果">
              <template slot-scope="{ row }" slot="product">
                <Tooltip theme="dark" max-width="300" :delay="600">
                  <div class="product productTime" v-for="(j,index) in row._info">
                    <div class="image" v-viewer>
                      <img v-lazy="j.cart_info.productInfo.attrInfo ? j.cart_info.productInfo.attrInfo.image : j.cart_info.productInfo.image">
                    </div>
                    <div class="title">
                      <div class="line2">
                        <span class="font-color-red" v-if="j.cart_info.is_gift">[赠品]</span>
                        {{ j.cart_info.productInfo.store_name }} | {{ j.cart_info.productInfo.attrInfo ? j.cart_info.productInfo.attrInfo.suk : '' }}
                      </div>
                    </div>
                  </div>
                  <div slot="content">
                    <div v-for="(j,index) in row._info" :key="index">
                      <p class="font-color-red" v-if="j.cart_info.is_gift">[赠品]</p>
                      <p>{{ j.cart_info.productInfo.store_name }}</p>
                      <p>{{ j.cart_info.productInfo.attrInfo ? j.cart_info.productInfo.attrInfo.suk : '' }}</p>
                      <p class="tabBox_pice">{{ "￥" + j.cart_info.sum_price + " x " + j.cart_info.cart_num }}</p>
                    </div>
                  </div>
                </Tooltip>
              </template>
              <template slot-scope="{ row }" slot="deliveryInfo">
                <div v-if="row.add_time">发货时间：{{row.add_time}}</div>
                <div v-if="row.delivery_type == 'express' || row.delivery_type == 'send' || row.delivery_type == 'fictitious'">
                  发货方式：
                  <span v-if="row.delivery_type == 'express'">物流发货</span>
                  <span v-if="row.delivery_type == 'send'">送货</span>
                  <span v-if="row.delivery_type == 'fictitious'">虚拟发货</span>
                </div>
                <div v-if="row.delivery_name">配  &nbsp;送  &nbsp;人：{{row.delivery_name}}</div>
                <div v-if="row.delivery_id" @click="openItemLogistics(row)">快递单号：<span style="color: #1890FF;cursor: pointer;">{{row.delivery_id}}</span></div>
              </template>
              <template slot-scope="{ row, index }" slot="action">
                <a :disabled="openErp" @click="distribution(row,1)" v-if="row._status === 2">分配</a>
               <Divider type="vertical" v-if="row._status === 2" />
                <a :disabled="openErp" @click="edit(row,1)" v-if="row._status === 1">编辑</a>
                <a
                        :disabled="openErp" 
												@click="sendOrder(row,1)"
                        v-if="
            (row._status === 2 || row._status === 8 || row.status === 4) &&
            row.shipping_type === 1 &&
            (row.pinkStatus === null || row.pinkStatus === 2)
          "
                >发送货</a
                >
                <a :disabled="openErp" @click="delivery(row,1)" v-if="row._status === 4 && !row.split.length"
                >配送信息</a
                >
                <a
								        :disabled="openErp"
                        @click="bindWrite(row,1)"
                        v-if="
            row.shipping_type == 2 &&
            row.status == 0 &&
            row.paid == 1 &&
            row.refund_status === 0
          "
                >立即核销</a
                >
         <!--     <Divider
                        type="vertical"
                        v-if="
            (((row._status === 2 || row._status === 8 || row.status === 4) &&
              row.shipping_type === 1 &&
              (row.pinkStatus === null || row.pinkStatus === 2)) ||
              (row.shipping_type == 2 && row.status == 0)) &&
            row.split.length
          "
                />
                <a @click="splitOrderDetail(row)" v-if="row.split.length">查看子订单</a> -->

                <Divider
                        type="vertical"
                        v-if="
						row._status === 1 ||			
            ((row._status === 2 || row.split.length) && row.shipping_type === 1 &&
              (row.pinkStatus === null || row.pinkStatus === 2)) ||
            row._status === 4 ||
            (row.shipping_type == 2 &&
              row.status == 0 &&
              row.paid == 1 &&
              row.refund_status === 0)
          "
                />
                <template>
                  <Dropdown :transfer="true" @on-click="changeMenu('',row, $event, 1)">
                    <a href="javascript:void(0)">
                      更多
                      <Icon type="ios-arrow-down"></Icon>
                    </a>
                    <DropdownMenu slot="list">
                      <DropdownItem
											        :disabled="openErp"
                              name="1"
                              ref="ones"
                              v-show="
                  row._status === 1 &&
                  row.paid === 0 &&
                  row.pay_type === 'offline'
                "
                      >立即支付</DropdownItem
                      >
                     <!-- <DropdownItem name="2">订单详情</DropdownItem> -->
                      <DropdownItem :disabled="openErp" name="3">订单记录</DropdownItem>
                      <DropdownItem
											        :disabled="openErp"
                              name="11"
                              v-show="row._status >= 3 && row.express_dump"
                      >电子面单打印</DropdownItem
                      >
                      <DropdownItem name="10" v-show="row._status >= 2"
                      >小票打印</DropdownItem
                      >
                      <DropdownItem
                              name="4"
                              v-show="
                  row._status !== 1 ||
                  (row._status === 3 &&
                    row.use_integral > 0 &&
                    row.use_integral >= row.back_integral)
                "
                      >订单备注</DropdownItem
                      >
                      <DropdownItem
											        :disabled="openErp"
                              name="5"
                              v-show="
                  row.refund_type != 2 &&
                  row.refund_type != 4 &&
                  row.refund_type != 6 &&
                  row.paid == 1 &&
                  row.refund_status !== 2 &&
                  parseFloat(row.pay_price) > 0 &&
                  (row.split == null || row.split.length == 0)
                "
                      >立即退款</DropdownItem
                      >
                      <DropdownItem :disabled="openErp" name="55" v-show="row.refund_type == 2"
                      >同意退货</DropdownItem
                      >
                      <DropdownItem
											        :disabled="openErp"
                              name="8"
                              v-show="row._status === 4 && !row.split.length"
                      >已收货</DropdownItem
                      >
                      <DropdownItem :disabled="openErp" name="9" v-if="row.is_del == 1"
                      >删除订单</DropdownItem
                      >
                    </DropdownMenu>
                  </Dropdown>
                </template>
              </template>
            </Table>
          </TabPane>
        </Tabs>
      </div>
    </Drawer>
    <Modal v-model="modal2" scrollable title="物流查询" width="350" class="order_box2">
      <div class="logistics acea-row row-top" v-if="orderDatalist || logisticsCon">
        <div class="logistics_img"><img src="../../../../assets/images/expressi.jpg"></div>
        <div class="logistics_cent">
          <span>物流公司：{{ logisticsCon?logisticsCon.delivery_name:orderDatalist.orderInfo.delivery_name }}</span>
          <span>物流单号：{{ logisticsCon?logisticsCon.delivery_id:orderDatalist.orderInfo.delivery_id }}</span>
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
    <!-- <div  v-if="orderDatalist">
        <Modal v-model="modals"  scrollable title="订单信息" width="1000" class="order_box" footer-hide :z-index="2">
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
                <Col span="12" class="fontColor1">订单状态：{{orderDatalist.orderInfo._status._title}}</Col>
              </Row>
              <Row class="mb10">
                <Col span="12">商品总数：{{orderDatalist.orderInfo.total_num}}</Col>
                <Col span="12">商品总价：{{orderDatalist.orderInfo.total_price}}</Col>
              </Row>
              <Row class="mb10">
                <Col span="12">交付邮费：{{orderDatalist.orderInfo.pay_postage}}</Col>
                <Col span="12">优惠券金额：{{orderDatalist.orderInfo.coupon_price}}</Col>
              </Row>
              <Row class="mb10">
                <Col span="12">会员商品优惠：{{orderDatalist.orderInfo.vip_true_price||0.00}}</Col>
                <Col span="12">积分抵扣：{{orderDatalist.orderInfo.deduction_price||0.00}}</Col>
              </Row>
              <Row class="mb10">
                <Col span="12" class="mb10">实际支付：{{orderDatalist.orderInfo.pay_price}}</Col>
                <Col span="12" class="fontColor3 mb10" v-if="parseFloat(orderDatalist.orderInfo.refund_price)">退款金额：{{parseFloat(orderDatalist.orderInfo.refund_price)}}</Col>
                <Col span="12" class="fontColor3 mb10" v-if="parseFloat(orderDatalist.orderInfo.use_integral)">使用积分：{{parseFloat(orderDatalist.orderInfo.use_integral)}}</Col>
                <Col span="12" class="fontColor3 mb10" v-if="parseFloat(orderDatalist.orderInfo.back_integral)">退回积分：{{parseFloat(orderDatalist.orderInfo.back_integral)}}</Col>
                <Col span="12" class="mb10">创建时间：{{orderDatalist.orderInfo._add_time}}</Col>
                <Col span="12" class="mb10">支付时间：{{orderDatalist.orderInfo._pay_time}}</Col>
                <Col span="12" class="mb10">支付方式：{{orderDatalist.orderInfo._status._payType}}</Col>
                <Col span="12" class="mb10">推广人：{{orderDatalist.userInfo.spread_name+'/'+orderDatalist.userInfo.spread_uid}}</Col>
                <Col span="12" class="mb10" v-if="orderDatalist.orderInfo.shipping_type === 2 && orderDatalist.orderInfo.refund_status === 0 && orderDatalist.orderInfo.paid === 1">门店名称：{{orderDatalist.orderInfo._store_name}}</Col>
                <Col span="12" class="mb10" v-if="orderDatalist.orderInfo.shipping_type === 2 && orderDatalist.orderInfo.refund_status === 0 && orderDatalist.orderInfo.paid === 1">核销码：{{orderDatalist.orderInfo.verify_code}}</Col>
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
              <div v-if="orderDatalist.orderInfo.refund_reason_wap">
                  <Divider style="margin: 20px 0 !important;"/>
                  <div class="ivu-description-list-title">退款信息</div>
                  <Row class="mb10">
                      <Col span="12">退款原因：{{orderDatalist.orderInfo.refund_reason_wap}}</Col>
                      <Col span="12">退款状态：{{orderDatalist.orderInfo.refund_status===1?'申请退款中':orderDatalist.orderInfo.refund_status===2?'已退款':'拒绝退款'}}</Col>
                  </Row>
				  <Row class="mb10">
				      <Col span="24">备注说明：{{orderDatalist.orderInfo.refund_reason_wap_explain}}</Col>
				  </Row>
                  <Row class="mb10">
                      <Col span="12" v-if="orderDatalist.orderInfo.refund_reason_wap_img.length">退款凭证：
                          <viewer class="tabBoxPic" v-for="item in orderDatalist.orderInfo.refund_reason_wap_img" :key="item">
                              <div class="tabBox_img">
                                  <img v-lazy="item">
                              </div>
                          </viewer>
                      </Col>
                      <Col span="12" v-if="orderDatalist.orderInfo.refund_type !=1 && orderDatalist.orderInfo.refund_type !=2 && orderDatalist.orderInfo.refund_type !=4 && orderDatalist.orderInfo.refund_type !=5 && orderDatalist.orderInfo.refund_reason">不退款原因：{{orderDatalist.orderInfo.refund_reason}}</Col>
                  </Row>
              </div>
            </Card>
            <Card :bordered="false" dis-hover class="i-table-no-border">
                <DescriptionList title="收货信息" :col="2">
                    <Description term="用户昵称：">{{orderDatalist.userInfo.nickname}}</Description>
                    <Description term="收货人：">{{ orderDatalist.orderInfo.real_name }}</Description>
                    <Description term="联系电话：">{{ orderDatalist.orderInfo.user_phone }}</Description>
                    <Description term="收货地址：">{{ orderDatalist.orderInfo.user_address }}</Description>
                </DescriptionList>
                <Divider style="margin: 20px 0 !important;"/>
                <DescriptionList title="订单信息" :col="2">
                    <Description term="订单ID：">{{orderDatalist.orderInfo.order_id}}</Description>
                    <Description term="订单状态：" class="fontColor1">{{orderDatalist.orderInfo._status._title}}</Description>
                    <Description term="商品总数：">{{orderDatalist.orderInfo.total_num}}</Description>
                    <Description term="商品总价：">{{orderDatalist.orderInfo.total_price}}</Description>
                    <Description term="交付邮费：">{{orderDatalist.orderInfo.pay_postage}}</Description>
                    <Description term="优惠券金额：">{{orderDatalist.orderInfo.coupon_price}}</Description>
                    <Description term="实际支付：">{{orderDatalist.orderInfo.pay_price}}</Description>
                    <Description term="退款金额：" class="fontColor3" v-if="parseFloat(orderDatalist.orderInfo.refund_price)">{{parseFloat(orderDatalist.orderInfo.refund_price)}}</Description>
                    <Description term="使用积分：" class="fontColor3" v-if="parseFloat(orderDatalist.orderInfo.use_integral)">{{parseFloat(orderDatalist.orderInfo.use_integral)}}</Description>
                    <Description term="退回积分：" class="fontColor3" v-if="parseFloat(orderDatalist.orderInfo.back_integral)">{{parseFloat(orderDatalist.orderInfo.back_integral)}}</Description>
                    <Description term="创建时间：">{{orderDatalist.orderInfo._add_time}}</Description>
                    <Description term="支付方式：">{{orderDatalist.orderInfo._status._payType}}</Description>
                    <Description term="推广人：">{{orderDatalist.userInfo.spread_name+'/'+orderDatalist.userInfo.spread_uid}}</Description>
                    <Description term="门店名称：" v-if="orderDatalist.orderInfo.shipping_type === 2 && orderDatalist.orderInfo.refund_status === 0 && orderDatalist.orderInfo.paid === 1">{{orderDatalist.orderInfo._store_name}}</Description>
                    <Description term="核销码：" v-if="orderDatalist.orderInfo.shipping_type === 2 && orderDatalist.orderInfo.refund_status === 0 && orderDatalist.orderInfo.paid === 1">{{orderDatalist.orderInfo.verify_code}}</Description>
                    <Description term="商家备注：">{{orderDatalist.orderInfo.remark}}</Description>
                </DescriptionList>
                <Divider v-if="orderDatalist.orderInfo.refund_status != 0" style="margin: 20px 0 !important;"/>
                <DescriptionList title="订单退款信息" v-if="orderDatalist.orderInfo.refund_status != 0" :col="2">
                    <Description term="退款原因：">{{orderDatalist.orderInfo.refund_reason_wap}}</Description>
                    <Description term="退款备注：">{{orderDatalist.orderInfo.refund_reason_wap_explain || '无'}}</Description>
                    <Description :col="1" term="退款凭证" v-if="orderDatalist.orderInfo.refund_reason_wap_img.length">
                        <viewer v-for="item in orderDatalist.orderInfo.refund_reason_wap_img" :key="item">
                            <div class="tabBox_img">
                                <img v-lazy="item">
                            </div>
                        </viewer>
                    </Description>
                </DescriptionList>
                <Divider style="margin: 20px 0 !important;"/>
                <DescriptionList title="物流信息" :col="2" v-if="orderDatalist.orderInfo.delivery_type==='express'">
                    <Description term="快递公司：">{{orderDatalist.orderInfo.delivery_name}}</Description>
                    <Description term="快递单号：">{{orderDatalist.orderInfo.delivery_id}} <Button type="info" size="small" @click="openLogistics">物流查询</Button></Description>
                </DescriptionList>
                <DescriptionList title="配送信息" :col="2" v-if="orderDatalist.orderInfo.delivery_type==='send'">
                    <Description term="送货人姓名：">{{orderDatalist.orderInfo.delivery_name}}</Description>
                    <Description term="送货人电话：">{{orderDatalist.orderInfo.delivery_id}}</Description>
                </DescriptionList>
                <Divider style="margin: 20px 0 !important;" v-if="orderDatalist.orderInfo.mark"/>
                <DescriptionList title="备注信息" :col="2" v-if="orderDatalist.orderInfo.mark">
                    <Description class="fontColor2">{{orderDatalist.orderInfo.mark}}</Description>
                </DescriptionList>
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
    </div> -->
</template>

<script>
    import { getExpress, getOrderRecord, splitOrder } from '@/api/order';
    export default {
        name: 'orderDetails',
        filters: {
            invoiceType: value => value == 1 ? '电子普通发票' : '纸质专用发票',
            invoiceHeaderType: value => value == 1 ? '个人' : '企业'
        },
        data () {
            return {
				isShow:0,
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
                        minWidth: 400
                    },
                    {
                        title: '售价',
                        key: 'sum_price',
                    },
                    {
                        title: '实付金额',
                        key: 'truePrice',
                    },
                    {
                        title: '数量',
                        key: 'cart_num',
                    },
                    {
                        title: '小计',
                        render: (h, params) => {
							return h('div', (params.row.truePrice * params.row.cart_num).toFixed(2));
						}
                    }
                ],
                columns2: [
                    {
                        title: '订单ID',
                        key: 'oid',
                        minWidth: 40
                    },
                    {
                        title: '操作记录',
                        key: 'change_message',
                        minWidth: 280
                    },
                    {
                        title: '操作时间',
                        key: 'change_time',
                        minWidth: 100
                    }
                ],
                columnSplit: [
                    {
                      title: '订单号',
                      key: 'order_id',
                      minWidth: 100
                    },
                    {
                      title: '商品信息',
                      slot: 'product',
                      minWidth: 250
                    },
                    {
                      title: '发货信息',
                      slot: 'deliveryInfo',
                      minWidth: 100
                    },
                    {
                      title: '操作',
                      slot: 'action',
                      minWidth: 90
                    }
                ],
                recordData: [],
				activeName:'detail',
				orderData:{},
				splitList:[],
                logisticsCon:{}
            }
        },
        props: {
            orderDatalist: Object,
            orderId: Number,
            rowActive: Object,
			formType: {
			  type: Number,
			  default: 0,
			},
			openErp:{
			  type: Boolean,
			  default: false,
			}
        },
        watch: {
			orderDatalist (value) {
				this.orderData = value.orderInfo;
				this.getList( !this.formType?value.orderInfo.store_order_id:value.orderInfo.id);
				if(this.formType){
				  this.getSplitOrder(value.orderInfo.id)
                }
			},
            orderData (value) {
                if(value && value.custom_form && value.custom_form.length){
                    value.custom_form.forEach((item)=>{
                        if(item.value){
                            return this.isShow = 1
                        }
                    })
                }
            }
		},
		mounted() {
			if(this.orderData && this.orderData.custom_form && this.orderData.custom_form.length){
				this.orderData.custom_form.forEach((item)=>{
					if(item.value){
						return this.isShow = 1
					}
				})
			}
		},
        methods: {
            openItemLogistics(row){
              this.logisticsCon = row;
              this.modal2 = true;
              getExpress(row.id).then(async res => {
                this.result = res.data.result;
              }).catch(res => {
                this.$Message.error(res.msg);
              })
            },
            openLogistics () {
				this.modal2 = true;
                this.getOrderData()
            },
            // 获取订单物流信息
            getOrderData () {
                getExpress(!this.formType?this.orderDatalist.orderInfo.store_order_id:this.orderDatalist.orderInfo.id).then(async res => {
                    this.result = res.data.result;
                }).catch(res => {
                    this.$Message.error(res.msg);
                })
            },
            //发货记录
            getSplitOrder(id){
              splitOrder(id,{status:2}).then(res=>{
                this.splitList = res.data;
              }).catch(err=>{
                this.$Message.error(err.msg);
              })
            },
            getList (id) {
                let data = {
                    id: id,
                    datas: this.page
                }
                this.loading = true;
                getOrderRecord(data).then(async res => {
                    this.recordData = res.data;
                    this.loading = false;
                }).catch(res => {
                    this.loading = false;
                    this.$Message.error(res.msg);
                })
            },
            changeMenu (value,row,name,num) {
              if(num){
                this.$parent.changeMenu(row, name, num);
              }else{
                this.$parent.changeMenu(this.rowActive, value);
              }
            },
            distribution (row,num) {
                if(num){
                  this.$parent.distribution(row);
                }else {
                  this.$parent.distribution(this.rowActive);
                }
            },
            edit(row,num){
              if(num){
                this.$parent.edit(row);
              }else{
                this.$parent.edit(this.rowActive);
              }
            },
            sendOrder (row,num) {
              if(num){
                this.$parent.sendOrder(row,num);
              }else{
                this.$parent.sendOrder(this.rowActive);
              }
            },
            delivery (row,num) {
              if(num){
                this.$parent.delivery(row,num);
              }else{
                this.$parent.delivery(this.rowActive);
              }
            },
            bindWrite (row,num) {
              if(num){
                this.$parent.bindWrite(row);
              }else{
                this.$parent.bindWrite(this.rowActive);
              }
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
	.productTime~.productTime{
		margin-top 10px;
	}
    .product .title .line2{
      height: 36px;
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
        align-items: cente
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
        //display flex

        .image
            width 50px
            height 50px
            display inline-block
            vertical-align middle

        img
            width 100%
            height 100%
            border-radius 4px

        .title
            width calc(100% - 63px)
            padding-left 13px
            text-align left
            display inline-block
            vertical-align middle

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
        padding 7px 19px !important
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
        padding 15px

        &:first-child
            padding 0 25px

>>> .ivu-table

	.ivu-table-header

		table
			border-top 0 !important

		th
			background-color #F7F7F7 !important
</style>
