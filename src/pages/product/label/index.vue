<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          ref="LabelCateFrom"
          inline
          :model="LabelCateFrom"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <Row :gutter="24" type="flex" justify="end">
            <Col span="24">
              <FormItem label="搜索：">
                <Input
                  v-model="LabelCateFrom.name"
                  placeholder="标签名称、ID"
                  style="width: 250px;margin-right:14px;"
                ></Input>
                <Button type="primary" @click="labelCateSearchs">查询</Button>
              </FormItem>
            </Col>
          </Row>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Row type="flex">
        <Col v-bind="grid">
          <Button type="primary" @click="add">添加标签组</Button>
        </Col>
      </Row>
      <Table
        :columns="columns1"
        :data="list"
        ref="table"
        class="mt25"
        :loading="loading"
        highlight-row
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
      >
        <template slot-scope="{ row, index }" slot="labelName">
          <div class="list acea-row">
            <div
              class="item acea-row row-center-wrapper mr10 mt5 mb5"
              v-for="(item, indexs) in row.productLabel"
              :key="indexs"
            >
              {{ item.label_name
              }}<span
                class="iconfont iconcha"
                @click="delLabel(row.productLabel, '删除标签', indexs)"
              ></span>
            </div>
            <Input
              v-model="row.labelVal"
              placeholder="按回车键完成"
              class="mr10 mt5"
              style="width: 150px"
              maxlength="20" 
              show-word-limit
              v-if="row.isShow"
              @on-enter="addLabel(row)"
            />
            <div
              class="bnt acea-row row-center-wrapper mt5"
              @click="addInput(row)"
            >
              <span class="iconfont iconjiahao"></span>
            </div>
          </div>
        </template>
        <template slot-scope="{ row, index }" slot="action">
          <a @click="edit(row.id)">编辑</a>
          <Divider type="vertical" />
          <a @click="del(row, '删除标签组', index)">删除</a>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="LabelCateFrom.limit"
        />
      </div>
    </Card>
  </div>
</template>

<script>
import { mapState } from "vuex";
import {
  productLabelCate,
  labelCateCreate,
  productLabelEdit,
  productLabel,
} from "@/api/product";
export default {
  name: "label",
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      loading: false,
      columns1: [
        {
          title: "ID",
          key: "id",
          width: 60,
        },
        {
          title: "标签分组",
          key: "name",
          minWidth: 60,
        },
        {
          title: "商品标签",
          slot: "labelName",
          minWidth: 400,
        },
        {
          title: "排序",
          key: "sort",
          width: 60,
        },
        {
          title: "创建时间",
          key: "add_time",
          minWidth: 100,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 120,
        },
      ],
      LabelCateFrom: {
        page: 1,
        limit: 15,
        name: "",
      },
      list: [],
      total: 0,
    };
  },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 96;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  created() {
    this.getList();
  },
  methods: {
    addLabel(row) {
      let data = {
        id: 0,
        label_cate: row.id,
        label_name: row.labelVal,
      };
      productLabel(data)
        .then((res) => {
          this.getList();
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    addInput(row) {
      row.isShow = true;
    },
    labelCateSearchs() {
      this.LabelCateFrom.page = 1;
      this.list = [];
      this.getList();
    },
    // 添加
    add() {
      this.$modalForm(labelCateCreate()).then(() => this.getList());
    },
    // 标签列表
    getList() {
      this.loading = true;
      productLabelCate(this.LabelCateFrom)
        .then((res) => {
          let data = res.data;
          data.list.forEach((item) => {
            item.isShow = false;
            item.labelVal = "";
          });
          this.list = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((err) => {
          this.loading = false;
          this.$Message.error(err.msg);
        });
    },
    pageChange(index) {
      this.LabelCateFrom.page = index;
      this.getList();
    },
    //修改
    edit(id) {
      this.$modalForm(productLabelEdit(id)).then(() => this.getList());
    },
    delLabel(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `product/label/${row[num].id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.getList();
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    // 删除
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `product/label_cate/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.list.splice(num, 1);
					if (!this.list.length) {
					  this.LabelCateFrom.page =
					    this.LabelCateFrom.page == 1 ? 1 : this.LabelCateFrom.page - 1;
					}
					this.getList();
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
  },
};
</script>

<style scoped lang="stylus">
.list {
  .item {
    min-width: 62px;
    min-height: 28px;
    background: rgba(24, 144, 255, 0.06);
    border-radius: 4px;
    border: 1px solid #1890FF;
    color: #1890FF;
    font-size: 13px;
    padding: 0 5px;
    position: relative;
    cursor: pointer;

    .iconfont {
      position: absolute;
      right: -8px;
      top: -11px;
      color: #999999;
      display: none;
    }

    &:hover {
      .iconfont {
        display: block;
      }
    }
  }

  .bnt {
    width: 38px;
    height: 28px;
    border-radius: 4px;
    border: 1px solid #1890FF;
    color: #1890FF;
    font-size: 18px;

    .iconfont {
      font-size: 14px;
    }
  }
}
</style>