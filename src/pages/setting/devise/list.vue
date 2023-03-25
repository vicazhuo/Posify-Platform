<template>
  <div>
    <div class="i-layout-page-header">
      <PageHeader
        class="product_tabs"
        :title="$route.meta.title"
        hidden-breadcrumb
        :style="'padding-right:'+(menuCollapse?105:20)+'px'"
      >
        <div slot="title">
          <div style="float: left;">
            <span v-text="$route.meta.title" class="mr20"></span>
          </div>
<!--          <div style="float: right;" v-if="cardShow==1 || cardShow==2">-->
<!--            <Button class="bnt" type="primary" @click="submit" :loading="loadingExist">保存</Button>-->
<!--            <Button class="bnt ml20" @click="reast">重置</Button>-->
<!--          </div>-->
        </div>
      </PageHeader>
    </div>
    <Row class="ivu-mt box-wrapper">
      <Col span="3" class="left-wrapper">
        <Menu :theme="theme3" :active-name="1" width="auto">
          <MenuGroup>
            <MenuItem
              :name="item.id"
              :class="index === current ? 'showOn' : ''"
              v-for="(item, index) in menuList"
              :key="index"
              @click.native="bindMenuItem(index)"
            >
              {{ item.name }}
            </MenuItem>
          </MenuGroup>
        </Menu>
      </Col>
      <Col span="21" class="right-wrapper">
        <Card :bordered="false" dis-hover v-if="cardShow==0">
          <Row v-if="cardShow==0">
            <Col style="width: 310px;height:550px;margin-right: 30px;position: relative" v-if="isDiy">
              <iframe class="iframe-box" :src="imgUrl" frameborder="0" ref="iframe"></iframe>
              <div class="mask"></div>
            </Col>
            <Col :span="isDiy?'':24" v-bind="isDiy?grid:''" :class="isDiy?'table':''">
              <div class="acea-row row-between-wrapper">
                <Row type="flex">
                  <Col v-bind="grid">
                    <div class="button acea-row row-middle">
                      <Button type="primary" @click="add">添加主题</Button>
                    </div>
                  </Col>
                </Row>
              </div>
              <Table
                      :columns="columns1"
                      :data="list"
                      ref="table"
                      class="mt25"
                      :loading="loading"
                      highlight-row
                      no-userFrom-text="暂无数据"
                      no-filtered-userFrom-text="暂无筛选结果"
              >
                <template slot-scope="{ row, index }" slot="region">
                  <div class="font-blue">
                    {{
                    row.type == 1
                    ? "默认模板"
                    : row.type == 2
                    ? "行业模板"
                    : "活动模板"
                    }}
                  </div>
                </template>
                <template slot-scope="{ row, index }" slot="action">
                  <a @click="edit(row)" v-if="row.status || row.is_diy">编辑</a>
                  <div v-if="row.id != 1 && row.is_diy" style="display: inline-block">
                    <Divider type="vertical" v-if="row.status || row.is_diy" />
                    <a @click="del(row, index)">删除</a>
                  </div>
                  <div style="display: inline-block" v-if="row.status != 1">
                    <Divider type="vertical" v-if="row.status || row.is_diy" />
                    <a @click="setStatus(row, index)">设为首页</a>
                  </div>
                  <div v-if="row.status || row.is_diy" style="display: inline-block">
                    <Divider type="vertical"/>
                    <!--<a class="copy-data"v-clipboard:copy="BaseURL+'pages/annex/special/index?id='+row.id"-->
                       <!--v-clipboard:success="onCopy"-->
                       <!--v-clipboard:error="onError">预览</a>-->
                    <a class="copy-data" @click="preview(row)">预览</a>
                  </div>
                  <div style="display: inline-block" v-if="!row.is_diy">
                    <Divider type="vertical" />
                    <a @click="recovery(row, index)">恢复初始设置</a>
                    <Divider type="vertical" />
                    <a @click="del(row, index)">删除</a>
                  </div>
                </template>
              </Table>
              <div class="acea-row row-right page">
                <Page :total="total"
                      :current="diyFrom.page"
                      show-elevator
                      show-total
                      @on-change="pageChange"
                      :page-size="diyFrom.limit"
                />
              </div>
            </Col>
          </Row>
        </Card>
        <goodClass v-else-if="cardShow==1" ref="category" @parentFun="getChildData"></goodClass>
        <users v-else ref="users" @parentFun="getChildData"></users>
      </Col>
    </Row>
    <Card :bordered="false" dis-hover class="fixed-card" :style="{left: `${!menuCollapse?'200px':isMobile?'0':'80px'}`}" v-show="cardShow==1 || cardShow==2">
      <div class="acea-row row-center-wrapper">
        <Button class="bnt" type="primary" @click="submit" :loading="loadingExist">保存</Button>
        <Button class="bnt ml20" @click="reast">重置</Button>
      </div>
    </Card>
    <Modal v-model="modal" title="预览" footer-hide>
      <div>
        <div v-viewer class="acea-row row-around code">
          <div class="acea-row row-column-around row-between-wrapper">
            <div class="QRpic" ref="qrCodeUrl"></div>
            <span class="mt10">公众号二维码</span>
          </div>
          <div class="acea-row row-column-around row-between-wrapper">
            <div class="QRpic">
              <img v-lazy="qrcodeImg" />
            </div>
            <span class="mt10">小程序二维码</span>
          </div>
        </div>
      </div>
    </Modal>
  </div>
</template>

<script>
import Setting from '@/setting';
import ClipboardJS from "clipboard";
import { diyList, diyDel, setStatus, recovery, getRoutineCode } from "@/api/diy";
import { mapState } from "vuex";
import QRCode from 'qrcodejs2'
import goodClass from './goodClass'
import users from './users'
export default {
  name: "devise_list",
  computed: {
    ...mapState('admin/layout', [
      'menuCollapse',
			'isMobile'
    ])
  },
  components: {
    goodClass,
    users
  },
  data() {
    return {
      grid: {
        sm: 10,
        md: 12,
        lg: 19,
      },
      loading: false,
      theme3: "light",
      menuList:[
        {
          name:'商城首页',
          id:1
        },
        {
          name:'商品分类',
          id:2
        },
        {
          name:'个人中心',
          id:3
        },
      ],
      columns1: [
        {
          title: "页面ID",
          key: "id",
          width: 80,
        },
        {
          title: "模板名称",
          key: "name",
          minWidth: 100,
        },
        {
          title: "添加时间",
          key: "add_time",
          minWidth: 100,
        },
        {
          title: "更新时间",
          key: "update_time",
          minWidth: 100,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 180,
        },
      ],
      list: [],
      imgUrl:'',
      modal: false,
      BaseURL: Setting.apiBaseURL.replace(/adminapi/, ""),
      cardShow: 0,
      loadingExist: false,
      isDiy: 1,
      qrcodeImg: '',
      diyFrom: {
        type: 1,
        page: 1,
        limit: 10
      },
      total: 0,
    };
  },
  created() {
    this.getList();
    // var storage = window.localStorage;
    // storage.setItem('imgUrl',imgUrl)
  },
  mounted: function() {
  },
  methods: {
    getChildData(e){
      this.loadingExist = e
    },
    submit(){
      if(this.cardShow==1){
        this.$refs.category.onSubmit()
      }else {
        this.$refs.users.onSubmit()
      }
    },
    reast(){
      if(this.cardShow==1){
        this.$refs.category.onSubmit(1)
      }else {
        this.$refs.users.getInfo();
      }
    },
    bindMenuItem(index) {
      this.cardShow = index;
    },
    onCopy() {
      this.$Message.success("复制预览链接成功");
    },
    onError() {
      this.$Message.error("复制预览链接失败");
    },
    //生成二维码
    creatQrCode(id,status) {
      this.$refs.qrCodeUrl.innerHTML = ''
			let url = '';
			if(status){
				url = `${this.BaseURL}pages/index/index`;
			}else{
				url= `${this.BaseURL}pages/annex/special/index?id=${id}`;
			}
      var qrcode = new QRCode(this.$refs.qrCodeUrl, {
        text: url, // 需要转换为二维码的内容
        width: 160,
        height: 160,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
      })
    },
    //小程序二维码
    routineCode(id){
      getRoutineCode(id).then(res=>{
        this.qrcodeImg = res.data.image;
      }).catch(err=>{
        this.$Message.error(err);
      })
    },
    preview(row){
      this.modal = true;
      this.creatQrCode(row.id,row.status);
      this.routineCode(row.id);
    },
    // 获取列表
    getList() {
      let storage = window.localStorage;
      this.imgUrl = storage.getItem('imgUrl');
      let that = this
      this.loading = true;
      diyList(this.diyFrom).then((res) => {
        this.loading = false;
        let data = res.data;
        this.list = data.list;
        this.total = data.count;
        data.list.forEach(function (e) {
          if(e.status==1){
            that.isDiy = e.is_diy;
            let imgUrl = `${that.BaseURL}pages/annex/special/index?id=${e.id}`;
            storage.setItem('imgUrl',imgUrl)
            that.imgUrl = imgUrl;
            // let imgUrl = JSON.parse(storage.getItem('menuList'));
            // that.imgUrl = `http://192.168.1.12:8080/pages/annex/special/index?id=${e.id}`;
          }
        })
      });
    },
    pageChange(status) {
      this.diyFrom.page = status;
      this.getList();
    },
    // 编辑
    edit(row) {
      if (row.is_diy) {
        // this.$store.commit('userInfo/setPageName', row.template_name || 'moren');
        this.$router.push({
          path: "/admin/pages/diy",
          query: { id: row.id, name: row.template_name || "moren" },
        });
      } else {
        let storage = window.localStorage;
        storage.setItem("pageName", row.template_name);
        this.$store.dispatch("admin/user/getPageName");
        this.$router.push({
          path: "/admin/setting/pages/template",
          query: { id: row.id, name: row.template_name },
        });
      }
    },
    // 添加
    add() {
      this.$router.push({
        path: "/admin/pages/diy",
        query: { id: 0, name: "首页", type: 1 },
      });
    },
    // 删除
    del(row) {
      let delfromData = {
        title: "删除",
        num: 2000,
        url: "diy/del/" + row.id,
        method: "DELETE",
        data: {
          type: 1,
        },
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.getList();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    // 使用模板
    async setStatus(row) {
      this.$Modal.confirm({
        title: "提示",
        content: "<p>是否把该模板设为首页</p>",
        onOk: () => {
          setStatus(row.id, {
            type: 1,
          })
            .then((res) => {
							let that = this;
              if (res.data.status) {
                this.$Message.success(res.data.msg);
                this.$Modal.remove();
                this.getList();
              } else {
                setTimeout((e) => {
                  that.$Modal.confirm({
                    title: "提示",
                    content: "<p>尚未安装模板，请安装后再试！</p>",
                    loading: false,
                    //okText: "点击购买",
                    // onOk: () => {
                    //   window.open("http://s.crmeb.com/goods_cate", `_blank`);
                    // },
                  });
                },500);
              }
            })
            .catch((res) => {
              this.$Modal.remove();
              this.$Message.error(res.msg);
            });
        },
      });
    },
    recovery(row) {
      recovery(row.id).then((res) => {
        this.$Message.success(res.msg);
        this.getList();
      });
    },
  },
};
</script>

<style scoped lang="stylus">
  .ivu-menu-light.ivu-menu-vertical .ivu-menu-item-active:not(.ivu-menu-submenu) {
    background: #eff6fe !important;
}
  .ivu-mt{
    background-color #fff;
  }
  .bnt{
    width 80px!important;
  }
  .iframe-box{
    width: 100%;
    height: 100%;
    border-radius: 10px;
    border 1px solid #eee;
  }
    .mask{
      position absolute;
      left:0;
      width 100%;
      top:0;
      height 100%;
      background-color rgba(0,0,0,0)
    }
  /deep/.ivu-menu-vertical .ivu-menu-item, .ivu-menu-vertical .ivu-menu-submenu-title{
    text-align center;
  }
 .fixed-card {
      position: fixed;
      right: 0;
      bottom: 0;
      left: 200px;
      z-index: 99;
      box-shadow: 0 -1px 2px rgb(240, 240, 240);
  }
  /deep/ .ivu-menu-vertical .ivu-menu-item-group-title {
    display: none;
  }
  /deep/ .ivu-menu-vertical.ivu-menu-light:after {
    display: none;
  }
  /deep/ .ivu-menu {
    z-index: 0 !important;
  }
  @media (max-width: 2175px){
    .table {
      display: block;
      flex: 0 0 76%;
      max-width: 76%;
    }
  }
  @media (max-width: 2010px){
    .table {
      display: block;
      flex: 0 0 75%;
      max-width: 75%;
    }
  }
  @media (max-width: 1860px){
    .table {
      display: block;
      flex: 0 0 70%;
      max-width: 70%;
    }
  }
  @media (max-width: 1597px){
    .table {
      display: block;
      flex: 0 0 65%;
      max-width: 65%;
    }
  }
  @media (max-width: 1413px){
    .table {
      display: block;
      flex: 0 0 60%;
      max-width: 60%;
    }
  }
  @media (max-width: 1275px){
    .table {
      display: block;
      flex: 0 0 55%;
      max-width: 55%;
    }
  }
  @media (max-width: 1168px){
    .table {
      display: block;
      flex: 0 0 48%;
      max-width: 48%;
    }
  }
  .code{
    position: relative;
  }
  .QRpic {
    width: 160px;
    height: 160px;

    img {
      width: 100%;
      height: 100%;
    }
  }
  .left-wrapper {
    background: #fff;
    border-right: 1px solid #dcdee2;
  }
  .picCon{
    width: 280px;
    height: 510px;
    background: #FFFFFF;
    border: 1px solid #EEEEEE;
    border-radius: 25px;
    .pictrue{
      width: 250px;
      height: 417px;
      border: 1px solid #EEEEEE;
      opacity: 1;
      border-radius: 10px;
      margin 30px auto 0 auto;
      img{
        width 100%;
        height 100%;
        border-radius: 10px;
      }
    }
    .circle{
      width 36px;
      height 36px;
      background: #FFFFFF;
      border: 1px solid #EEEEEE;
      border-radius: 50%;
      margin 13px auto 0 auto;
    }
  }
</style>
