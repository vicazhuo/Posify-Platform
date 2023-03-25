<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          ref="formValidate"
          :model="formValidate"
          :label-width="labelWidth"
          inline
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <FormItem label="活动状态：" clearable>
            <Select
              v-model="formValidate.start_status"
              placeholder="请选择"
              clearable
              style="width: 250px"
            >
              <Option :value="0">未开始</Option>
              <Option :value="1">进行中</Option>
              <Option :value="-1">已结束</Option>
            </Select>
          </FormItem>
          <FormItem label="上架状态：" clearable>
            <Select
              v-model="formValidate.is_show"
              placeholder="请选择"
              clearable
              style="width: 250px"
            >
              <Option :value="1">上架</Option>
              <Option :value="0">下架</Option>
            </Select>
          </FormItem>
          <FormItem label="商品搜索：" prop="store_name" label-for="store_name">
            <Input
              placeholder="请输入拼团名称,ID"
              v-model="formValidate.store_name"
              style="width: 250px;margin-right:14px"
            />
            <Button
              type="primary"
              @click="userSearchs()"
              style="margin-right: 14px"
              >查询</Button
            >
            <Button
              v-auth="['export-storeCombination']"
              class="export"
              @click="exports"
              >导出</Button
            >
          </FormItem>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Button
        v-auth="['marketing-store_combination-create']"
        type="primary"
        class="bnt mr15"
        @click="add"
        >添加拼团商品</Button
      >
      <Table
        :columns="columns1"
        :data="tableList"
        class="ivu-mt"
        :loading="loading"
        highlight-row
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
      >
        <template slot-scope="{ row }" slot="is_fail">
          <Icon
            type="md-checkmark"
            v-if="row.is_fail === 1"
            color="#0092DC"
            size="14"
          />
          <Icon type="md-close" v-else color="#ed5565" size="14" />
        </template>
        <template slot-scope="{ row }" slot="image">
          <viewer>
            <div class="tabBox_img">
              <img v-lazy="row.image" />
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row }" slot="title">
          <Tooltip max-width="200" placement="bottom">
          <span class="line2">{{row.title}}</span>
              <p slot="content">{{row.title}}</p>
          </Tooltip>
        </template>
        <template slot-scope="{ row }" slot="status_item">
          <span> {{ row | filterStatus }}</span>
        </template>
        <template slot-scope="{ row }" slot="start_name">
          <Tag color="orange" size="large" v-show="row.start_name === '未开始'">{{row.start_name}}</Tag>
          <Tag color="green" size="large" v-show="row.start_name === '进行中'">{{row.start_name}}</Tag>
          <Tag color="default" size="large" v-show="row.start_name === '已结束'">{{row.start_name}}</Tag>
        </template>
        <template slot-scope="{ row }" slot="stop_time">
          <span> {{ row.stop_time | formatDate }}</span>
        </template>
        <template slot-scope="{ row }" slot="is_show">
          <i-switch
            v-model="row.is_show"
            :value="row.is_show"
            :true-value="1"
            :false-value="0"
            @on-change="onchangeIsShow(row)"
            size="large"
          >
            <span slot="open">上架</span>
            <span slot="close">下架</span>
          </i-switch>
        </template>
        <template slot-scope="{ row, index }" slot="action">
          <!-- <a v-if="row.stop_status === 1" @click="copy(row)" >一键复制</a>
                    <a v-else @click="edit(row)" >编辑</a> -->
          <a v-if="row.stop_status === 0" @click="edit(row)">编辑</a>
          <Divider v-if="row.stop_status === 0" type="vertical" />
          <a @click="copy(row)">复制</a>
          <Divider type="vertical" />
          <a @click="del(row, '删除拼团商品', index)">删除</a>
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
  </div>
</template>

<script>
import {
  combinationListApi,
  combinationSetStatusApi,
  storeCombinationApi,
} from "@/api/marketing";
import { mapState } from "vuex";
import { formatDate } from "@/utils/validate";
import exportExcel from "@/utils/newToExcel.js";

export default {
  name: "index",
  filters: {
    formatDate(time) {
      if (time !== 0) {
        let date = new Date(time * 1000);
        return formatDate(date, "yyyy-MM-dd hh:mm");
      }
    },
    filterStatus(val) {
      let nowTime = Date.parse(new Date()) / 1000;
      if (
        val.start_time < nowTime &&
        val.stop_time > nowTime &&
        val.is_show == 1
      ) {
        return "活动进行中";
      } else if (val.stop_time < nowTime && val.status == 1) {
        return "活动已结束";
      } else {
        return "活动未开始";
      }
    },
  },
  data() {
    return {
      loading: false,
      grid: {
        xl: 7,
        lg: 7,
        md: 12,
        sm: 24,
        xs: 24,
      },
      formValidate: {
        start_status: "",
        is_show: "",
        store_name: "",
        page: 1,
        limit: 15,
      },
      value: "",
      columns1: [
        {
          title: "ID",
          key: "id",
          width: 80,
        },
        {
          title: "拼团图片",
          slot: "image",
          minWidth: 90,
        },
        {
          title: "拼团名称",
          slot: "title",
          minWidth: 150,
        },
        {
          title: "原价",
          key: "ot_price",
          minWidth: 100,
        },
        {
          title: "拼团价",
          key: "price",
          minWidth: 120,
        },
        // {
        //     title: '库存',
        //     key: 'stock',
        //     minWidth: 100
        // },
        {
          title: "拼团人数",
          key: "count_people",
          minWidth: 80,
        },
        // {
        //     title: '访问人数',
        //     key: 'count_people_browse',
        //     minWidth: 100
        // },
        // {
        //     title: '展现量',
        //     key: 'browse',
        //     minWidth: 150
        // },
        {
          title: "参与人数",
          key: "count_people_all",
          minWidth: 80,
        },
        {
          title: "成团数量",
          key: "count_people_pink",
          minWidth: 80,
        },
        // {
        //     title: '浏览量',
        //     key: 'browse',
        //     minWidth: 150
        // },
        {
          title: "限量",
          key: "quota_show",
          minWidth: 80,
        },
        {
          title: "限量剩余",
          key: "quota",
          minWidth: 100,
        },
        {
          title: "活动状态",
          slot: "start_name",
          minWidth: 100,
        },
        {
          title: "结束时间",
          slot: "stop_time",
          minWidth: 150,
        },
        {
          title: "状态",
          slot: "is_show",
          minWidth: 120,
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
      statisticsList: [],
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
    // 导出
    // exports () {
    //     let formValidate = this.formValidate;
    //     let data = {
    //         start_status: formValidate.start_status,
    //         is_show: formValidate.is_show,
    //         store_name: formValidate.store_name
    //     };
    //     storeCombinationApi(data).then(res => {
    //         location.href = res.data[0];
    //     }).catch(res => {
    //         this.$Message.error(res.msg)
    //     })
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
        storeCombinationApi(excelData).then((res) => {
          return resolve(res.data);
        });
      });
    },
    // 添加
    add() {
      this.$router.push({ path: "/admin/marketing/store_combination/create" });
    },
    // 编辑
    edit(row) {
      this.$router.push({
        path: "/admin/marketing/store_combination/create/" + row.id + "/0",
      });
    },
    // 一键复制
    copy(row) {
      this.$router.push({
        path: "/admin/marketing/store_combination/create/" + row.id + "/1",
      });
    },
    // 删除
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `marketing/combination/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.tableList.splice(num, 1);
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 列表
    getList() {
      this.loading = true;
      combinationListApi(this.formValidate)
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
    // 修改是否显示
    onchangeIsShow(row) {
      let data = {
        id: row.id,
        status: row.is_show,
      };
      combinationSetStatusApi(data)
        .then(async (res) => {
          this.$Message.success(res.msg);
        })
        .catch((res) => {
          this.$Message.error(res.msg);
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
