<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding= "0">
      <div class="new_card_pd">
      <Form
        ref="formValidate"
        :label-width="labelWidth"
        :label-position="labelPosition"
        inline
        class="tabform"
        @submit.native.prevent
      >
            <FormItem label="时间选择：">
              <DatePicker
                :editable="false"
                @on-change="onchangeTime"
                :value="timeVal"
                format="yyyy/MM/dd HH:mm"
                type="datetimerange"
                placement="bottom-start"
                placeholder="自定义时间"
                style="width: 250px"
                :options="options"
              ></DatePicker>
            </FormItem>
            <FormItem label="昵称/ID：">
              <Input
                enter-button
                placeholder="请输入"
                element-id="nickname"
                style="width: 250px"
                v-model="formValidate.nickname"
                clearable
              />
            </FormItem>
            <FormItem label="佣金范围：" class="tab_data" >
              <Input
                enter-button
                placeholder="￥"
                element-id="price_min"
                class="mr10"
                v-model="formValidate.price_min"
                clearable
                style="width: 109px"
              />
              <span class="mr10">一</span>
              <Input
                enter-button
                placeholder="￥"
                element-id="price_max"
                v-model="formValidate.price_max"
                clearable
                style="width: 109px;margin-right:14px"
              />
              <Button type="primary" @click="userSearchs"
              >查询</Button>
              <Button
              v-auth="['export-userCommission']"
              class="export"
              @click="exports"
              >导出</Button>
            </FormItem>
      </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Table
        ref="table"
        :columns="columns"
        :data="tabList"
        class="mt25"
        :loading="loading"
        no-data-text="暂无数据"
        no-filtered-data-text="暂无筛选结果"
        @on-sort-change="sortChanged"
      >
				<template slot-scope="{ row, index }" slot="nickname">
					<span>{{ row.nickname }} </span>
					<span style="color: #ed4014;" v-if="row.delete_time != null"> (已注销)</span>
				</template>
        <template slot-scope="{ row, index }" slot="action">
          <a @click="Info(row)">详情</a>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="formValidate.page"
          show-elevator
          show-total
          :page-size="formValidate.limit"
          @on-change="pageChange"
        />
      </div>
    </Card>
    <commission-details ref="commission"></commission-details>
  </div>
</template>
<script>
import { mapState } from "vuex";
import { commissionListApi, userCommissionApi } from "@/api/finance";
import commissionDetails from "./handle/commissionDetails";
import exportExcel from "@/utils/newToExcel.js";
import timeOptions from "@/utils/timeOptions";
export default {
  name: "commissionRecord",
  components: { commissionDetails },
  data() {
    return {
      timeVal: [], //时间筛选
      total: 0,
      loading: false,
      tabList: [],
      formValidate: {
        nickname: "",
        price_max: "",
        price_min: "",
        excel: 0,
        page: 1, // 当前页
        limit: 20, // 每页显示条数
      },
      options:timeOptions,
      columns: [
        {
          title: "昵称/姓名/ID",
          slot: "nickname",
          minWidth: 230,
        },
        {
          title: "总佣金金额",
          key: "sum_number",
          sortable: "custom",
          minWidth: 120,
        },
        {
          title: "账户余额",
          key: "now_money",
          minWidth: 100,
        },
        {
          title: "账户佣金",
          key: "brokerage_price",
          sortable: "custom",
          minWidth: 120,
        },
        {
          title: "提现到账佣金",
          key: "extract_price",
          minWidth: 150,
        },
        {
          title: "时间",
          key: "time",
          minWidth: 150,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 100,
        },
      ],
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
  mounted() {
    this.getList();
  },
  methods: {
    // 列表
    getList() {
      this.loading = true;
      commissionListApi(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.tabList = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$Message.error(res.msg);
        });
    },
    // 选择时间
    selectChange(tab) {
      this.formValidate.page = 1;
      this.formValidate.date = tab;
      this.timeVal = [];
      this.getList();
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.date = this.timeVal.join("-");
      this.formValidate.page = 1;
      if (!e[0]) {
        this.formValidate.date = "";
      }
      this.getList();
    },
    pageChange(index) {
      this.formValidate.page = index;
      this.getList();
    },
    // 搜索
    userSearchs() {
      this.formValidate.page = 1;
      this.getList();
    },
    // 导出
    // exports() {
    //   let formValidate = this.formValidate;
    //   let data = {
    //     price_max: formValidate.price_max,
    //     price_min: formValidate.price_min,
    //     nickname: formValidate.nickname,
    //   };
    //   userCommissionApi(data)
    //     .then((res) => {
    //       location.href = res.data[0];
    //     })
    //     .catch((res) => {
    //       this.$Message.error(res.msg);
    //     });
    // },
    // 数据导出；
    async exports() {
      let [th, filekey, data, fileName] = [[], [], [], ""];
      //   let fileName = "";
      let excelData = JSON.parse(JSON.stringify(this.formValidate));
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
        userCommissionApi(excelData).then((res) => {
          return resolve(res.data);
        });
      });
    },
    // 详情
    Info(row) {
      this.$refs.commission.modals = true;
      this.$refs.commission.getDetails(row.uid);
      this.$refs.commission.getList(row.uid);
    },
    // 排序
    sortChanged(e) {
      if (e.key == "sum_number") {
        delete this.formValidate.brokerage_price;
      } else {
        delete this.formValidate.sum_number;
      }
      this.formValidate[e.key] = e.order;
      this.getList();
    },
  },
};
</script>

<style scoped lang="stylus">
.lines {
  padding-top: 6px !important;
}

.tabform .export {
  margin-left: 10px;
}

.tab_data >>> .ivu-form-item-content {
  display: flex !important;
}
</style>
