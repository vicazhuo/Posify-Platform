<template>
  <div>
    <!-- <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/client/group' }">
              <Button icon="ios-arrow-back" size="small" class="mr20"
                >返回</Button
              >
            </router-link>
          </div>
          <span class="mr20">群发详情</span>
        </div>
      </PageHeader>
    </div> -->
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/client/group' }">
              <div class="font-sm after-line">
                <span class="iconfont iconfanhui"></span>
                <span class="pl10">返回</span>
              </div>
            </router-link>
          </div>
          <span class="mr20 ml16">群发详情</span>
        </div>
      </PageHeader>
    </div>
    <cards-data :cardLists="cardLists" v-if="cardLists.length >= 0"></cards-data>
    <Card :bordered="false" dis-hover>
      <div class="new_tab">
        <Tabs v-model="tabIndex" @on-click="onChangeType">
          <TabPane label="成员详情" name="0"></TabPane>
          <TabPane label="客户详情" name="1"></TabPane>
        </Tabs>
      </div>
      <Form
          ref="formValidate"
          :label-width="96"
          inline
          @submit.native.prevent
        >
          <FormItem label="发送状态:" v-if="tabIndex == 0">
             <Select v-model="tableForm.status" clearable  style="width:250px">
                <Option value="0">未发送</Option>
                <Option value="1">已发送</Option>
            </Select>
          </FormItem>
          <FormItem label="发送状态:" v-if="tabIndex == 1">
             <Select v-model="tableForm.status1" clearable  style="width:250px">
                <Option value="0">未发送</Option>
                <Option value="1">已发送</Option>
                <Option value="2">客户不是好友发送失败</Option>
                <Option value="3">客户已收到群消息发送失败</Option>
            </Select>
          </FormItem>
          <FormItem label="成员搜索:">
            <Input v-model="tableForm.user_name" style="width: 250px;margin-right:14px;" placeholder="请输入名称"/>
           <Button type="primary" @click="search()">查询</Button>
          </FormItem>
        </Form>
      <Table :columns="tabIndex == 1 ? tableColumn1 : tableColumn" :data="tableData.list" :loading="userLoading">
        <!-- sendResult -->
        <template slot-scope="{ row }" slot="sendResult">
          <span>{{row.sendResult.num_count}}</span>
        </template>
        <template slot-scope="{ row }" slot="send_time">
          <span>{{row.send_time}}</span>
        </template>
        <template slot-scope="{ row }" slot="status">
					<Tag color="green" size="medium" v-if="row.status == 2">已发送</Tag>
					<Tag color="red" size="medium" v-if="row.status !=2">未发送</Tag>
				</template>
        <template slot-scope="{ row }" slot="status1">
					<Tag color="green" size="medium" v-if="row.status == 1">已发送</Tag>
					<Tag color="orange" size="medium" v-if="row.status == 2">客户不是好友发送失败</Tag>
					<Tag color="red" size="medium" v-if="row.status == 0">未发送</Tag>
					<Tag color="blue" size="medium" v-if="row.status == 3">客户已收到群消息发送失败</Tag>
				</template>
        <template slot-scope="{ row, index }" slot="action">
          <a @click="sendMsg(row, index)" :disabled="row.status == 2">提醒</a>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="tableData.count"
          :current="tableForm.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="tableForm.limit"
        />
      </div>
    </Card>
  </div>
</template>
<script>
import cardsData from "@/components/cards/cards";
import { workGroupTemplateInfo,groupTemplateMemberList,groupTemplateClientList,workGroupTemplateSendMsg } from "@/api/work";
 import { formatDate } from '@/utils/validate';
export default {
  data() {
    return {
      cardLists: [],
      optionData: {},
      style: { height: "400px" },
      spinShow: false,
      tableColumn: [
        {
          title: "成员",
          key: "name",
          minWidth: 100,
        },
        {
          title: "发送状态",
          slot: "status",
          minWidth: 100,
        },
        {
          title: "发送数目",
          slot: "sendResult",
          minWidth: 100,
        },
        {
          title: "发送时间",
          slot: "send_time",
          minWidth: 100,
        },
        {
          title: "操作",
          slot: "action",
          minWidth: 100,
        },
      ],
      tableColumn1:[
        {
          title: "客户",
          key: "name",
          minWidth: 100,
        },
        {
          title: "发送状态",
          slot: "status1",
          minWidth: 100,
        },
        {
          title: "发送时间",
          slot: "send_time",
          minWidth: 100,
        },
      ],
      tabIndex:0,
      tableData: [],
      userLoading: false,
      timeVal: [],
      tableForm: {
        page: 1,
        limit: 15,
        client_name:"", //客户名称
        user_name:"", //成员名称
        status:"",
        status1:"",
        id: 0,
      },
      
    };
  },
  filters: {
      formatDate (time) {
          if (time !== 0) {
              let date = new Date(time * 1000);
              return formatDate(date, 'yyyy-MM-dd hh:mm');
          }
      }
  },
  components: { cardsData },
  mounted() {
    this.tableForm.id = this.$route.params.id;
    this.getData();
    this.getMemberList();
    // this.getList();
  },
  methods: {
    getList() {
      this.userLoading = true;
      workGroupStatisticsList(this.tableForm)
        .then((res) => {
          this.tableData = res.data;
          this.userLoading = false;
        })
        .catch((err) => {
          this.userLoading = false;
          this.$Message.error(err.msg);
        });
    },
    getData() {
      workGroupTemplateInfo(this.$route.params.id).then((res) => {
        this.cardLists = [
          {
            col: 6,
            count: res.data.user_count,
            name: "已发送成员",
            type:1,
            className: "iconjinrixinzeng",
          },
          {
            col: 6,
            count: res.data.external_user_count,
            name: "送达客户",
            type:1,
            className: "iconjinrituiqun",
          },
          {
            col: 6,
            count: res.data.unuser_count,
            name: "未发送成员",
            type:1,
            className: "icondangqianqunchengyuan",
          },
          {
            col: 6,
            count: res.data.external_unuser_count,
            name: "未送达客户",
            type:1,
            className: "iconleijituiqun",
          },
        ];
        
      });
    },
    getMemberList(){
      groupTemplateMemberList(this.tableForm.id,{
        user_name:this.tableForm.user_name,
        status:this.tableForm.status,
        page:this.tableForm.page,
        limit:this.tableForm.limit
      }).then(res=>{
        this.tableData = res.data;
      })
    },
    getClientList(){
      groupTemplateClientList(this.tableForm.id,{
        client_name:this.tableForm.user_name,
        status:this.tableForm.status1,
        page:this.tableForm.page,
        limit:this.tableForm.limit
      }).then(res=>{
        this.tableData = res.data;
      })
    },
    sendMsg(row,index){
      workGroupTemplateSendMsg({
					userid:row.userid,
					time:row.create_time,
					id:""
				}).then(res=>{
					this.$Message.success(res.msg)
				}).catch(err=>{
					this.$Message.error(err.msg);
				})
    },
    onChangeType(){
      if(this.tabIndex == 0){
        this.getMemberList();
      }else{
        this.getClientList();
      }
    },
    search(){
      if(this.tabIndex == 0){
        this.getMemberList();
      }else{
        this.getClientList();
      }
      
    },
    pageChange(index) {
      this.tableForm.page = index;
      if(this.tabIndex == 0){
        this.getMemberList();
      }else{
        this.getClientList();
      }
    },
  },
};
</script>
<style scoped lang="stylus">
.mb20 {
  margin-bottom: 20px;
}

.mb30 {
  margin-bottom: 30px;
}

.mr-20 {
  margin-right: 20px;
}

.header_title {
  font-size: 16px;
  font-family: PingFangSC-Semibold, PingFang SC;
  font-weight: 600;
  color: #000000;
  padding-left: 8px;
  position: relative;

  &::before {
    position: absolute;
    content: '';
    width: 2px;
    height: 18px;
    background: #1890ff;
    top: 0;
    left: 0;
  }
}
.new_tab {
  >>>.ivu-tabs-nav .ivu-tabs-tab{
      padding:4px 16px 20px !important;
      font-weight: 500;
  }
}
</style>