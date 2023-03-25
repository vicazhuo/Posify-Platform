<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
        inline
        ref="artFrom"
        :model="artFrom"
        :label-width="labelWidth"
        :label-position="labelPosition"
        class="tabform"
        @submit.native.prevent
      >
        <Row :gutter="24" type="flex" justify="end">
          <Col span="24" class="ivu-text-left">
            <FormItem label="规格搜索：">
              <Input
                v-model="artFrom.rule_name"
                placeholder="请输入规格分类名称"
                style="width: 250px;margin-right:14px;"
              ></Input>
              <Button type="primary" @click="userSearchs">查询</Button>
            </FormItem>
          </Col>
        </Row>
      </Form>
      </div>
    </Card>
      
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Button
              v-auth="['product-rule-save']"
              class="mr20"
              type="primary"
              @click="addAttr"
              >添加商品规格</Button
            >
            <Tooltip
              content="本页至少选中一项"
              :disabled="!!selectionList.length"
            >
              <Button
                v-auth="['product-product-rule-delete']"
                :disabled="!selectionList.length"
                @click="del(null, '批量删除规格')"
                >批量删除</Button
              >
            </Tooltip>
      <Table
        class="mt25"
        ref="selection"
        :columns="columns4"
        :data="tableList"
        :loading="loading"
        highlight-row
        @on-selection-change="onSelectTab"
        no-data-text="暂无数据"
        no-filtered-data-text="暂无筛选结果"
        @on-select-all="selectAll"
        @on-select-all-cancel="selectAll"
      >
        <template slot-scope="{ row }" slot="attr_value">
          <span
            v-for="(item, index) in row.attr_value"
            :key="index"
            v-text="item"
            style="display: block"
          ></span>
        </template>
        <template slot-scope="{ row }" slot="action">
          <a @click="edit(row)">编辑</a>
          <Divider type="vertical" />
          <a @click="del(row, '删除规格')">删除</a>
        </template>
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
    </Card>
    <add-attr ref="addattr" @getList="userSearchs"></add-attr>
  </div>
</template>

<script>
import { mapState } from "vuex";
import addAttr from "./addAttr";
import { ruleListApi } from "@/api/product";
export default {
  name: "productAttr",
  components: { addAttr },
  data() {
    return {
      loading: false,
      artFrom: {
        page: 1,
        limit: 10,
        rule_name: "",
      },
      columns4: [
        // {
        //     type: 'selection',
        //     minWidth: 60
        // },
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
											this.$refs.selection.selectAll(this.checkBox);
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
                              this.$refs.selection.selectAll(this.checkBox);
                            } else {
                              this.isAll = 0;
                              if (!this.selectionList.length) {
                                this.checkBox = true;
                                this.$refs.selection.selectAll(this.checkBox);
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
                          cursor: "pointer",
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
                            this.$refs.selection.selectAll(this.checkBox);
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
                    this.selectionList.push(params.row);
                  } else {
                    this.checkBox = false;
                    this.selectionList.forEach((item, index) => {
                      if (item.id === params.row.id) {
                        this.selectionList.splice(index, 1);
                      }
                    });
                  }
                  this.isAll = this.selectionList.length ? 0 : -1;
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
          title: "ID",
          key: "id",
          width: 80,
        },
        {
          title: "分类",
          key: "rule_name",
          minWidth: 150,
        },
        {
          title: "规格名",
          key: "attr_name",
          minWidth: 250,
        },
        {
          title: "规格值",
          slot: "attr_value",
          minWidth: 300,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 120,
        },
      ],
      tableList: [],
      total: 0,
      ids: "",
      selectionList: [],
      selectionCopy: [],
      display: "none",
      isAll: 0,
      checkBox: false,
    };
  },
  watch: {
    tableList: {
      deep: true,
      handler(value) {
        value.forEach((item) => {
          this.selectionList.forEach((itm) => {
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
    selectionList: {
      deep: true,
      handler(value) {
        let arr = value.map((item) => item.id);
        this.ids = arr.join();
      },
    },
  },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    ...mapState("admin/order", ["orderChartType"]),
    labelWidth() {
      return this.isMobile ? undefined : 96;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  created() {
    this.getDataList();
  },
  methods: {
    selectAll(row) {
      if (row.length) {
        this.selectionList = row;
        this.selectionCopy = row;
      }

      this.selectionCopy.forEach((item, index) => {
        item.checkBox = this.checkBox;
        this.$set(this.tableList, index, item);
      });
    },
    // 全选
    onSelectTab(selection) {
      this.selectionList = selection;
      let data = [];
      this.selectionList.map((item) => {
        data.push(item.id);
      });
      this.ids = data.join(",");
    },
    // 删除
    del(row, tit) {
      let data = {};
      if (tit === "批量删除规格") {
        if (this.selectionList.length === 0)
          return this.$Message.warning("请选择要删除的规格！");
        data = { all: this.isAll };
        if (this.isAll === 0) {
          data.ids = this.ids;
        } else if (this.isAll === 1) {
          // data.where = this.artFrom;
          data.where = {
            rule_name: this.artFrom.rule_name,
          };
        }
      } else {
        data = {
          ids: row.id,
        };
      }
      let delfromData = {
        title: tit,
        num: 0,
        url: `product/product/rule/delete`,
        method: "DELETE",
        ids: data,
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.artFrom.page = 1
          this.getDataList();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    addAttr() {
      this.$refs.addattr.ids = 0;
      this.$refs.addattr.modal = true;
    },
    // 编辑
    edit(row) {
      // this.$refs.addattr.ids = row.id;
      this.$refs.addattr.modal = true;
      this.$refs.addattr.getIofo(row);
    },
    // 列表；
    getDataList() {
      this.loading = true;
      ruleListApi(this.artFrom)
        .then((res) => {
          let data = res.data;
					data.list.forEach(item => {
					    item.checkBox = false;
					})
		  this.tableList = data.list
		  if(this.isAll == 1){
			  this.tableList = data.list.map((item) => {
				item.checkBox = true;
				return item;
			  });
		  }
          this.total = res.data.count;
          // this.isAll = -1;
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
    },
    // 表格搜索
    userSearchs() {
      this.artFrom.page = 1;
      this.selectionList = [];
      this.getDataList();
    },
  },
};
</script>

<style scoped lang="stylus">
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
  // background: #f3f3f3;
}
</style>
