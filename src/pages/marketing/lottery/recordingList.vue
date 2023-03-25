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
			  <FormItem label="活动类型：">
			    <Select v-model="tableFrom.factor"
          style="width: 250px">
			       <Option v-for="item in lotteryList" :value="item.val" :key="item.val">{{ item.text }}</Option>
			    </Select>
			  </FormItem>
            <FormItem label="时间选择：">
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
            <FormItem label="奖品类型：">
				<Select v-model="tableFrom.type" style="width: 250px">
				   <Option v-for="item in typeList" :value="item.val" :key="item.val">{{ item.text }}</Option>
				</Select>
            </FormItem>
          <!-- <Col v-bind="grid">
            <FormItem label="领取状态：" clearable>
              <Select
                v-model="tableFrom.is_receive"
                placeholder="请选择"
                clearable
                @on-change="userSearchs"
              >
                <Option value="0">待发货</Option>
                <Option value="1">已发货</Option>
              </Select>
            </FormItem>
          </Col>
          <Col v-bind="grid">
            <FormItem label="处理状态：" clearable>
              <Select
                v-model="tableFrom.is_deliver"
                placeholder="请选择"
                clearable
                @on-change="userSearchs"
              >
                <Option value="0">未处理</Option>
                <Option value="1">已处理</Option>
              </Select>
            </FormItem>
          </Col> -->
            <FormItem label="搜索用户：" label-for="store_name">
              <Input
                placeholder="请输入用户信息"
                v-model="tableFrom.keyword"
                style="width: 250px;margin-right:14px"
              />
              <Button type="primary" @click="userSearchs()" style="margin-right:10px">查询</Button>
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
        <template slot-scope="{ row }" slot="is_fail">
          <Icon
            type="md-checkmark"
            v-if="row.is_fail === 1"
            color="#0092DC"
            size="14"
          />
          <Icon type="md-close" v-else color="#ed5565" size="14" />
        </template>
        <template slot-scope="{ row }" slot="user">
          <span v-if="row.user">{{ row.user.nickname }} </span>
					<span style="color: #ed4014;" v-if="row.user && row.user.delete_time != null"> (已注销)</span>
        </template>
        <template slot-scope="{ row }" slot="mark">
          <span>{{ row.deliver_info.mark }}</span>
        </template>
        <template slot-scope="{ row }" slot="receive_info">
          <div v-if="row.receive_info.name">
            <div>姓名：{{ row.receive_info.name }}</div>
            <div>电话：{{ row.receive_info.phone }}</div>
            <div>地址：{{ row.receive_info.address }}</div>
            <div v-if="row.receive_info.mark">
              备注：{{ row.receive_info.mark }}
            </div>
          </div>
        </template>
        <template slot-scope="{ row }" slot="prize">
          <div class="prize">
            <img :src="row.prize.image" alt="" />
            <span>{{ row.prize.name }}</span>
          </div>
        </template>
        <template slot-scope="{ row }" slot="action">
          <a
            @click="deliver(row, 1)"
            v-if="row.type == 6 && row.is_deliver === 0"
            >发货</a
          >
          <a
            v-else-if="row.type == 6 && row.is_deliver === 1"
            @click="isDeliver(row)"
            >配送信息</a
          >
          <Divider type="vertical" v-if="row.type == 6" />
          <a @click="tapMark(row)">备注</a>
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
    <!--备注-->
    <Modal v-model="modals" scrollable title="备注" :closable="false">
      <Form ref="formValidate" :model="markForm" :label-width="80" @submit.native.prevent>
        <FormItem label="备注：" prop="remark">
          <Input v-model="markForm.mark" maxlength="200" show-word-limit type="textarea" placeholder="请输入备注" />
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="primary" @click="putRemark">提交</Button>
        <Button @click="closeRemark">关闭</Button>
      </div>
    </Modal>
    <!-- 发货-->
    <Modal
      v-model="shipModel"
      width="40%"
      scrollable
      closable
      footer-hide
      :title="!modelTitle ? '发货' : modelTitle"
      :mask-closable="false"
      :z-index="1"
      @on-cancel="cancel('shipForm')"
    >
      <Form
        ref="shipForm"
        :model="shipForm"
        :rules="ruleShip"
        :label-width="80"
      >
        <FormItem label="快递公司" prop="deliver_name">
          <Select v-model="shipForm.deliver_name">
            <Option
              v-for="item in locationList"
              :value="item.value"
              :key="item.id"
              >{{ item.value }}</Option
            >
          </Select>
        </FormItem>
        <FormItem label="快递单号" prop="deliver_number">
          <Input
            v-model="shipForm.deliver_number"
            placeholder="请输入快递单号"
          ></Input>
          <div class="trips" v-if="shipForm.deliver_name == '顺丰速运'">
            <p>顺丰请输入单号 :收件人或寄件人手机号后四位</p>
            <p>例如：SF000000000000:3941</p>
          </div>
        </FormItem>
        <FormItem>
          <Button
            type="primary"
            @click="ok('shipForm')"
            >提交</Button
          >
          <Button @click="cancel('shipForm')" style="margin-left: 8px"
            >关闭</Button
          >
        </FormItem></Form
      >
    </Modal>
  </div>
</template>

<script>
import { mapState } from "vuex";
import { recordList, lotteryRecordDeliver } from "@/api/lottery";
import { formatDate } from "@/utils/validate";
import { getExpressData } from "@/api/order";
import { ruleShip, ruleMark } from "./formRule/ruleShip";
export default {
  name: "lotteryRecordList",
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
      modals: false,
      shipModel: false,
      loading: false,
      locationList: [],
      shipForm: {
        id: "",
        deliver_name: "",
        deliver_number: null,
      },
      markForm: {
        id: "",
        mark: "",
      },
      ruleShip: ruleShip,
      ruleMark: ruleMark,
	  lotteryList:[
		  { text: "全部", val: "0" },
		  { text: "积分抽取", val: "1" },
		  { text: "订单支付", val: "3" },
		  { text: "订单评价", val: "4" },
		  { text: "关注公众号", val: "5" },
	  ],
      typeList: [
        { text: "全部", val: "0" },
        { text: "未中奖", val: "1" },
        { text: "积分", val: "2" },
        { text: "余额", val: "3" },
        { text: "红包", val: "4" },
        { text: "优惠券", val: "5" },
        { text: "商品", val: "6" },
      ],
      columns1: [
        {
          title: "ID",
          key: "id",
          width: 80,
        },
        {
          title: "用户信息",
          slot: "user",
          minWidth: 90,
        },
        {
          title: "奖品信息",
          slot: "prize",
          minWidth: 130,
        },
        {
          title: "抽奖时间",
          key: "add_time",
          minWidth: 100,
        },
        {
          title: "收货信息",
          slot: "receive_info",
          minWidth: 100,
        },
        {
          title: "备注",
          slot: "mark",
          minWidth: 100,
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
        xl: 6,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableFrom: {
		type:'',  
		factor:'',  
        keyword: "",
        date: [],
        page: 1,
        limit: 15,
      },
      total: 0,
      timeVal: [],
      modelType: 1,
      lottery_id: "",
      modelTitle: "",
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
    this.getExpressData();
  },
  methods: {
    clear(){
      this.markForm = {
        id: "",
        mark: "",
      };
      this.shipForm = {
        id: "",
        deliver_name: "",
        deliver_number: null,
      };
      this.modelTitle = "";
    },
    tapMark(row){
      this.markForm.id = row.id;
      this.markForm.mark = row.deliver_info.mark;
      this.modals = true;
    },
    closeRemark(){
      this.modals = false;
      this.clear();
    },
    putRemark(){
      lotteryRecordDeliver(this.markForm).then(()=>{
        this.$Message.success("操作成功");
        this.modals = false;
        this.getList();
        this.clear();
      }).catch(err=>{
        this.$Message.error(err.msg)
      })
    },
    deliver(row, type) {
      this.shipForm.id = row.id;
	  this.shipForm.deliver_name = '';
	  this.shipForm.deliver_number = '';
      this.modelType = type;
      this.shipModel = true;
    },
    isDeliver(row) {
      this.shipForm.id = row.id;
      this.modelType = 1;
      this.modelTitle = "配送信息";
      this.shipModel = true;
      this.shipForm.deliver_name = row.deliver_info.deliver_name;
      this.shipForm.deliver_number = row.deliver_info.deliver_number;
    },
    ok(name) {
      this.$refs[name].validate((valid) => {
        if(valid){
          lotteryRecordDeliver(this.shipForm)
                  .then(() => {
                    this.$Message.success("操作成功");
                    this.shipModel = false;
                    this.getList();
                    this.clear();
                    this.$refs[name].resetFields();
                  })
                  .catch((err) => {
                    this.$Message.error(err.msg);
                  });
        }
      });
    },
    cancel(name) {
      this.modelType = 1;
      this.shipModel = false;
      this.clear();
      this.$refs[name].resetFields();
    },
    // 物流公司列表
    getExpressData() {
      getExpressData('')
        .then(async (res) => {
          this.locationList = res.data;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 具体日期
    onchangeTime(e) {
      this.timeVal = e;
      this.tableFrom.data = this.timeVal[0] ? this.timeVal.join("-") : "";
      this.tableFrom.page = 1;
      this.getList();
    },
    // 列表
    getList() {
      this.loading = true;
      recordList(this.tableFrom)
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

.prize {
  display: flex;
  align-items: center;
}

.prize img {
  width: 36px;
  height: 36px;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 5px;
}

.trips {
  color: #ccc;
}
</style>
