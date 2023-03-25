<template>
  <div>
    <Row class="ivu-mt box-wrapper">
      <Col span="3" class="left-wrapper">
        <Menu :theme="theme3" :active-name="sortName" width="auto">
          <MenuGroup>
            <MenuItem
              :name="item.id"
              class="menu-item"
              :class="index === current ? 'showOn' : ''"
              v-for="(item, index) in labelSort"
              :key="index"
              @click.native="bindMenuItem(item, index)"
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
                    <div class="ivu-poptip-body-content-inner">编辑分类</div>
                  </div>
                </div>
                <div
                  class="ivu-poptip-body"
                  @click="deleteSort(item, '删除分类', index)"
                >
                  <div class="ivu-poptip-body-content">
                    <div class="ivu-poptip-body-content-inner">删除分类</div>
                  </div>
                </div>
              </div>
            </MenuItem>
          </MenuGroup>
        </Menu>
      </Col>
      <Col span="21" ref="rightBox">
        <Card :bordered="false" dis-hover>
          <Row type="flex" class="mb20">
            <Col span="24">
              <Button
                v-auth="['setting-store_service-add']"
                type="primary"
                @click="add"
                style="margin-right:14px;"
                >添加话术</Button
              >
              <Button
                v-auth="['setting-store_service-add']"
                type="success"
                @click="addSort"
                >添加分类</Button
              >
            </Col>
          </Row>
          <Table
            :columns="columns1"
            :data="tableList"
            :loading="loading"
            highlight-row
            no-userFrom-text="暂无数据"
            no-filtered-userFrom-text="暂无筛选结果"
          >
            <template slot-scope="{ row }" slot="avatar">
              <viewer>
                <div class="tabBox_img">
                  <img v-lazy="row.avatar" />
                </div>
              </viewer>
            </template>
            <template slot-scope="{ row }" slot="add_time">
              <span> {{ row.add_time }}</span>
            </template>

            <template slot-scope="{ row, index }" slot="action">
              <a @click="edit(row)">编辑</a>
              <Divider type="vertical" />
              <a @click="del(row, '删除话术', index)">删除</a>
            </template>
          </Table>
          <div class="acea-row row-right page">
            <Page
              :total="total"
              show-elevator
              show-total
              @on-change="pageChange"
              :page-size="tableFrom.limit"
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
  wechatSpeechcraft,
  speechcraftCreate,
  speechcraftEdit,
  speechcraftcate,
  speechcraftcateCreate,
  speechcraftcateEdit,
} from "@/api/setting";
export default {
  name: "index",
  computed: {
    
  },
  data() {
    return {
      labelSort:[],
      sortName:"",
      current:0,
      modals: false,
      total: 0,
      theme3: 'light',
      tableFrom: {
        page: 1,
        limit: 10,
        cate_id: 0,
      },
      timeVal: [],
     
      loading: false,
      tableList: [],
      columns1: [
        {
          title: "ID",
          key: "id",
          width: 80,
        },
        {
          title: "详情",
          key: "message",
          minWidth: 320,
        },
        {
          title: "排序",
          key: "sort",
          minWidth: 60,
        },
        {
          title: "添加时间",
          slot: "add_time",
          minWidth: 120,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 150,
          maxWidth: 160
        },
      ]
    };
  },
  created() {
    this.getUserLabelAll();
  },
  methods: {
    getUserLabelAll(key) {
      speechcraftcate().then((res) => {
        let data = res.data.data;
        let obj = {
          name: "全部",
          id: "",
        };
        data.unshift(obj);
        data.forEach((el) => {
          el.status = false;
        });
        if (!key) {
          this.sortName = data[0].id;
          this.tableFrom.cate_id = data[0].id;
          this.getList();
        }
        this.labelSort = data;
      });
    },
    // 添加分类
    addSort() {
      this.$modalForm(speechcraftcateCreate()).then(() =>
        this.getUserLabelAll()
      );
    },
    //编辑标签分类
    labelEdit(item) {
      this.$modalForm(speechcraftcateEdit(item.id)).then(() =>
        this.getUserLabelAll(1)
      );
    },
    // 删除分类
    deleteSort(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `app/wechat/speechcraftcate/${row.id}`,
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
    // 选择分类
    bindMenuItem(name, index) {
      this.current = index;
      this.labelSort.forEach((el) => {
        el.status = false;
      });
      this.tableFrom.cate_id = name.id;
      this.getList();
    },
   
    // 编辑话术
    edit(row) {
      this.$modalForm(speechcraftEdit(row.id)).then(() => this.getList());
    },
    // 添加话术
    add() {
      this.$modalForm(speechcraftCreate({cate_id:this.tableFrom.cate_id})).then(() => this.getList());
    },
    
    // 删除
    del(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `/app/wechat/speechcraft/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.tableList.splice(num, 1);
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 列表
    getList() {
      this.loading = true;
      wechatSpeechcraft(this.tableFrom)
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
    // 翻页
    pageChange(index) {
      this.tableFrom.page = index;
      this.getList();
    },
  },
};
</script>

<style scoped lang="stylus">
  .showOn{
    color: #2d8cf0;
    background: #eff6fe;
    z-index: 2;
  }
  /deep/ .ivu-table-wrapper{
    min-height: 400px;
  }
  .tabBox_img
    width 36px
    height 36px
    border-radius:4px;
    cursor pointer
    img
      width 100%
      height 100%
  .modelBox
    >>>
    .ivu-table-header
      width 100% !important
  .trees-coadd
    width: 100%;
    height: 385px;
    .scollhide
      width: 100%;
      height: 100%;
      overflow-x: hidden;
      overflow-y: scroll;
  // margin-left: 18px;
  .scollhide::-webkit-scrollbar {
    display: none;
  }
  /deep/ .ivu-menu-vertical .ivu-menu-item-group-title{
    display: none;
  }
  /deep/ .ivu-menu-vertical.ivu-menu-light:after{
    display none
  }

  .left-wrapper
    /*height 904px*/
    z-index 50
    background #fff
    border-right 1px solid #dcdee2
  .menu-item
    z-index 50
    position: relative;
    display flex
    justify-content space-between
    word-break break-all
    .icon-box
      z-index 3
      position absolute
      right 20px
      top 50%
      transform translateY(-50%)
      display none
    &:hover .icon-box
      display block
    .right-menu
      z-index 10
      position absolute
      right: -106px;
      top: -11px;
      width auto
      min-width: 121px;
</style>
