<template>
  <div>
    <Row class="ivu-mt box-wrapper">
      <Col span="3" class="left-wrapper" v-if="labelSort.length > 0">
        <Menu :theme="theme3" :active-name="sortName" width="auto">
          <MenuGroup>
            <MenuItem
              :name="item.id"
              class="menu-item"
              v-for="(item, index) in labelSort"
              :key="index"
              @click.native="bindMenuItem(item)"
            >
              {{ item.name }}
              <div class="icon-box" v-if="index != 0">
                <Icon type="ios-more" size="24" @click.stop="showMenu(item)" />
              </div>
              <div
                class="right-menu ivu-poptip-inner"
                v-show="item.status"
                v-if="index != 0"
              >
                <div class="ivu-poptip-body" @click="labelEdit(item)">
                  <div class="ivu-poptip-body-content">
                    <div class="ivu-poptip-body-content-inner">编辑</div>
                  </div>
                </div>
                <div
                  class="ivu-poptip-body"
                  @click="deleteSort(item, '删除分类', index)"
                >
                  <div class="ivu-poptip-body-content">
                    <div class="ivu-poptip-body-content-inner">删除</div>
                  </div>
                </div>
              </div>
            </MenuItem>
          </MenuGroup>
        </Menu>
      </Col>
      <Col span="21" ref="rightBox">
        <Card :bordered="false" dis-hover>
          <Row type="flex">
            <Col v-bind="grid">
              <Button
                v-auth="['admin-user-label_add']"
                type="primary"
                @click="add"
                >添加标签</Button
              >
              <Button
                v-auth="['admin-user-label_add']"
                type="success"
                @click="addSort"
                style="margin-left: 10px"
                >添加分类</Button
              >
              <Button
                ghost
                type="primary"
                style="margin-left: 10px"
                @click="userLabeSync()"
                >同步企业微信标签</Button>
            </Col>
          </Row>
          <Table
            :columns="columns1"
            :data="labelLists"
            ref="table"
            class="mt25"
            :loading="loading"
            highlight-row
            no-userFrom-text="暂无数据"
            no-filtered-userFrom-text="暂无筛选结果"
          >
            <template slot-scope="{ row, index }" slot="icons">
              <viewer>
                <div class="tabBox_img">
                  <img v-lazy="row.icon" />
                </div>
              </viewer>
            </template>
            <template slot-scope="{ row, index }" slot="action">
              <a @click="edit(row.id)">修改</a>
              <Divider type="vertical" />
              <a @click="del(row, '删除标签', index)">删除</a>
            </template>
          </Table>
          <div class="acea-row row-right page">
            <Page
              :total="total"
              show-elevator
              show-total
              @on-change="pageChange"
              :page-size="labelFrom.limit"
            />
          </div>
        </Card>
      </Col>
    </Row>
  </div>
</template>

<script>
import { mapState } from "vuex";
import {
  userLabelAll,
  userLabelApi,
  userLabelAddApi,
  userLabelEdit,
  userLabelCreate,
  workLabelSync
} from "@/api/user";
export default {
  name: "user_label",
  data() {
    return {
      grid: {
        xl: 24,
        lg: 24,
        md: 24,
        sm: 24,
        xs: 24,
      },
      loading: false,
      columns1: [
        {
          title: "ID",
          key: "id",
          Width: 80,
          minWidth: 50,
          maxWidth: 80
        },
        {
          title: "标签名称",
          key: "label_name",
          // minWidth: 600,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          align: 'center',
          minWidth: 120,
          maxWidth: 140
        },
      ],
      labelFrom: {
        page: 1,
        limit: 10,
        label_cate: "",
      },
      labelLists: [],
      total: 0,
      theme3: "light",
      labelSort: [],
      sortName: "",
    };
  },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 75;
    },
    labelPosition() {
      return this.isMobile ? "top" : "right";
    },
  },
  created() {
    this.getUserLabelAll();
  },
  methods: {
    // 添加
    add() {
			let id = this.labelFrom.label_cate?this.labelFrom.label_cate:0;
      this.$modalForm(userLabelAddApi(0,{label_cate:id})).then(() => this.getList());
    },
    // 分组列表
    getList() {
      this.loading = true;
      userLabelApi(this.labelFrom)
        .then(async (res) => {
          let data = res.data;
          this.labelLists = data.list;
          this.total = data.count;
          this.loading = false;
        })
        .catch((res) => {
          this.loading = false;
          this.$Message.error(res.msg);
        });
    },
    pageChange(index) {
      this.labelFrom.page = index;
      this.getList();
    },
    // 修改
    edit(id) {
      this.$modalForm(userLabelAddApi(id)).then(() => this.getList());
    },
    // 删除
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `user/user_label/del/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.labelLists.splice(num, 1);

          this.getList();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 标签分类
    getUserLabelAll(key) {
      userLabelAll().then((res) => {
        let obj = {
          name: "全部",
          id: "",
        };
        res.data.unshift(obj);
        res.data.forEach((el) => {
          el.status = false;
        });
        if (!key) {
          this.sortName = res.data[0].id;
          this.labelFrom.label_cate = res.data[0].id;
          this.getList();
        }
        this.labelSort = res.data;
      });
    },
    // 显示标签小菜单
    showMenu(item) {
      this.labelSort.forEach((el) => {
        if (el.id == item.id) {
          el.status = item.status ? false : true;
        } else {
          el.status = false;
        }
      });
    },
    //编辑标签
    labelEdit(item) {
      this.$modalForm(userLabelEdit(item.id)).then(() =>
        this.getUserLabelAll(1)
      );
    },
    // 添加分类
    addSort() {
      this.$modalForm(userLabelCreate()).then(() => this.getUserLabelAll());
    },
    deleteSort(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `user/user_label_cate/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.labelSort.splice(num, 1);
          this.labelSort = [];
          this.getUserLabelAll();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    bindMenuItem(name) {
      this.labelSort.forEach((el) => {
        el.status = false;
      });
      this.labelFrom.label_cate = name.id;
      this.getList();
    },
    userLabeSync(){
      workLabelSync().then(res=>{
        this.$Message.success(res.msg)
      }).catch(err=>{
        this.$Message.error(err.msg)
      })
    }
  },
};
</script>

<style lang="stylus" scoped>
.ivu-menu-light.ivu-menu-vertical .ivu-menu-item-active:not(.ivu-menu-submenu) {
    background: #eff6fe !important;
    z-index:1 !important;
}
/deep/.ivu-menu{
  position unset!important
}
/deep/ .ivu-menu-vertical .ivu-menu-item-group-title {
  display: none;
}

/deep/ .ivu-menu-vertical.ivu-menu-light:after {
  display: none;
}

.left-wrapper {
  // height: 904px;
  background: #fff;
  border-right: 1px solid #dcdee2;
}

/deep/ .ivu-menu {
  z-index: 0 !important;
}

/deep/ .ivu-table-wrapper {
  min-height: 535px;
}
.menu-item {
  position: relative;
  display: flex;
  justify-content: space-between;

  .icon-box {
    z-index: 3;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    display: none;
  }

  &:hover .icon-box {
    display: block;
  }

  .right-menu {
    z-index: 10;
    position: absolute;
    right: -106px;
    top: -11px;
    width: auto;
    min-width: 121px;
  }
}
</style>
