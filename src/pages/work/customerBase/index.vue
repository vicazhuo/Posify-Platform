<template>
  <div>
     <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
       <div class="new_card_pd">
          <Alert closable show-icon>
            客户群，是由具有客户群使用权限的成员创建的外部群。成员在手机端创建群后，自动显示在后台列表中，群聊上限人数500人，包含成员+外部联系人
        </Alert>
        <Form :label-width="96" inline :label-colon="true">
            <FormItem label="创建时间">
              <DatePicker 
              :editable="false" 
              @on-change="onchangeTime" 
              :value="timeVal"  
              format="yyyy/MM/dd HH:mm" 
              type="datetimerange" 
              placement="bottom-start" 
              placeholder="自定义时间" 
              :options="options"
              style="width: 250px"></DatePicker>
            </FormItem>
            <FormItem label="群名称">
                <Input v-model="tableFrom.name" placeholder="请输入群名称" style="width:250px;margin-right:14px;" />
                <Button type="primary" @click="searchData()">查询</Button>
            </FormItem>
        </Form>
       </div>
     </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
        <div class="acea-row">
            <Button type="primary" class="mr-12" @click="groupSynch()">同步企微群</Button>
        </div>
        <Table ref="selection" :columns="columns1" :data="tableData.list" :loading="loading" class="ivu-mt">
          <template slot-scope="{ row }" slot="ownerInfo">
            <span>{{row.ownerInfo.name}}</span>
          </template>
           <template slot-scope="{ row }" slot="admin_user_list">
            <div v-if="row.admin_user_list">
              <!-- <span v-for="(item,index1) in row.admin_user_list" :key="index1">{{item.name}}</span> -->
              <Tag  size="medium" v-for="(item,index1) in row.admin_user_list" :key="index1">{{item.name}}</Tag>
            </div>
          </template>
            <template slot-scope="{ row, index }" slot="action">
                <a @click="statistical(row,index)">群统计</a>
                <Divider type="vertical" />
                <a @click="groupMember(row,index)">群成员</a>
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
    <Modal
      v-model="clentStatus"
      title="群成员"
      :footer-hide="true"
      width="900"
      >
      <Table :columns="clientColumn" :data="clientList.list" :loading="userLoading">
        <template slot-scope="{ row }" slot="type">
          <span>{{row.type == 1 ? '企业成员' : '外部联系人'}}</span>
        </template>
        <template slot-scope="{ row }" slot="join_scene">
          <span>{{row.join_scene | scene}}</span>
        </template>
        <template slot-scope="{ row }" slot="member">
          <span v-if="row.member && row.type == 1">{{row.member.name}}</span>
          <span v-if="row.client && row.type == 2">{{row.client.name}}</span>
        </template>
         <template slot-scope="{ row }" slot="join_time">
          <span v-if="row.join_time">{{row.join_time | formatDate}}</span>
        </template>

      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="clientList.count"
          :current="clientForm.page"
          show-elevator
          show-total
          @on-change="clientChange"
          :page-size="clientForm.limit"
        />
      </div>
    </Modal>
  </div>
</template>

<script>
import { mapState } from "vuex";
import {workGroupSynch,workGroupChat,getGroupChatMember} from "@/api/work"
import timeOptions from "@/utils/timeOptions"
export default {
  name: "",
  data() {
    return {
      loading: false,
      userLoading:false,
      formInline:{},
      clentStatus:false,
      options: timeOptions,
      columns1: [
        {
          title: "ID",
          key: "id",
          minWidth: 40,
          maxWidth: 80,
        },
        {
          title: "群名称",
          key: "name",
          minWidth: 80,
        },
        {
          title: "群主",
          slot: "ownerInfo",
          minWidth: 100,
        },
        {
          title: "群公告",
          key: "notice",
          minWidth: 100,
        },
        {
          title: "管理员",
          slot: "admin_user_list",
          minWidth: 120,
        },
        {
          title: "创建时间",
          key: "group_create_time",
          minWidth: 110,
        },
        {
          title: "群人数",
          key: "member_num",
          minWidth: 80,
        },
        {
          title: "退群人数",
          key: "retreat_group_num",
          minWidth: 80,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 130,
        },
      ],
      tableData: [],
      clientColumn:[
        {
          title: "ID",
          key: "id",
          minWidth: 60,
          align: 'center'
        },
        {
          title: "姓名",
          slot: "member",
          minWidth: 80,
          align: 'center'
        },
        {
          title: "成员类型",
          slot: "type",
          minWidth: 80,
          align: 'center'
        },
        {
          title: "入群方式",
          slot: "join_scene",
          minWidth: 80,
          align: 'center'
        },
        {
          title: "群昵称",
          key: "group_nickname",
          minWidth: 60,
          align: 'center'
        },
        {
          title: "入群时间",
          slot: "join_time",
          minWidth: 100,
          align: 'center'
        },
      ],
      clientList:[],
      grid: {
        xl: 7,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      timeVal:[],
      tableFrom: {
        time: "",
        name: "",
        page: 1,
        limit: 15,
      },
      clientForm:{
        page: 1,
        limit: 15,
      },
      total: 0,
    };
  },
  filters:{
    scene(value){
      if(value == 1){
        return '直接邀请'
      }else if(value == 2){
        return '链接邀请'
      }else if(value == 3){
        return '扫码入群'
      }
    }
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
    this.getWorkGroupChat();
  },
  methods: {
    getWorkGroupChat(){
      this.loading = true;
      workGroupChat(this.tableFrom).then(res=>{
        this.tableData = res.data;
        this.loading = false;
      }).catch(err=>{
        this.$Message.error(err.msg)
        this.loading = false;
      })
    },
    groupSynch(){
      workGroupSynch().then(res=>{
        this.$Message.success(res.msg);
      })
    },
    statistical(row,index){
      this.$router.push('/admin/work/client/statistical/' + row.id)
    },
    groupMember(row,index){
      this.clentStatus = true;
      this.userLoading = true;
      this.$set(this.clientForm,'id',row.id);
      getGroupChatMember(this.clientForm).then(res=>{
        this.clientList = res.data;
        this.userLoading = false;
      }).catch(err=>{
        this.$Message.error(err.msg);
      })
    },
    onchangeTime(e){
      this.timeVal = e;
      this.tableFrom.time = this.timeVal.join("-");
      this.tableFrom.page = 1;
      this.getWorkGroupChat();
    },
    searchData(){
      this.tableFrom.page = 1;
      this.getWorkGroupChat();
    },
    pageChange(index){
      this.tableFrom.page = index;
      this.getWorkGroupChat();
    },
    clientChange(index){
      this.clientForm.page = index;
      this.userLoading = true;
      getGroupChatMember(this.clientForm).then(res=>{
        this.clientList = res.data;
        this.userLoading = false;
      }).catch(err=>{
        this.$Message.error(err.msg);
      })
    }
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
.mt-24{
    margin-top:24px;
}
.ml-40{
    margin-left:40px;
}
.mr-12{
    margin-right: 12px;
}
.mb-30{
    margin-bottom:30px;
}
.conditions{
    display: flex;
}
/deep/.ivu-alert{
	margin-bottom: 20px !important;
}
</style>
