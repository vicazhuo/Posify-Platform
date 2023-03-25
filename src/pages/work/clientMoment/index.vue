<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form
          ref="formValidate"
          :label-width="96"
          inline
          @submit.native.prevent
        >
          <FormItem label="创建时间:">
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
          <FormItem label="朋友圈类型:">
            <Input v-model="tableFrom.name" style="width: 250px;margin-right:14px;" placeholder="请输入名称"/>
           <Button type="primary" @click="search()">查询</Button>
          </FormItem>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <router-link :to="'/admin/work/client/add_moment'">
        <Button type="primary" class="mr-12">发布朋友圈</Button>
      </router-link>

      <Table
        ref="selection"
        :columns="columns1"
        :data="tableData.list"
        :loading="loading"
        class="ivu-mt"
      >
        <template slot-scope="{ row }" slot="send_type">
          <Tag color="cyan" size="medium" v-if="row.send_type">定时发送</Tag>
          <Tag color="blue" size="medium" v-else>立即发送</Tag>
        </template>
        <template slot-scope="{ row }" slot="type">
          <Tag color="purple" size="medium" v-if="row.type">选定成员</Tag>
          <Tag color="orange" size="medium" v-else>全部成员</Tag>
        </template>
        <template slot-scope="{ row }" slot="user_list">
          <Tag size="medium" v-for="(item,index1) in row.user_list" :key="index1">{{item}}</Tag>
        </template>
        <template slot-scope="{ row, index }" slot="action">
          <a @click="detailsItem(row, index)">详情</a>
          <Divider type="vertical" />
					<a @click="delItem(row,index)">删除</a>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="tableData.count"
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
import { getWorkMomenttList } from "@api/work";
import timeOptions from "@/utils/timeOptions";
export default {
  name: "",
  data() {
    return {
      loading: false,
      timeVal: [],
      options: timeOptions,
      columns1: [
        {
          title: "任务名称",
          key: "name",
          minWidth: 80,
        },
		{
          title: "已发送成员",
          key: "user_count",
          minWidth: 100,
        },
        {
          title: "未发送成员",
          key: "unuser_count",
          minWidth: 100,
        },
        {
          title: "成员类型",
          slot: "type",
          minWidth: 120,
        },
		{
          title: "发布方式",
          slot: "send_type",
          minWidth: 120,
        },
        {
          title: "发送人",
          slot: "user_list",
          minWidth: 150,
        },
        {
          title: "创建时间",
          key: "create_time",
          minWidth: 150,
        },
        {
          title: "操作",
          slot: "action",
          minWidth: 100,
        },
      ],
      tableData: [],
      tableFrom: {
        time: "",
        name: "",
        page: 1,
        limit: 15,
      },
    };
  },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 80;
    },
    labelPosition() {
      return this.isMobile ? "top" : "left";
    },
  },
  created() {
    this.getList();
  },
  methods: {
    getList() {
      this.loading = true;
      getWorkMomenttList(this.tableFrom)
        .then((res) => {
          this.tableData = res.data;
          this.loading = false;
        })
        .catch((err) => {
          this.$Message.error(err.msg);
          this.loading = false;
        });
    },
    search() {
      this.tableFrom.page = 1;
      this.getList();
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.tableFrom.time = this.timeVal.join("-");
    },
    pageChange(index) {
      this.tableFrom.page = index;
      this.getList();
    },
    detailsItem(row,index){
      this.$router.push("/admin/work/client/moment_info/" + row.id)
    },
    delItem(row,index){
      let delfromData = {
					title: '删除该朋友圈',
					num:index,
					url: `work/moment/${row.id}`,
					method: "DELETE",
					ids: "",
				};
				this.$modalSure(delfromData)
					.then((res) => {
					this.$Message.success(res.msg);
					if(this.tableData.list.length == 1){
						this.tableFrom.page = this.tableFrom.page -1;
						this.getList();
					}else{
						this.getList();
					}
					})
					.catch((res) => {
					this.$Message.error(res.msg);
				});
    },
  },
};
</script>
<style>
</style>