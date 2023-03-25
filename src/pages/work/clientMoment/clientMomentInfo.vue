<template>
  <div>
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/client/moment' }">
              <div class="font-sm after-line">
                <span class="iconfont iconfanhui"></span>
                <span class="pl10">返回</span>
              </div>
            </router-link>
          </div>
          <span class="mr20 ml16">朋友圈详情</span>
        </div>
      </PageHeader>
    </div>
    <cards-data :cardLists="cardLists" v-if="cardLists.length >= 0"></cards-data>
    <Card :bordered="false" dis-hover>
      
      <Form
          ref="formValidate"
          :label-width="96"
          inline
          @submit.native.prevent
        >
          <FormItem label="发送状态:" >
             <Select v-model="tableForm.status" clearable  style="width:250px;margin-right:14px;">
                <Option value="0">未发送</Option>
                <Option value="1">已发送</Option>
            </Select>
            <Button type="primary" @click="search()">查询</Button>
          </FormItem>
        </Form>
      <Table :columns=" tableColumn" :data="tableData.list" :loading="userLoading">
        <!-- sendResult -->
        <template slot-scope="{ row }" slot="sendResult">
          <span>{{row.sendResult.num_count}}</span>
        </template>
        <template slot-scope="{ row }" slot="create_time">
          <span>{{row.create_time | formatDate}}</span>
        </template>
        <template slot-scope="{ row }" slot="status">
					<Tag color="green" size="medium" v-if="row.status == 1">已发表</Tag>
					<Tag color="red" size="medium" v-if="row.status == 0">未发表</Tag>
				</template>
        <template slot-scope="{ row }" slot="external_user_list">
				<span v-for="(item,index) in row.external_user_list" :key="index">{{item}},</span>
				</template>
        <!-- <template slot-scope="{ row, index }" slot="action">
          <a @click="sendMsg(row, index)">提醒</a>
        </template> -->
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
import { workMomentInfo,workGroupTemplateSendMsg,workMomentList } from "@/api/work";
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
          title: "员工",
          key: "name",
          minWidth: 100,
        },
        {
          title: "发表状态",
          slot: "status",
          minWidth: 100,
        },
        {
          title: "发送时间",
          slot: "create_time",
          minWidth: 100,
        },
        {
          title: "已送达客户",
          slot: "external_user_list",
          minWidth: 150,
        },
        // {
        //   title: "操作",
        //   slot: "action",
        //   minWidth: 100,
        // },
      ],
      tabIndex:0,
      tableData: [],
      userLoading: false,
      timeVal: [],
      tableForm: {
        page: 1,
        limit: 15,
        status:"",
        moment_id:"",
        userid:"",
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
    this.getData();
    // this.getMemberList();
  },
  methods: {
    getList() {
      this.userLoading = true;
      workMomentList(this.tableForm)
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
      workMomentInfo(this.$route.params.id).then((res) => {
        this.tableForm.moment_id = res.data.moment_id;
        if(!!res.data.moment_id){
          this.getList();
        }
        this.cardLists = [
          {
            col: 6,
            count: res.data.info.externalUserCount,
            name: "已送达客户",
            type:1,
            className: "iconjinrixinzeng",
          },
          {
            col: 6,
            count: res.data.info.userCount,
            name: "全部成员",
            type:1,
            className: "iconjinrituiqun",
          },
          {
            col: 6,
            count: res.data.info.unSendUserCount,
            name: "未发送成员",
            type:1,
            className: "icondangqianqunchengyuan",
          },
          {
            col: 6,
            count: res.data.info.sendUserCount,
            name: "已发送成员",
            type:1,
            className: "iconleijituiqun",
          },
        ];
        
      });
    },
    
    sendMsg(row,index){
      workGroupTemplateSendMsg({
					userid:row.msg_id,
					time:row.create_time,
					id:""
				}).then(res=>{
					this.$Message.success(res.msg)
				}).catch(err=>{
					this.$Message.error(err.msg);
				})
    },
    search(){
      this.tableForm.page = 1;
      this.getList();
      
    },
    pageChange(index) {
      this.tableForm.page = index;
      this.getList();
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