<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          ref="tableFrom"
          inline
          :model="tableFrom"
          :label-width="labelWidth"
          :label-position="labelPosition"
          @submit.native.prevent
        >
          <FormItem label="活动状态：" clearable>
            <Select
              v-model="tableFrom.start_status"
              placeholder="请选择"
              clearable
              style="width: 250px"
            >
              <Option value="0">未开始</Option>
              <Option value="1">进行中</Option>
              <Option value="-1">已结束</Option>
            </Select>
          </FormItem>
          <FormItem label="上架状态：">
            <Select
              placeholder="请选择"
              v-model="tableFrom.status"
              clearable
              style="width: 250px"
            >
              <Option value="1">上架</Option>
              <Option value="0">下架</Option>
            </Select>
          </FormItem>
          <FormItem label="商品搜索：" label-for="store_name">
            <Input
              placeholder="请输入砍价名称，ID"
              v-model="tableFrom.store_name"
              style="width: 250px;margin-right:14px"
            />
            <Button @click="userSearchs()" type="primary" style="margin-right:14px">查询</Button>
            <Button
              v-auth="['export-storeBargain']"
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
          v-auth="['marketing-store_bargain-create']"
          type="primary"
          @click="add"
          class="mr10"
          >添加砍价商品</Button
        >
      <Table
        :columns="columns1"
        :data="tableList"
        :loading="loading"
        highlight-row
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
        class="ivu-mt"
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

        <template slot-scope="{ row }" slot="bargain_min_price">
          <span>{{ row.bargain_min_price }}~{{ row.bargain_max_price }}</span>
        </template>
        <template slot-scope="{ row }" slot="title">
          <Tooltip max-width="200" placement="bottom">
          <span class="line2">{{row.title}}</span>
              <p slot="content">{{row.title}}</p>
          </Tooltip>
        </template>
        <template slot-scope="{ row }" slot="status_item">
          {{ row | filterStatus }}
        </template>
        <template slot-scope="{ row }" slot="start_name">
          <Tag color="orange" size="large" v-show="row.start_name === '未开始'">{{row.start_name}}</Tag>
          <Tag color="green" size="large" v-show="row.start_name === '进行中'">{{row.start_name}}</Tag>
          <Tag color="default" size="large" v-show="row.start_name === '已结束'">{{row.start_name}}</Tag>
        </template>
        <template slot-scope="{ row }" slot="stop_time">
          <span> {{ row.stop_time | formatDate }}</span>
        </template>
        <template slot-scope="{ row }" slot="status">
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
        <template slot-scope="{ row, index }" slot="action">
          <!-- <a v-if="row.stop_status === 1" @click="copy(row)" >一键复制</a>
                    <a v-else @click="edit(row)" >编辑</a> -->
          <a v-if="row.stop_status === 0" @click="edit(row)">编辑</a>
          <Divider v-if="row.stop_status === 0" type="vertical" />
          <a @click="copy(row)">复制</a>
          <Divider type="vertical" />
          <a @click="del(row, '删除砍价商品', index)">删除</a>
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
import {
  bargainListApi,
  bargainSetStatusApi,
  stroeBargainApi,
} from "@/api/marketing";
import { formatDate } from "@/utils/validate";
import exportExcel from "@/utils/newToExcel.js";

export default {
  name: "storeBargain",
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
        val.status == 1
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
      columns1: [
        {
          title: "ID",
          key: "id",
          width: 80,
        },
        {
          title: "砍价图片",
          slot: "image",
          minWidth: 90,
        },
        {
          title: "砍价名称",
          slot: "title",
          minWidth: 150,
        },
        {
          title: "砍价价格",
          key: "price",
          minWidth: 100,
        },
        {
          title: "最低价",
          key: "min_price",
          minWidth: 100,
        },
        {
          title: "参与人数",
          key: "count_people_all",
          minWidth: 100,
        },
        {
          title: "帮忙砍价人数",
          key: "count_people_help",
          minWidth: 100,
        },
        {
          title: "砍价成功人数",
          key: "count_people_success",
          minWidth: 100,
        },
        {
          title: "限量",
          key: "quota_show",
          minWidth: 80,
        },
        {
          title: "限量剩余",
          key: "quota",
          minWidth: 80,
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
          title: "上架状态",
          slot: "status",
          minWidth: 130,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 130,
        },
      ],
      tableList: [],
      grid: {
        xl: 7,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableFrom: {
        start_status: "",
        status: "",
        store_name: "",
        export: 0,
        page: 1,
        limit: 15,
      },
      tableFrom2: {
        status: "",
        store_name: "",
        export: 1,
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
      this.$router.push({ path: "/admin/marketing/store_bargain/create" });
    },
    // 导出
    // exports () {
    //     let formValidate = this.tableFrom;
    //     let data = {
    //         start_status: formValidate.start_status,
    //         status: formValidate.status,
    //         store_name: formValidate.store_name
    //     };
    //     stroeBargainApi(data).then(res => {
    //         location.href = res.data[0];
    //     }).catch(res => {
    //         this.$Message.error(res.msg)
    //     })
    // },
    // 数据导出；
    async exports() {
      let [th, filekey, data, fileName] = [[], [], [], ""];
      //   let fileName = "";
      let excelData = JSON.parse(JSON.stringify(this.tableFrom));
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
        stroeBargainApi(excelData).then((res) => {
          return resolve(res.data);
        });
      });
    },
    // 编辑
    edit(row) {
      this.$router.push({
        path: "/admin/marketing/store_bargain/create/" + row.id + "/0",
      });
    },
    // 一键复制
    copy(row) {
      this.$router.push({
        path: "/admin/marketing/store_bargain/create/" + row.id + "/1",
      });
    },
    // 删除
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `marketing/bargain/${row.id}`,
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
      this.tableFrom.start_status = this.tableFrom.start_status || "";
      this.tableFrom.status = this.tableFrom.status || "";
      bargainListApi(this.tableFrom)
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
      if (!parseInt(row.status)) {
        this.$Modal.confirm({
          title: "您确定要下架【" + row.title + "】商品吗？",
          content:
            "下架将会删除前台用户已砍的所有记录，所有用户将重新发起砍价,您确定要这样操作吗？",
          okText: "我想好了，确定要下架",
          cancelText: "取消下架操作",
          onOk: () => {
            bargainSetStatusApi(data)
              .then(async (res) => {
                this.$Message.success(res.msg);
              })
              .catch((res) => {
                this.$Message.error(res.msg);
              });
          },
          onCancel: () => {
            row.status = 1;
          },
        });
      } else {
        bargainSetStatusApi(data)
          .then(async (res) => {
            this.$Message.success(res.msg);
          })
          .catch((res) => {
            this.$Message.error(res.msg);
          });
      }
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
