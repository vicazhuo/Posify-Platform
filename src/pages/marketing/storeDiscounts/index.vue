<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding= "0">
    <div class="new_card_pd">
      <Form
        ref="tableFrom"
        inline
        :model="tableFrom"
        :label-width="labelWidth"
        :label-position="labelPosition"
        @submit.native.prevent
      >
            <FormItem label="套餐类型：" clearable>
              <Select
                v-model="tableFrom.type"
                placeholder="请选择套餐类型"
                clearable
                @on-change="userSearchs"
                style="width: 250px"
              >
                <Option value="0">固定套餐</Option>
                <Option value="1">搭配套餐</Option>
              </Select>
            </FormItem>
            <FormItem label="套餐状态：">
              <Select
                placeholder="请选择"
                v-model="tableFrom.status"
                clearable
                @on-change="userSearchs"
                style="width: 250px"
              >
                <Option value="''">全部</Option>
                <Option value="1">上架</Option>
                <Option value="0">下架</Option>
              </Select>
            </FormItem>
            <FormItem label="套餐搜索：" label-for="title">
              <Input
                placeholder="请输入套餐名称"
                v-model="tableFrom.title"
                @on-search="userSearchs"
                style="width: 250px;margin-right:14px"
              />
              <Button type="primary" @click="userSearchs()">查询</Button>
            </FormItem>
      </Form>
    </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Button
      v-auth="['marketing-store_bargain-create']"
      type="primary"
      @click="add"
      class="mr10"
      >添加套餐</Button>
      <Table
        :columns="columns1"
        :data="tableList"
        :loading="loading"
        highlight-row
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
        class="ivu-mt"
      >
        <template slot-scope="{ row, index }" slot="is_fail">
          <Icon
            type="md-checkmark"
            v-if="row.is_fail === 1"
            color="#0092DC"
            size="14"
          />
          <Icon type="md-close" v-else color="#ed5565" size="14" />
        </template>
        <template slot-scope="{ row, index }" slot="image">
          <viewer>
            <div class="tabBox_img">
              <img v-lazy="row.image" />
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row, index }" slot="type">
          {{ row.type == 0 ? "固定套餐" : "搭配套餐" }}
        </template>
        <template slot-scope="{ row, index }" slot="time">
          <div v-if="row.start_time == 0">不限时</div>
          <div v-else>
            <div>起：{{ row.start_time || "--" }}</div>
            <div>止：{{ row.stop_time || "--" }}</div>
          </div>
        </template>
        <template slot-scope="{ row, index }" slot="status">
          <i-switch
            v-model="row.status"
            :value="row.status"
            :true-value="1"
            :false-value="0"
            @on-change="onchangeIsShow(row)"
            size="large"
          >
            <span slot="open">上架</span>
            <span slot="close">下架</span>
          </i-switch>
        </template>
        <template slot-scope="{ row, index }" slot="limit_num">
           {{row.is_limit?row.limit_num:'不限量'}}
        </template>
        <template slot-scope="{ row, index }" slot="action">
          <a @click="edit(row)">编辑</a>
          <Divider type="vertical" />
          <a @click="del(row, '删除套餐', index)">删除</a>
<!--          <Divider type="vertical" />-->
<!--          <a>查看订单记录</a>-->
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="tableFrom.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="tableFrom.limit"
        />
      </div>
    </Card>
  </div>
</template>

<script>
import { mapState } from "vuex";
import { discountsList, discountsChangeStatus } from "@/api/discounts";
import { formatDate } from "@/utils/validate";
export default {
  name: "storeBargain",
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, "yyyy-MM-dd hh:mm");
      }
    },
  },
  data() {
    return {
      loading: false,
      columns1: [
        {
          title: "ID",
          key: "id",
          width: 80,
        },
        {
          title: "套餐名称",
          key: "title",
          minWidth: 90,
        },
        {
          title: "套餐类型",
          slot: "type",
          minWidth: 90,
        },
        {
          title: "上架状态",
          slot: "status",
          minWidth: 100,
        },
        {
          title: "限时",
          slot: "time",
          minWidth: 100,
        },
        {
          title: "创建时间",
          key: "add_time",
          minWidth: 110,
        },
        {
          title: "剩余数量",
          slot: "limit_num",
          minWidth: 80,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 130,
        },
      ],
      tableList: [],
      tableFrom: {
        status: "",
        title: "",
        page: 1,
        type: "",
        limit: 15,
      },
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
    // 添加
    add() {
      this.$router.push({ path: "/admin/marketing/store_discounts/create" });
    },
    // 编辑
    edit(row) {
      console.log(row);
      this.$router.push({
        name: "marketing_store_discounts_create",
        query: {
          id: row.id,
        },
      });
    },
    // 一键复制
    copy(row) {
      this.$router.push({
        name: "marketing_store_discounts_create",
        query: {
          id: row.id,
          copy: 1,
        },
      });
    },
    // 删除
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `marketing/discounts/del/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.getList()
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 列表
    getList() {
      console.log(this.tableFrom);
      this.loading = true;
      this.tableFrom.type = this.tableFrom.type;
      this.tableFrom.status = this.tableFrom.status;
      discountsList(this.tableFrom)
        .then(async (res) => {
          let data = res.data;
          this.tableList = data.list;
          this.total = res.data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$Message.error(res.msg);
        });
    },
    pageChange(index) {
      this.tableFrom.page = index;
      this.getList();
    },
    // 表格搜索
    userSearchs() {
      this.tableFrom.page = 1;
      this.getList();
    },
    // 修改是否显示
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.status,
      };
      discountsChangeStatus(data)
        .then(async (res) => {
          this.$Message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
          this.getList();
        });
    },
  },
};
</script>

<style scoped lang="stylus">
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
</style>
