<template>
  <div class="form-submit">
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <router-link :to="{ path: '/admin/marketing/store_discounts/index' }"
            >
            <div class="font-sm after-line">
              <span class="iconfont iconfanhui"></span>
              <span class="pl10">返回</span>
            </div>
            </router-link
          >
          <span
            v-text="$route.query.id ? '编辑套餐信息' : '添加套餐信息'"
            class="mr20 ml16"
          ></span>
        </div>
      </PageHeader>
    </div>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Row type="flex" class="acea-row row-middle">
        <Col span="23">
          <Form
            class="form"
            ref="formValidate"
            :rules="ruleValidate"
            :model="formValidate"
            @on-validate="validate"
            :label-width="labelWidth"
            :label-position="labelPosition"
            @submit.native.prevent
          >
            <Row v-show="current === 0" type="flex">
              <Col span="24">
                <FormItem label="套餐名称：" prop="title" label-for="title">
                  <Input
                    class="perW30"
                    placeholder="请输入套餐名称"
                    element-id="title"
                    v-model="formValidate.title"
                    :maxlength="20"
                  />
                </FormItem>
              </Col>
              <Col span="24">
                <FormItem
                  label="套餐时间："
                  :prop="formValidate.is_time == 1 ? 'time' : ''"
                >
                  <RadioGroup
                    element-id="is_time"
                    v-model="formValidate.is_time"
                  >
                    <Radio :label="0">不限时</Radio>
                    <Radio :label="1" class="radio">限时</Radio>
                  </RadioGroup>
                  <div
                    class="acea-row row-middle"
                    v-if="formValidate.is_time == 1"
                  >
                    <DatePicker
                      :editable="false"
                      type="daterange"
                      format="yyyy-MM-dd"
                      placeholder="请选择套餐时间"
                      @on-change="onchangeTime"
                      class="perW30"
                      v-model="formValidate.time"
                    ></DatePicker>
                  </div>
                </FormItem>
              </Col>
              <Col span="24">
                <FormItem label="套餐类型：" prop="type" label-for="type">
                  <RadioGroup element-id="type" v-model="formValidate.type">
                    <Radio :label="0" class="radio">固定套餐</Radio>
                    <Radio :label="1">搭配套餐</Radio>
                  </RadioGroup>
                  <div class="ml100 grey">
                    {{
                      formValidate.type == 0
                        ? "套餐内所有商品打包销售，消费者需成套购买整个套餐"
                        : "套餐内主商品必选，搭配商品任意选择1件及以上即可购买套餐"
                    }}
                  </div>
                </FormItem>
              </Col>
              <Col span="24" v-if="formValidate.type == 1">
                <FormItem
                  label="套餐主商品："
                  prop="products"
                  label-for="products"
                >
                  <Table
                    :data="specsMainData"
                    :columns="columns"
                    border
                    @on-drag-drop="onDragDrop"
                  >
                    <template slot-scope="{ row, index }" slot="store_name">
                      <div class="product-data">
                        <img class="image" :src="row.image" />
                        <div>{{ row.store_name }}</div>
                      </div>
                    </template>
                    <template slot-scope="{ row, index }" slot="attr">
                      <div v-for="(item, index) in row.attr" :key="index">
                        {{ item.value }} | {{ item.price }}
                      </div>
                    </template>
                    <template slot-scope="{ row, index }" slot="setting">
                      <a @click="editGoods(row, index, 'Main')">设置规格</a>
                      <Divider type="vertical" />
                      <a @click="deleteGoods(index, 'Main')">删除</a>
                    </template>
                  </Table>
                  <Button
                    v-if="specsMainData.length < 1"
                    type="primary"
                    class="submission mr15 mt20"
                    @click="addGoods('Main')"
                    >添加商品</Button
                  >
                </FormItem>
              </Col>
              <Col span="24">
                <FormItem
                  :label="
                    formValidate.type == 1 ? '套餐搭配商品：' : '套餐商品'
                  "
                  prop="products"
                  label-for="products"
                >
                  <Table
                    :data="specsData"
                    :columns="columns"
                    border
                    @on-drag-drop="onDragDrop"
                  >
                    <template slot-scope="{ row, index }" slot="store_name">
                      <div class="product-data">
                        <img class="image" :src="row.image" />
                        <!-- <div>{{ row.store_name }}</div>
                      </div> -->
                        <div class="info">
                          <Tooltip max-width="200" placement="bottom">
                            <span class="line2">{{ row.store_name }}{{row.suk}}</span>
                            <p slot="content">{{ row.store_name }}{{row.suk}}</p>
                          </Tooltip>
                        </div>
                      </div>
                    </template>
                    <template slot-scope="{ row, index }" slot="attr">
                      <div v-for="(item, index) in row.attr" :key="index">
                        {{ item.value }} | {{ item.price }}
                      </div>
                    </template>
                    <template slot-scope="{ row, index }" slot="setting">
                      <a @click="editGoods(row, index, 'Other')">设置规格</a>
                      <Divider type="vertical" />
                      <a @click="deleteGoods(index, 'Other')">删除</a>
                    </template>
                  </Table>
                  <Button
                    v-if="specsData.length < 50"
                    type="primary"
                    class="submission mr15 mt20"
                    @click="addGoods('Other')"
                    >添加商品</Button
                  >
                </FormItem>
              </Col>
              <Col span="24">
                <FormItem prop="image">
                  <div class="custom-label" slot="label">
                    <div>
                      <div>套餐主图</div>
                      <div>(750*750)</div>
                    </div>
                    <div>：</div>
                  </div>
                  <div class="acea-row">
                    <div class="pictrue" v-if="formValidate.image">
                      <img v-lazy="formValidate.image" />
                      <Button
                        shape="circle"
                        icon="md-close"
                        @click.native="handleRemove()"
                        class="btndel"
                      ></Button>
                    </div>
                    <div
                      v-else
                      class="upLoad acea-row row-center-wrapper"
                      @click="modalPicTap('dan', 'danFrom')"
                    >
                      <Icon
                        type="ios-camera-outline"
                        size="26"
                        class="iconfonts"
                      />
                    </div>
                  </div> </FormItem
              ></Col>
              <Col span="24">
                <FormItem
                  label="套餐数量："
                  :prop="formValidate.is_limit == 1 ? 'limit_num' : ''"
                  label-for="limit_num"
                >
                  <RadioGroup
                    element-id="is_limit"
                    v-model="formValidate.is_limit"
                  >
                    <Radio :label="0">不限量</Radio>
                    <Radio :label="1" class="radio">限量</Radio>
                  </RadioGroup>
                  <InputNumber
                    v-if="formValidate.is_limit == 1"
                    class="perW30"
                    placeholder="请输入限量数量"
                    element-id="limit_num"
                    v-model="formValidate.limit_num"
                    :min="0"
                    :max="99999"
                    :precision="0"
                  ></InputNumber>
                </FormItem>
              </Col>
              <Col span="24">
                <FormItem label="关联用户标签：" prop="link_ids">
									<div class="labelInput acea-row row-between-wrapper" @click="openLabel">
										<div style="width: 90%;">
											<div v-if="dataLabel.length">
												<Tag closable v-for="(item,index) in dataLabel" @on-close="closeLabel(item)">{{item.label_name}}</Tag>
											</div>
											<span class="span" v-else>选择用户关联标签</span>
										</div>
										<div class="iconfont iconxiayi"></div>
									</div>
                <!--  <div class="acea-row row-middle">
                    <Select
                      multiple
                      v-model="formValidate.link_ids"
                      class="perW30"
                      placeholder="关联用户标签"
                    >
                      <Option
                        v-for="item in userLabelList"
                        :value="item.id"
                        :key="item.id"
                        >{{ item.label_name }}</Option
                      >
                    </Select>
                  </div> -->
                </FormItem>
              </Col>
              <Col span="24">
                <FormItem label="排序：" label-for="sort">
                  <InputNumber
                    class="perW30"
                    placeholder="请输入排序序号"
                    element-id="sort"
                    v-model="formValidate.sort"
                    :min="0"
                    :max="999999"
                    :precision="0"
                  ></InputNumber>
                </FormItem>
              </Col>
              <Col span="24">
                <FormItem
                  label="套餐包邮："
                  prop="free_shipping"
                  label-for="status"
                >
                  <Switch
                    size="large"
                    element-id="free_shipping"
                    v-model="formValidate.free_shipping"
                    :false-value="0"
                    :true-value="1"
                  >
                    <span false-value="0" slot="close">否</span>
                    <span true-value="1" slot="open">是</span>
                  </Switch>
                  <div class="ml100 grey">
                    不包邮时，将按照商品的运费模板进行计算
                  </div>
                </FormItem>
              </Col>
              <Col span="24">
                <FormItem label="上架状态：" prop="status" label-for="status">
                  <Switch
                    size="large"
                    element-id="status"
                    v-model="formValidate.status"
                    :false-value="0"
                    :true-value="1"
                  >
                    <span slot="close">下架</span>
                    <span slot="open">上架</span>
                  </Switch>
                </FormItem>
              </Col>
            </Row>

            <!-- <FormItem>
              <Button
                type="primary"
                class="submission"
                :loading="submitOpen"
                @click="next('formValidate')"
              >
                <div v-if="!submitOpen">提交</div>
                <div v-else>提交中</div>
              </Button>
            </FormItem> -->
            <Spin size="large" fix v-if="spinShow"></Spin>
          </Form>
        </Col>
      </Row>
    </Card>
    <Card :bordered="false" dis-hover class="fixed-card" :style="{left: `${!menuCollapse?'200px':isMobile?'0':'80px'}`}">
			<Form>
        <FormItem>
          <Button
            type="primary"
            class="submission"
            :loading="submitOpen"
            @click="next('formValidate')"
          >
            <div v-if="!submitOpen">提交</div>
            <div v-else>提交中</div>
          </Button>
        </FormItem>
			</Form>
		</Card>	
    <!-- 上传图片-->
    <Modal
      v-model="modalPic"
      width="960px"
      scrollable
      footer-hide
      closable
      title="上传商品图"
      :mask-closable="false"
      :z-index="1"
    >
      <uploadPictures
        :isChoice="isChoice"
        @getPic="getPic"
        v-if="modalPic"
      ></uploadPictures>
    </Modal>
    <Modal
      v-model="modals"
      title="商品列表"
      footerHide
      class="paymentFooter"
      scrollable
      width="900"
      @on-cancel="cancel"
    >
      <goods-list
        ref="goodslist"
        v-if="modals"
        :ischeckbox="true"
        :goodsType="1"
        @getProductId="getProductId"
      ></goods-list>
    </Modal>
    <Modal
      v-model="ggModel"
      title="规格设置"
      footerHide
      class="paymentFooter"
      scrollable
      width="900"
      @on-cancel="cancel"
    >
      <div class="df">
        <span style="width: 75px"> 优惠价：</span>
        <InputNumber
          class="m10"
          v-model="rate_price"
          :min="0"
          style="width: 100%"
        ></InputNumber>
        <Button type="primary" @click="changeRatePrice()">
          <div>批量添加</div>
        </Button>
      </div>
      <Table
        v-if="ggModel"
        :data="manyFormValidate"
        @on-selection-change="selectOne"
        :columns="header"
        border
        height="500"
      >
        <template slot-scope="{ row, index }" slot="image">
          <div class="product-data">
            <img class="image" :src="row.pic" />
          </div>
        </template>
      </Table>
      <Button class="mt10" type="primary" @click="getAttr()" long>
        <div>提交</div>
      </Button>
    </Modal>
		<!-- 用户标签 -->
		<Modal
		  v-model="labelShow"
		  scrollable
		  title="请选择用户标签"
		  :closable="false"
		  width="320"
		  :footer-hide="true"
			:mask-closable="false"
		>
		  <userLabel ref="userLabel" @activeData="activeData" @close="labelClose"></userLabel>
		</Modal>
  </div>
</template>

<script>
import { mapState } from "vuex";
import goodsList from "@/components/goodsList/index";
import uploadPictures from "@/components/uploadPictures";
import {
  lotteryDetailApi,
  discountsSave,
  discountsGetDetails,
} from "@/api/discounts"; //详情 创建 编辑
import { discountsFrom } from "./formRule/discountsFrom";
import {
  // labelListApi,
  generateIdGetAttrApi,
  getProductAttr,
} from "@/api/product";
import { productAttrsApi } from "@/api/marketing";
import { formatDate } from "@/utils/validate";
import userLabel from "@/components/labelList";

export default {
  name: "lotteryCreate",
  components: { goodsList, uploadPictures, userLabel },
  data() {
    return {
			dataLabel:[],
			labelShow:false,
      ggModel: false,
      modals: false,
      loading: false,
      manyFormValidate: [],
      selectProductAttrList: [],
      header: [
        {
          type: "selection",
          width: 60,
          align: "center",
        },
        {
          title: "图片",
          slot: "image",
          width: 120,
          align: "center",
        },
        {
          title: "规格",
          key: "value",
          align: "center",
          minWidth: 120,
        },
        {
          title: "售价",
          key: "p_price",
          align: "center",
          minWidth: 120,
        },
        {
          title: "优惠价",
          key: "price",
          align: "center",
          minWidth: 120,
          render: (h, params) => {
            return h("div", [
              h("InputNumber", {
                props: {
                  min: 0,
                  // max: params.row.cart_num,
                  value: params.row.price,
                },
                on: {
                  "on-change": (e) => {
                    console.log(e);
                    params.row.price = e;
                    this.manyFormValidate[params.index] = params.row;
                    console.log(this.selectProductAttrList);
                    this.selectProductAttrList.forEach((v, index) => {
                      if (v.value === params.row.value) {
                        this.selectProductAttrList.splice(index, 1, params.row);
                      }
                    });
                  },
                },
              }),
            ]);
          },
        },
      ],
      // userLabelList: [], //用户标签列表
      userLevelListApi: [], //用户等级列表
      submitOpen: false,
      spinShow: false,
      addGoodsModel: false,
      editData: {},
      myConfig: {
        autoHeightEnabled: false, // 编辑器不自动被内容撑高
        initialFrameHeight: 500, // 初始容器高度
        initialFrameWidth: "100%", // 初始容器宽度
        UEDITOR_HOME_URL: "/admin/UEditor/",
        serverUrl: "",
      },
      isChoice: "单选",
      current: 0,
      modalPic: false,
      modal_loading: false,
      images: [],
      templateList: [
        { id: 0, name: "非付费会员" },
        { id: 1, name: "付费会员" },
      ],
      columns: [
        {
          title: "商品名称",
          slot: "store_name",
          width: 300,
          align: "center",
        },
        {
          title: "参与规格",
          slot: "attr",
          align: "center",
          minWidth: 120,
        },
        {
          title: "操作",
          slot: "setting",
          align: "center",
          width: 180,
        },
      ],
      goodsAddType: "",
      specsMainData: [],
      specsData: [],
      formValidate: {
        title: "", //套餐名称
        type: 0, //套餐类型
        image: "", //套餐主图
        is_time: 0, //是否限时
        is_limit: 0, //限量1/不限量0
        limit_num: 0, //限量
        link_ids: [], //参与用户标签
        time: [], //套餐时间
        sort: 0, //排序
        free_shipping: 1, //是否包邮
        status: 1,
        products: [],
      },
      ruleValidate: discountsFrom,
      currentid: "",
      picTit: "",
      tableIndex: 0,
      copy: 0,
      editIndex: null,
      id: "",
      rate_price: 0,
    };
  },
  filters: {
    typeName(type) {
      if (type == 1) {
        return "未中奖";
      } else if (type == 2) {
        return "积分";
      } else if (type == 3) {
        return "余额";
      } else if (type == 4) {
        return "红包";
      } else if (type == 5) {
        return "优惠券";
      } else if (type == 6) {
        return "商品";
      }
    },
  },
  computed: {
    ...mapState("admin/layout", ["isMobile","menuCollapse"]),
    labelWidth() {
      return this.isMobile ? undefined : 135;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  mounted() {
    console.log(this.$route.query);
    if (this.$route.query.id) {
      this.id = this.$route.query.id;
      this.current = 0;
      console.log(this.$route.query.copy);
      this.copy = this.$route.query.copy || 0;
      this.getInfo();
    }
    // this.labelListApi();
  },
  methods: {
		closeLabel(label){
			let index = this.dataLabel.indexOf(this.dataLabel.filter(d=>d.id == label.id)[0]);
			this.dataLabel.splice(index,1);
		},
		activeData(dataLabel){
			this.labelShow = false;
			this.dataLabel = dataLabel;
		},
		openLabel(row) {
		  this.labelShow = true;
			this.$refs.userLabel.userLabel(JSON.parse(JSON.stringify(this.dataLabel)));
		},
		// 标签弹窗关闭
		labelClose() {
		  this.labelShow = false;
		},
    //勾选规格
    selectOne(data) {
      this.selectProductAttrList = data;
    },
    //提交选中规格
    getAttr() {
      if (!this.selectProductAttrList.length)
        return this.$Message.warning("请先选择规格");
      let type;
      this.goodsAddType === "Main"
        ? (type = this.specsMainData)
        : (type = this.specsData);

      this.$set(type[this.tabIndex], "attr", this.selectProductAttrList);
      this.$set(
        type[this.tabIndex],
        "items",
        this.selectProductAttrList[0].items
      );
      console.log(type[this.tabIndex]);
      this.ggModel = false;
    },
    clearOne(data) {
      console.log(data);
    },
    //修改优惠价格
    changeRatePrice() {
      for (let val of this.manyFormValidate) {
        this.$set(val, "price", this.rate_price);
        let list = this.selectProductAttrList;
        for (let i = 0; i < list.length; i++) {
          if (val.value === list[i].value) {
            this.$set(val, "_checked", true);
          }
        }
      }
    },
    // 规格列表
    generate(id, type) {
      productAttrsApi(id, type)
        .then((res) => {
          let info = JSON.parse(JSON.stringify(res.data.info.attrs));
          let items = JSON.parse(JSON.stringify(res.data.info.items));
          for (let k = 0; k < info.length; k++) {
            const element = info[k];
            element.items = items;
          }
          console.log(info);
          let data =
            this.goodsAddType === "Main"
              ? this.specsMainData[this.tabIndex].attr || []
              : this.specsData[this.tabIndex].attr || [];
          console.log(data);
          this.selectProductAttrList = [];
          if (data.length) {
            for (let i = 0; i < data.length; i++) {
              const element = data[i];
              for (let j = 0; j < info.length; j++) {
                if (element.value === info[j].value) {
                  this.$set(info[j], "price", element.price);
                  this.$set(info[j], "_checked", true);
                  this.selectProductAttrList.push(info[j]);
                }
              }
            }
          }

          this.manyFormValidate = info;
          console.log(this.manyFormValidate);
          this.ggModel = true;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 选择的商品
    getProductId(productList) {
      if (
        this.goodsAddType === "Main" &&
        (productList.length > 1 || this.specsMainData.length > 0)
      ) {
        this.$Message.warning("最多添加一个商品");
        return;
      }

      let pData = JSON.parse(JSON.stringify(productList));
      for (let i = 0; i < pData.length; i++) {
        pData[i].attr = [];
      }
      this.$nextTick((e) => {
        if (this.goodsAddType === "Main") {
          let arr = this.concat_arr(this.specsData, pData);
          console.log(arr);
          if (arr.length) {
            return this.$Message.warning("已存在搭配商品");
          }
          this.specsMainData = this.specsMainData.concat(pData);
        } else {
          console.log(this.specsData, pData);
          let arr = this.concat_arr(this.specsData, pData);
          let mainArr = this.concat_arr(this.specsMainData, pData);
          console.log(arr);
          if (arr.length || mainArr.length) {
            return this.$Message.warning("已存在相同主商品/搭配商品");
          }
          this.specsData = this.specsData.concat(pData);
        }
        this.modals = false;
      });
    },
    //用户标签列表
    // labelListApi() {
    //   labelListApi().then((res) => {
    //     this.userLabelList = res.data.list;
    //     this.userLabelList.map((i) => {
    //       i.id = i.id + "";
    //     });
    //   });
    // },
    // 具体日期
    onchangeTime(e) {
      console.log(e);
      this.$nextTick(() => {
        this.$set(this.formValidate, "time", e);
        // this.formValidate.time = e;
        console.log(this.formValidate.time);
      });
    },
    // 详情
    getInfo() {
      this.spinShow = true;
      discountsGetDetails(this.id).then((res) => {
        this.spinShow = false;
        this.formValidate = res.data;
        // this.formValidate.link_ids = res.data.link_ids || [];
				this.dataLabel = res.data.link_ids || [];
        this.formValidate.time = res.data.time || [];
        for (let i = 0; i < res.data.products.length; i++) {
          const element = res.data.products[i];
          if (element.type == 1) {
            this.specsMainData.push(element);
          } else {
            this.specsData.push(element);
          }
        }
      });
    },
    // 下一步
    next(name) {
      if (this.formValidate.type === 1 && !this.specsMainData.length) {
        return this.$Message.warning("请选择主商品");
      } else if (this.formValidate.type === 0 && !this.specsData.length) {
        return this.$Message.warning("请选择套餐商品");
      }
      this.specsMainData.forEach((v) => {
        v.type = 1;
      });
      this.specsData.forEach((v) => {
        v.type = 0;
      });
      let productData;
      if (this.formValidate.type == 1) {
        console.log(this.specsMainData);
        productData = this.specsMainData.concat(this.specsData);
      } else {
        productData = this.specsData;
      }

      if (productData.length < 2) {
        return this.$Message.warning("套餐内商品不能少于2个");
      }
      for (let i = 0; i < productData.length; i++) {
        const element = productData[i];
        if (!element.items) {
          console.log(element);
          return this.$Message.warning(`请选择 ${element.store_name} 的规格`);
        }
      }
      this.formValidate.products = productData;
			let activeIds = [];
			this.dataLabel.forEach((item)=>{
				activeIds.push(item.id)
			});
			this.formValidate.link_ids = activeIds
      if (this.submitOpen) return false;
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.submitOpen = true;
          if (this.id && !this.copy) {
            discountsSave(this.formValidate)
              .then(async (res) => {
                this.$Message.success(res.msg);
                setTimeout(() => {
                  this.submitOpen = false;
                  this.$router.push({
                    path: "/admin/marketing/store_discounts/index",
                  });
                }, 500);
              })
              .catch((res) => {
                this.submitOpen = false;
                this.$Message.error(res.msg);
              });
          } else {
            discountsSave(this.formValidate)
              .then(async (res) => {
                console.log(res);
                this.$Message.success(res.msg);
                setTimeout(() => {
                  this.submitOpen = false;
                  this.$router.push({
                    path: "/admin/marketing/store_discounts/index",
                  });
                }, 500);
              })
              .catch((res) => {
                this.submitOpen = false;
                this.$Message.error(res.msg);
              });
          }
        } else {
          return false;
        }
      });
    },
    concat_arr(arr1, arr2) {
      let result = [];
      for (var i = 0; i < arr1.length; i++) {
        var obj = arr1[i];
        var num = obj.product_id;
        var flag = false;
        for (var j = 0; j < arr2.length; j++) {
          var aj = arr2[j];
          var n = aj.product_id;
          if (n == num) {
            flag = true;
            break;
          }
        }
        if (flag) {
          result.push(obj);
        }
      }
      return result;
      console.log(result);
    },
    // 点击商品图
    modalPicTap(tit, picTit, index) {
      this.modalPic = true;
      this.isChoice = tit === "dan" ? "单选" : "多选";
      this.picTit = picTit || "";
      this.tableIndex = index;
    },
    // 获取单张图片信息
    getPic(pc) {
      switch (this.picTit) {
        case "danFrom":
          this.formValidate.image = pc.att_dir;
          break;
        default:
          this.specsData[this.tableIndex].image = pc.att_dir;
      }
      this.modalPic = false;
    },
    handleRemove() {
      this.formValidate.image = "";
    },
    // 表单验证
    validate(prop, status, error) {
      if (status === false) {
        this.$Message.error(error);
        return false;
      } else {
        return true;
      }
    },
    cancel() {
      this.modals = false;
    },
    //新增商品
    addGoods(type) {
      this.goodsAddType = type;
      this.modals = true;
    },
    //编辑设置规格
    editGoods(row, index, type) {
      this.goodsAddType = type;
      this.tabIndex = index;
      this.generate(row.product_id, 0);
    },
    //删除商品
    deleteGoods(index, type) {
      if (type === "Main") {
        this.specsMainData.splice(index, 1);
      } else {
        this.specsData.splice(index, 1);
      }
    },
    //获取数组中某个字段之和
    sumArr(arr, name) {
      let arrData = [];
      for (let i = 0; i < arr.length; i++) {
        arrData.push(arr[i][name]);
      }
      return eval(arrData.join("+"));
    },
    addGoodsData(data) {
      console.log(data);
      console.log(this.editIndex, data);
      console.log(this.specsData);
      this.editIndex != null
        ? this.specsData.concat(data)
        : this.specsData.length < 8
        ? this.specsData.push(data)
        : this.$Message.warning("最多添加8个奖品");
      this.addGoodsModel = false;
      this.editIndex = null;
      console.log(this.prize, data);
    },
    //修改排序
    onDragDrop(a, b) {
      console.log(a, b);
      this.specsData.splice(
        b,
        1,
        ...this.specsData.splice(a, 1, this.specsData[b])
      );
    },
    //时间格式转换
    formatDate(time) {
      if (time) {
        let date = new Date(time * 1000);
        return formatDate(date, "yyyy-MM-dd hh:mm");
      } else {
        return "";
      }
    },
  },
};
</script>

<style scoped lang="stylus">
.labelInput{
	border: 1px solid #dcdee2;
	width 50%;
	padding 0 5px;
	border-radius 5px;
	min-height 30px;
	cursor pointer;
	.span{
		 color: #c5c8ce;
	}
	.iconxiayi{
		font-size 12px
	}
}		
.custom-label {
  display: inline-flex;
  line-height: 1.5;
}

.grey {
  color: #999;
}

.maxW /deep/.ivu-select-dropdown {
  max-width: 600px;
}

.ivu-table-wrapper {
  border-left: 1px solid #dcdee2;
  border-top: 1px solid #dcdee2;
}

.tabBox_img {
  width: 50px;
  height: 50px;
}

.tabBox_img img {
  width: 100%;
  height: 100%;
}

.priceBox {
  width: 100%;
}

.form {
  .picBox {
    display: inline-block;
    cursor: pointer;
  }

  .pictrue {
    width: 60px;
    height: 60px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    margin-right: 15px;
    display: inline-block;
    position: relative;
    cursor: pointer;

    img {
      width: 100%;
      height: 100%;
    }

    .btndel {
      position: absolute;
      z-index: 9;
      width: 20px !important;
      height: 20px !important;
      left: 46px;
      top: -4px;
    }
  }

  .upLoad {
    width: 58px;
    height: 58px;
    line-height: 58px;
    border: 1px dotted rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.02);
    cursor: pointer;
  }
}

.product-data {
  display: flex;
  align-items: center;

  .image {
    width: 50px !important;
    height: 50px !important;
    margin-right: 10px;
  }
}

.df {
  display: flex;
  align-items: center;
}

.m10 {
  margin: 10px;
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
