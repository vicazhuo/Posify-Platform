<template>
  <div class="article-manager">
    <Card :bordered="false" dis-hover class="mt15 ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          ref="formValidate"
          :model="formValidate"
          inline
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <FormItem label="开启时间选择：">
            <DatePicker
              :editable="false"
              @on-change="onchangeTime"
              :value="timeVal"
              format="yyyy/MM/dd"
              type="datetimerange"
              placement="bottom-start"
              placeholder="自定义时间"
              style="width: 250px"
              :options="options"
            ></DatePicker>
          </FormItem>
          <FormItem label="砍价状态：">
            <Select
              v-model="formValidate.status"
              placeholder="请选择"
              clearable
              style="width: 250px;margin-right:14px"
            >
              <Option :value="1">进行中</Option>
              <Option :value="2">已失败</Option>
              <Option :value="3">已成功</Option>
            </Select>
            <Button @click="userSearchs()" type="primary">查询</Button>
          </FormItem>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Table
        :columns="columns1"
        :data="tableList"
        :loading="loading"
        highlight-row
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
      >
        <template slot-scope="{ row, index }" slot="avatar">
          <viewer>
            <div class="tabBox_img">
              <img v-lazy="row.avatar" />
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row, index }" slot="nickname">
          <span> {{ row.nickname + " / " + row.uid }}</span>
					<span style="color: #ed4014;" v-if="row.delete_time != null"> (已注销)</span>
        </template>
        <template slot-scope="{ row, index }" slot="title">
          <span> {{ row.title }}</span>
        </template>
        <template slot-scope="{ row, index }" slot="add_time">
          <span> {{ row.add_time }}</span>
        </template>
        <template slot-scope="{ row, index }" slot="datatime">
          <span> {{ row.datatime }}</span>
        </template>
        <template slot-scope="{ row, index }" slot="status">
          <Tag color="green" size="large" v-show="row.status === 1">进行中</Tag>
          <Tag color="volcano" size="large" v-show="row.status === 2">已失败</Tag>
          <Tag color="cyan" size="large" v-show="row.status === 3">已成功</Tag>
          <!-- <Tag color="blue" v-show="row.status === 1">进行中</Tag>
                    <Tag color="cyan" v-show="row.status === 2">已失败</Tag>
                    <Tag color="volcano" v-show="row.status === 3">已完成</Tag> -->
        </template>
        <template slot-scope="{ row, index }" slot="action">
          <a @click="Info(row)">查看详情</a>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="formValidate.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="formValidate.limit"
        />
      </div>
    </Card>

    <!-- 详情模态框-->
    <Modal
      v-model="modals"
      class="tableBox"
      scrollable
      footer-hide
      closable
      title="查看详情"
      :mask-closable="false"
      width="750"
    >
      <Table
        ref="selection"
        :columns="columns2"
        :data="tabList3"
        :loading="loading2"
        no-data-text="暂无数据"
        highlight-row
        max-height="600"
        size="small"
        no-filtered-data-text="暂无筛选结果"
      >
        <template slot-scope="{ row, index }" slot="nickname">
          <span> {{ row.nickname + " / " + row.uid }}</span>
					<span style="color: #ed4014;" v-if="row.delete_time != null"> (已注销)</span>
        </template>
        <template slot-scope="{ row, index }" slot="avatar">
          <viewer>
            <div class="tabBox_img">
              <img v-lazy="row.avatar" />
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row, index }" slot="action">
          <Tag color="cyan" v-show="row.is_refund === 1">已退款</Tag>
          <Tag color="volcano" v-show="row.is_refund === 0">未退款</Tag>
        </template>
      </Table>
    </Modal>
  </div>
</template>

<script>
// import cardsData from '@/components/cards/cards';
import { mapState } from "vuex";
import { formatDate } from "@/utils/validate";
import { bargainUserListApi, bargainUserInfoApi } from "@/api/marketing";
import timeOptions from "@/utils/timeOptions";
export default {
  name: "bargainList",
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, "yyyy-MM-dd hh:mm");
      }
    },
  },
  // components: { cardsData },
  data() {
    return {
      cardLists: [],
      modals: false,
      options: timeOptions,
      fromList: {
        title: "选择时间",
        custom: true,
        fromTxt: [
          { text: "全部", val: "" },
          { text: "今天", val: "today" },
          { text: "昨天", val: "yesterday" },
          { text: "最近7天", val: "lately7" },
          { text: "最近30天", val: "lately30" },
          { text: "本月", val: "month" },
          { text: "本年", val: "year" },
        ],
      },
      grid: {
        xl: 7,
        lg: 10,
        md: 12,
        sm: 12,
        xs: 24,
      },
      loading: false,
      formValidate: {
        status: "",
        data: "",
        page: 1,
        limit: 15,
      },
      columns1: [
        {
          title: "头像",
          slot: "avatar",
          minWidth: 100,
        },
        {
          title: "发起用户",
          slot: "nickname",
          minWidth: 170,
        },
        {
          title: "开启时间",
          key: "add_time",
          minWidth: 150,
        },
        {
          title: "砍价商品",
          key: "title",
          minWidth: 300,
        },
        {
          title: "最低价",
          key: "bargain_price_min",
          minWidth: 120,
        },
        {
          title: "当前价",
          key: "now_price",
          minWidth: 100,
        },
        {
          title: "总砍价次数",
          key: "people_num",
          minWidth: 100,
        },
        {
          title: "剩余砍价次数",
          key: "num",
          minWidth: 100,
        },
        {
          title: "结束时间",
          key: "datatime",
          minWidth: 150,
        },
        {
          title: "状态",
          slot: "status",
          minWidth: 100,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 170,
        },
      ],
      tableList: [],
      total: 0,
      timeVal: [],
      loading2: false,
      tabList3: [],
      columns2: [
        {
          title: "用户ID",
          key: "uid",
          width: 80,
        },
        {
          title: "用户头像",
          slot: "avatar",
        },
        {
          title: "用户名称",
          slot: "nickname",
          minWidth: 150,
        },
        {
          title: "砍价金额",
          key: "price",
        },
        {
          title: "砍价时间",
          key: "add_time",
        },
      ],
      rows: {},
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
    // 查看详情
    Info(row) {
      this.modals = true;
      this.rows = row;
      bargainUserInfoApi(row.id)
        .then(async (res) => {
          let data = res.data;
          this.tabList3 = data.list;
          // this.total = res.data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$Message.error(res.msg);
        });
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.data = this.timeVal[0] ? this.timeVal.join("-") : "";
      // this.formValidate.data = this.timeVal.join('-');
      this.formValidate.page = 1;
      this.getList();
    },
    // 选择时间
    selectChange(tab) {
      this.formValidate.page = 1;
      this.formValidate.data = tab;
      this.timeVal = [];
      this.getList();
    },
    // 列表
    getList() {
      this.loading = true;
      this.formValidate.status = this.formValidate.status || "";
      bargainUserListApi(this.formValidate)
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
      this.formValidate.page = index;
      this.getList();
    },
    // 表格搜索
    userSearchs() {
      this.formValidate.page = 1;
      this.getList();
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