<template>
    <div>
        <Card :bordered="false" dis-hover class="ivu-mt" :padding= "0">
            <div class="new_card_pd">
                <Form ref="orderData" :model="orderData" 
                :label-width="labelWidth" 
                inline 
                :label-position="labelPosition"
                class="tabform" 
                @submit.native.prevent>
                    <FormItem label="创建时间：">
                        <DatePicker :editable="false" 
                        @on-change="onchangeTime" 
                        :value="timeVal" 
                        format="yyyy/MM/dd HH:mm"
                        type="datetimerange" 
                        placement="bottom-start" 
                        placeholder="自定义时间"
                        style="width: 250px;" 
                        :options="options"></DatePicker>
                    </FormItem>
                    <FormItem label="搜索：" prop="real_name" label-for="real_name">
                        <Input v-model="orderData.real_name" placeholder="请输入" 
                        style="width: 250px"
                        element-id="name"  >
                        <Select v-model="orderData.field_key" slot="prepend" style="width: 80px">
                            <Option value="all">全部</Option>
                            <Option value="order_id">订单号</Option>
                            <Option value="uid">UID</Option>
                            <Option value="real_name">用户姓名</Option>
                            <Option value="user_phone">用户电话</Option>
                        </Select>
                        </Input>
                    </FormItem>
                    <FormItem>
                        <Button type="primary" @click="orderSearch()" style="margin: 0 14px 0 -90px">查询</Button>
                        <Button @click="exports">导出</Button>
                    </FormItem>
                </Form>        
            </div>
        </Card>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <Table :columns="columns" :data="orderList" ref="table" class="mt25"
                   :loading="loading" highlight-row
                   no-userFrom-text="暂无数据"
                   no-filtered-userFrom-text="暂无筛选结果">
                <template slot-scope="{ row, index }" slot="pay_price">
                    <div>¥ {{row.pay_price}}</div>
                </template>
                <template slot-scope="{ row, index }" slot="type">
                    <div v-if="row.type===1">电子普通发票</div>
                    <div v-else>纸质专用发票</div>
                </template>
                <template slot-scope="{ row, index }" slot="is_invoice">
                    <Tag color="orange" size="medium" v-if="row.is_invoice===1">已开票</Tag>
                    <Tag color="green" size="medium" v-else>未开票</Tag>
                </template>
                <template slot-scope="{ row, index }" slot="status">
                    <template v-if="row.refund_status">
                        <div v-if="row.refund_status===1">退款中</div>
                        <div v-else-if="row.refund_status===2">已退款</div>
                    </template>
                    <template v-else>
                        <Tag color="green" size="medium" v-if="row.status===0">未发货</Tag>
                        <Tag color="blue" size="medium" v-else-if="row.status===1">待收货</Tag>
                        <Tag color="orange" size="medium" v-else-if="row.status===2">待评价</Tag>
                        <Tag color="volcano" size="medium" v-else-if="row.status===3">已完成</Tag>
                    </template>
                </template>
                <template slot-scope="{ row, index }" slot="header_type">
                    <div v-if="row.header_type===1">个人</div>
                    <div v-else>企业</div>
                </template>
                <template slot-scope="{ row, index }" slot="action">
                    <a :disabled="row.refund_status !== 0" @click="edit(row)">编辑</a>
                    <Divider type="vertical"/>
                    <a @click="orderInfo(row.id)">订单信息</a>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" :current="orderData.page" show-elevator show-total @on-change="pageChange"
                      :page-size="orderData.limit"/>
            </div>
        </Card>
        <Modal v-model="invoiceShow" scrollable title="发票详情" class="order_box" width="700" @on-cancel="cancel" footer-hide>
            <Form ref="formInline" :model="formInline" :label-width="100" @submit.native.prevent>
                <div v-if="invoiceDetails.header_type===1 && invoiceDetails.type===1">
                    <div class="list">
                        <div class="title">发票信息</div>
                        <Row class="row">
                            <Col span="12">发票抬头: <span class="info">{{invoiceDetails.name}}</span></Col>
                            <Col span="12">发票类型: <span class="info">电子普通发票</span></Col>
                        </Row>
                        <Row class="row">
                            <Col span="12">发票抬头类型: 个人</Col>
                            <Col span="12" v-if="invoiceDetails.is_invoice === 1">发票金额: ￥{{ invoiceDetails.pay_price }}</Col>
                        </Row>
                    </div>
                    <div class="list">
                        <div class="title row">联系信息</div>
                        <Row  class="row">
                            <Col span="12">真实姓名: {{invoiceDetails.name}}</Col>
                            <Col span="12">联系电话: {{invoiceDetails.drawer_phone}}</Col>
                        </Row>
                        <Row  class="row">
                            <Col span="12">联系邮箱: {{invoiceDetails.email}}</Col>
                        </Row>
                    </div>
                </div>
                <div v-if="invoiceDetails.header_type===2 && invoiceDetails.type===1">
                    <div class="list">
                        <div class="title">发票信息</div>
                        <Row class="row">
                            <Col span="12">发票抬头: <span class="info">{{invoiceDetails.name}}</span></Col>
                            <Col span="12">企业税号: <span class="info">{{invoiceDetails.duty_number}}</span></Col>
                        </Row>
                        <Row class="row">
                            <Col span="12">发票类型: 电子普通发票</Col>
                            <Col span="12">发票抬头类型: 企业</Col>
                        </Row>
                    </div>
                    <div class="list">
                        <div class="title row">联系信息</div>
                        <Row  class="row">
                            <Col span="12">真实姓名: {{invoiceDetails.name}}</Col>
                            <Col span="12">联系电话: {{invoiceDetails.drawer_phone}}</Col>
                        </Row>
                        <Row  class="row">
                            <Col span="12">联系邮箱: {{invoiceDetails.email}}</Col>
                        </Row>
                    </div>
                </div>
                <div v-if="invoiceDetails.header_type===2 && invoiceDetails.type===2">
                    <div class="list">
                        <div class="title">发票信息</div>
                        <Row class="row">
                            <Col span="12">发票抬头: <span class="info">{{invoiceDetails.name}}</span></Col>
                            <Col span="12">企业税号: <span class="info">{{invoiceDetails.duty_number}}</span></Col>
                        </Row>
                        <Row class="row">
                            <Col span="12">发票类型: 纸质专用发票</Col>
                            <Col span="12">发票抬头类型: 企业</Col>
                        </Row>
                        <Row class="row">
                            <Col span="12">开户银行: <span class="info">{{invoiceDetails.bank}}</span></Col>
                            <Col span="12">银行账号: <span class="info">{{invoiceDetails.card_number}}</span></Col>
                        </Row>
                        <Row class="row">
                            <Col span="12">企业地址: {{invoiceDetails.address}}</Col>
                            <Col span="12">企业电话: {{invoiceDetails.tell}}</Col>
                        </Row>
                    </div>
                    <div class="list">
                        <div class="title row">联系信息</div>
                        <Row  class="row">
                            <Col span="12">真实姓名: {{invoiceDetails.name}}</Col>
                            <Col span="12">联系电话: {{invoiceDetails.drawer_phone}}</Col>
                        </Row>
                        <Row  class="row">
                            <Col span="12">联系邮箱: {{invoiceDetails.email}}</Col>
                        </Row>
                    </div>
                </div>
                <FormItem label="开票状态：" style="margin-top: 14px;">
                    <RadioGroup v-model="formInline.is_invoice" @on-change="kaiInvoice(formInline.is_invoice)">
                        <Radio :label=1>已开票</Radio>
                        <Radio :label=0>未开票</Radio>
                    </RadioGroup>
                </FormItem>
                <FormItem label="发票编号：" v-if="formInline.is_invoice===1">
                    <Input  v-model="formInline.invoice_number" placeholder="请输入发票编号"></Input>
                </FormItem>
                <!-- @keyup.native="keyUp($event,'pay_price',true)" -->
                <FormItem label="发票金额：" v-if="formInline.is_invoice===1">
                    <Input @input="inputEnter"
                    @blur="inputBlur"  v-model="formInline.pay_price" placeholder="请输入发票金额"></Input>
                    <!-- <InputNumber @on-change="keyUp($event,'pay_price',true)" v-model="formInline.pay_price" placeholder="请输入发票金额"></InputNumber> -->
                </FormItem>
                <FormItem label="发票备注：" v-if="formInline.is_invoice===1">
                    <Input v-model="formInline.remark" value="备注" type="textarea" :autosize="{minRows: 2,maxRows: 5}" placeholder="请输入发票备注"></Input>
                </FormItem>
                <Button type="primary" @click="handleSubmit()" style="width: 100%;margin: 0 10px;">确定</Button>
            </Form>
        </Modal>
        <Modal v-model="orderShow" scrollable title="订单详情" footer-hide class="order_box" width="700">
            <orderDetall :orderId="orderId" @detall="detall" v-if="orderShow"></orderDetall>
        </Modal>
    </div>
</template>
<script>
    import orderDetall from './orderDetall'
    import { orderInvoiceChart, orderInvoiceList, orderInvoiceSet,exportInvoiceList } from '@/api/order'
    import { mapState } from 'vuex';
    import exportExcel from "@/utils/newToExcel.js";
    export default {
        name: 'invoice',
        components: {
            orderDetall
        },
        computed: {
            ...mapState('media', ['isMobile']),
            labelWidth () {
                return this.isMobile ? undefined : 96;
            },
            labelPosition () {
                return this.isMobile ? 'top' : 'right';
            }
        },
        data () {
            return {
                orderShow: false,
                invoiceShow: false,
                invoiceDetails: {},
                formInline: {
                    is_invoice: 0,
                    invoice_number: '',
                    remark: '',
                    // pay_price: ''
                },
                loading: false,
                currentTab: '',
                tablists: null,
                timeVal: [],
                options: {
                    shortcuts: [
                        {
                            text: '今天',
                            value () {
                                const end = new Date()
                                const start = new Date()
                                start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()))
                                return [start, end]
                            }
                        },
                        {
                            text: '昨天',
                            value () {
                                const end = new Date()
                                const start = new Date()
                                start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 1)))
                                end.setTime(end.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 1)))
                                return [start, end]
                            }
                        },
                        {
                            text: '最近7天',
                            value () {
                                const end = new Date()
                                const start = new Date()
                                start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 6)))
                                return [start, end]
                            }
                        },
                        {
                            text: '最近30天',
                            value () {
                                const end = new Date()
                                const start = new Date()
                                start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate() - 29)))
                                return [start, end]
                            }
                        },
                        {
                            text: '本月',
                            value () {
                                const end = new Date()
                                const start = new Date()
                                start.setTime(start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), 1)))
                                return [start, end]
                            }
                        },
                        {
                            text: '本年',
                            value () {
                                const end = new Date()
                                const start = new Date()
                                start.setTime(start.setTime(new Date(new Date().getFullYear(), 0, 1)))
                                return [start, end]
                            }
                        }
                    ]
                },
                grid: {
                    xl: 8,
                    lg: 8,
                    md: 12,
                    sm: 24,
                    xs: 24
                },
                columns: [
                    {
                        title: '订单号',
                        key: 'order_id',
                        minWidth: 170
                    },
                    // {
                    //     title: '订单类型',
                    //     key: 'pink_name',
                    //     minWidth: 150
                    // },
                    {
                        title: '订单金额',
                        slot: 'pay_price',
                        minWidth: 100
                    },
                    {
                        title: '发票类型',
                        slot: 'type',
                        minWidth: 120,
                        filters: [
                            {
                                label: '电子普通发票',
                                value: 1
                            },
                            {
                                label: '纸质专用发票',
                                value: 2
                            }
                        ],
                        filterMultiple: false,
                        filterMethod (value, row) {
                            if (value === 1) {
                                return row.type === 1;
                            } else if (value === 2) {
                                return row.type === 2;
                            }
                        }
                    },
                    {
                        title: '发票抬头名称',
                        key: 'name',
                        minWidth: 150,
                    },
                    {
                        title: '发票抬头类型',
                        slot: 'header_type',
                        minWidth: 110,
                        filters: [
                            {
                                label: '个人',
                                value: 1
                            },
                            {
                                label: '企业',
                                value: 2
                            }
                        ],
                        filterMultiple: false,
                        filterMethod (value, row) {
                            if (value === 1) {
                                return row.header_type === 1;
                            } else if (value === 2) {
                                return row.header_type === 2;
                            }
                        }
                    },
                    // {
                    //     title: '支付状态',
                    //     key: 'pay_type_name',
                    //     minWidth: 90
                    // },
                    {
                        title: '下单时间',
                        key: 'add_time',
                        minWidth: 150,
                        sortable: true
                    },
                    {
                        title: '开票状态',
                        slot: 'is_invoice',
                        minWidth: 80
                    },
                    {
                        title: '订单状态',
                        slot: 'status',
                        minWidth: 80
                    },
                    {
                        title: '操作',
                        slot: 'action',
                        fixed: 'right',
                        minWidth: 150,
                        align: 'center'
                    }
                ],
                orderList: [],
                total: 0, // 总条数
                orderData: {
                    page: 1, // 当前页
                    limit: 10, // 每页显示条数
                    status: '',
                    data: '',
                    real_name: '',
                    field_key: '',
                    type: ''
                },
                orderId: 0
            }
        },
        created () {
            this.getTabs();
            this.getList();
        },
        mounted () {
        },
        methods: {
            // keyUp(e, key, money){
            //     console.log(key,e);
            //     if(!this.formInline[key]) {
            //         return (this.formInline[key] = "")
            //     }
            //     let num = "";
            //     if(money) {
            //         num = this.formInline[key].match(/^\d*(\.?\d{0,2})/g)[0];
            //     } else {
            //         num = this.formInline[key].replace(/^[^\d]+$/g, "").split('.')[0]
            //     }
            //     this.formInline[key] = `${num}`
            //     //  this.$set(this.formInline,key,num)
            //     console.log(num);
            //     console.log(this.formInline[key]);
            // },
            detall (e) {
                this.orderShow = e;
            },
            orderInfo (id) {
                this.orderId = id;
                this.orderShow = true;
            },
            empty () {
                this.formInline = {
                    is_invoice: 0,
                    invoice_number: '',
                    remark: ''
                };
            },
            cancel () {
                this.invoiceShow = false;
                this.empty();
            },
            kaiInvoice (invoice) {
                if (invoice !== 1) {
                    this.formInline.invoice_number = '';
                    this.formInline.remark = '';
                }
            },
            handleSubmit () {
                if (this.formInline.is_invoice === 1) {
                    if (this.formInline.invoice_number.trim() === '') return this.$Message.error('请填写发票编号');
                }
                console.log(this.invoiceDetails.invoice_id);
                
                orderInvoiceSet(this.invoiceDetails.invoice_id, this.formInline).then(res => {
                    this.$Message.success(res.msg);
                    this.invoiceShow = false;
                    this.getList();
                    this.empty();
                }).catch(err => {
                    this.$Message.error(err.msg);
                })
            },
            edit (row) {
                this.invoiceShow = true;
                this.invoiceDetails = row;
                // this.formInline = row;
                this.formInline.invoice_number = row.invoice_number;
                this.formInline.invoice_amount = row.invoice_amount;
                this.formInline.remark = row.invoice_reamrk;
                this.formInline.is_invoice = row.is_invoice;
                this.formInline.pay_price = row.pay_price;
            },
            // 订单列表
            getList () {
                this.loading = true;
                orderInvoiceList(this.orderData).then(async res => {
                    this.loading = false
                    let data = res.data;
                    this.orderList = data.list;
                    this.total = data.count;
                }).catch(res => {
                    this.loading = false;
                    this.$Message.error(res.msg);
                })
            },
            pageChange (index) {
                this.orderData.page = index;
                this.getList();
            },
            getTabs () {
                orderInvoiceChart().then(res => {
                    this.tablists = res.data;
                }).catch(err => {
                    this.$Message.error(err.msg)
                })
            },
            // 精确搜索()
            orderSearch () {
                this.orderData.page = 1;
                this.getList();
            },
            // 具体日期搜索()；
            onchangeTime (e) {
                this.orderData.page = 1;
                this.timeVal = e;
                this.orderData.data = this.timeVal[0] ? this.timeVal.join('-') : '';
                this.getList();
            },
            // 订单状态搜索()
            selectChange () {
                this.orderData.page = 1;
                this.getList();
            },
            // 订单搜索()
            onClickTab () {
                this.orderData.page = 1;
                this.orderData.type = this.currentTab;
                this.getList();
            },
           //导出
			async exports() {
				let [th, filekey, data, fileName] = [[], [], [], ""];
				let lebData = await this.getExcelData(this.orderData); 
				if (!fileName) fileName = lebData.filename;
				filekey = lebData.filekey;
				if (!th.length) th = lebData.header; //表头
				data = data.concat(lebData.export);
				// console.log(th, filekey, fileName, data);
          		exportExcel(th, filekey, fileName, data);
			},
			getExcelData(excelData) {
				return new Promise((resolve, reject) => {
					exportInvoiceList(excelData).then((res) => {
						return resolve(res.data);
					});
				});
            },

            inputEnter() {
	this.formInline.pay_price = this.formInline.pay_price.replace(/[^\d.]/g, ""); // 清除"数字"和"."以外的字符 只能输入数字和小数点
	this.formInline.pay_price = this.formInline.pay_price.replace(/\.{2,}/g, "."); // 不能连续输入两个及以上小数点
	this.formInline.pay_price = this.formInline.pay_price
		.replace(".", "$#$")
		.replace(/\./g, "")
		.replace("$#$", "."); // 只保留第一个".", 清除多余的"."
	this.formInline.pay_price = this.formInline.pay_price.replace(
		/^(-)*(\d+)\.(\d\d).*$/,
		"$1$2.$3"
	); // 只能输入两位小数
	if (
		this.formInline.pay_price &&
		this.formInline.pay_price.indexOf(".") < 0 &&
		this.formInline.pay_price != ""
	) {
		this.formInline.pay_price = parseFloat(this.formInline.pay_price);
		this.formInline.pay_price = this.formInline.pay_price + "";
	} // 如果没有小数点，首位不能为类似于 01、02的值
	// 输入过程中，只能输入六位小数且六位小数都为零，则清空小数点和后面的六个零（如果输入完成了只输入四个零，则在blur事件中处理）
	if (
		this.formInline.pay_price.indexOf(".") > 0 &&
		this.formInline.pay_price.length - this.formInline.pay_price.indexOf(".") > 6
	) {
		let str = this.formInline.pay_price.slice(
			this.formInline.pay_price.indexOf("."),
			this.formInline.pay_price.length
		);
		if (str / 1 <= 0) {
			this.formInline.pay_price = this.formInline.pay_price.replace(str, "");
		}
	}
	if (this.formInline.pay_price / 1 > 256) {
		this.formInline.pay_price = this.formInline.pay_price + "";
		this.formInline.pay_price = this.formInline.pay_price.slice(0, this.formInline.pay_price.length - 1);
    }
    console.log('发票金额',this.formInline.pay_price);
    
},
    inputBlur() {
        // 若小数点后面全是零，则清楚小数点和后面的零
        if (this.formInline.pay_price.indexOf(".") > 0) {
            let str = this.formInline.pay_price.slice(
                this.formInline.pay_price.indexOf("."),
                this.formInline.pay_price.length
            );
            if (str / 1 <= 0) {
                this.formInline.pay_price = this.formInline.pay_price.replace(str, "");
            }
        }
    },
        }
    }
</script>
<style scoped lang="stylus">
    .order_box .list{
        font-size 12px;
        color #17233D;
        border-bottom 1px solid #E7EAEC;
        margin 0 10px;
        padding-bottom 22px;
    }
    
    .order_box .list .title{
        color #000000;
        font-weight bold;
    }
    .order_box .list .row{
        margin-top 13px;
    }
    .order_box .list .info{
        color #515A6E;
    }
    .tab_data >>> .ivu-form-item-content
        margin-left 0 !important
    .table_box >>> .ivu-divider-horizontal
        margin-top 0px !important
    .table_box >>> .ivu-form-item
        margin-bottom: 15px !important;
    .tabform
        margin-bottom 10px
    .Refresh
        font-size 12px
        color #1890FF
        cursor pointer
</style>
