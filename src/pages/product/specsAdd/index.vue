<template>
  <div class="form-submit">
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <router-link :to="{ path: '/admin/product/specs' }"
            >
            <!-- <Button icon="ios-arrow-back" size="small" class="mr20"
              >返回</Button
            > -->
              <div class="font-sm after-line">
								<span class="iconfont iconfanhui"></span> 
								<span class="pl10">返回</span>
							</div>
            </router-link
          >
          <span
            v-text="
              $route.params.id !== '0' ? '编辑商品参数模板' : '添加商品参数模板'
            "
            class="mr20 ml16"
          ></span>
        </div>
      </PageHeader>
    </div>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Form
        class="formValidate mt20"
        ref="formValidate"
        :rules="ruleValidate"
        :model="formValidate"
        :label-width="labelWidth"
        :label-position="labelPosition"
        @submit.native.prevent
      >
        <Row :gutter="24" type="flex">
          <Col span="24">
            <FormItem label="参数模板名称：" prop="name">
              <Input
                v-model="formValidate.name"
                placeholder="请输入参数模板名称"
                v-width="'400'"
              />
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem label="排序：" props="">
              <InputNumber
                placeholder="排序"
                element-id="sort"
                :min="1"
                :precision="0"
                v-model="formValidate.sort"
                v-width="'200'"
              />
            </FormItem>
          </Col>
          <Col span="24">
            <FormItem label="" props="">
              <Table
                border
                :columns="columns"
                :data="data"
                ref="table"
                class="table"
                width="700"
              >
                <template slot-scope="{ row, index }" slot="action">
                  <a @click="del(index)" v-if="index > 0">删除</a>
                </template>
              </Table>
              <Button class="mt20" @click="add">添加参数</Button>
            </FormItem>
          </Col>
        </Row>
      </Form>
    </Card>
    <Card
      :bordered="false"
      dis-hover
      class="fixed-card"
      :style="{ left: `${!menuCollapse ? '200px' : isMobile ? '0' : '80px'}` }"
    >
      <Form>
        <FormItem>
          <Button
            type="primary"
            class="submission"
            @click="handleSubmit('formValidate')"
            >保存</Button
          >
        </FormItem>
      </Form>
    </Card>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";
import { productSpecs, productSpecsInfo } from "@/api/product";
export default {
  name: "specsAdd",
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      formValidate: {
        id: 0,
        name: "",
        sort: 0,
        specs: [],
      },
      ruleValidate: {
        name: [
          { required: true, message: "请输入参数模板名称", trigger: "blur" },
        ],
      },
      columns: [
        {
          title: "参数名称",
          key: "name",
          width: 150,
          render: (h, params) => {
            return h("div", [
              h("Input", {
                props: {
                  value: params.row.name,
                  placeholder: "请输入参数名称",
                },
                on: {
                  "on-change": (e) => {
                    params.row.name = e.target.value;
                    this.data[params.index].name = e.target.value;
                  },
                },
              }),
            ]);
          },
        },
        {
          title: "参数值",
          key: "value",
          width: 300,
          render: (h, params) => {
            return h("div", [
              h("Input", {
                props: {
                  value: params.row.value,
                  placeholder: "请输入参数值",
                },
                on: {
                  "on-change": (e) => {
                    params.row.value = e.target.value;
                    this.data[params.index].value = e.target.value;
                  },
                },
              }),
            ]);
          },
        },
        {
          title: "排序",
          key: "sort",
          width: 100,
          render: (h, params) => {
            return h("div", [
              h("InputNumber", {
                props: {
                  value: params.row.sort || 0,
                  placeholder: "排序",
                  precision: 0,
                },
                on: {
                  "on-change": (e) => {
                    params.row.sort = e;
                    this.data[params.index].sort = e;
                  },
                },
              }),
            ]);
          },
        },
        {
          title: "操作",
          slot: "action",
          minWidth: 120,
        },
      ],
      data: [],
    };
  },
  computed: {
    ...mapState("admin/layout", ["isMobile", "menuCollapse"]),
    labelWidth() {
      return this.isMobile ? undefined : 120;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  created() {
    this.add();
    this.getInfo();
  },
  mounted() {
    this.setCopyrightShow({ value: false });
  },
  destroyed() {
    this.setCopyrightShow({ value: true });
  },
  methods: {
    ...mapMutations("admin/layout", ["setCopyrightShow"]),
    getInfo() {
      productSpecsInfo(this.$route.params.id).then((res) => {
        this.formValidate = res.data;
        this.data = res.data.specs;
      });
    },
    del(index) {
      this.data.splice(index, 1);
    },
    add() {
      let obj = { name: "", value: "", sort: 0 };
      this.data.push(obj);
    },
    handleSubmit(name) {
      this.$refs[name].validate((valid) => {
        if (valid) {
          this.formValidate.id = this.$route.params.id;
          this.formValidate.specs = this.data;
          for (let i = 0; i < this.formValidate.specs.length; i++) {
            let data = this.formValidate.specs[i];
            if (!data.name.trim()) {
              return this.$Message.error("请输入参数名称");
            }
            if (!data.value.trim()) {
              return this.$Message.error("请输入参数值");
            }
          }
          productSpecs(this.formValidate)
            .then((res) => {
              this.$Message.success(res.msg);
              this.$router.push({ path: "/admin/product/specs" });
            })
            .catch((err) => {
              this.$Message.error(err.msg);
            });
        } else {
          this.$Message.error("请输入参数模板名称");
        }
      });
    },
  },
};
</script>

<style scoped lang="stylus">
.table {
  /deep/.ivu-table-header table {
    border: 0 !important;
  }

  /deep/.ivu-table-header thead tr th:nth-of-type(1) {
    padding-left: 16px;
  }

  /deep/.ivu-table td:nth-of-type(1) {
    padding-left: 16px;
  }

  /deep/.ivu-table-cell {
    padding: 0 !important;
  }

  /deep/.ivu-table-border th, /deep/.ivu-table-border td {
    border-right: unset;
  }

  /deep/.ivu-table td {
    height: 59px;
  }
}

.form-submit {
  /deep/.ivu-card {
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

.after-line {
    display: inline-block;
    position: relative;
    margin-right: 16px;
}
.ml16 {
    margin-left: 16px;
}
</style>
