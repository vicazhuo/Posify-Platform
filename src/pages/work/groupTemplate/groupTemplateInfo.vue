<template>
  <div>
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/group/template' }">
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
    <cards-data
      :cardLists="cardLists"
      v-if="cardLists.length >= 0"
    ></cards-data>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <div class="new_tab">
        <Tabs v-model="tabIndex" @on-click="onChangeType">
          <TabPane label="客户群接收详情" name="0"></TabPane>
          <TabPane label="群主发送详情" name="1"></TabPane>
        </Tabs>
      </div>
      <Form ref="formValidate" :label-width="96" inline @submit.native.prevent>
        <FormItem label="选择群主:">
          <Select
            v-model="tableForm.owner"
            clearable
            style="width: 250px"
          >
            <Option v-for="item in ownerData" :value="item.ownerInfo.userid" :key="item.id">{{ item.ownerInfo.name }}</Option>
          </Select>
        </FormItem>
        <FormItem label="送达状态:">
          <Select v-model="tableForm.status" clearable style="width: 250px">
            <Option value="0">未发送</Option>
            <Option value="1">已发送</Option>
          </Select>
        </FormItem>
        <FormItem label="群聊搜索:" v-if="tabIndex == 0">
          <Input
            v-model="tableForm.name"
            style="width: 250px;"
            placeholder="请输入群发名称"
          />
        </FormItem>
        <Button type="primary" @click="search()">查询</Button>
      </Form>
      <Table
        :columns="tabIndex == 0 ? tableColumn2 : tableColumn1"
        :data="tableData.list"
        :loading="userLoading"
      >
        <template slot-scope="{ row }" slot="ownerInfo">
            <span>{{row.ownerInfo.name}}</span>
          </template>
        <template slot-scope="{ row }" slot="status">
          <Tag color="green" size="medium" v-if="row.status == 1">已发送</Tag>
          <Tag color="orange" size="medium" v-if="row.status == 2"
            >正在发送</Tag
          >
          <Tag color="red" size="medium" v-if="row.status == 0">未发送</Tag>
        </template>
        <template slot-scope="{ row, index }" slot="action">
          <a @click="groupCount(row, index)" v-if="tabIndex == 0">群统计</a>
          <a @click="sendMessage(row, index)" :disabled="row.status == 1" v-if="tabIndex == 1">提醒发送</a>
          <Divider type="vertical" v-if="tabIndex == 1" />
          <a @click="detailsInfo(row, index)" v-if="tabIndex == 1">群发详情</a>
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
    <!-- 详情模态框-->
    <Modal
      v-model="modals"
      class="tableBox"
      scrollable
      footer-hide
      closable
      title="群发详情"
      :mask-closable="false"
      width="750"
    >
      <Form ref="formValidate" :label-width="96" inline @submit.native.prevent>
        <FormItem label="送达状态:">
          <Select
            v-model="tableForm2.status"
            clearable
            style="width: 250px; margin-right: 14px"
          >
            <Option value="0">未发送</Option>
            <Option value="1">已发送</Option>
            <Option value="2">发送失败</Option>
          </Select>
          <Button type="primary" @click="search1()">查询</Button>
        </FormItem>
      </Form>
      <Table
        ref="selection"
        :columns="columns2"
        :data="tabList3.list"
        no-data-text="暂无数据"
        highlight-row
        max-height="600"
        size="small"
        no-filtered-data-text="暂无筛选结果"
      >
        <template slot-scope="{ row }" slot="owner">
          <span>{{row.owner}}</span>
        </template>
        <template slot-scope="{ row }" slot="status">
          <Tag color="green" size="medium" v-if="row.status == 1">已发送</Tag>
          <Tag color="red" size="medium" v-if="row.status == 0">未发送</Tag>
        </template>
      </Table>
      <div class="acea-row row-right page">
        <Page
          :total="tabList3.count"
          :current="tableForm2.page"
          show-elevator
          show-total
          @on-change="pageChange1"
          :page-size="tableForm2.limit"
        />
      </div>
    </Modal>
  </div>
</template>
<script>
import cardsData from "@/components/cards/cards";
import {
  workGroupChat,
  groupChatList,
  workGroupTemplateChatInfo,
  groupChatOwnerList,
  workGroupTemplateSendMsg,
  groupOwnerChatList,
} from "@/api/work";
export default {
  data() {
    return {
      formItem: {},
      modals: false,
      labelList: [],
      cardLists: [],
      tabList3: [],
      optionData: {},
      style: { height: "400px" },
      spinShow: false,
      tableColumn2: [
        {
          title: "群聊名称",
          key: "name",
          minWidth: 100,
        },
        {
          title: "群主",
          slot: "ownerInfo",
          minWidth: 100,
        },
        {
          title: "群聊成员数量",
          key: "member_num",
          minWidth: 100,
        },
        {
          title: "消息送达状态",
          slot: "status",
          minWidth: 100,
        },
        {
          title: "群聊创建时间",
          key: "group_create_time",
          minWidth: 100,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 170,
        },
      ],
      tableColumn1: [
        {
          title: "群主",
          slot: "ownerInfo",
          minWidth: 100,
        },
        {
          title: "群发发送状态",
          slot: "status",
          minWidth: 100,
        },
        {
          title: "本次群发群聊总数",
          key: "member_num",
          minWidth: 100,
        },
        {
          title: "已发送群聊数",
          key: "status",
          minWidth: 100,
        },
        {
          title: "确认发送时间",
          key: "create_time",
          minWidth: 100,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 170,
        },
      ],
      columns2: [
        {
          title: "群聊名称",
          key: "name",
          minWidth: 150,
        },
        {
          title: "群人数",
          key: "member_num",
        },
        {
          title: "送达状态",
          slot: "status",
        },
      ],
      selectGroup: {},
      tabIndex: 0,
      ownerData: [],
      tableData: [],
      ownerForm: {
        page: 1,
        limit: 15,
      },
      userLoading: false,
      timeVal: [],
      tableForm: {
        id: "",
        page: 1,
        limit: 15,
        status: "",
        name:"",
        owner:null
      },
      tableForm2: {
        page: 1,
        limit: 15,
        status: "",
      },
    };
  },
  components: { cardsData },
  mounted() {
    this.tableForm.id = this.$route.params.id;
    this.getData();
    this.getWorkGroupChat();
    this.getgroupChatList();
  },
  methods: {
    getData() {
      workGroupTemplateChatInfo(this.$route.params.id).then((res) => {
        this.cardLists = [
          {
            col: 6,
            count: res.data.user_count,
            name: "已发群主",
            type: 1,
            className: "iconjinrixinzeng",
          },
          {
            col: 6,
            count: res.data.unuser_count,
            name: "未发送群主",
            type: 1,
            className: "iconjinrituiqun",
          },
          {
            col: 6,
            count: res.data.external_user_count,
            name: "已送达群聊",
            type: 1,
            className: "icondangqianqunchengyuan",
          },
          {
            col: 6,
            count: res.data.external_unuser_count,
            name: "未送达群聊",
            type: 1,
            className: "iconleijituiqun",
          },
        ];
      });
    },
    onChangeType() {
      if (this.tabIndex == 0) {
        this.getgroupChatList();
      } else {
        this.getgroupChatOwnerList();
      }
    },
    search() {
      if (this.tabIndex == 0) {
        this.getgroupChatList();
      } else {
        this.getgroupChatOwnerList();
      }
    },
    search1() {
      this.tableForm2.page = 1;
      this.detailsInfo(this.selectGroup);
    },
    pageChange(index) {
      this.tableForm.page = index;
      if (this.tabIndex == 0) {
        this.getgroupChatList();
      } else {
        this.getgroupChatOwnerList();
      }
    },
    //获取群主
    getWorkGroupChat() {
      workGroupChat({})
        .then((res) => {
          this.ownerData = res.data.list;
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    // 客户群接收详情
    getgroupChatList() {
      groupChatList(this.tableForm.id, this.tableForm).then((res) => {
        this.tableData = res.data;
      });
    },
    // 群主发送详情
    getgroupChatOwnerList() {
      groupChatOwnerList(this.tableForm.id, this.tableForm).then((res) => {
        this.tableData = res.data;
      });
    },
    sendMessage(row, index) {
      workGroupTemplateSendMsg({
        userid: row.owner,
        time: row.create_time,
        id: "",
      })
        .then((res) => {
          this.$Message.success(res.msg);
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    // 群发详情
    detailsInfo(row) {
      this.modals = true;
      this.selectGroup = row;
      groupOwnerChatList({
        chat_id : row.chat_ids,
        status: this.tableForm2.status,
        page: this.tableForm2.page,
        limit: this.tableForm2.limit,
      })
        .then((res) => {
          this.tabList3 = res.data;
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    groupCount(row, index) {
      this.$router.push("/admin/work/client/statistical/" + row.id);
    },
    pageChange1(index) {
      this.tableForm2.page = index;
      this.detailsInfo(this.selectGroup);
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
  >>>.ivu-tabs-nav .ivu-tabs-tab {
    padding: 4px 16px 20px !important;
    font-weight: 500;
  }
}
</style>