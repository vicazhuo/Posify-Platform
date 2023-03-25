<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Form :label-width="100" inline :label-colon="true">
          <FormItem label="员工搜索">
            <Input
              v-model="tableFrom.name"
              placeholder="请输入员工 ID 名称 手机号"
              style="width:250px;"
            />
            <Button type="primary" @click="search()" class="mx-14">查询</Button>
            <!-- <Button  @click="setDefault()">重置</Button> -->
          </FormItem>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Row type="flex" :wrap="false">
        <Col style="width:260px">
          <Menu
            ref="side_menu"
            width="260px"
            :active-name="activeName"
            @on-select="onselect"
            @on-open-change="onOpenChange"
            :open-names="openMenu"
            v-if="menuList.length"
          >
            <template v-for="item in menuList">
              <side-menu-item
                :name="item.department_id"
                v-if="item.children && item.children.length !== 0"
                :parent-item="item"
                :key="'menu-' + item.name"
              >
              </side-menu-item>
              <menu-item
                v-else
                :name="item.department_id"
                :key="'menu-' + item.name"
              >
                <Icon type="ios-folder" size="15" color="#FFCA28" />
                <span>{{ item.name }}</span>
              </menu-item>
            </template>
          </Menu>
        </Col>
        <Col class="auto">
          <Button
            type="primary"
            @click="synchMember()"
            style="margin-left:20px;"
            >同步企业微信</Button
          >
          <Table
            :columns="columns1"
            :data="tableData.list"
            :loading="loading"
            class="ivu-mt"
          >
            <template slot-scope="{ row }" slot="avatar">
              <viewer class="acea-row">
                <div class="tabBox_img">
                  <img v-lazy="row.avatar" />
                </div>
              </viewer>
            </template>
            <template slot-scope="{ row }" slot="chat">
              <span v-if="row.chat">{{ row.chat.sum_chat }}</span>
            </template>
            <template slot-scope="{ row }" slot="clientFollow">
              <span v-if="row.clientFollow">{{
                row.clientFollow.sum_follow
              }}</span>
            </template>
            <template slot-scope="{ row }" slot="status">
              <span>{{ row.status | typeFilter }}</span>
            </template>
            <template slot-scope="{ row }" slot="department_list">
              <Tag
                size="medium"
                v-for="(item, index1) in row.department_list"
                :key="index1"
                >{{ item.name }}</Tag
              >
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
        </Col>
      </Row>
    </Card>
  </div>
</template>

<script>
import { mapState } from "vuex";
import { workDepartment, workMember } from "@/api/work";
import sideMenuItem from "./menuChild.vue";
import { workSynchMember } from "@/api/work";
export default {
  name: "",
  data() {
    return {
      activeName: 1,
      menuList: [],
      tableFrom: {
        department: "",
        name: "",
        page: 1,
        limit: 15,
      },
      loading: false,
      columns1: [
        {
          title: "ID",
          key: "id",
          minWidth: 60,
        },
        {
          title: "员工头像",
          slot: "avatar",
          minWidth: 60,
        },
        {
          title: "员工名称",
          key: "name",
          minWidth: 100,
        },
        {
          title: "所属部门",
          slot: "department_list",
          minWidth: 140,
        },
        {
          title: "客户数量",
          slot: "clientFollow",
          minWidth: 110,
        },
        {
          title: "所在外部群数量",
          slot: "chat",
          minWidth: 110,
        },

        {
          title: "手机号",
          key: "mobile",
          minWidth: 110,
        },
        {
          title: "邮箱",
          key: "biz_mail",
          minWidth: 130,
        },
        {
          title: "激活状态",
          slot: "status",
          minWidth: 60,
        },
        {
          title: "创建时间",
          key: "create_time",
          minWidth: 110,
        },
      ],
      tableData: [],
      openMenu: [],
    };
  },
  components: {
    sideMenuItem,
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
  filters: {
    typeFilter(status) {
      const statusMap = {
        1: "已激活",
        2: "已禁用",
        4: "未激活",
        5: "退出企业",
      };
      return statusMap[status];
    },
  },
  created() {
    this.getWorkTree();
    this.getMemberList();
  },
  methods: {
    synchMember() {
      workSynchMember()
        .then((res) => {
          this.$Message.success("同步成功");
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    getMemberList() {
      this.loading = true;
      workMember(this.tableFrom)
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
      this.getMemberList();
    },
    setDefault() {
      this.tableFrom.page = 1;
      this.tableFrom.department = "";
      this.tableFrom.name = "";
      this.getMemberList();
    },
    onOpenChange(name) {
      console.log(name);
      if (!name.length) {
        this.tableFrom.department = "";
        this.tableFrom.page = 1;
        this.getMemberList();
      } else {
        this.$nextTick(() => {
          this.tableFrom.department =
            this.activeName == 1 ? "" : this.activeName;
          this.getMemberList();
        });
      }
    },
    onselect(name) {
      this.activeName = name;
      this.tableFrom.department = name;
      this.tableFrom.page = 1;
      this.getMemberList();
    },
    getWorkTree() {
      workDepartment().then((res) => {
        // let obj = {
        //     name: "全部",
        //     department_id: "",
        //     parentid: 1
        // };
        // res.data[0].children.unshift(obj);
        this.menuList = res.data;
        this.activeName == res.data[0].department_id;
        this.getTreeId(this.menuList);
        this.$nextTick(() => {
          this.$refs.side_menu.updateOpened();
          this.$refs.side_menu.updateActiveName();
        });
      });
    },
    getTreeId(datas) {
      for (var i in datas) {
        this.openMenu.push(datas[i].department_id);
        if (datas[i].children) {
          this.getTreeId(datas[i].children);
        }
      }
    },
    pageChange(index) {
      this.tableFrom.page = index;
      this.getMemberList();
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
.auto{
	width calc(100% - 260px)
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
.mx-14{
    margin:0 14px 0;
}
</style>
