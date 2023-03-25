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
        class="tabform"
        @submit.native.prevent
      >
        <FormItem label="会员类型：">
          <Select
            v-model="formValidate.member_type"
            clearable
            @on-change="userSearchs"
            style="width:250px;"
          >
            <Option
              v-for="item in treeSelect"
              :value="item.value"
              :key="item.value"
              >{{ item.label }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="支付方式：">
          <Select
            v-model="formValidate.pay_type"
            clearable
            @on-change="paySearchs"
            style="width:250px;"
          >
            <Option
              v-for="item in payList"
              :value="item.val"
              :key="item.val"
              >{{ item.label }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="购买时间：">
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
        <FormItem label="搜索：">
          <Input
            placeholder="请输入用户名称搜索"
            element-id="name"
            v-model="formValidate.name"
            style="width: 250px;margin-right:14px;"
          />
          <Button type="primary" @click="selChange()">查询</Button>
        </FormItem>
       
      </Form>
			</div>
		</Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Table
        :columns="thead"
        :data="tbody"
        ref="table"
        :loading="loading"
        highlight-row
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
      >
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="total"
          :current="tablePage.page"
          :page-size="tablePage.limit"
          show-elevator
          show-total
          @on-change="pageChange"
        />
      </div>
    </Card>
  </div>
</template>

<script>
import { userMemberCard, memberRecord, memberTab } from "@/api/user";
import { mapState } from "vuex";

export default {
  name: "card",
  data() {
    return {
      treeSelect: [],
      payList: [
        {
          val: "free",
          label: "免费",
        },
        {
          val: "weixin",
          label: "微信",
        },
        {
          val: "alipay",
          label: "支付宝",
        },
      ],
      thead: [
        {
          title: "订单号",
          key: "order_id",
          minWidth: 100,
        },
        {
          title: "用户名",
          minWidth: 50,
          ellipsis: true,
          render: (h, params) => {
            return h("span", params.row.user.nickname);
          },
        },
        {
          title: "手机号码",
          minWidth: 80,
          render: (h, params) => {
            return h("span", params.row.user.phone || "--");
          },
        },
        {
          title: "会员类型",
          key: "member_type",
          minWidth: 40,
        },
        {
          title: "有效期限（天）",
          minWidth: 50,
          render: (h, params) => {
            return h(
              "span",
              params.row.vip_day === -1 ? "永久" : params.row.vip_day
            );
          },
        },
        {
          title: "支付金额（元）",
          key: "pay_price",
          minWidth: 50,
        },
        {
          title: "支付方式",
          key: "pay_type",
          minWidth: 30,
        },
        {
          title: "购买时间",
          key: "pay_time",
          minWidth: 90,
        },
        {
          title: "到期时间",
          minWidth: 90,
          render: (h, params) => {
            return h("span", params.row.overdue_time);
          },
        },
      ],
      tbody: [],
      loading: false,
      total: 0,
      formValidate: {
        name: "",
        member_type: "",
        pay_type: "",
        add_time: "",
      },
      options: {
        shortcuts: [
          {
            text: "今天",
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(
                new Date(
                  new Date().getFullYear(),
                  new Date().getMonth(),
                  new Date().getDate()
                )
              );
              return [start, end];
            },
          },
          {
            text: "昨天",
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(
                start.setTime(
                  new Date(
                    new Date().getFullYear(),
                    new Date().getMonth(),
                    new Date().getDate() - 1
                  )
                )
              );
              end.setTime(
                end.setTime(
                  new Date(
                    new Date().getFullYear(),
                    new Date().getMonth(),
                    new Date().getDate() - 1
                  )
                )
              );
              return [start, end];
            },
          },
          {
            text: "最近7天",
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(
                start.setTime(
                  new Date(
                    new Date().getFullYear(),
                    new Date().getMonth(),
                    new Date().getDate() - 6
                  )
                )
              );
              return [start, end];
            },
          },
          {
            text: "最近30天",
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(
                start.setTime(
                  new Date(
                    new Date().getFullYear(),
                    new Date().getMonth(),
                    new Date().getDate() - 29
                  )
                )
              );
              return [start, end];
            },
          },
          {
            text: "本月",
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(
                start.setTime(
                  new Date(new Date().getFullYear(), new Date().getMonth(), 1)
                )
              );
              return [start, end];
            },
          },
          {
            text: "本年",
            value() {
              const end = new Date();
              const start = new Date();
              start.setTime(
                start.setTime(new Date(new Date().getFullYear(), 0, 1))
              );
              return [start, end];
            },
          },
        ],
      },
      timeVal: [],
      tablePage: {
        page: 1,
        limit: 15,
      },
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
	this.sletab()
    this.getMemberRecord();
  },
  methods: {
	// 获取会员类型
	sletab(){
		memberTab().then(res=>{
			this.treeSelect = res.data
		})
	},
    // 用户名搜索；
    selChange() {
      this.tablePage.page = 1;
      this.getMemberRecord();
    },
    //用户类型搜索；
    userSearchs() {
      this.tablePage.page = 1;
      this.getMemberRecord();
    },
    //支付方式搜索；
    paySearchs() {
      this.tablePage.page = 1;
      this.getMemberRecord();
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.formValidate.add_time = this.timeVal[0]
        ? this.timeVal.join("-")
        : "";
      this.tablePage.page = 1;
      this.getMemberRecord();
    },
    getMemberRecord() {
      this.loading = true;
      let data = {
        page: this.tablePage.page,
        limit: this.tablePage.limit,
        member_type: this.formValidate.member_type,
        pay_type: this.formValidate.pay_type,
        add_time: this.formValidate.add_time,
        name: this.formValidate.name,
      };
      memberRecord(data)
        .then((res) => {
          this.loading = false;
          const { list, count } = res.data;
          this.tbody = list;
          this.total = count;
        })
        .catch((err) => {
          this.loading = false;
          this.$Message.error(err.msg);
        });
    },
    // 分页
    pageChange(index) {
      this.tablePage.page = index;
      this.getMemberRecord();
    },
  },
};
</script>
