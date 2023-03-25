<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
         inline
          ref="ensureFrom"
          :model="ensureFrom"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <Row :gutter="24" type="flex" justify="end">
            <Col span="24">
              <FormItem label="搜索：">
                <Input
                  v-model="ensureFrom.name"
                  placeholder="请输入服务条款、ID"
                  style="width: 250px;margin-right:14px;"
                ></Input>
                <Button type="primary" @click="ensureSearchs">查询</Button>
              </FormItem>
            </Col>
          </Row>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Row type="flex">
        <Col v-bind="grid">
          <Button type="primary" @click="add">添加保障服务</Button>
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
        <template slot-scope="{ row, index }" slot="image">
          <viewer>
            <div class="tabBox_img">
              <img v-lazy="row.image" />
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row, index }" slot="status">
          <i-switch
            v-model="row.status"
            :true-value="1"
            :false-value="0"
            size="large"
            @on-change="showBnt(row)"
          >
            <span slot="open">开启</span>
            <span slot="close">关闭</span>
          </i-switch>
        </template>
        <template slot-scope="{ row, index }" slot="desc"> 
          <Tooltip max-width="200" placement="bottom">
            <span class="line2">{{ row.desc }}</span>
            <p slot="content">{{ row.desc }}</p>
          </Tooltip>
        </template>
        <template slot-scope="{ row, index }" slot="action">
          <a @click="edit(row.id)">编辑</a>
          <Divider type="vertical" />
          <a @click="del(row, '删除保障服务', index)">删除</a>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="ensureFrom.limit"
        />
      </div>
    </Card>
  </div>
</template>

<script>
import { mapState } from "vuex";
import {
  productEnsure,
  productEnsureCreate,
  productEnsureEdit,
  ensureSetShow,
} from "@/api/product";
export default {
  name: "ensure",
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
          width: 80,
        },
        {
          title: "服务条款",
          key: "name",
          minWidth: 100,
        },
        {
          title: "图标",
          slot: "image",
          minWidth: 100,
        },
        {
          title: "内容描述",
          slot: "desc",
          minWidth: 100,
        },
        {
          title: "使用商品",
          key: "product_count",
          minWidth: 100,
        },
        {
          title: "是否显示",
          slot: "status",
          minWidth: 100,
        },
        {
          title: "排序",
          key: "sort",
          minWidth: 100,
        },
        {
          title: "创建时间",
          key: "add_time",
          minWidth: 130,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 120,
        },
      ],
      ensureFrom: {
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
    ensureSearchs() {
      this.ensureFrom.page = 1;
      this.list = [];
      this.getList();
    },
    showBnt(row) {
      let data = {
        id: row.id,
        is_show: row.status,
      };
      ensureSetShow(data)
        .then((res) => {
          this.$Message.success(res.msg);
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    // 添加
    add() {
      this.$modalForm(productEnsureCreate()).then(() => this.getList());
    },
    // 单位列表
    getList() {
      this.loading = true;
      productEnsure(this.ensureFrom)
        .then((res) => {
          let data = res.data;
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
      this.ensureFrom.page = index;
      this.getList();
    },
    //修改
    edit(id) {
      this.$modalForm(productEnsureEdit(id)).then(() => this.getList());
    },
    // 删除
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `product/ensure/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.list.splice(num, 1);
          if (!this.list.length) {
            this.ensureFrom.page =
              this.ensureFrom.page == 1 ? 1 : this.ensureFrom.page - 1;
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
.tabBox_img {
  width: 36px;
  height: 36px;

  img {
    width: 100%;
    height: 100%;
  }
}
</style>