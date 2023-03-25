<template>
    <div>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <div class="auth acea-row row-between-wrapper">
                <div class="acea-row row-middle">
                    <Icon type="ios-bulb-outline" class="iconIos blue"/>
                    <div class="text" v-if="status === -1 || status === -9">
                        <div>体验时间剩余 {{dayNum}}天</div>
                        <div class="code">到期后后台将不能正常使用，如果您对我们的系统满意，请支持正版！</div>
                    </div>
                    <div class="text" v-else-if="status === 2">
                        <div>体验时间剩余 {{dayNum}}天</div>
                        <div class="code red">审核未通过</div>
                    </div>
                    <div class="text" v-else-if="status === 1">
                        <div>商业授权</div>
                        <div class="code">授权码：{{ authCode }}</div>
                    </div>
                    <div class="text" v-else-if="status === 0">
                        <div>体验时间剩余 {{dayNum}}天</div>
                        <div class="code blue">授权申请已提交，请等待审核</div>
                    </div>
                </div>
                <!-- <Button class="grey" @click="toCrmeb()" v-if="status === 1">进入官网</Button> -->
                <div>
                    <Button @click="toCrmeb()" v-if="status === 1">进入官网</Button>
                    <Button type="primary" @click="isTemplate = true" v-else-if="status === -1 || status === -9">申请授权</Button>
                    <Button type="primary" @click="isTemplate = true" v-else-if="status === 2">重新申请</Button>
                    <Button class="grey" v-else-if="status === 0">审核中</Button>
                    <Button class="ml20" @click="payment()" v-if="status !== 1">购买授权</Button>
                </div>
            </div>
        </Card>
        <Card :bordered="false" dis-hover class="ivu-mt" v-if="!copyright && status == 1">
            <!-- v-if="copyright == '0' && status == 1" -->
             <div class="auth acea-row row-between-wrapper">
                 <div class="acea-row row-middle">
                     <span class="iconfont iconbanquan iconIos blue"></span>
                     <div class="text">
                        <div>去版权服务</div>
                        <div class="code">购买之后可以设置</div>
                        <div class="pro_price" v-if="productStatus">￥{{price}}</div>
                    </div>
                 </div>
                 <Button type="primary" @click="payLogin()">立即购买</Button>
             </div>
        </Card>
          <Card :bordered="false" dis-hover class="ivu-mt" v-if="copyright">
             <div class="auth acea-row row-between-wrapper">
                <div class="acea-row row-middle">
                    <span class="iconfont iconbanquan iconIos blue"></span>
                    <div class="acea-row row-middle">
                        <span class="update">修改授权信息:</span>
                        <Input style="width:460px;" v-model="copyrightText"/>
                    </div>
                </div>
                 <Button type="primary" @click="saveCopyRight">保存</Button>
             </div>
        </Card>
        <Modal v-model="isTemplate" scrollable footer-hide closable title="申请商业授权" :z-index="1" width="640"
               @on-cancel="cancel">
            <div class="article-manager">
                <Card :bordered="false" dis-hover>
                    <Form ref="formItem" :model="formItem" :label-width="labelWidth" :label-position="labelPosition"
                          :rules="ruleValidate" @submit.native.prevent>
                        <Row type="flex" :gutter="24">
                            <Col span="24">
                                <Col>
                                    <FormItem label="企业名称：" prop="company_name" label-for="company_name">
                                        <Input v-model="formItem.company_name" placeholder="请填写您的企业名称"/>
                                    </FormItem>
                                </Col>
                            </Col>
                            <Col span="24">
                                <Col>
                                    <FormItem label="企业域名：" prop="domain_name" label-for="domain_name">
                                        <Input v-model="formItem.domain_name" :disabled="true" placeholder="请输入域名，格式：baidu.com"/>
                                    </FormItem>
                                </Col>
                            </Col>
                            <Col span="24">
                                <Col>
                                    <FormItem label="订单号：" label-for="order_id" prop="order_id">
                                        <Input v-model="formItem.order_id" placeholder="请输入您在淘宝或小程序购买的源码订单号"
                                               class="customer">
                                            <!-- <a slot="append" target="_blank" href="http://www.crmeb.com">联系客服获取订单号</a> -->
                                            <a slot="append" target="_blank" href="http://www.crmeb.com/web/auth/query.html">联系客服获取订单号</a>
                                        </Input>
                                    </FormItem>
                                </Col>
                            </Col>
                            <Col span="24">
                                <Col>
                                    <FormItem label="手机号：" label-for="phone" prop="phone">
                                        <Input v-model="formItem.phone" type="number" placeholder="负责人电话"/>
                                    </FormItem>
                                </Col>
                            </Col>
                            <Col span="24">
                                <Col>
                                    <FormItem label="验证码：" label-for="captcha" prop="captcha">
                                        <div class="acea-row row-middle code">
                                            <Input v-model="formItem.captcha" placeholder="验证码" class="input"/>
                                            <img @click="captchsChang" :src="captchs"
                                                 class="pictrue"/>
                                        </div>
                                    </FormItem>
                                </Col>
                            </Col>
                        </Row>
                        <Row type="flex">
                            <Col span="24">
                                <Button type="primary" @click="handleSubmit('formItem')" class="submit">提交</Button>
                            </Col>
                        </Row>
                    </Form>
                </Card>
            </div>
        </Modal>
        <Modal v-model="isPay" scrollable footer-hide closable :title="orderSuccess ? '微信支付' : '短信验证' " :z-index="1" width="640"
               @on-cancel="close">
            <div>
                <Card :bordered="false" dis-hover>
                    <div v-if="!orderSuccess">
                        <Form ref="formLine" :model="form" :rules="ruleForm" :label-width="labelWidth"
                        :label-position="labelPosition" @submit.native.prevent>
                            <Row type="flex" :gutter="24">
                                <Col span="24">
                                    <Col>
                                        <FormItem label="手机号：" label-for="phone" prop="phone">
                                            <Input v-model="form.phone" type="number" placeholder="负责人电话"/>
                                        </FormItem>
                                    </Col>
                                </Col>
                                <Col span="24">
                                    <Col>
                                        <FormItem label="短信验证：" label-for="captcha" prop="code">
                                            <div class="acea-row row-middle code">
                                                <Input v-model="form.code" placeholder="验证码" class="input"/>
                                                <div class="phone_code" @click="send()">{{text}}</div>
                                            </div>
                                        </FormItem>
                                    </Col>
                                </Col>
                                <Col span="24">
                                        <Col>
                                            <FormItem label="企业名称：" prop="company_name" label-for="company_name">
                                                <Input v-model="form.company_name" placeholder="请填写您的企业名称"/>
                                            </FormItem>
                                        </Col>
                                    </Col>
                                    <Col span="24">
                                        <Col>
                                            <FormItem label="企业域名：" prop="domain_name" label-for="domain_name">
                                                <Input v-model="form.domain_name" :disabled="true" placeholder="请输入域名，格式：baidu.com"/>
                                            </FormItem>
                                        </Col>
                                    </Col>
                            </Row>
                            <Row type="flex">
                                <Col span="24">
                                    <Button type="primary" @click="submit()" class="submit">提交</Button>
                                </Col>
                            </Row>
                        </Form>
                    </div>
                    <div class="acea-row row-column row-center row-middle" v-else>
                        <div class="qrcode" ref="qrCodeUrl"></div>
                        <span class="qrcode_desc">微信扫码支付，将于{{ orderInfo.content.invalid | formatDate }}过期</span>
                        <Button @click="updatePay()">更新二维码</Button>
                    </div>
                </Card>
            </div>
        </Modal>
        <Modal v-model="isCopyright" scrollable footer-hide closable :title="success ? '微信支付' : '短信验证' " :z-index="1" width="640"
               @on-cancel="close">
               <Form ref="formCode" :model="formCode" :rules="ruleFormCode" :label-width="labelWidth"  :label-position="labelPosition"
                @submit.native.prevent v-if="!success">
                    <Row type="flex" :gutter="24">
                        <Col span="24">
                            <Col>
                                <FormItem label="手机号：" label-for="phone" prop="phone">
                                    <Input v-model="formCode.phone" type="number" placeholder="负责人电话"/>
                                </FormItem>
                            </Col>
                        </Col>
                        <Col span="24">
                            <Col>
                                <FormItem label="短信验证：" label-for="captcha" prop="code">
                                    <div class="acea-row row-middle code">
                                        <Input v-model="formCode.code" placeholder="验证码" class="input"/>
                                        <div class="phone_code" @click="phoneSend()">{{text}}</div>
                                    </div>
                                </FormItem>
                            </Col>
                        </Col>
                    </Row>
                    <Row type="flex">
                        <Col span="24">
                            <Button type="primary" @click="phoneLogin()" class="submit">提交</Button>
                        </Col>
                    </Row>
                </Form>
                <div class="acea-row row-column row-center row-middle" v-else>
                    <div class="qrcode" ref="qrCodeUrl"></div>
                    <span class="qrcode_desc">微信扫码支付，将于{{ orderInfo.content.invalid | formatDate }}过期</span>
                    <Button @click="updatePay()">更新二维码</Button>
                </div>
        </Modal>
        <vcode
        :show="isShow"
        @success="onSuccess()"
        @close="onClose()"
        successText="行为验证通过"
        />
        <!--  :imgs="[Img1, Img2]" 支持自定义背景图片，详见 https://juejin.cn/post/6978777429447966757 -->
    </div>
</template>
<script>
    import { auth, authApply,crmebProduct,crmebVerify,crmebLogin,crmebOrder,crmebPay,getCrmebOrder,saveCrmebCopyRight,crmebCopyRight,getCrmebCopyRight } from '@/api/system';
    import { mapState } from 'vuex';
    import { formatDate } from '@/utils/validate'
    import QRCode from 'qrcodejs2'
    import Vcode from "vue-puzzle-vcode";
    export default {
        name: 'system_auth',
        computed: {
            ...mapState('admin/layout', [
                'isMobile'
            ]),
            ...mapState('admin/userLevel', [
                'categoryId'
            ]),
            labelWidth () {
                return this.isMobile ? undefined : 85;
            },
            labelPosition () {
                return this.isMobile ? 'top' : 'right';
            }
        },
        data () {
            return {
                captchs: 'http://authorize.crmeb.net/api/captchs/',
                authCode: '',
                status: 1,
                dayNum: 0,
                copyright:"",
                isTemplate: false,
                price:"",
                proPrice:"",
                productStatus:false,
                isPay:false,
                isLogin:false,//是否登录过
                orderSuccess:false,
                isCopyright:false,
                copyrightText:'',
                success:false,
                payType:"",
                orderInfo:{
                    content:{
                        code_url:"",
                        invalid:0
                    },
                    order_id:""
                },
                disabled: false,
                isShow: false, // 验证码模态框是否出现
                text: "获取验证码",
                formItem: {
                    company_name: '',
                    domain_name: location.hostname,
                    order_id: '',
                    phone: '',
                    captcha: ''
                },
                form:{
                    phone:"",
                    code:"",
                    company_name: '',
                    domain_name: location.hostname,
                },
                formCode:{
                    phone:"",
                    code:"",
                },
                ruleForm:{
                    phone: [
                        { required: true, message: '请输入电话号码', trigger: 'blur' }
                    ],
                    code: [
                        { required: true, message: '请输入短信验证码', trigger: 'blur' }
                    ],
                    company_name: [
                        { required: true, message: '请填写您的企业名称', trigger: 'blur' }
                    ],
                    domain_name: [
                        { required: true, message: '请输入域名，格式：baidu.com', trigger: 'blur' }
                    ],
                },
                ruleFormCode:{
                    phone: [
                        { required: true, message: '请输入电话号码', trigger: 'blur' }
                    ],
                    code: [
                        { required: true, message: '请输入短信验证码', trigger: 'blur' }
                    ]
                },
                ruleValidate: {
                    company_name: [
                        { required: true, message: '请填写您的企业名称', trigger: 'blur' }
                    ],
                    domain_name: [
                        { required: true, message: '请输入域名，格式：baidu.com', trigger: 'blur' }
                    ],
                    order_id: [
                        { required: true, message: '请输入您在淘宝或小程序购买的源码订单号', trigger: 'blur' }
                    ],
                    phone: [
                        { required: true, message: '请输入负责人电话', trigger: 'blur' }
                    ],
                    captcha: [
                        { required: true, message: '请输入验证码', trigger: 'blur' }
                    ]
                },
                loginTab:['已购买授权','未购买授权'],
                active:0,
                timer:null

            };
        },
        filters: {
            formatDate (time) {
                if (time !== 0) {
                    let date = new Date(time * 1000);
                    return formatDate(date, 'yyyy-MM-dd hh:mm');
                }
            }
        },
        components:{
            Vcode
        },
        mounted () {
            this.getAuth();
            this.captchsChang();
            this.getProduct();


        },
        methods: {
            //保存版权信息
            saveCopyRight(){
                saveCrmebCopyRight({copyright: this.copyrightText}).then(res=>{
                    return this.$Message.success(res.msg);
                })
            },
            //获取版权信息
            getCopyRight(){
                getCrmebCopyRight().then(res=>{
                    this.copyrightText = res.data.copyright || '';
                });
            },
            captchsChang () {
                this.captchs = this.captchs + Date.parse(new Date());
            },
            cancel () {
                this.isTemplate = false
            },
            close(){
                this.isPay  =false;
            },
            loginTabSwitch(index){
                this.active = index;
            },
            // 提交
            handleSubmit (name) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        authApply(this.formItem).then(res => {
                        this.isTemplate = false;
                        this.getAuth();
                        return this.$Message.success(res.msg);
                        }).catch(res => {
                            this.captchsChang();
                            return this.$Message.error(res.msg);
                        })
                    } else {
                        return false;
                    }
                })
            },
            getAuth () {
                auth().then(res => {
                    let data = res.data || {};
                    this.authCode = data.authCode || '';
                    this.status = data.status === undefined ? -1 : data.status;
                    this.dayNum = data.day || 0;
                    this.copyright = data.copyright;
                    if (this.copyright) {
                        this.getCopyRight();
                    }
                }).catch(err=>{
                    this.$Message.error(err.msg)
                })
            },
            toCrmeb () {
                window.open('http://www.crmeb.com');
            },
            getProduct(){
                crmebProduct({type:"copyright"}).then(res=>{
                    this.price = res.data.attr.price;
                    this.productStatus = true;
                }).catch(err=>{
                    this.$Message.error(err.msg)
                })
                crmebProduct({type:"pro"}).then(res=>{
                    this.proPrice = res.data.attr.price;
                }).catch(err=>{
                    this.$Message.error(err.msg)
                })
            },
            payment(){
                this.payType = "pay";
                this.isPay = true;
            },
            payLogin(){
                this.payType = "login";
                this.isCopyright = true;
            },
            send(){
                if(!this.form.phone) return this.$Message.warning("请输入手机号")
                if(!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(this.form.phone)) return this.$Message.warning("请输入正确的手机号码");
                this.isShow = true;
            },
            phoneSend(){
                if(!this.formCode.phone) return this.$Message.warning("请输入手机号")
                if(!/^1(3|4|5|7|8|9|6)\d{9}$/i.test(this.formCode.phone)) return this.$Message.warning("请输入正确的手机号码");
                this.isShow = true;
            },
            submit(){
                this.$refs.formLine.validate((valid) => {
                    if (valid) {
                        crmebLogin({
                            code:this.form.code,
                            phone:this.form.phone
                        }).then( res => {
                            this.isLogin = true;
                            this.$cache.session.setJSON( 'crmeb-accredit',{
                                login:true,
                                phone:this.form.phone
                            });
                            this.$Message.success("登录成功");
                            crmebOrder({
                                phone:this.form.phone,
                                product_type:"pro",
                                company_name: this.form.company_name,
                                domain_name: this.form.domain_name
                            }).then(response=>{
                                this.orderInfo = response.data;
                                this.orderSuccess = true;
                                setTimeout(_=>{
                                    this.creatQrCode()
                                },500)
                                this.createSetInterval()
                            }).catch(err=>{
                                this.$Message.error(err.msg)
                            })
                        }).catch(err=>{
                            this.$Message.error(err.msg)
                        })
                    } else {
                        return false;
                    }
                })
            },
            phoneLogin(){
                this.$refs.formCode.validate((valid) => {
                    if (valid) {

                        if (this.isLogin && this.form.phone) {
                            crmebOrder({
                                phone: this.form.phone,
                                product_type:"copyright"
                            }).then(response=>{
                                this.orderInfo = response.data;
                                this.success = true;
                                setTimeout(_=>{
                                    this.creatQrCode()
                                },500)
                                this.createSetInterval()
                            })
                        } else {
                            crmebLogin({
                                code:this.formCode.code,
                                phone:this.formCode.phone
                            }).then(res=>{
                                this.$cache.session.setJSON( 'crmeb-accredit',{
                                    login:true,
                                    phone:this.formCode.phone
                                });
                                crmebOrder({
                                    phone:this.formCode.phone,
                                    product_type:"copyright"
                                }).then(response=>{
                                    this.orderInfo = response.data;
                                    this.success = true;
                                    setTimeout(_=>{
                                        this.creatQrCode()
                                    },500)
                                    this.createSetInterval()
                                })
                            }).catch(err=>{
                                this.$Message.error(err.msg)
                            })
                        }

                    } else {
                        return false;
                    }
                })
            },
            createSetInterval(ids) {
                this.stopSetInterval()
                let _this = this
                this.timer = setInterval(() => {
                    this.getCrmebOrder();
                }, 5000)
            },
            stopSetInterval() {
                if (this.timer) {
                    clearInterval(this.timer)
                    this.timer = null
                }
            },
            getCrmebOrder(){
                let accredit = this.$cache.session.has('crmeb-accredit') ? this.$cache.session.getJSON('crmeb-accredit') : '';
                getCrmebOrder(this.orderInfo.order_id,{
                    phone:accredit.phone
                }).then(res=>{
                    if(res.data.paid){
                        this.stopSetInterval();
                        this.isCopyright = false;
                        this.isPay = false;
                        if(this.payType == "pay"){
                            authApply({
                                phone:this.form.phone,
                                order_id:this.orderInfo.order_id,
                                company_name: this.form.company_name,
                                domain_name: this.form.domain_name
                            }).then(res=>{
                                this.isTemplate = false;
                                this.getAuth();
                            }).catch(res => {
                                this.captchsChang();
                                return this.$Message.error(res.msg);
                            })
                        }else{
                            console.log("购买去版权成功，申请授权···");
                            crmebCopyRight({
                                phone:this.formCode.phone,
                                order_id:this.orderInfo.order_id
                            }).then(res=>{
                                this.getAuth();
                                return this.$Message.success(res.msg);
                            }).catch(res => {
                                this.captchsChang();
                                return this.$Message.error(res.msg);
                            })
                        }
                    }
                })
            },
            sendCode() {
                if (this.disabled) return;
                this.disabled = true;
                let n = 60;
                this.text = "剩余 " + n + "s";
                const run = setInterval(() => {
                    n = n - 1;
                    if (n < 0) {
                    clearInterval(run);
                    }
                    this.text = "剩余 " + n + "s";
                    if (this.text < "剩余 " + 0 + "s") {
                    this.disabled = false;
                    this.text = "重新获取";
                    }
                }, 1000);
            },
            creatQrCode() {
                let url= this.orderInfo.content.code_url;
                var qrcode = new QRCode(this.$refs.qrCodeUrl, {
                    text: url, // 需要转换为二维码的内容
                    width: 160,
                    height: 160,
                    colorDark: '#000000',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H
                })
            },
            updatePay(){
                this.$refs.qrCodeUrl.innerHTML = "";
                let accredit = this.$cache.session.has('crmeb-accredit') ? this.$cache.session.getJSON('crmeb-accredit') : '';
                if(accredit){
                    crmebPay({
                        phone:accredit.phone,
                        order_id:this.orderInfo.order_id
                    }).then(res=>{
                        this.orderInfo = res.data;
                        this.creatQrCode()
                    })
                }

            },
            onSuccess(){
                this.isShow = false; // 通过验证后，需要手动隐藏模态框
                    if(this.payType == "pay"){
                        crmebVerify({phone:this.form.phone}).then(res=>{
                            this.sendCode();
                        }).catch(err=>{
                            this.$Message.error(err.msg)
                        })
                }else{
                    crmebVerify({phone:this.formCode.phone}).then(res=>{
                        this.sendCode();
                    }).catch(err=>{
                        this.$Message.error(err.msg)
                    })
                }


            },
            // 用户点击遮罩层，应该关闭模态框
            onClose(){
                this.isShow = false;
            }
        },
        destroyed() {
            this.stopSetInterval()
        },
    }
</script>
<style scoped lang="stylus">
    .auth {
        padding: 9px 16px 9px 10px;
    }

    .auth .iconIos {
        font-size: 40px;
        margin-right: 10px;
        color: #001529;
    }

    .auth .text {
        font-weight: 400;
        color: rgba(0, 0, 0, 1);
        font-size: 18px;
    }

    .auth .text .code {
        font-size: 14px;
        color: rgba(0, 0, 0, 0.5);
    }
    .auth .text .pro_price{
        height: 18px;
        font-size: 14px;
        font-family: PingFangSC-Semibold, PingFang SC;
        font-weight: 600;
        color: #F5222D;
        line-height: 18px;
    }
    .auth .blue {
        color: #1890FF !important;
    }

    .auth .red {
        color: #ED4014 !important;
    }
    .phone_code{
        border:1px solid #eee;
        padding:0 10px 0;
        cursor: pointer;
    }
    .grey {
        background-color: #999999;
        border-color: #999999;
        color: #fff;
    }
    .update{
        font-size: 13px;
        color: rgba(0, 0, 0, 0.85);
        padding-right:12px;
    }
    .submit {
        width: 100%;
    }

    .code .input {
        width: 83%;
    }

    .code .input .ivu-input {
        border-radius: 4px 0 0 4px !important;
    }

    .code .pictrue {
        height: 32px;
        width: 17%;
    }

    .customer {
        border-right: 0;
    }

    .customer a {
        font-size: 12px;
    }

    .ivu-input-group-prepend, .ivu-input-group-append {
        background-color: #fff;
    }

    .ivu-input-group .ivu-input {
        border-right: 0 !important;
    }
    .qrcode{
        display: flex;
        align-items: center;
        justify-content: center;
        width: 180px;
        height: 180px;
        border: 1px solid #E5E5E6;
    }
    .qrcode_desc{
        display:inline-block;
        text-align: center;
        margin:10px 0 10px;
        width: 180px;
        font-size:12px;
        color:#666;
        line-height: 16px;
    }
    .login_tab{
        font-size: 16px;
        margin:0 0 20px;
        justify-content: center;
    }
    .login_tab_item{
        width:50%;
        text-align:center;
        padding-bottom:15px;
        border-bottom:1px solid #eee;
        cursor :pointer;
    }
    .active_tab{
        border-bottom:2px solid #1495ED;
        color:#1495ED;
        font-weight:600;
    }
</style>
