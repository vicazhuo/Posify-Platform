<template>
    <div class="form-submit">
        <div class="i-layout-page-header">
            <PageHeader class="product_tabs" hidden-breadcrumb>
                <div slot="title">
                    <router-link :to="{ path: '/admin/marketing/store_combination/index' }">
                        <!-- <Button icon="ios-arrow-back" size="small"  class="mr20">返回</Button> -->
                        <div class="font-sm after-line">
                            <span class="iconfont iconfanhui"></span>
                            <span class="pl10">返回</span>
                        </div>
                    </router-link>
                    <span v-text="$route.params.id ? '编辑拼团商品' : '添加拼团商品'" class="mr20 ml16"></span>
                </div>
            </PageHeader>
        </div>
        <Card :bordered="false" dis-hover class="ivu-mt">
            <Row type="flex" class="mt30 acea-row row-middle row-center">
                <Col span="20">
                <Steps :current="current">
                    <Step title="选择拼团商品"></Step>
                    <Step title="填写基础信息"></Step>
                    <Step title="修改商品详情"></Step>
                </Steps>
                </Col>
                <Col span="23">
                <Col span="24">
                <div class="lines" style="margin-top: 25px;"></div>
                </Col>
                <Form class="form mt30" ref="formValidate" :model="formValidate" :rules="ruleValidate"
                    :label-width="labelWidth" :label-position="labelPosition" @submit.native.prevent>
                    <FormItem label="选择商品：" prop="image_input" v-if="current === 0">
                        <div class="picBox" @click="changeGoods">
                            <div class="pictrue" v-if="formValidate.image"><img v-lazy="formValidate.image"></div>
                            <div class="upLoad acea-row row-center-wrapper" v-else>
                                <Icon type="ios-camera-outline" size="26" />
                            </div>
                        </div>
                    </FormItem>
                    <Row v-show="current === 1" type="flex">
                        <Col span="24">
                        <FormItem label="拼团活动名称：" prop="title" label-for="title">
                            <Input placeholder="请输入拼团活动名称" element-id="title" v-model="formValidate.title"
                                class="perW40" />
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="拼团活动简介：" prop="info" label-for="info">
                            <Input placeholder="请输入拼团活动简介" type="textarea" :rows="4" element-id="info"
                                v-model="formValidate.info" class="perW40" />
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="单位：" prop="unit_name" label-for="unit_name">
                            <Select v-model="formValidate.unit_name" clearable placeholder="请输入单位" class="perW40">
                                <Option v-for="(item, index) in unitNameList" :value="item.name" :key="index">{{
                                        item.name
                                }}</Option>
                            </Select>
                            <span class="addClass" @click="addUnit">新增单位</span>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem prop="images">
                            <div class="custom-label" slot="label">
                                <div>
                                    <div>商品轮播图</div>
                                </div>
                                <div>：</div>
                            </div>
                            <div class="acea-row">
                                <div class="pictrue" v-for="(item, index) in formValidate.images" :key="index"
                                    draggable="true" @dragstart="handleDragStart($event, item)"
                                    @dragover.prevent="handleDragOver($event, item)"
                                    @dragenter="handleDragEnter($event, item)" @dragend="handleDragEnd($event, item)">
                                    <img v-lazy="item">
                                    <Button shape="circle" icon="md-close" @click.native="handleRemove(index)"
                                        class="btndel"></Button>
                                </div>
                                <div v-if="formValidate.images.length < 10" class="upLoad acea-row row-center-wrapper"
                                    @click="modalPicTap('duo')">
                                    <Icon type="ios-camera-outline" size="26" />
                                </div>
                            </div>
                            <div class="tips"> 建议尺寸：800 * 800px，可拖拽改变图片顺序，默认首张图为主图，最多上传10张</div>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <div class="lines"></div>
                        </Col>
                        <Col span="24">
                        <FormItem label="活动时间：" prop="section_time">
                            <div class="acea-row row-middle">
                                <DatePicker :editable="false" type="datetimerange" format="yyyy-MM-dd HH:mm"
                                    placeholder="请选择活动时间" @on-change="onchangeTime" :value="formValidate.section_time"
                                    v-model="formValidate.section_time" class="perW40"></DatePicker>
                            </div>
                            <div class="tips">设置活动开启结束时间，用户可以在设置时间内发起参与拼团</div>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="拼团时效：" prop="effective_time">
                            <div class="acea-row row-middle">
                                <InputNumber :min="1" :precision="0" placeholder="请输入拼团时效" class="perW40"
                                    element-id="effective_time" v-model="formValidate.effective_time" />
                                <span class="ml10">小时</span>
                            </div>
                            <div class="tips">用户发起拼团后开始计时，需在设置时间内邀请到规定好友人数参团，超过时效时间，则系统判定拼团失败，自动发起退款</div>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="拼团人次：" prop="people">
                            <div class="acea-row row-middle">
                                <InputNumber :min="2" @on-change="peopleChange" placeholder="请输入拼团人次" :precision="0"
                                    element-id="people" v-model="formValidate.people" class="perW40" />
                                <span class="ml10">次</span>
                            </div>
                            <div class="tips">单次拼团需要参与的用户数</div>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="总购买数量限制：" prop="num">
                            <div class="acea-row row-middle">
                                <InputNumber :min="1" placeholder="请输入总数量限制" :precision="0" element-id="num"
                                    v-model="formValidate.num" class="perW40" />
                                <span class="ml10">个</span>
                            </div>
                            <div class="tips">该商品活动期间内，用户可购买的最大数量。例如设置为4，表示本次活动有效期内，每个用户最多可购买4件</div>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="单次购买数量限制：" prop="once_num">
                            <div class="acea-row row-middle">
                                <InputNumber :min="1" placeholder="请输入单次购买数量限制" :precision="0" element-id="once_num"
                                    v-model="formValidate.once_num" class="perW40" />
                                <span class="ml10">个</span>
                            </div>
                            <div class="tips">用户参与拼团时，一次购买最大数量限制。例如设置为2，表示每次参与拼团时，用户一次购买数量最大可选择2个</div>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="补齐人数：">
                            <div class="acea-row row-middle">
                                <InputNumber placeholder="请输入补齐人数" @on-change="peopleNumchange" :min="0"
                                    :max="formValidate.people - 1" :precision="0" v-model="formValidate.peopleNum"
                                    class="perW40" />
                                <span class="ml10">人</span>
                            </div>
                            <div class="tips">
                                当用户参与拼团后，成团时效内未成团情况下，设置补齐人数可虚拟成团。例如：成团人数为10人，补齐人数为4人，真实用户需要参与6人成团才可以在最后未成团时自动补齐虚拟人员
                            </div>
                        </FormItem>
                        </Col>
                        <Col span="24" v-show="">
                        <FormItem label="虚拟成团比例(%)：" prop="virtual">
                            <div class="acea-row row-middle">
                                <InputNumber :min="0" disabled placeholder="请输入虚拟成团比例" :precision="0" :max="100"
                                    element-id="virtual" v-model="formValidate.virtual" class="perW40"
                                    :formatter="value => `${value}%`" :parser="value => value.replace('%', '')" />
                            </div>
                            <div class="tips">拼团结束前若用户发起的活动未成团，并且现有人数比例大于设置比例，自动补齐人数，拼团成功。100%为不自动补齐</div>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="规格选择：">
                            <Table :data="specsData" :columns="columns" border @on-selection-change="changeCheckbox">
                                <template slot-scope="{ row, index }" slot="pic">
                                    <div class="acea-row row-middle row-center-wrapper"
                                        @click="modalPicTap('dan', 'danTable', index)">
                                        <div class="pictrue pictrueTab" v-if="row.pic"><img v-lazy="row.pic"></div>
                                        <div class="upLoad pictrueTab acea-row row-center-wrapper" v-else>
                                            <Icon type="ios-camera-outline" size="21" class="iconfont" />
                                        </div>
                                    </div>
                                </template>
                            </Table>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <div class="lines"></div>
                        </Col>
                        <Col v-if="formValidate.product_type == 0">
                        <FormItem label="配送方式：">
                            <CheckboxGroup v-model="formValidate.delivery_type">
                                <Checkbox label="1" disabled>快递</Checkbox>
                                <Checkbox label="3">门店配送</Checkbox>
                                <Checkbox label="2">到店自提</Checkbox>
                            </CheckboxGroup>
                        </FormItem>
                        </Col>
                        <Col span="24" v-if="formValidate.product_type == 0">
                        <FormItem label="运费设置：">
                            <RadioGroup v-model="formValidate.freight">
                                <Radio :label="1">包邮</Radio>
                                <Radio :label="2">固定邮费</Radio>
                                <Radio :label="3">运费模板</Radio>
                            </RadioGroup>
                        </FormItem>
                        </Col>
                        <Col span="24" v-if="formValidate.freight == 2 && formValidate.product_type == 0">
                        <FormItem label="" prop="freight">
                            <div class="acea-row row-middle">
                                <InputNumber :min="0" v-model="formValidate.postage" placeholder="请输入金额"
                                    class="perW20 maxW" />
                                <span class="ml10">元</span>
                            </div>
                        </FormItem>
                        </Col>
                        <Col span="24" v-if="formValidate.freight == 3 && formValidate.product_type == 0">
                        <FormItem label="" prop="">
                            <div class="acea-row">
                                <Select v-model="formValidate.temp_id" clearable class="perW20 maxW">
                                    <Option v-for="(item, index) in templateList" :value="item.id" :key="index">{{
                                            item.name
                                    }}</Option>
                                </Select>
                                <Button @click="addTemp" class="ml15">添加运费模板</Button>
                            </div>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="排序：">
                            <InputNumber placeholder="请输入排序" element-id="sort" :precision="0" :min="0"
                                v-model="formValidate.sort" class="perW20 maxW" />
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="活动状态：" props="is_show" label-for="is_show">
                            <i-switch v-model="formValidate.is_show" :true-value="1" :false-value="0" size="large">
                                <span slot="open">开启</span>
                                <span slot="close">关闭</span>
                            </i-switch>
                        </FormItem>
                        </Col>
                        <Col span="24">
                        <FormItem label="热门推荐：" props="is_hot" label-for="is_hot">
                            <i-switch v-model="formValidate.is_host" :true-value="1" :false-value="0" size="large">
                                <span slot="open">开启</span>
                                <span slot="close">关闭</span>
                            </i-switch>
                        </FormItem>
                        </Col>
                        <Col span="24" v-if="formValidate.product_type">
                        <FormItem label="支持退款：" props="status" label-for="status">
                            <i-switch v-model="formValidate.is_support_refund" :true-value="1" :false-value="0"
                                size="large">
                                <span slot="open">开启</span>
                                <span slot="close">关闭</span>
                            </i-switch>
                        </FormItem>
                        </Col>

                        <!--<Col v-bind="grid2">-->
                        <!--<FormItem label="拼团价：" prop="price" label-for="price">-->
                        <!--<InputNumber placeholder="请输入拼团价" element-id="price" v-width="'100%'"  v-model="formValidate.price"/>-->
                        <!--</FormItem>-->
                        <!--</Col>-->

                        <!--<Col v-bind="grid2">-->
                        <!--<FormItem label="库存：" prop="stock" label-for="stock">-->
                        <!--<InputNumber placeholder="请输入库存" :min="1" :precision="0" element-id="stock"  v-model="formValidate.stock" v-width="'100%'"/>-->
                        <!--</FormItem>-->
                        <!--</Col>-->
                        <!--<Col v-bind="grid2">-->
                        <!--<FormItem label="虚拟销量：">-->
                        <!--<InputNumber placeholder="请输入虚拟销量" :precision="0" element-id="sales"  v-model="formValidate.sales" style="width: 100%"/>-->
                        <!--</FormItem>-->
                        <!--</Col>-->

                        <!--<Col v-bind="grid2">-->
                        <!--<FormItem label="邮费：">-->
                        <!--<InputNumber placeholder="请输入邮费" element-id="postage" v-model="formValidate.postage" v-width="'100%'"/>-->
                        <!--</FormItem>-->
                        <!--</Col>-->
                        <!--<Col v-bind="grid2">-->
                        <!--<FormItem label="是否包邮：" props="is_postage" label-for="is_postage">-->
                        <!--<RadioGroup element-id="is_postage" v-model="formValidate.is_postage">-->
                        <!--<Radio :label="1" class="radio">是</Radio>-->
                        <!--<Radio :label="0">否</Radio>-->
                        <!--</RadioGroup>-->
                        <!--</FormItem>-->
                        <!--</Col>-->
                    </Row>
                    <Row v-show="current === 2">
                        <Col span="24">
                        <FormItem label="内容：">
                            <WangEditor style="width: 90%" :content="formValidate.description"
                                @editorContent="getEditorContent"></WangEditor>
                        </FormItem>
                        </Col>
                    </Row>
                    <!-- <FormItem>
                            <Button class="submission mr15" @click="step" v-show="current!==0" :disabled="$route.params.id&&current===1">上一步</Button>
                            <Button type="primary" :disabled="submitOpen && current === 2" class="submission" @click="next('formValidate')" v-text="current===2?'提交':'下一步'"></Button>
                        </FormItem> -->
                </Form>
                <Spin size="large" fix v-if="spinShow"></Spin>
                </Col>
            </Row>
        </Card>
        <Card :bordered="false" dis-hover class="fixed-card"
            :style="{ left: `${!menuCollapse ? '200px' : isMobile ? '0' : '80px'}` }">
            <Form>
                <FormItem>
                    <Button class="submission mr15" @click="step" v-show="current !== 0"
                        :disabled="$route.params.id && current === 1">上一步</Button>
                    <Button type="primary" :disabled="submitOpen && current === 2" class="submission"
                        @click="next('formValidate')" v-text="current === 2 ? '提交' : '下一步'"></Button>
                </FormItem>
            </Form>
        </Card>
        <!-- 选择商品-->
        <Modal v-model="modals" title="商品列表" footerHide class="paymentFooter" scrollable width="900"
            @on-cancel="cancel">
            <goods-list ref="goodslist" :goodsType="1" v-if="modals" @getProductId="getProductId"></goods-list>
        </Modal>
        <!-- 上传图片-->
        <Modal v-model="modalPic" width="960px" scrollable footer-hide closable title='上传商品图' :mask-closable="false"
            :z-index="1">
            <uploadPictures :isChoice="isChoice" @getPic="getPic" @getPicD="getPicD" :gridBtn="gridBtn"
                :gridPic="gridPic" v-if="modalPic"></uploadPictures>
        </Modal>
        <freightTemplate :template="template" v-on:changeTemplate="changeTemplate" ref="templates"></freightTemplate>
    </div>
</template>

<script>
import { mapState } from 'vuex';
import goodsList from '@/components/goodsList/index';
import UeditorWrap from '@/components/ueditorFrom/index';
import WangEditor from "@/components/wangEditor/index.vue";
import uploadPictures from '@/components/uploadPictures';
import freightTemplate from "@/components/freightTemplate";
import { combinationInfoApi, combinationCreatApi, productAttrsApi } from '@/api/marketing';
import { productGetTemplateApi, productAllUnit, productUnitCreate } from '@/api/product';
export default {
    name: 'storeCombinationCreate',
    components: { UeditorWrap, goodsList, uploadPictures, WangEditor, freightTemplate },
    data() {
        return {
            template: false,
            submitOpen: false,
            spinShow: false,
            isChoice: '',
            current: 0,
            modalPic: false,
            grid: {
                xl: 12,
                lg: 20,
                md: 24,
                sm: 24,
                xs: 24
            },
            grid2: {
                xl: 8,
                lg: 8,
                md: 12,
                sm: 24,
                xs: 24
            },
            gridPic: {
                xl: 6,
                lg: 8,
                md: 12,
                sm: 12,
                xs: 12
            },
            gridBtn: {
                xl: 4,
                lg: 8,
                md: 8,
                sm: 8,
                xs: 8
            },
            myConfig: {
                autoHeightEnabled: false, // 编辑器不自动被内容撑高
                initialFrameHeight: 500, // 初始容器高度
                initialFrameWidth: '100%', // 初始容器宽度
                UEDITOR_HOME_URL: '/admin/UEditor/',
                serverUrl: ''
            },
            modals: false,
            modal_loading: false,
            images: [],
            templateList: [],
            columns: [],
            specsData: [],
            picTit: '',
            tableIndex: 0,
            unitNameList: [],
            formValidate: {
                is_support_refund: 0,
                product_type: 0,
                freight: 1, //运费设置
                delivery_type: ['1'],
                images: [],
                info: '',
                title: '',
                image: '',
                unit_name: '',
                price: 0,
                effective_time: 24,
                stock: 1,
                sales: 0,
                sort: 0,
                postage: 0,
                is_postage: 0,
                is_host: 0,
                is_show: 0,
                section_time: [],
                description: '',
                id: 0,
                product_id: 0,
                people: 2,
                once_num: 1,
                num: 1,
                temp_id: '',
                attrs: [],
                items: [],
                virtual: 100,
                peopleNum: 0
            },
            description: '',
            ruleValidate: {
                image: [
                    { required: true, message: '请选择主图', trigger: 'change' }
                ],
                images: [
                    { required: true, type: 'array', message: '请选择主图', trigger: 'change' },
                    { type: 'array', min: 1, message: 'Choose two hobbies at best', trigger: 'change' }
                ],
                title: [
                    { required: true, message: '请输入拼团名称', trigger: 'blur' }
                ],
                info: [
                    { required: true, message: '请输入拼团简介', trigger: 'blur' }
                ],
                section_time: [
                    { required: true, type: 'array', message: '请选择活动时间', trigger: 'change' }
                ],
                unit_name: [
                    {
                        required: true,
                        message: "请输入单位",
                        trigger: "change",
                    }
                ],
                price: [
                    { required: true, type: 'number', message: '请输入拼团价', trigger: 'blur' }
                ],
                cost: [
                    { required: true, type: 'number', message: '请输入成本价', trigger: 'blur' }
                ],
                stock: [
                    { required: true, type: 'number', message: '请输入库存', trigger: 'blur' }
                ],
                give_integral: [
                    { required: true, type: 'number', message: '请输入赠送积分', trigger: 'blur' }
                ],
                effective_time: [
                    { required: true, type: 'number', message: '请输入拼团时效', trigger: 'blur' }
                ],
                people: [
                    { required: true, type: 'number', message: '请输入拼团人数', trigger: 'blur' }
                ],
                num: [
                    { required: true, type: 'number', message: '请输入购买数量限制', trigger: 'blur' }
                ],
                once_num: [
                    { required: true, type: 'number', message: '请输入单次购买数量限制', trigger: 'blur' }
                ],
                virtual: [
                    { required: true, type: 'number', message: '请输入虚拟成团比例', trigger: 'blur' }
                ]
            },
            copy: 0
        }
    },
    watch: {
        'formValidate.peopleNum': (n) => {
        }
    },
    computed: {
        ...mapState('admin/layout', [
            'isMobile',
            'menuCollapse'
        ]),
        labelWidth() {
            return this.isMobile ? undefined : 155;
        },
        labelPosition() {
            return this.isMobile ? 'top' : 'right';
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.copy = this.$route.params.copy;
            this.current = 1;
            this.getInfo();
        }
        this.productGetTemplate();
        this.getAllUnit();
    },
    methods: {
        changeTemplate(msg) {
            this.template = msg;
        },
        // 添加运费模板
        addTemp() {
            this.$refs.templates.isTemplate = true;
        },
        addUnit() {
            this.$modalForm(productUnitCreate()).then(() => this.getAllUnit());
        },
        getAllUnit() {
            productAllUnit().then(res => {
                this.unitNameList = res.data;
            }).catch(err => {
                this.$Message.error(err.msg);
            })
        },
        getEditorContent(data) {
            this.description = data;
        },
        peopleChange(n) {
            if (n < 2) {
                this.formValidate.people = 2;
                this.$set(this.formValidate, 'people', 2);
            }
        },
        peopleNumchange(n) {
            if (n != 0) {
                this.formValidate.virtual = Math.floor((this.formValidate.people - n) / this.formValidate.people * 100);
            }
        },
        // 拼团规格；
        productAttrs(row) {
            let that = this;
            productAttrsApi(row.id, 3).then(res => {
                let data = res.data.info;
                let selection = {
                    type: 'selection',
                    width: 60,
                    align: 'center'
                };
                that.specsData = data.attrs;
                that.specsData.forEach(function (item, index) {
                    that.$set(that.specsData[index], 'id', index);
                });
                that.formValidate.items = data.items;
                that.columns = data.header;
                that.columns.unshift(selection);
                that.inputChange(data);
            }).catch(res => {
                that.$Message.error(res.msg);
            })
        },
        inputChange(data) {
            let that = this;
            let $index = [];
            data.header.forEach(function (item, index) {
                if (item.type === 1) {
                    $index.push({ index: index, key: item.key, title: item.title });
                }
            });
            $index.forEach(function (item, index) {
                let title = item.title;
                let key = item.key;
                let row = {
                    title: title,
                    key: key,
                    align: 'center',
                    minWidth: 100,
                    render: (h, params) => {
                        return h('div', [
                            h('InputNumber', {
                                props: {
                                    min: 0,
                                    value: key === 'price' ? params.row.price : params.row.quota
                                },
                                on: {
                                    'on-change': e => {
                                        key === 'price' ? params.row.price = e : params.row.quota = e;
                                        that.specsData[params.index] = params.row;
                                        if (!!that.formValidate.attrs && that.formValidate.attrs.length) {
                                            that.formValidate.attrs.forEach((v, index) => {
                                                if (v.id === params.row.id) {
                                                    that.formValidate.attrs.splice(index, 1, params.row);
                                                }
                                            });
                                        }
                                    }
                                }
                            })
                        ])
                    }
                };
                that.columns.splice(item.index, 1, row);
            });
        },
        // 多选
        changeCheckbox(selection) {
            this.formValidate.attrs = selection;
        },
        // 获取运费模板；
        productGetTemplate() {
            productGetTemplateApi().then(res => {
                this.templateList = res.data;
            })
        },
        // 表单验证
        validate(prop, status, error) {
            if (status === false) {
                this.$Message.error(error);
            }
        },
        // 商品id
        getProductId(row) {
            this.modal_loading = false;
            this.modals = false;
            setTimeout(() => {
                this.formValidate = {
                    is_support_refund: row.is_support_refund,
                    product_type: row.product_type,
                    images: row.slider_image,
                    info: row.store_info,
                    title: row.store_name,
                    image: row.image,
                    unit_name: row.unit_name,
                    price: 0, // 不取商品中的原价
                    effective_time: 24,
                    stock: row.stock,
                    sales: row.sales,
                    sort: row.sort,
                    postage: row.postage,
                    is_postage: row.is_postage,
                    is_host: row.is_hot,
                    is_show: 0,
                    section_time: [],
                    description: row.description, // 不取商品中的
                    id: 0,
                    people: 2,
                    num: 1,
                    once_num: 1,
                    product_id: row.id,
                    temp_id: row.temp_id,
                    virtual: 100,
                    peopleNum: 0,
                    freight: 1, //运费设置
                    delivery_type: ['1']
                };
                this.productAttrs(row);
            }, 500);
        },
        cancel() {
            this.modals = false;
        },
        // 具体日期
        onchangeTime(e) {
            this.formValidate.section_time = e;
        },
        // 详情
        getInfo() {
            this.spinShow = true;
            combinationInfoApi(this.$route.params.id).then(async res => {
                let that = this;
                let info = res.data.info;
                let selection = {
                    type: 'selection',
                    width: 60,
                    align: 'center'
                };
                this.formValidate = info;
                this.description = info.description

                if (parseInt(this.formValidate.virtual) !== 100) {
                    this.formValidate.peopleNum = Math.floor(this.formValidate.people - this.formValidate.virtual / 100 * this.formValidate.people);
                } else {
                    this.formValidate.peopleNum = 0;
                }
                this.$set(this.formValidate, 'items', info.attrs.items);
                this.columns = info.attrs.header;
                this.columns.unshift(selection);
                this.specsData = info.attrs.value;
                that.specsData.forEach(function (item, index) {
                    that.$set(that.specsData[index], 'id', index);
                });
                let data = info.attrs;
                let attr = [];
                for (let index in info.attrs.value) {
                    if (info.attrs.value[index]._checked) {
                        attr.push(info.attrs.value[index]);
                    }
                }
                that.formValidate.attrs = attr;
                that.inputChange(data);
                this.spinShow = false;
            }).catch(res => {
                this.spinShow = false;
                this.$Message.error(res.msg);
            })
        },
        // 下一步
        next(name) {
            let that = this;
            if (this.current === 2) {
                this.formValidate.description = this.description
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        if (this.copy == 1) this.formValidate.copy = 1;
                        this.formValidate.id = Number(this.$route.params.id) || 0;
                        this.submitOpen = true;
                        if (!this.formValidate.product_type) {
                            this.formValidate.is_support_refund = 1;
                        }
                        combinationCreatApi(this.formValidate).then(async res => {
                            this.submitOpen = false;
                            this.$Message.success(res.msg);
                            setTimeout(() => {
                                this.$router.push({ path: '/admin/marketing/store_combination/index' });
                            }, 500);
                        }).catch(res => {
                            this.submitOpen = false;
                            this.$Message.error(res.msg);
                        })
                    } else {
                        return false
                    }
                })
            } else if (this.current === 1) {
                this.$refs[name].validate((valid) => {
                    if (valid) {
                        if (that.formValidate.people < 2) {
                            return that.$Message.error('拼团人数必须大于2')
                        }
                        if (that.formValidate.num < 0) {
                            return that.$Message.error('购买数量限制必须大于0')
                        }
                        if (that.formValidate.once_num < 0) {
                            return that.$Message.error('单次购买数量限制必须大于0')
                        }
                        if (!that.formValidate.attrs) {
                            return that.$Message.error('请选择属性规格');
                        } else {
                            for (let index in that.formValidate.attrs) {
                                if (that.formValidate.attrs[index].quota <= 0) {
                                    return that.$Message.error('拼团限量必须大于0');
                                }
                            }
                        }
                        if (this.formValidate.product_type == 0 && this.formValidate.freight == 2 && this.formValidate.postage <= 0) {
                            return this.$Message.warning("物流设置-固定邮费不能为0");
                        }
                        if (this.formValidate.product_type == 0 && this.formValidate.freight == 3 && !this.formValidate.temp_id) {
                            return this.$Message.warning("物流设置-运费模板不能为空");
                        }
                        this.current += 1;
                    } else {
                        return this.$Message.warning('请完善商品信息');
                    }
                })
            } else {
                if (this.formValidate.image) {
                    this.current += 1;
                } else {
                    this.$Message.warning('请选择商品');
                }
            }
        },
        // 上一步
        step() {
            this.current--;
        },
        // 内容
        getContent(val) {
            this.formValidate.description = val;
        },
        // 点击商品图
        modalPicTap(tit, picTit, index) {
            this.modalPic = true;
            this.isChoice = tit === 'dan' ? '单选' : '多选';
            this.picTit = picTit;
            this.tableIndex = index;
        },
        // 获取单张图片信息
        getPic(pc) {
            switch (this.picTit) {
                case 'danFrom':
                    this.formValidate.image = pc.att_dir;
                    break;
                default:
                    if (!!this.formValidate.attrs && this.formValidate.attrs.length) {
                        this.$set(this.specsData[this.tableIndex], '_checked', true);
                    }
                    this.specsData[this.tableIndex].pic = pc.att_dir;
            }
            this.modalPic = false;
        },
        // 获取多张图信息
        getPicD(pc) {
            this.images = pc;
            this.images.map((item) => {
                this.formValidate.images.push(item.att_dir)
                this.formValidate.images = this.formValidate.images.splice(0, 10);
            });
            this.modalPic = false;
        },
        handleRemove(i) {
            this.images.splice(i, 1);
            this.formValidate.images.splice(i, 1);
        },
        // 选择商品
        changeGoods() {
            this.modals = true;
        },
        // 移动
        handleDragStart(e, item) {
            this.dragging = item;
        },
        handleDragEnd(e, item) {
            this.dragging = null
        },
        // 首先把div变成可以放置的元素，即重写dragenter/dragover
        handleDragOver(e) {
            e.dataTransfer.dropEffect = 'move';
        },
        handleDragEnter(e, item) {
            e.dataTransfer.effectAllowed = 'move'
            if (item === this.dragging) {
                return
            }
            const newItems = [...this.formValidate.images]
            const src = newItems.indexOf(this.dragging)
            const dst = newItems.indexOf(item)
            newItems.splice(dst, 0, ...newItems.splice(src, 1))
            this.formValidate.images = newItems;
        },
        // 添加自定义弹窗
        addCustomDialog(editorId) {
            window.UE.registerUI('test-dialog', function (editor, uiName) {
                // 创建 dialog
                let dialog = new window.UE.ui.Dialog({
                    // 指定弹出层中页面的路径，这里只能支持页面，路径参考常见问题 2
                    iframeUrl: '/admin/widget.images/index.html?fodder=dialog',
                    // 需要指定当前的编辑器实例
                    editor: editor,
                    // 指定 dialog 的名字
                    name: uiName,
                    // dialog 的标题
                    title: '上传图片',
                    // 指定 dialog 的外围样式
                    cssRules: 'width:1200px;height:500px;padding:20px;'
                });
                this.dialog = dialog;
                // 参考上面的自定义按钮
                var btn = new window.UE.ui.Button({
                    name: 'dialog-button',
                    title: '上传图片',
                    cssRules: `background-image: url(../../../assets/images/icons.png);background-position: -726px -77px;`,
                    onclick: function () {
                        // 渲染dialog
                        dialog.render();
                        dialog.open();
                    }
                });
                return btn;
            }, 37);
        }
    }
}
</script>

<style scoped lang="stylus">
    .custom-label
        display inline-flex
        line-height 1.5
    .grey
        color #999;
    .maxW /deep/.ivu-select-dropdown{
        max-width 600px;
    }
    .ivu-table-wrapper
        border-left: 1px solid #dcdee2;
        border-top: 1px solid #dcdee2;
    .tabBox_img
        width 50px;
        height 50px;
    .tabBox_img img
        width 100%;
        height 100%;
    .priceBox
        width 100%
    .form
        .picBox
            display: inline-block;
            cursor: pointer;
        .pictrue
            width:60px;
            height:60px;
            border:1px dotted rgba(0,0,0,0.1);
            margin-right:15px;
            display: inline-block;
            position: relative;
            cursor: pointer;
            img
                width 100%
                height 100%
            .btndel
                position: absolute;
                z-index: 9;
                width :20px !important;
                height: 20px !important;
                left: 46px;
                top: -4px;
        .upLoad {
            width: 58px;
            height: 58px;
            line-height: 58px;
            border: 1px dotted rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            background: rgba(0, 0, 0, 0.02);
            cursor: pointer;
        }
.lines {
	  border-bottom: 1px dashed #eee;
	  margin-bottom: 20px;
	}		
.tips {
	  display: inline-bolck;
	  font-size: 12px;
	  font-weight: 400;
	  color: #999999;
	  margin-top: 10px;
	  line-height: initial;
	}
.addClass{
		color: #1890FF;
		margin-left 14px;
		padding 9px 0;
		cursor pointer;
	}

        .form-submit {
	  /deep/.ivu-card{
	  	border-radius: 0;
	  }
    margin-bottom: 79px;

    .fixed-card {
        position: fixed;
        right: 0;
        bottom: 0;
        left: 200px;
        z-index: 99;
        box-shadow: 0 -1px 2px rgb(240, 240, 240);

        /deep/ .ivu-card-body {
            padding: 15px 16px 14px;
        }

        .ivu-form-item {
            margin-bottom: 0;
        }

        /deep/ .ivu-form-item-content {
            margin-right: 124px;
            text-align: center;
        }

        .ivu-btn {
            height: 36px;
            padding: 0 20px;
        }
    }
}
</style>
