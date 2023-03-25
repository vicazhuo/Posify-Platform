<template>
  <div class="goodList">
    <Form
      ref="formValidate"
      :model="formValidate"
      :label-width="labelWidth"
      :label-position="labelPosition"
      inline
      class="tabform"
    >
      <FormItem label="商品分类：" label-for="pid" v-if="!liveStatus">
        <Cascader
            :data="treeSelect"
            placeholder="请选择商品分类"
            change-on-select
            filterable
          style="width:250px;"
            @on-change="treeSearchs"
        ></Cascader>
      </FormItem>
      <FormItem label="商品标签：" label-for="pid" v-if="!liveStatus">
          <Select
            v-model="formValidate.store_label_id"
            style="width: 250px"
            clearable
            @on-change="userSearchs"
          >
            <Option v-for="item in labelSelect" :value="item.id" :key="item.id"
              >{{ item.label_name }}
            </Option>
          </Select>
      </FormItem>
      <FormItem label="商品搜索：" label-for="store_name">
        <Input
          placeholder="请输入商品名称,关键字,编号"
          v-model="formValidate.store_name"
          style="width: 250px;margin-right:14px;"
        />
        <Button type="primary" @click="userSearchs()">查询</Button>
      </FormItem>
    </Form>
    <Table
      ref="table"
      no-data-text="暂无数据"
      @on-select-all="selectAll" 
			@on-select-all-cancel="cancelAll" 
			@on-select="TableSelectRow" 
			@on-select-cancel="TableSelectCancelRow"
      no-filtered-data-text="暂无筛选结果"
      :columns="liveStatus == false ? columns4 : columns5"
      :data="tableList"
      :loading="loading"
      class="mr-20"
    >
      <template slot-scope="{ row }" slot="store_name">
          <Tooltip max-width="200" placement="bottom">
            <span class="line2">{{ row.store_name }}{{row.suk}}</span>
            <p slot="content">{{ row.store_name }}{{row.suk}}</p>
          </Tooltip>
      </template>
      <template slot-scope="{ row }" slot="image">
        <viewer>
          <div class="tabBox_img">
            <img v-lazy="row.image" />
          </div>
        </viewer>
      </template>
	  <template slot-scope="{ row }" slot="product_type" v-if="row.hasOwnProperty('product_type')">
	  	<span v-if="row.product_type==0">普通商品</span>
	  	<span v-if="row.product_type==1">卡密商品</span>
	  	<span v-if="row.product_type==3">虚拟商品</span>
	  </template>
    </Table>
    <div class="acea-row row-right page">
      <Page
        :total="total"
        show-elevator
        show-total
        @on-change="pageChange"
        :page-size="formValidate.limit"
      />
    </div>
    <div class="footer" slot="footer" v-if="many === 'many' && !diy">
      <Button
        type="primary"
        size="large"
        :loading="modal_loading"
        long
        @click="ok"
        >提交</Button
      >
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
import { cascaderListApi, changeListApi, allLabelApi } from "@/api/product";
import { liveGoods } from "@/api/live";
export default {
  name: "index",
  props: {
	goodsType: {
	  type: Number,
	  default: 0,
	  },
    is_new: {
      type: String,
      default: "",
    },
    diy: {
      type: Boolean,
      default: false,
    },
    isdiy: {
      type: Boolean,
      default: false,
    },
    ischeckbox: {
      type: Boolean,
      default: false,
    },
    liveStatus: {
      type: Boolean,
      default: false,
    },
    isLive: {
      type: Boolean,
      default: false,
    },
    datas: {
      type: Object,
      default: function () {
        return {};
      },
    },
  },
  data() {
    return {
			//选中商品集合
			selectEquips:[],
			// 选中的id集合
			selectEquipsIds: [],
			labelSelect:[],
			cateIds:[],
      modal_loading: false,
      treeSelect: [],
      formValidate: {
        page: 1,
        limit: 15,
        cate_id: "",
        store_name: "",
        is_new: this.is_new,
				store_label_id: ""
        // is_live: 0
      },
      total: 0,
      modals: false,
      loading: false,
      grid: {
        xl: 10,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableList: [],
      currentid: 0,
      productRow: {},
      columns4: [
        {
          title: "商品ID",
          key: "id",
        },
        {
          title: "图片",
          slot: "image",
          width:60,
        },
        {
          title: "商品名称",
          slot: "store_name",
          minWidth: 200,
        },
		{
		  title: "商品类型",
		  slot: "product_type",
		  minWidth: 100,
		},
        {
          title: "商品分类",
          key: "cate_name",
          minWidth: 150,
        },
      ],
      columns5: [
        {
          title: "商品ID",
          key: "id",
        },
        {
          title: "图片",
          slot: "image",
        },
        {
          title: "商品名称",
          key: "name",
          minWidth: 250,
        },
      ],
      images: [],
      many: "",
    };
  },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 120;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  created() {
    let radio = {
      width: 60,
      align: "center",
      render: (h, params) => {
        let id = params.row.id;
        let flag = false;
        if (this.currentid === id) {
          flag = true;
        } else {
          flag = false;
        }
        let self = this;
        return h("div", [
          h("Radio", {
            props: {
              value: flag,
            },
            on: {
              "on-change": () => {
                self.currentid = id;
                this.productRow = params.row;
                this.$emit("getProductId", this.productRow);
                if (this.productRow.id) {
                  if (this.$route.query.fodder === "image") {
                    /* eslint-disable */
                    let imageObject = {
                      image: this.productRow.image,
                      product_id: this.productRow.id,
                      name: this.productRow.name,
                    };
                    form_create_helper.set("image", imageObject);
                    form_create_helper.close("image");
                  }
                } else {
                  this.$Message.warning("请先选择商品");
                }
              },
            },
          }),
        ]);
      },
    };

    let checkbox = {
      type: "selection",
      width: 60,
      align: "center",
    };
    let many = "";
    if (this.ischeckbox) {
      many = "many";
    } else {
      many = this.$route.query.type;
    }
    this.many = many;
    if (many === "many") {
      this.columns4.unshift(checkbox);
      this.columns5.unshift(checkbox);
    } else {
      this.columns4.unshift(radio);
      this.columns5.unshift(radio);
    }
  },
  mounted() {
    this.goodsCategory();
    this.getList();
		this.getAllLabelApi();
  },
  methods: {
		// 判断是否选中
		sortData() {
			if (this.selectEquipsIds.length) {
				this.tableList.forEach(ele => {
					if (this.selectEquipsIds.includes(ele.id)) ele._checked = true;
				})
			}
		},
		// 选中一行
		TableSelectRow(selection, row) {
			if (!this.selectEquipsIds.includes(row.id)) {
				this.selectEquipsIds.push(row.id);
				this.selectEquips.push(row);
			}
		},
		// 取消选中一行
		TableSelectCancelRow(selection, row) {
			var _index = this.selectEquipsIds.indexOf(row.id);
			if (_index != -1) {
				this.selectEquipsIds.splice(_index, 1);
				this.selectEquips.splice(_index, 1);
			}
		},
		// 选中所有
		selectAll() {
			for (let i = this.tableList.length - 1; i >= 0; i--) {
				this.TableSelectRow(null, this.tableList[i]);
			}
		},
		// 取消选中所有
		cancelAll() {
			for (let i = this.tableList.length - 1; i >= 0; i--) {
				this.TableSelectCancelRow(null, this.tableList[i]);
			}
		},
		getAllLabelApi () {
			allLabelApi().then(res=>{
				this.labelSelect = res.data
			}).catch(err=>{
				this.$Message.error(err.msg);
			})
		},
		handleSelectAll () {
		  this.$refs.table.selectAll(false);
		},
    // 商品分类；
    goodsCategory() {
      cascaderListApi(1)
        .then((res) => {
          this.treeSelect = res.data;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    pageChange(index) {
      this.formValidate.page = index;
      this.getList();
    },
    // 列表
    getList() {
      this.loading = true;
      if (!this.liveStatus) {
        if (this.isLive) {
          this.formValidate.is_live = 1;
        }
		if(this.goodsType){
			this.formValidate.is_presale_product = 0;
			this.formValidate.is_vip_product = 0;
		}
		this.formValidate.cate_id = this.cateIds[this.cateIds.length-1]
        changeListApi(this.formValidate)
          .then(async (res) => {
            let data = res.data;
            this.tableList = data.list;
            this.total = res.data.count;
						this.sortData();
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$Message.error(res.msg);
          });
      } else {
        liveGoods({
          is_show: "1",
          status: "1",
          live_id: this.datas.id,
          kerword: this.formValidate.store_name,
          page: this.formValidate.page,
          limit: this.formValidate.limit,
        })
          .then(async (res) => {
            let data = res.data;
            data.list.forEach((el) => {
              el.image = el.cover_img;
            });
            this.tableList = data.list;
            this.total = res.data.count;
						this.sortData();
            this.loading = false;
          })
          .catch((res) => {
            this.loading = false;
            this.$Message.error(res.msg);
          });
      }
    },
		changeCheckbox(selection) {
		  let images = [];
		  selection.forEach(function (item) {
		    let imageObject = {
		      image: item.image,
		      product_id: item.id,
		      store_name: item.store_name,
		      temp_id: item.temp_id
		    };
		    images.push(imageObject);
		  });
		  this.images = images;
		  this.$emit("getProductDiy", selection);
		},
    ok() {
			let images = [];
			this.selectEquips.forEach(function (item) {
			  let imageObject = {
			    image: item.image,
			    product_id: item.id,
			    store_name: item.store_name,
			    temp_id: item.temp_id
			  };
			  images.push(imageObject);
			});
      if (images.length > 0) {
        if (this.$route.query.fodder === "image") {
          let imageValue = form_create_helper.get("image");
          form_create_helper.set("image", imageValue.concat(images));
          form_create_helper.close("image");
        } else {
          if(this.isdiy){
            this.$emit("getProductId", this.selectEquips);
          }else {
            this.$emit("getProductId", images);
          }
        }
      } else {
        this.$Message.warning("请先选择商品");
      }
    },
		treeSearchs(value){
			this.cateIds = value;
			this.formValidate.page = 1;
			this.getList();
		},
		// 表格搜索
    userSearchs() {
      this.formValidate.page = 1;
      this.getList();
    },
    clear() {
      this.productRow.id = "";
      this.currentid = "";
    },
  },
};
</script>

<style scoped lang="stylus">
.footer {
  margin: 15px 0;
}

.tabBox_img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;

  img {
    width: 100%;
    height: 100%;
  }
}

.tabform {
  >>> .ivu-form-item {
    margin-bottom: 16px !important;
  }
}

.btn {
  margin-top: 20px;
  float: right;
}

.goodList {
 
}
.mr-20{
  margin-right:10px;
}
</style>
