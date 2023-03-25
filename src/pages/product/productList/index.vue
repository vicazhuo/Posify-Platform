<template>
  <div class="article-manager">
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
         <Alert
          v-if="alertShow"
          banner
          closable
          type="warning"
          class="ivu-mt"
          @on-close="closeAlert"
        >
          <router-link to="/admin/product/add_product/0"
            >您有未完成的商品添加操作，点击此处可继续添加操作？</router-link
          >
        </Alert>
        <Alert show-icon closable>
          温馨提示：1.新增商品时可选统一规格或者多规格，满足商品不同销售属性场景；2.商品销售状态分为销售中且库存足够时才可下单购买
        </Alert>
        <Form
        ref="artFrom"
        inline
        :model="artFrom"
        :label-width="96"
        :label-position="labelPosition"
        @submit.native.prevent
      >
        <FormItem label="商品分类：" prop="cate_id">
            <el-cascader
            placeholder="请选择商品分类"
              style="width:250px"
              size="mini"
              v-model="artFrom.cate_id"
              :options="data1"
              :props="props"
              @on-change="cascaderSearchs"
              filterable
              clearable>
            </el-cascader>
        </FormItem>
        <FormItem label="商品搜索：" label-for="store_name">
          <Input
            placeholder="请输入商品名称,关键字,ID"
            v-model="artFrom.store_name"
            style="width:250px;margin-right:14px;"
          />
          <Button type="primary" @click="userSearchs()">查询</Button>
        </FormItem>
      </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <div class="new_tab">
        <Tabs v-model="artFrom.type" @on-click="onClickTab">
          <TabPane
            :label="item.name + ' (' + item.count + ')'"
            :name="item.type.toString()"
            v-for="(item, index) in headeNum"
            :key="index"
          />
        </Tabs>
      </div>
			<div class="acea-row row-between-wrapper">
				<div class="Button">
				  <router-link
				    v-auth="['product-product-save']"
				    :to="'/admin/product/add_product'"
				    ><Button type="primary" class="bnt mr15"
				      >添加商品</Button
				    ></router-link
				  >
				  <Button
				    v-auth="['product-crawl-save']"
				    type="success"
				    class="bnt mr15"
				    @click="onCopy"
				    >商品采集</Button
				  >
					<Upload
					    ref="upload"
							v-if="openErp"
							:show-upload-list="false"
					    :action="erpUrl"
					    :before-upload="beforeUpload"
					    :headers="header"
					    :on-success="upFile"
							:format="['xlsx']"
							:on-format-error="handleFormatError"
					  >
					    <Button>导入ERP商品</Button>
					</Upload>
					<Tooltip content="本页至少选中一项" :disabled="!!formSelection.length">
					  <Button
						  v-auth="['product-product-set_delivery_type']"
					    class="bnt mr15"
					    :disabled="!formSelection.length"
					    @click="deliveryType"
					    >配送方式</Button
					  >
					</Tooltip>
				  <Tooltip content="本页至少选中一项" :disabled="!!formSelection.length">
				    <Button
				      v-auth="['product-product-product_show']"
				      class="bnt mr15"
				      :disabled="!formSelection.length"
				      @click="onDismount"
				      v-show="artFrom.type === '1'"
				      >批量下架</Button
				    >
				  </Tooltip>
				  <Tooltip content="本页至少选中一项" :disabled="!!formSelection.length">
				    <Button
				      v-auth="['product-product-product_show']"
				      class="bnt mr15"
				      :disabled="!formSelection.length"
				      @click="onShelves"
				      v-show="artFrom.type === '2'"
				      >批量上架</Button
				    >
				  </Tooltip>
				  <Tooltip content="本页至少选中一项" :disabled="!!formSelection.length">
				    <Button
				      v-auth="['export-storeProduct']"
				      class="export"
				      :disabled="!formSelection.length"
				      @click="exports"
				      >导出</Button
				    >
				  </Tooltip>
				</div>
				<div>
					<Button
					  v-if="openErp"
					  class="bnt mr15"
						@click="frontDownload"
					  >下载erp商品模板</Button
					>
				</div>
			</div>
			<!-- artFrom.type !== '1' && artFrom.type !== '2' ? columns2 : (artFrom.type === '6')?columns3 : columns -->
      <Table
        ref="table"
        :columns="artFrom.type === '4' || artFrom.type === '5' ? columns2 : artFrom.type === '6'?columns3:columns"
        :data="tableList"
        class="ivu-mt"
        :loading="loading"
        highlight-row
        @on-selection-change="onSelectTab"
        no-data-text="暂无数据"
        no-filtered-data-text="暂无筛选结果"
        @on-select-all="selectAll"
        @on-select-all-cancel="selectAll"
      >
        <template slot-scope="{ row }" slot="id">
          {{ row.id }}
        </template>
        <template slot-scope="{ row }" slot="image">
          <viewer>
           <div class="tabBox_img">
              <img v-lazy="row.image" />
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row }" slot="store_name">
			<Tooltip theme="dark" max-width="300" :delay="600" :content="row.store_name">
				<div class="line2">{{row.store_name}}</div>
			</Tooltip>
        </template>
		<template slot-scope="{ row }" slot="product_type">
			<span v-if="row.product_type==0">普通商品</span>
			<span v-if="row.product_type==1">卡密商品</span>
			<span v-if="row.product_type==3">虚拟商品</span>
		</template>
        <template slot-scope="{ row }" slot="state">
          <i-switch
            v-model="row.is_show"
            :value="row.is_show"
            :true-value="1"
            :false-value="0"
						:disabled="artFrom.type == 6?true:false"
            @on-change="changeSwitch(row)"
            size="large"
          >
            <span slot="open">上架</span>
            <span slot="close">下架</span>
          </i-switch>
          <div v-if="row.auto_off_time" style="margin-top: 10px;line-height: 1.2;">定时下架：<br>{{ row.auto_off_time | timeFormat }}</div>
        </template>
		<template slot-scope="{ row, index }" slot="action">
		  <a @click="edit(row)">编辑</a>
		  <Divider type="vertical" />
		  <a @click="lookGoods(row.id)">预览</a>
		  <Divider type="vertical" />
		  <template>
		    <Dropdown
		      @on-click="changeMenu(row, $event, index)"
					:transfer="true"
		    >
		      <a href="javascript:void(0)">
		        更多
		        <Icon type="ios-arrow-down"></Icon>
		      </a>
		      <DropdownMenu slot="list">
		        <DropdownItem name="1">查看评论</DropdownItem>
		        <DropdownItem name="2" v-if="artFrom.type === '6'">恢复商品</DropdownItem>
		        <DropdownItem name="3" v-else>移到回收站</DropdownItem>
		        <DropdownItem name="4"  v-if="row.product_type!=1 && openErp == false">库存管理</DropdownItem>
		      </DropdownMenu>
		    </Dropdown>
		  </template>
		</template>
        <!-- <template slot-scope="{ row, index }" slot="action">
          <a @click="edit(row)">编辑</a>
          <Divider type="vertical" />
          <a @click.stop="lookGoods(row.id)">预览</a>
          <Divider type="vertical" />
          <router-link :to="{ path: '/admin/product/product_reply/' + row.id }"
            ><a>查看评论</a></router-link
          >
          <Divider type="vertical" />
          <a @click="del(row, '恢复商品', index)" v-if="artFrom.type === '6'"
            >恢复商品</a
          >
          <a @click="del(row, '移入回收站', index)" v-else>移到回收站</a>
		  <Divider type="vertical" v-if="row.product_type!=1" />
		  <a @click="stockControl(row)" v-if="row.product_type!=1">库存管理</a>
        </template> -->
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="artFrom.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="artFrom.limit"
        />
      </div>
      <attribute
        :attrTemplate="attrTemplate"
        v-on:changeTemplate="changeTemplate"
      ></attribute>
    </Card>
    <!-- 生成淘宝京东表单-->
    <Modal
      v-model="modals"
      :z-index="100"
      class="Box"
      scrollable
      footer-hide
      closable
      title="复制淘宝、天猫、京东、苏宁、1688"
      :mask-closable="false"
      width="1200"
      height="500"
    >
      <tao-bao ref="taobaos" v-if="modals" @on-close="onClose"></tao-bao>
    </Modal>
		<!-- 配送方式 -->
		<Modal v-model="modalsType"  scrollable title="配送方式"  :closable="false"
    class-name="vertical-center-modal">
		    <Form :label-width="80" @submit.native.prevent>
						<FormItem label="配送方式：" class="deliveryStyle">
						  <CheckboxGroup v-model="delivery_type">
						    <Checkbox label="1" disabled>快递</Checkbox>
						    <Checkbox label="3">门店配送</Checkbox>
						    <Checkbox label="2">到店自提</Checkbox>
						  </CheckboxGroup>
						</FormItem>
		    </Form>
		    <div slot="footer">
		        <Button  type="primary"  @click="putDelivery">提交</Button>
		        <Button  @click="cancelDelivery">取消</Button>
		    </div>
		</Modal>
    <!-- 商品弹窗 -->
    <div v-if="isProductBox">
      <div class="bg" @click.stop="isProductBox = false"></div>
      <goodsDetail :goodsId="goodsId" :product="1"></goodsDetail>
    </div>
		<stockEdit ref="stock" @stockChange="stockChange"></stockEdit>
  </div>
</template>

<script>
import goodsDetail from "@/pages/kefu/pc/components/goods_detail";
import stockEdit from "../components/stockEdit.vue"
import expandRow from "./tableExpand.vue";
import attribute from "./attribute";
import toExcel from "../../../utils/Excel.js";
import { mapState } from "vuex";
import taoBao from "./taoBao";
import dayjs from "dayjs";
import Setting from "@/setting";
import util from "@/libs/util";
import {
  getGoodHeade,
  getGoods,
  PostgoodsIsShow,
  treeListApi,
  productShowApi,
  productUnshowApi,
  storeProductApi,
  cascaderListApi,
  productCache,
  cacheDelete,
	setDeliveryType
} from "@/api/product";
import { erpConfig,erpProduct } from "@/api/erp";
import exportExcel from "@/utils/newToExcel.js";

export default {
  name: "product_productList",
  components: { expandRow, attribute, taoBao, goodsDetail, stockEdit },
  filters: {
    timeFormat: value => dayjs(value * 1000).format('YYYY-MM-DD HH:mm')
  },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    ...mapState("admin/userLevel", ["categoryId"]),
    labelWidth() {
      return this.isMobile ? undefined : 75;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  data() {
    return {
			header: {}, //请求头部信息
			erpUrl: Setting.apiBaseURL + "/file/upload/1",
      template: false,
      modals: false,
			modalsType:false,
			delivery_type:['1'],
      grid: {
        xl: 7,
        lg: 8,
        md: 12,
        sm: 24,
        xs: 24,
      },
      artFrom: {
        page: 1,
        limit: 15,
        cate_id: '',
        type: "1",
        store_name: "",
        excel: 0,
      },
      list: [],
      tableList: [],
      headeNum: [],
      treeSelect: [],
      isProductBox: false,
      loading: false,
      columns: [
        {
          type: "expand",
          width: 50,
          render: (h, params) => {
            return h(expandRow, {
              props: {
                row: params.row,
              },
            });
          },
        },
        {
          width: 60,
          align: "center",
          renderHeader: (h, params) => {
            return h(
              "div",
              {
                class: {
                  "select-panel": true,
                },
                on: {
                  mouseenter: (e) => {
                    this.display = "block";
                  },
                  mouseleave: (e) => {
                    this.display = "none";
                  },
                },
              },
              [
                h("Checkbox", {
                  props: {
                    value: this.checkBox,
                  },
                  on: {
                    "on-change": (e) => {
                      this.checkBox = e;
                      this.$refs.table.selectAll(this.checkBox);
                      this.isAll = e ? 0 : -1;
                    },
                  },
                }),
                h(
                  "div",
                  {
                    style: {
                      position: "absolute",
                      top: 0,
                      zIndex: 2,
                      display: this.display,
                      width: "80px",
                      padding: "5px",
                      borderRadius: "4px",
                      backgroundColor: "#fff",
                      boxShadow: "0 0px 5px rgba(0, 0, 0, 0.2)",
                      transform: "translateX(25%)",
                    },
                  },
                  [
                    h(
                      "div",
                      {
                        class: {
                          "select-item": true,
                          on: this.isAll === 0,
                        },
                        style: {
                          padding: "1px 6px",
                          cursor: "pointer",
                          height: "50%",
                        },
                        on: {
                          click: (e) => {
                            if (this.isAll === 0) {
                              this.isAll = -1;
                              this.checkBox = false;
                              this.$refs.table.selectAll(this.checkBox);
                            } else {
                              this.isAll = 0;
                              if (!this.formSelection.length) {
                                this.checkBox = true;
                                this.$refs.table.selectAll(this.checkBox);
                              }
                            }
                            this.display = "none";
                          },
                        },
                      },
                      // "选择当页"
                    ),
                    h(
                      "div",
                      {
                        class: {
                          "select-item": true,
                          on: this.isAll === 1,
                        },
                        style: {
                          padding: "4px",
                          cursor: "pointer"
                        },
                        on: {
                          click: (e) => {
                            if (this.isAll === 1) {
                              this.isAll = -1;
                              this.checkBox = false;
                            } else {
                              this.isAll = 1;
                              this.checkBox = true;
                            }
                            this.$refs.table.selectAll(this.checkBox);
                            this.display = "none";
                          },
                        },
                      },
                      "选择全部"
                    ),
                  ]
                ),
              ]
            );
          },
          render: (h, params) => {
            return h("Checkbox", {
              props: {
                value: params.row.checkBox,
              },
              on: {
                "on-change": (e) => {
                  if (e) {
                    this.formSelection.push(params.row);
                  } else {
                    this.checkBox = false;
                    this.formSelection.forEach((item, index) => {
                      if (item.id === params.row.id) {
                        this.formSelection.splice(index, 1);
                      }
                    });
                  }
                  this.ids = this.formSelection.map((item) => item.id);
                  this.isAll = this.formSelection.length ? 0 : -1;
                  // this.responseData为查询出的结果数据
                  // params.row.checkBox = e;
                  params.row.checkBox = e;
                  this.tableList[params.index].checkBox = e;
                },
              },
              ref: "checkbox",
              refInFor: true,
            });
          },
        },
        {
          title: "商品ID",
          slot: "id",
          width: 80,
        },
        {
          title: "商品图",
          slot: "image",
          width: 60,
        },
        {
          title: "商品名称",
          slot: "store_name",
          minWidth: 250,
        },
				{
					title: "商品类型",
					slot: "product_type",
					minWidth: 100,
				},
        {
          title: "商品售价",
          key: "price",
          minWidth: 90,
        },
        {
          title: "销量",
          key: "sales",
          sortable: true,
          minWidth: 90,
        },
        {
          title: "库存",
          key: "stock",
          minWidth: 80,
        },
        {
          title: "排序",
          key: "sort",
          minWidth: 70,
        },
        {
          title: "状态",
          slot: "state",
          width: 120,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 150,
        },
      ],
      data: [],
      total: 0,
      props: { emitPath: false, multiple: true },
      attrTemplate: false,
      ids: [],
      display: "none",
      formSelection: [],
      selectionCopy: [],
      checkBox: false,
      isAll: -1,
      data1: [],
      value1: [],
      alertShow: false,
      goodsId: "",
	  columns3:[],
	  openErp:false
      // activeKey:1
    };
  },
  watch: {
    $route() {
      if (this.$route.fullPath === "/admin/product/product_list?type=5") {
        this.getPath();
      }
    },
    formSelection(value) {
      // this.checkBox = value.length === this.tableList.length;
    },
    tableList: {
      deep: true,
      handler(value) {
        value.forEach((item) => {
          this.formSelection.forEach((itm) => {
            if (itm.id === item.id) {
              item.checkBox = true;
            }
          });
        });
        const arr = this.tableList.filter((item) => item.checkBox);
        if (this.tableList.length) {
          this.checkBox = this.tableList.length === arr.length;
        } else {
          this.checkBox = false;
        }
      },
    },
  },
  created() {
    // this.artFrom.type = this.$route.query.key || 1
    // this.columns2 = [...this.columns];
    // if (name !== '1' && name !== '2') {
    //     this.columns2.shift({
    //         type: 'selection',
    //         width: 60,
    //         align: 'center'
    //     })
    // }
		this.getToken();
    productCache()
      .then((res) => {
        const info = res.data.info;
        if (!Array.isArray(info)) {
          this.alertShow = true;
        }
      })
      .catch((err) => {
        this.$Message.error(err.msg);
      });
	this.getErpConfig();
  },
  mounted() {
    this.goodHeade();
    this.goodsCategory();
    if (this.$route.fullPath === "/admin/product/product_list?type=5") {
      this.getPath();
    } else {
      this.getDataList();
    }
    // this.getDataList();
  },
	activated(){
			this.getDataList();
	},
  beforeRouteEnter(to, from, next) {
      next(vm => {
				if (from.path.indexOf('/admin/product/add_product') != -1) {
				    document.documentElement.scrollTop = to.meta.scollTopPosition;
				}
      });
  },
  beforeRouteLeave(to, from, next) {
      if(from.meta.keepAlive) {
  　    from.meta.scollTopPosition = document.documentElement.scrollTop;
      }
      next();
  },
	methods: {
		frontDownload(){
			let a = document.createElement("a"); //创建一个<a></a>标签
			  a.href = "/statics/ERP商品导入模板.xlsx"; // 给a标签的href属性值加上地址，注意，这里是绝对路径，不用加 点.
			  a.download = "ERP商品导入模板.xlsx"; //设置下载文件文件名，这里加上.xlsx指定文件类型，pdf文件就指定.fpd即可
			  a.style.display = "none"; // 障眼法藏起来a标签
			  document.body.appendChild(a); // 将a标签追加到文档对象中
			  a.click(); // 模拟点击了a标签，会触发a标签的href的读取，浏览器就会自动下载了
			  a.remove(); // 一次性的，用完就删除a标签
		},
		handleFormatError(file){
			return this.$Message.error('必须上传xlsx格式文件');
		},
		// 上传头部token
		getToken() {
		  this.header["Authori-zation"] = "Bearer " + util.cookies.get("token");
		},
		upFile(res) {
		  erpProduct({ path: res.data.src }).then((res) => {
				this.$Message.success(res.msg);
				this.getDataList();
		  }).catch(err=>{
			  return this.$Message.error(err.msg);
		  })
		},
		beforeUpload() {
		  let promise = new Promise((resolve) => {
		    this.$nextTick(function () {
		      resolve(true);
		    });
		  });
		  return promise;
		},
		//erp配置
		getErpConfig(){
			erpConfig().then(res=>{
				this.openErp = res.data.open_erp;
			}).catch(err=>{
				this.$Message.error(err.msg);
			})
		},
		stockChange(stock){
			this.tableList.forEach(item=>{
				if(this.goodsId == item.id){
					item.stock = stock;
				}
			})
		},
		// 库存管理
		stockControl(row){
			this.goodsId = row.id;
			this.$refs.stock.modals = true;
			this.$refs.stock.productAttrs(row);
		},
		cancelDelivery(){
			this.modalsType = false;
			this.delivery_type = ['1'];
			// this.isAll = 0;
			// this.$refs.table.selectAll(this.isAll);
			// this.getDataList();
		},
		deliveryType(){
			this.modalsType = true;
		},
		putDelivery () {
		    if(this.ids.length === 0){
		    	this.$Message.error('请选择要配送的商品')
		    }else{
		    	let data = {
		    		all:this.isAll,
		    		delivery_type:this.delivery_type
		    	}
		    	if(this.isAll === 0){
		    		data.ids = this.ids;
		    	}
		    	setDeliveryType(data).then(res => {
		    	    this.$Message.success(res.msg);
		    	    this.modalsType = false;
							this.delivery_type = ['1'];
							this.isAll = 0;
							this.$refs.table.selectAll(this.isAll);
							this.getDataList();
		    	}).catch(res => {
		    	    this.$Message.error(res.msg);
		    	})
		    }
		},
    // 商品详情
    lookGoods(id) {
      this.goodsId = id;
      this.isProductBox = true;
    },
    closeAlert() {
      cacheDelete()
        .then((res) => {
          this.$Message.success(res.msg);
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    selectAll(row) {
      if (row.length) {
        this.formSelection = row;
        this.selectionCopy = row;
      }

      this.selectionCopy.forEach((item, index) => {
        item.checkBox = this.checkBox;
        this.$set(this.tableList, index, item);
      });
    },
    getPath() {
      this.columns2 = [...this.columns];
      if (name !== "1" && name !== "2") {
        this.columns2.shift();
      }
      this.artFrom.page = 1;
      this.artFrom.type = this.$route.query.type.toString();
      this.getDataList();
    },
    changeMenu(row,name,index){
		switch (name) {
		  case "1":
		  this.$router.push({ path: "/admin/product/product_reply/" + row.id });
		    break;
		  case "2":
		    this.del(row, '恢复商品', index);
		    break;
		  case "3":
		    this.del(row, '移入回收站', index);
		    break;
		  case "4":
		    this.stockControl(row);
		}
	},
    // 数据导出；
    async exports() {
      let [th, filekey, data, fileName] = [[], [], [], ""];
      let formValidate = this.artFrom;
      let excelData = {};
      if (this.isAll === 0) {
        excelData.ids = this.ids.join();
      } else if (this.isAll === 1) {
        excelData.cate_id = formValidate.cate_id;
        excelData.type = formValidate.type;
        excelData.store_name = formValidate.store_name;
      }
      excelData.page = 1;
      for (let i = 0; i < excelData.page + 1; i++) {
        let lebData = await this.getExcelData(excelData);
        if (!fileName) fileName = lebData.filename;
        if (!filekey.length) {
          filekey = lebData.filekey;
        }
        if (!th.length) th = lebData.header;
        if (lebData.export.length) {
          data = data.concat(lebData.export);
          excelData.page++;
        } else {
          exportExcel(th, filekey, fileName, data);
          return;
        }
      }
    },
    getExcelData(excelData) {
      return new Promise((resolve, reject) => {
        storeProductApi(excelData).then((res) => {
          return resolve(res.data);
        });
      });
    },
    changeTemplate(e) {
      // this.template = e;
    },
    freight() {
      this.$refs.template.isTemplate = true;
    },
    // 批量上架
    onShelves() {
      if (this.ids.length === 0) {
        this.$Message.warning("请选择要上架的商品");
      } else {
        let data = {
          all: this.isAll,
        };
        if (this.isAll === 0) {
          data.ids = this.ids;
        } else if (this.isAll === 1) {
          data.where = {
            cate_id: this.artFrom.cate_id,
            excel: this.artFrom.excel,
            store_name: this.artFrom.store_name,
            type: this.artFrom.type,
          };
        }
        productShowApi(data)
          .then((res) => {
            this.$Message.success(res.msg);
            this.goodHeade();
            this.getDataList();
          })
          .catch((res) => {
            this.$Message.error(res.msg);
          });
      }
    },
    // 批量下架
    onDismount() {
      if (this.ids.length === 0) {
        this.$Message.warning("请选择要下架的商品");
      } else {
        let data = {
          all: this.isAll,
        };
        if (this.isAll === 0) {
          data.ids = this.ids;
        } else if (this.isAll === 1) {
          data.where = {
            cate_id: this.artFrom.cate_id,
            excel: this.artFrom.excel,
            store_name: this.artFrom.store_name,
            type: this.artFrom.type,
          };
        }
        productUnshowApi(data)
          .then((res) => {
            this.$Message.success(res.msg);
            this.goodHeade();
            this.getDataList();
          })
          .catch((res) => {
            this.$Message.error(res.msg);
          });
      }
    },
    // 全选
    onSelectTab(selection) {
      this.formSelection = selection;
      let data = [];
      selection.map((item) => {
        data.push(item.id);
      });
      this.ids = data;
    },
    // 添加淘宝商品成功
    onClose() {
      this.modals = false;
    },
    // 复制淘宝
    onCopy() {
      this.$router.push({path:'/admin/product/add_product',query:{type:-1}});
      // this.modals = true;
    },
    // tab选择
    onClickTab(name) {
      this.artFrom.type = name;
      this.columns2 = [...this.columns];
      if (name !== "1" && name !== "2") {
        this.columns2.shift();
      }
			let obj = [...this.columns];
			obj.shift();
			obj.splice(8,1);
			this.columns3 = obj;
      this.checkBox = false;
			this.artFrom.page = 1;
      this.getDataList();
    },
    // 下拉树
    handleCheckChange(data) {
      let value = "";
      let title = "";
      this.list = [];
      this.artFrom.cate_id = 0;
      data.forEach((item, index) => {
        value += `${item.id},`;
        title += `${item.title},`;
      });
      value = value.substring(0, value.length - 1);
      title = title.substring(0, title.length - 1);
      this.list.push({
        value,
        title,
      });
      this.artFrom.cate_id = value;
      this.getDataList();
    },
    // 获取商品表单头数量
    goodHeade() {
			let data = {
				store_name: this.artFrom.store_name,
				cate_id: this.artFrom.cate_id || ''
			}
      getGoodHeade(data)
        .then((res) => {
          this.headeNum = res.data.list;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 商品分类；
    goodsCategory() {
      // treeListApi(1).then(res => {
      //     this.treeSelect = res.data;
      // }).catch(res => {
      //     this.$Message.error(res.msg);
      // })
      cascaderListApi(1)
        .then((res) => {
          this.data1 = res.data;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 商品列表；
    getDataList() {
      this.loading = true;
      this.artFrom.cate_id = this.artFrom.cate_id || "";
      getGoods(this.artFrom)
        .then((res) => {
          let data = res.data;
          this.tableList = data.list.map((item) => {
            if (this.isAll === 1) {
              item.checkBox = true;
            } else {
              item.checkBox = false;
            }
            return item;
          });
          this.total = res.data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$Message.error(res.msg);
        });
    },
    pageChange(status) {
      this.artFrom.page = status;
      this.getDataList();
      this.$refs.table.clearCurrentRow();
    },
    cascaderSearchs(value, selectedData) {
      this.artFrom.cate_id = value[value.length - 1];
      this.userSearchs();
    },
    // 表格搜索
    userSearchs() {
      this.artFrom.page = 1;
      this.formSelection = [];
			this.goodHeade();
      this.getDataList();
    },
    // 上下架
    changeSwitch(row) {
      PostgoodsIsShow(row.id, row.is_show)
        .then((res) => {
          this.$Message.success(res.msg);
          this.goodHeade();
          this.getDataList();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
		  this.goodHeade();
		  this.getDataList();
        });
    },
    // 数据导出；
    exportData: function () {
      let th = [
        "商品名称",
        "商品简介",
        "商品分类",
        "价格",
        "库存",
        "销量",
        "收藏人数",
      ];
      let filterVal = [
        "store_name",
        "store_info",
        "cate_name",
        "price",
        "stock",
        "sales",
        "collect",
      ];
      this.where.page = "nopage";
      getGoods(this.where).then((res) => {
        let data = res.data.map((v) => filterVal.map((k) => v[k]));
        let fileTime = Date.parse(new Date());
        let [fileName, fileType, sheetName] = [
          "商户数据_" + fileTime,
          "xlsx",
          "商户数据",
        ];
        toExcel({ th, data, fileName, fileType, sheetName });
      });
    },
    // 属性弹出；
    attrTap() {
      this.attrTemplate = true;
    },
    changeTemplate(msg) {
      this.attrTemplate = msg;
    },
    // 编辑
    edit(row) {
      this.$router.push({ path: "/admin/product/add_product/" + row.id });
    },
    // 确认
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `product/product/${row.id}`,
        method: "DELETE",
        ids: "",
				tips: '确定要恢复商品吗？'
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.tableList.splice(num, 1);
          this.goodHeade();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 删除成功
    // submitModel () {
    //     this.tableList.splice(this.delfromData.num, 1);
    //     this.goodHeade();
    // }
    // 排序
    sortChanged(e) {
      this.artFrom[e.key] = e.order;
      this.getDataList();
    },
  },
};
</script>
<style scoped lang="stylus">
.line2{
	max-height 36px;
}	
.bg {
    z-index: 100;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}
.deliveryStyle /deep/.ivu-checkbox-wrapper{
	  margin-right: 14px;
}
.Button /deep/.ivu-upload{
	width 105px;
	display inline-block;
	margin-right 10px;
}
/deep/.ivu-modal-mask {
  z-index: 999 !important;
}

/deep/.ivu-modal-wrap {
  z-index: 999 !important;
}
/deep/.ivu-alert{
  margin-bottom: 20px;
}
.Box {
  >>> .ivu-modal-body {
    height: 700px;
    overflow: auto;
  }
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

/deep/.ivu-table-cell-expand-expanded {
  margin-top: -6px;
  margin-right: 33px;
  transition: none;

  .ivu-icon {
    vertical-align: 2px;
  }
}

/deep/.ivu-table-header {
  // overflow visible
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
  /*background: #f3f3f3;*/
}
.new_tab {
    >>>.ivu-tabs-nav .ivu-tabs-tab{
        padding:4px 16px 20px !important;
        font-weight: 500;
    }
  }
</style>
