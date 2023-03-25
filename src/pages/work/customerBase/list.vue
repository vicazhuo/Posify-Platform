<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form :label-width="96" inline :label-colon="true">
          <FormItem :label="tabIndex == 0 ? '创建时间':'访问时间'">
            <DatePicker
              :editable="false"
              @on-change="onchangeTime"
              :value="timeVal"
              format="yyyy/MM/dd HH:mm"
              type="datetimerange"
              placement="bottom-start"
              placeholder="自定义时间"
              :options="options"
              style="width: 250px"
            ></DatePicker>
          </FormItem>
          <FormItem label="选择客服" v-show="tabIndex == 0">
            <Input
              v-model="usersName"
              placeholder="请输入"
              :clearable="true"
              @on-focus="addUser"
              style="width: 250px"
            />
          </FormItem>
          <FormItem label="客户标签">
            <div class="check_label acea-row row-middle" @click="setLabel(0)">
              <Tag v-for="(item,index) in labelFormList" :key="index" :name="item.title" closable @on-close="handleClose2">{{item.title}}</Tag>
            </div>
          </FormItem>
          <FormItem label="客户名称">
            <Input
              v-model="tableFrom.name"
              placeholder="请输入客户名称"
              style="width: 250px; margin-right: 14px"
            />
            <Button type="primary" @click="searchData()">查询</Button>
          </FormItem>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <div class="new_tab">
        <Tabs v-model="tabIndex" @on-click="onChangeType">
          <TabPane label="企微客户" name="0"></TabPane>
          <TabPane label="非企微客户" name="1"></TabPane>
        </Tabs>
      </div>
      <div class="acea-row">
        <Button
          type="primary"
          class="mr-12"
          @click="clientSynch()"
          v-show="tabIndex == 0"
          >同步企业微信客户</Button
        >
        <Button
          @click="setLabel(1)"
          :disabled="!selectGroup.length"
          v-show="tabIndex == 0"
          >批量设置标签</Button
        >
        <div v-show="tabIndex == 1">
          <Button
            v-auth="['admin-user-coupon']"
            class="mr-12"
            :disabled="!selectUser.length"
            @click="onSend"
            >发送优惠券</Button
          >
          <Button
            @click="setLabel(2)"
            :disabled="!selectUser.length"
            v-show="tabIndex == 1"
            >批量设置标签</Button
          >
        </div>
      </div>
      <!--  -->
      <Table
        ref="selection"
        :columns="columns1"
        :data="tableData.list"
        :loading="loading"
        class="ivu-mt"
        @on-select-all="selectAll"
        @on-select-all-cancel="selectAll"
        @on-selection-change="handleSelectRow"
        v-if="tabIndex == 0"
      >
        <template slot-scope="{ row }" slot="avatar">
          <viewer class="acea-row">
            <div class="tabBox_img mr-12">
              <img v-lazy="row.avatar" />
            </div>
            <div class="acea-row row-column row-center">
              <span>{{ row.name }}</span>
              <span>ID:{{ row.id }}</span>
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row }" slot="gender">
          <span>{{ row.gender | genderFilter }}</span>
        </template>
        <template slot-scope="{ row }" slot="followOne">
          <span v-if="row.followOne">{{ row.followOne.member.name }}</span>
        </template>
        <template slot-scope="{ row }" slot="corp_id">
          <div v-if="row.followOne">
            <Tag
            size="medium"
            v-for="(item, index1) in row.followOne.tags"
            :key="index1"
            >{{ item.tag_name }}</Tag
          >
          </div>
        </template>
        <template slot-scope="{ row }" slot="external_userid">
          <span v-if="row.followOne">{{ row.followOne.member.mastare_department_name }}</span>
        </template>
        <!--  -->
        <template slot-scope="{ row }" slot="type">
          <Tag color="blue" size="medium" v-if="row.type == 1">微信用户</Tag>
          <Tag color="gold" size="medium" v-else>企微用户</Tag>
        </template>
        <!-- action -->
        <template slot-scope="{ row }" slot="action">
          <a @click="workInfo(row)" :disabled="row.uid == 0">详情</a>
          <Divider type="vertical" />
          <a @click="editMark(row)">备注</a>
          <Divider type="vertical" />
          <a @click="labeSet(row)">设置标签</a>
        </template>
      </Table>
      <Table
        :columns="columns2"
        :data="tableData1.list"
        class="ivu-mt"
        ref="table"
        highlight-row
        :loading="loading"
        no-userFrom-text="暂无数据"
        no-filtered-userFrom-text="暂无筛选结果"
        @on-select-all="userAll"
        @on-select-all-cancel="userAll"
        @on-selection-change="userSelectRow"
        v-if="tabIndex == 1"
      >
        <template slot-scope="{ row }" slot="avatars">
          <viewer class="acea-row">
            <div class="tabBox_img mr-12">
              <img v-lazy="row.avatar" />
            </div>
            <div class="acea-row row-column row-center">
              <span>{{ row.nickname }}</span>
              <span>UID:{{ row.uid }}</span>
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row }" slot="nickname">
          <div class="acea-row">
            <Icon
              type="md-male"
              v-show="row.sex === '男'"
              color="#2db7f5"
              size="15"
              class="mr5"
            />
            <Icon
              type="md-female"
              v-show="row.sex === '女'"
              color="#ed4014"
              size="15"
              class="mr5"
            />
            <div v-text="row.nickname"></div>
          </div>
        </template>
        <template slot-scope="{ row }" slot="isMember">
          <div>{{ row.isMember ? "是" : "否" }}</div>
        </template>
        <template slot-scope="{ row }" slot="follow_list">
          <div v-if="row.follow_list">
            <span v-for="(item, index1) in row.follow_list" :key="index1">{{
              item.member.name
            }}</span>
          </div>
        </template>
        <template slot-scope="{ row }" slot="action">
          <a @click="changeInfo(row)">详情</a>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="tabIndex == 0 ? tableData.count : tableData1.count"
          :current="tableFrom.page"
          show-elevator
          show-total
          @on-change="pageChange"
          :page-size="tableFrom.limit"
        />
      </div>
    </Card>
    <department
      ref="department"
      :active-department="activeDepartment"
      :is-site="isSite"
      :only-department="onlyDepartment"
      @changeMastart="changeMastart"
    />
    <Modal
      v-model="labelShow"
      scrollable
      title="请选择用户标签"
      :closable="false"
      width="600"
      :footer-hide="true"
      @on-cancel="labelCancel"
    >
      <div class="label-wrapper">
        <div class="list-box">
          <div
            class="label-box"
            v-for="(item, index) in labelList"
            :key="index"
          >
            <div class="title" v-if="item.children && item.children.length">
              {{ item.title }}
            </div>
            <div class="list" v-if="item.children && item.children.length">
              <div
                class="label-item"
                :class="{ on: label.selected }"
                v-for="(label, j) in item.children"
                :key="j"
                @click="selectLabel(label)"
              >
                {{ label.title }}
              </div>
            </div>
          </div>
          <div v-if="!labelList">暂无标签</div>
        </div>
        <div class="footer">
          <Button type="primary" class="btns" @click="labelConfirm()">确定</Button>
          <Button type="primary" class="btns" ghost @click="labelCancel()">取消</Button>
        </div>
      </div>
    </Modal>
    <!-- 非企微用户详情 -->
    <user-details ref="userDetails" :group-list="groupList"></user-details>
    <!-- 发送优惠券-->
    <send-from ref="sends" :userIds="user_ids"></send-from>
    <!-- 企微用户编辑 -->
     <Modal
      v-model="markShow"
      title="设置备注"
      width="400"
      :closable="false"
      class-name="vertical-center-modal"
      @on-ok="editMaekConfirm()"
      @on-cancel="editMarkCancel()"
    >
       <Form v-model="markShow" :label-width="75" label-position="right">
        <FormItem label="备注：" required >
            <Input type="textarea" v-model="remark"  placeholder="请输入备注" style="width: 250px"></Input>
        </FormItem>
       </Form>
    </Modal>
    <!-- 编辑表单 积分余额-->
    <edit-from
      ref="edits"
      :FromData="FromData"
	    :userEdit="1"
      @submitFail="submitFail"
    ></edit-from>
  </div>
</template>

<script>
import { mapState } from "vuex";
import department from "@/components/department/index.vue";
import {
  getWorkClientList,
  workClientSynch,
  workLabel,
  workClientBatchLabel,
  workClientEdit
} from "@/api/work";
import { userList, userGroupApi, putUsersSetLabel,editOtherApi,giveLevelTimeApi } from "@/api/user";
import timeOptions from "@/utils/timeOptions";
import userDetails from "../../user/list/handle/userDetails";
import sendFrom from "@/components/sendCoupons/index";
import editFrom from "@/components/from/from";
export default {
  name: "",
  data() {
    return {
      loading: false,
      formInline: {},
      options: timeOptions,
      columns1: [
        {
          type: "selection",
          width: 60,
          align: "center",
        },
        {
          title: "客户信息",
          slot: "avatar",
          minWidth: 120,
        },
        {
          title: "所属客服",
          slot: "followOne",
          minWidth: 80,
        },
        {
          title: "客服所属部门",
          slot: "external_userid",
          minWidth: 80,
        },
        {
          title: "标签",
          slot: "corp_id",
          minWidth: 120,
        },
        {
          title: "性别",
          slot: "gender",
          minWidth: 80,
        },
        {
          title: "客户类型",
          slot: "type",
          minWidth: 80,
        },
        // {
        //   title: "职位",
        //   key: "position",
        //   minWidth: 100,
        // },
        // {
        //   title: "所在企业主体名称",
        //   key: "corp_full_name",
        //   minWidth: 100,
        // },
        {
          title: "备注",
          key: "remark",
          minWidth: 110,
        },
        {
          title: "添加时间",
          key: "create_time",
          minWidth: 110,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 120,
        },
      ],
      columns2: [
        {
          type: "selection",
          width: 60,
          align: "center",
        },
        {
          title: "客户信息",
          slot: "avatars",
          minWidth: 120,
        },
        {
          title: "付费会员",
          slot: "isMember",
          minWidth: 90,
        },
        {
          title: "用户等级",
          key: "level",
          minWidth: 90,
        },
        {
          title: "分组",
          key: "group_id",
          minWidth: 100,
        },
        {
          title: "手机号",
          key: "phone",
          minWidth: 100,
        },
        {
          title: "用户类型",
          key: "user_type",
          minWidth: 100,
        },
        {
          title: "内部联系人",
          slot: "follow_list",
          minWidth: 100,
        },
        {
          title: "余额",
          key: "now_money",
          minWidth: 100,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 80,
        },
      ],
      tableData: [],
      tableData1: [],
      tabIndex: 0,
      grid: {
        xl: 7,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      timeVal: [],
      //客户标签列表
      labelList: [],
      labelShow: false,
      activeLabel: {
        add_tag: [],
        removeTag: [],
        userid: [],
        is_all: 0,
      },
      activeLabel2: {
        label_id: [],
        uids: [],
        all: 0,
      },
      tableFrom: {
        userid: [],
        label: [],
        time: "",
        name: "",
        page: 1,
        limit: 15,
      },
      labelIds:[],
      usersName: "",
      activeDepartment: {},
      isSite: true,
      onlyDepartment: false,
      openType: "",
      userList: [],
      selectGroup: [],
      selectUser: [],
      groupList: [],
      user_ids: "",
      dataLabel: [],
      setLabelIndex:null,
      labelFormList:[],
      markShow:false,
      remark:"",
      selectRow:{},
      FromData: null,
    };
  },
  components: { department, userDetails, sendFrom,editFrom },
  filters: {
    genderFilter(value) {
      if (value == 0) {
        return "未知";
      } else if (value == 1) {
        return "男";
      } else if (value == 2) {
        return "女";
      }
    },
  },
  watch: {
    usersName(val, oldVal) {
      if (!val) {
        this.tableFrom.userid = [];
      }
    },
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
    this.userGroup();
    this.getWorkLabel();
    this.getUserList();
  },
  methods: {
    selectAll(row) {
      if (row.length) {
        this.selectGroup = row;
      }
    },
    handleSelectRow(row) {
      this.selectGroup = row;
    },
    userAll(row) {
      if (row.length) {
        this.selectUser = row;
      }
    },
    userSelectRow(row) {
      this.selectUser = row;
    },
    getList() {
      this.loading = true;
      getWorkClientList(this.tableFrom)
        .then((res) => {
          this.tableData = res.data;
          this.loading = false;
        })
        .catch((err) => {
          this.$Message.error(err.msg);
          this.loading = false;
        });
    },
    onchangeTime(e) {
      this.timeVal = e;
      this.tableFrom.time = this.timeVal.join("-");
    },
    dateChange() {
      this.tableFrom.page = 1;
      this.getList();
    },
    clientSynch() {
      workClientSynch().then((res) => {
        this.$Message.success(res.msg);
      });
    },
    searchData() {
      this.tableFrom.page = 1;
      if(this.tabIndex == 0){
        this.tableFrom.label = this.labelFormList.map(item=>{
          return item.value
        })
        this.getList();
      }else{
        this.getUserList();
      }
      
    },
    pageChange(index) {
      this.tableFrom.page = index;
      if (this.tabIndex == 0) {
        this.getList();
      } else {
        this.getUserList();
      }
    },
    //获取客户标签
    getWorkLabel() {
      workLabel().then((res) => {
        this.labelList = res.data.map((org) => this.mapTree(org));
      });
    },
    mapTree(org) {
      const haveChildren =
        Array.isArray(org.children) && org.children.length > 0;
      return {
        //分别将我们查询出来的值做出改变他的key
        title: org.label,
        expand: true,
        value: org.value,
        id:org.id,
        selected: false,
        checked: false,
        children: haveChildren ? org.children.map((i) => this.mapTree(i)) : [],
      };
    },
    addUser() {
      this.$refs.department.memberStatus = true;
    },
    changeMastart(arr, type) {
      this.tableFrom.userid = arr.map((item) => {
        return item.userid;
      });
      let name = arr.map((item1) => {
        return item1.name;
      });
      this.usersName = name.toString();
    },
    getUserList() {
      this.loading = true;
      userList({
        page: this.tableFrom.page,
        limit: this.tableFrom.limit,
        user_time_type: "all",
        user_time:this.tableFrom.time,
        nickname:this.tableFrom.name,
        label_ids: this.labelIds.toString()
      })
        .then((res) => {
          let data = res.data;
          this.tableData1 = data;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$Message.error(res.msg);
        });
    },
    changeMenu(row,name){
      switch (name) {
        case "2":
          console.log(123456);
          this.getOtherFrom(row.uid);
          break;
        case "3":
          this.giveLevelTime(row.uid);
          break;
        default:
          break;
      }
    },
    // 获取积分余额表单
    getOtherFrom(id) {
      editOtherApi(id)
        .then(async (res) => {
          if (res.data.status === false) {
            return this.$authLapse(res.data);
          }
          res.data.rules[1].props.max = 999999;
          res.data.rules[1].props.precision = 0;
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    submitFail(p) {
      if (this.$refs.userDetails.modals) {
        this.$refs.userDetails.getDetails(this.selectRow.uid); 
      }
    },
    giveLevelTime(id) {
      giveLevelTimeApi(id)
        .then(async (res) => {
          if (res.data.status === false) {
            return this.$authLapse(res.data);
          }
          this.FromData = res.data;
          this.$refs.edits.modals = true;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    onChangeType() {
      this.selectGroup = [];
      this.selectUser = [];
      this.dataLabel = [];
      this.tableFrom.time = "";
      this.tableFrom.name = "";
    },
    selectLabel(label) {
      if (label.selected) {
        let index = this.dataLabel.indexOf(
          this.dataLabel.filter((d) => d.id == label.id)[0]
        );
        this.dataLabel.splice(index, 1);
        this.activeLabel.removeTag.push(label.value)
        label.selected = false;
      } else {
        this.dataLabel.push({
          id:label.id,
          value:label.value,
          title:label.title
        });
        label.selected = true;
      }
    },
    onSend() {
      if (this.selectUser.length === 0) {
        this.$Message.warning("请选择要发送优惠券的用户");
      } else {
        let arr = this.selectUser.map((item) => item.uid);
        this.user_ids = arr.join();
        this.$refs.sends.modals = true;
        this.$refs.sends.getList();
      }
    },
    setLabel(index) {
      this.setLabelIndex = index;
      this.labelShow = true;
    },
    labeSet(row){
      // this.getWorkLabel();
      if(row.followOne && row.followOne.tags.length){
        this.labelList.forEach(item1=>{
          item1.children.forEach(item2=>{
            row.followOne.tags.forEach(item=>{
              if(item.tag_id == item2.value){
                item2.selected = true;
                this.dataLabel.push({
                  id:item2.id,
                  value:item2.value,
                  title:item2.title
                })
              }
            })
          })
        })
      }
      this.labelShow = true;
      this.selectGroup[0] = row;

    },
    labelConfirm() {
      if(this.setLabelIndex == 0){
        //顶部卡片客户标签作为搜索条件时打开
        this.labelFormList = this.dataLabel;
        this.labelIds = this.labelFormList.map(item=>{
          return item.id
        })
        //打开标签弹窗
        this.labelShow = false;
      }else{
        if (this.tabIndex == 0) {
          //企微客户设置标签
          //selectGroup是选中的用户，可以是多个，也可以是单个
        this.activeLabel.userid = this.selectGroup.map((item) => {
          //将选中用户的user_id push到activeLabel.userid；
          return item.external_userid;
        });
        //设置要添加的tab标签value
        this.activeLabel.add_tag = this.dataLabel.map(item=>{
          return item.value
        })
        //从add_tag标签中剔除要删除的标签，在编辑时使用
        this.activeLabel.add_tag.forEach(i=>{
          this.activeLabel.removeTag.forEach(j=>{
            if(i == j){
              this.activeLabel.add_tag.splice(i,1)
            }
          })
        })
        workClientBatchLabel(this.activeLabel)
          .then((res) => {
            this.$Message.success(res.msg);
            this.labelShow = false;
          })
          .catch((err) => {
            this.$Message.error(err.msg);
            this.labelShow = false;
          });
      } else {
        this.activeLabel2.uids = this.selectUser.map((item) => {
          return item.uid;
        });
        this.activeLabel2.label_id = this.dataLabel.map(item=>{
          return item.id
        })
        putUsersSetLabel(this.activeLabel2)
          .then((res) => {
            this.$Message.success(res.msg);
            this.labelShow = false;
          })
          .catch((err) => {
            this.$Message.error(err.msg);
            this.labelShow = false;
          });
        }
      }
    },
    labelCancel(){
      this.labelShow = false;
      if(this.tabIndex == 0){
        this.getList()
      }else{
        this.getUserList();
      }
      this.getWorkLabel();
    },
    handleClose2(e,name){
      let index = this.labelFormList.indexOf(name);
      this.labelFormList.splice(index, 1);
      this.tableFrom.label.splice(index,1);
      this.labelIds.splice(index,1);
    },
    userGroup() {
      let data = {
        page: 1,
        limit: "",
      };
      userGroupApi(data).then((res) => {
        this.groupList = res.data.list;
      });
    },
    changeInfo(row) {
      this.$refs.userDetails.modals = true;
      this.$refs.userDetails.activeName = "info";
      this.$refs.userDetails.getDetails(row.uid);
    },
    workInfo(row){
      this.selectRow = row;
      this.$refs.userDetails.modals = true;
      this.$refs.userDetails.activeName = "info";
      this.$refs.userDetails.getDetails(row.uid);
    },
    editMark(row){
      this.remark = row.remark;
      this.selectRow = row;
      this.markShow = true;
    },
    editMaekConfirm(){
      workClientEdit(this.selectRow.id,{remark:this.remark}).then(res=>{
        this.$Message.success(res.msg);
        this.getList();
      }).catch(err=>{
        this.$Message.error(err.msg);
      })
    },
    editMarkCancel(){
      this.remark = "";
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

/deep/ .ivu-table-wrapper {
  min-height: 400px;
}

.mt-24 {
  margin-top: 24px;
}

.ml-40 {
  margin-left: 40px;
}

.mr-12 {
  margin-right: 12px;
}

.ml-14 {
  margin-left: 14px;
}

.mb-30 {
  margin-bottom: 30px;
}
.check_label{
  height:32px;
  width:250px;
  padding: 0 7px;
  font-size: 12px;
  border: 1px solid #dcdee2;
  border-radius: 4px;
  color: #515a6e;
  background-color: #fff;
  cursor: text;
}
.conditions {
  display: flex;
}

.new_tab {
  >>>.ivu-tabs-nav .ivu-tabs-tab {
    padding: 4px 16px 20px !important;
    font-weight: 500;
  }
}

.label-wrapper {
  .list {
    display: flex;
    flex-wrap: wrap;

    .label-item {
      margin: 10px 8px 10px 0;
      padding: 3px 8px;
      background: #EEEEEE;
      color: #333333;
      border-radius: 2px;
      cursor: pointer;
      font-size: 12px;

      &.on {
        color: #fff;
        background: #1890FF;
      }
    }
  }

  .footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 40px;

    button {
      margin-left: 10px;
    }
  }
}

.btn {
  width: 60px;
  height: 24px;
}

.title {
  font-size: 13px;
}

.list-box {
  overflow-y: auto;
  overflow-x: hidden;
  max-height: 640px;
}
</style>
