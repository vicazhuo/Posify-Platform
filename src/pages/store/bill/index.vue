<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          ref="formValidate"
          inline
          :model="formValidate"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
         <FormItem label="选择门店：" label-for="status1">
            <Select
              v-model="formValidate.store_id"
              placeholder="请选择"
              clearable
              filterable
              element-id="status1"
              style="width: 250px"
            >
              <Option
                :value="item.id"
                v-for="(item, index) in staff"
                :key="index"
                >{{ item.name }}</Option
              >
            </Select>
          </FormItem>
          <FormItem label="创建时间：">
            <DatePicker
              :editable="false"
              @on-change="onchangeTime"
              :value="timeVal"
              format="yyyy/MM/dd"
              type="datetimerange"
              placement="bottom-start"
              placeholder="自定义时间"
              style="width: 250px;margin-right:14px;"
              :options="options"
            ></DatePicker>
            <Button type="primary" @click="search()">查询</Button>
          </FormItem>
          
        </Form>
      </div>
    </Card>

    <Card :bordered="false" dis-hover class="ive-mt tablebox">
      <div class="new_tab">
        <Tabs @on-click="onClickTab">
          <TabPane label="日账单" name="day" />
          <TabPane label="周账单" name="week" />
          <TabPane label="月账单" name="month" />
        </Tabs>
      </div>
      <div class="table">
        <Table
          :columns="columns"
          :data="orderList"
          ref="table"
          :loading="loading"
          highlight-row
          no-userFrom-text="暂无数据"
          no-filtered-userFrom-text="暂无筛选结果"
        >
          <template slot-scope="{ row, index }" slot="income_num">
            <span style="color: #f5222d">￥{{ row.income_num }}</span>
          </template>
          <template slot-scope="{ row, index }" slot="exp_num">
            <span style="color: #00c050">￥{{ row.exp_num }}</span>
          </template>
          <template slot-scope="{ row, index }" slot="entry_num">
            <span>￥{{ row.entry_num }}</span>
          </template>
          <template slot-scope="{ row, index }" slot="action">
            <a @click="Info(row)">账单详情</a>
            <Divider type="vertical" />
            <a @click="download(row)">下载</a>
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
      </div>
    </Card>
    <commission-details ref="commission"></commission-details>
  </div>
</template>
		
		<script>
import exportExcel from "@/utils/newToExcel.js";
import commissionDetails from "../components/commissionDetails.vue";
import {
  storeFfundRecordApi,
  exportfundRecordApi,
  staffListInfo,
} from "@/api/store";
import timeOptions from "@/utils/timeOptions";
export default {
  name: "bill",
  components: {
    commissionDetails,
  },
  data() {
    return {
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      total: 0,
      loading: false,
      tab: "day",
      staff: [],
      columns: [
        {
          title: "ID",
          key: "id",
          width: 60,
        },
        {
          title: "标题",
          key: "title",
          minWidth: 80,
        },
        {
          title: "日期",
          key: "add_time",
          minWidth: 80,
        },
        {
          title: "收入金额",
          slot: "income_num",
          minWidth: 80,
        },
        {
          title: "支出金额",
          slot: "exp_num",
          minWidth: 80,
        },
        {
          title: "门店应入账金额",
          slot: "entry_num",
          minWidth: 80,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 120,
          align: "center",
        },
      ],
      orderList: [
        {
          id: "1",
          order_id: "200",
          pay_price: "200",
          status: 1,
          phone: "13000000000",
          address: "100",
        },
      ],
      formValidate: {
        store_id: "",
        data: "",
        page: 1,
        limit: 15,
      },
      timeVal: [],
      options: timeOptions,
    };
  },
  computed: {
    labelWidth() {
      return this.isMobile ? undefined : 96;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  mounted() {
    this.onClickTab(this.tab);
    this.staffApi();
  },
  methods: {
    staffApi() {
      staffListInfo().then((res) => {
        this.staff = res.data;
      });
    },
    onClickTab(e) {
      this.tab = e;
      this.getList();
    },
    search() {
      this.formValidate.page = 1;
      this.getList();
    },
    getList() {
      this.loading = true;
      let data = {
        timeType: this.tab,
        data: this.formValidate.data,
        page: this.formValidate.page,
        limit: this.formValidate.limit,
        store_id: this.formValidate.store_id,
      };
      storeFfundRecordApi(data).then((res) => {
        this.orderList = res.data.list;
        this.loading = false;
        this.total = res.data.count;
      });
    },
    // 选择时间
    selectChange(tab) {
      this.formValidate.page = 1;
      this.formValidate.data = tab;
      this.timeVal = [];
      this.getList();
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.data = this.timeVal[0] ? this.timeVal.join("-") : "";
      this.formValidate.page = 1;
    },
    //分页
    pageChange(status) {
      this.formValidate.page = status;
      this.getList();
    },
    // 账单详情
    Info(row) {
      this.$refs.commission.modals = true;
      this.$refs.commission.getList(row.ids);
    },
    //下载
    async download(row) {
      let [th, filekey, data, fileName] = [[], [], [], ""];
      let excelData = {
        ids: row.ids,
        page: 1,
        store_id: this.formValidate.store_id,
      };
      let lebData = await this.getExcelData(excelData);
      if (!fileName) fileName = lebData.filename;
      if (!filekey.length) {
        filekey = lebData.filekey;
      }
      if (!th.length) th = lebData.header;
      data = data.concat(lebData.export);
      exportExcel(th, filekey, fileName, data);
      return;
    },
    getExcelData(excelData) {
      return new Promise((resolve, reject) => {
        exportfundRecordApi(excelData).then((res) => {
          return resolve(res.data);
        });
      });
    },
  },
};
</script>
		
		<style scoped lang="stylus">
		/deep/.ivu-tabs-nav {
		  height: 45px;
		}

		.tabbox {
		  padding: 16px 20px 0px;
		}

		.box {
		  padding: 20px;
		  padding-bottom: 1px;
		}

		.tablebox {
		  margin-top: 15px;
		  padding-bottom: 10px;
		}

		.btnbox {
		  padding: 20px 0px 0px 30px;

		  .btns {
		    width: 99px;
		    height: 32px;
		    background: #1890FF;
		    border-radius: 4px;
		    text-align: center;
		    line-height: 32px;
		    color: #FFFFFF;
		    cursor: pointer;
		  }
		}

		.table {
		  padding: 0px 0 15px 0;
		}
    .new_tab {
    >>>.ivu-tabs-nav .ivu-tabs-tab{
        padding:4px 16px 20px !important;
        font-weight: 500;
    }
  }
</style>
		