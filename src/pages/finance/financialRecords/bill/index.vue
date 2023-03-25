<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding= "0">
      <div class="new_card_pd">
          <Form 
          ref="formValidate"
          :model="formValidate"
          :label-width="labelWidth"
          inline
          :label-position="labelPosition"
          class="tabform"
          @submit.native.prevent
        >
            <FormItem label="昵称/ID：">
              <Input
                enter-button
                placeholder="请输入"
                element-id="name"
                style="width: 250px"
                v-model="formValidate.nickname"
              />
            </FormItem>
            <FormItem label="时间范围：" class="tab_data">
              <DatePicker
                :editable="false"
                style="width: 250px"
                @on-change="onchangeTime"
                format="yyyy/MM/dd"
                type="datetimerange"
                placement="bottom-start"
                placeholder="自定义时间"
                :options="options"
              ></DatePicker>
            </FormItem>
            <FormItem label="筛选类型：" class="tab_data">
              <Select
                v-model="formValidate.type"
                style="width: 250px;margin-right:14px"
                clearable
              >
                <Option
                  v-for="(item, index) in billList"
                  :key="index"
                  :value="item.type"
                  >{{ item.title }}</Option
                >
              </Select>
              <Button type="primary" @click="userSearchs"
              >搜索</Button
              >
              <Button
                v-auth="['export-userFinance']"
                class="export"
                @click="exports"
                >导出</Button
              >
            </FormItem>
      </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Table
        ref="table"
        highlight-row
        :columns="columns"
        :data="tabList"
        :loading="loading"
        no-data-text="暂无数据"
        no-filtered-data-text="暂无筛选结果"
      >
        <template slot-scope="{ row }" slot="number">
          <div :class="[row.pm === 1 ? 'green' : 'red']">
            {{ row.pm === 1 ? row.number : "-" + row.number }}
          </div>
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
  </div>
</template>

<script>
import { mapState } from "vuex";
import { billTypeApi, billListApi, userFinanceApi } from "@/api/finance";
import exportExcel from "../../../../utils/newToExcel.js";
import timeOptions from "@/utils/timeOptions";
export default {
  name: "bill",
  data() {
    return {
      billList: [],
      formValidate: {
        nickname: "",
        start_time: "",
        end_time: "",
        type: "",
        page: 1, // 当前页
        limit: 20, // 每页显示条数
      },
      options: timeOptions,
      loading: false,
      tabList: [],
      total: 0,
      columns: [
        {
          title: "用户ID",
          key: "uid",
          sortable: true,
          width: 80,
        },
        {
          title: "昵称",
          key: "nickname",
          minWidth: 150,
        },
        {
          title: "金额",
          // sortable: true,
          minWidth: 150,
          slot: "number",
          // render: (h, params) => {
          //     return h('div', {
          //         style: {
          //             color: '#FF5722'
          //         }
          //     })
          // }
        },
        {
          title: "类型",
          key: "title",
          minWidth: 100,
        },
        {
          title: "备注",
          key: "mark",
          minWidth: 150,
        },
        {
          title: "创建时间",
          key: "add_time",
          minWidth: 200,
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
  created() {
    this.selList();
    this.getList();
  },
  methods: {
    // 时间
    onchangeTime(e) {
      this.formValidate.start_time = e[0];
      this.formValidate.end_time = e[1];
    },
    // 获取筛选类型
    selList() {
      billTypeApi()
        .then(async (res) => {
          this.billList = res.data.list;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 列表
    getList() {
      this.loading = true;
      billListApi(this.formValidate)
        .then(async (res) => {
          let data = res.data;
          this.tabList = data.data;
          this.total = data.count;
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
    // 搜索
    userSearchs() {
      this.formValidate.page = 1;
      this.getList();
    },
    // 导出
    //   let formValidate = this.formValidate;
    //   let data = {
    //     start_time: formValidate.start_time,
    //     end_time: formValidate.end_time,
    //     nickname: formValidate.nickname,
    //     type: formValidate.type,
    //   };
    //   userFinanceApi(data)
    //     .then((res) => {
    //       location.href = res.data[0];
    //     })
    //     .catch((res) => {
    //       this.$Message.error(res.msg);
    //     });
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
        userFinanceApi(excelData).then((res) => {
          return resolve(res.data);
        });
      });
    },
  },
};
</script>

<style scoped lang="stylus">
.ivu-form-label-left >>> .ivu-form-item-label {
  text-align: right;
}

.tabform .export {
  margin-left: 10px;
}

.red {
  color: #FF5722;
}

.green {
  color: #009688;
}
</style>
