<template>
  <div>
    <!-- <div class="i-layout-page-header">
      <PageHeader
        class="product_tabs"
        title="企业渠道码"
        hidden-breadcrumb
      ></PageHeader>
    </div> -->
     <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Alert closable show-icon>
          <p>
            1.可以生成带参数的二维码名片，支持活码功能，即随机选取设置的活码成员推给用户。加企业微信为好友后，可以给微信联系人自动回复相应欢迎消息和打标签。
          </p>
          <p>
            2.受限于官方，单人类型的渠道码创建后尽量不要再修改成员，否则会造成列表中，该二维码中间的头像与配置的成员头像不一致，但是并不影响功能使用。
          </p>
          <p>
            3.如果企业在企业微信后台为相关成员配置了可用的欢迎语，使用本系统配置欢迎语，则均不起效，推送的还是企业微信官方的。
          </p>
        </Alert>
      <Form :label-width="96" inline :label-colon="true">
          <FormItem label="二维码搜索">
              <Input placeholder="请输入二维码名称" v-model="tableFrom.name" style="width: 250px"/>
          </FormItem>
          <FormItem label="类型选择">
              <Select style="width: 250px;margin-right:14px;" clearable  v-model="tableFrom.type">
                <Option value="0">全天在线</Option>
                <Option value="1">自动上下线</Option>
              </Select>
              <Button type="primary" @click="codeSearchs()">查询</Button>
          </FormItem>
          </Form>
      </div>
     </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
      <Row type="flex" :wrap="false">
        <Col class="left-wrapper">
          <Menu :active-name="sortName">
            <MenuItem :name="item.id" 
            class="menu-item"
            :class="index === current ? 'showOn' : ''"
            v-for="(item,index) in channelCateList" :key="index"
            @click.native="bindMenuItem(item, index)">
            {{item.name}}
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
                  @click="delChannelCate(item, '删除分类', index)"
                >
                  <div class="ivu-poptip-body-content">
                    <div class="ivu-poptip-body-content-inner">删除</div>
                  </div>
                </div>
              </div>
            </MenuItem>
          </Menu>
        </Col>
        <Col class="auto">
        <div class="acea-row ml-20 mb-15">
            <router-link :to="'/admin/work/createCode'">
              <Button type="primary" class="mr-12">添加二维码</Button
              >
            </router-link>
            <Button type="success" class="mr-12"  @click="addChannelCate()">添加分类</Button>
            <Button class="mr-12"  @click="branchCate()" :disabled="!branchRow.length">批量移动</Button>
            <Button class="mr-12"  @click="downPic()" :disabled="!branchRow.length">批量下载</Button>
          </div>
          <Table ref="selection" 
          :columns="columns1" 
          :data="tableData.list" 
          :loading="loading"
          @on-select-all="selectAll"
          @on-select-all-cancel="selectAll"
          @on-selection-change="handleSelectRow">
            <template slot-scope="{ row }" slot="qrcode_url">
              <viewer class="acea-row">
                <div class="tabBox_img">
                  <img v-lazy="row.qrcode_url" />
                </div>
              </viewer>
            </template>
            <template slot-scope="{ row }" slot="category">
              <span v-if="row.category">{{row.category.name}}</span>
            </template>
            <template slot-scope="{ row }" slot="skip_verify">
              <span>{{row.skip_verify ? '自动添加' : '手动添加'}}</span>
            </template>
            <template slot-scope="{ row }" slot="type">
              <span>{{row.type ? '自动上线' : '全天在线'}}</span>
            </template>
            <template slot-scope="{ row }" slot="label_name">
              <Tag  size="medium" v-for="(item,index1) in row.label_name" :key="index1">{{item}}</Tag>
            </template>
            <template slot-scope="{ row, index }" slot="action">
              <a @click="editData(row)">编辑</a>
              <Divider type="vertical" />
              <a @click="codeInfo(row)">详情</a>
              <Divider type="vertical" />
              <template>
                <Dropdown
                  @on-click="changeMenu(row, $event, index)"
                  style="z-index: 999"
                >
                  <a href="javascript:void(0)">
                    更多
                    <Icon type="ios-arrow-down"></Icon>
                  </a>
                  <DropdownMenu slot="list">
                    <DropdownItem name="1">移动</DropdownItem>
                    <DropdownItem name="2">删除</DropdownItem>
                    <DropdownItem name="3">客户列表</DropdownItem>
                  </DropdownMenu>
                </Dropdown>
              </template>
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
     <Modal
        v-model="codeInfoStatus"
        title="渠道码详情"
        :footer-hide="true"
        width="900"
        >
        <div class="acea-row">
          <div class="left_container acea-row row-center">
            <div class="qrcode_box">
              <img :src="infoData.qrcode_url" alt="">
              <p class="qrcode_name">{{infoData.name}}</p>
              <Button type="primary" :long="true" @click="downloadCodeImg()">下载活码</Button>
              <Button :long="true" style="margin-top:12px;" @click="editData(infoData)">修改</Button>
            </div>
          </div>
          <div class="right_container">
            <p class="base_info">基本信息</p>
            <p class="base_cell">创建时间：{{infoData.create_time}} </p> 
            <p class="base_cell">当前使用成员：
              <span v-for="(item,index) in infoData.presentUseUserList" :key="index">{{item}}&nbsp;</span>
            </p>
            <p class="base_cell">备用成员：
              <span v-for="(item,index) in infoData.reserve_user_list" :key="index">{{item}}&nbsp;</span>
            </p>
            <p class="base_cell" v-if="infoData.type === 1">使用成员：
              <span v-for="(item,index) in infoData.cycle" :key="index">
                <span v-for="(item1,index1) in item.userItem" :key="index1">
                  {{item1.name}}
                </span>
                &nbsp;</span>
            </p>
            <p class="base_cell" v-if="infoData.type === 0">使用成员：
              <span v-for="(item,index) in infoData.user_list" :key="index">{{item.name}}&nbsp;</span>
            </p>
            <p class="base_cell">员工添加上限：{{infoData.add_upper_limit ? '开启' : '关闭'}}</p>
            <p class="base_cell">自动通过好友：{{infoData.skip_verify ? '开启' : '关闭'}}</p>
            <div class="dashed_line"></div>
            <p class="base_info">欢迎语设置</p>
            <div class="welcome_card acea-row" v-for="(item,index) in infoData.welcome_words.attachments" :key="index">
              <span class="pr-10">欢迎语{{index + 1}}: </span>
              <img v-if="item.msgtype == 'image'" :src="item.image.pic_url" alt="" >
              <div v-if="item.msgtype == 'miniprogram'">
                <span class="iconfont iconxiaochengxu"></span>
                <span>{{item.miniprogram.title}}</span>
              </div>
            </div>
          </div>
        </div>
    </Modal>
    <Modal
      v-model="clentStatus"
      title="客户列表"
      :footer-hide="true"
      width="600"
      >
      <Table :columns="clientColumn" :data="clientList.list" :loading="userLoading">
         <template slot-scope="{ row }" slot="avatar">
          <viewer class="acea-row row-center">
            <div class="tabBox_img">
              <img v-lazy="row.client.avatar" />
            </div>
          </viewer>
        </template>
        <template slot-scope="{ row }" slot="name">
          <span>{{row.client.name}}</span>
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
    <Modal
      v-model="branchStatus"
      title="移动分类"
      width="400"
      class-name="vertical-center-modal"
      @on-ok="branchCateConfirm"
      >
      <Form :label-width="75" label-position="right" :label-colon="true">
        <FormItem label="分类">
          <Select v-model="branchCateId" style="width:250px;" >
              <Option v-for="item in channelCateList" :value="item.id.toString()" :key="item.id">{{ item.name }}</Option>
          </Select>
        </FormItem>
      </Form>
    </Modal>
  </div>
</template>

<script>
import { mapState } from "vuex";
import {
  workChannelCate,
  workCateCreate,
  workCateEdit,
  workCateDel,
  workChannelCode,
  getWorkCodeClient,
  getWorkCodeInfo,
  workBranchCate
} from "@/api/work";
export default {
  name: "",
  data() {
    return {
      loading: false,
      formInline: {},
      channelCateList:[],
      current: 0,
      sortName: "",
      codeInfoStatus:false,
      clentStatus:false,
      branchStatus:false,
      columns1: [
        {
          type: "selection",
          width: 60,
        },
        {
          title: "二维码",
          slot: "qrcode_url",
          minWidth: 80,
        },
        {
          title: "二维码名称",
          key: "name",
          minWidth: 100,
        },
        {
          title: "分类",
          slot: "category",
          minWidth: 100,
        },
        {
          title: "添加好友",
          slot: "skip_verify",
          minWidth: 80,
        },
        {
          title: "类型",
          slot: "type",
          minWidth: 80,
        },
        {
          title: "客户数",
          key: "client_num",
          minWidth: 80,
        },
        {
          title: "创建时间",
          key: "create_time",
          minWidth: 110,
        },
        {
          title: "用户标签",
          slot: "label_name",
          minWidth: 130,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          width: 150,
        },
      ],
      tableData: [],
      clientList:{
        count:0,
        list:[]
      },
      infoData:{
        welcome_words:{
          attachments:[]
        },
      }, //详情弹窗内容赋值
      grid: {
        xl: 7,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableFrom: {
        type: "",
        name: "",
        cate_id:null,
        page: 1,
        limit: 15,
      },
      clientColumn:[
        {
          title: "ID",
          key: "client_id",
          minWidth: 60,
          align: "center",
        },
        {
          title: "头像",
          slot: "avatar",
          minWidth: 100,
          align: "center",
        },
        {
          title: "客户名称",
          slot: "name",
          minWidth: 100,
          align: "center",
        },
        {
          title: "加入时间",
          key: "create_time",
          minWidth: 120,
          align: "center",
        },
      ],
      branchCateId:'',
      branchRow:[],
      loading: false,
      userLoading:false,
      clientForm:{
        id:0,
        page: 1,
        limit: 15,
      },
      total: 0,
    };
  },
  computed: {
    ...mapState("admin/layout", ["isMobile"]),
    labelWidth() {
      return this.isMobile ? undefined : 80;
    },
    labelPosition() {
      return this.isMobile ? "top" : "left";
    }
  },
  created() {
    this.getWorkChannelCate();
    this.getChannelCode();
  },
  methods: {
    //编辑
    editData(row) {
      this.$router.push({ path: "/admin/work/createCode/" + row.id });
    },
    //移动
    move() {},
    // 操作
    changeMenu(row, name, index) {
      switch (name) {
        case "1":
          this.branchRow[0] = row;
          this.branchStatus = true;
          break;
        case "2":
          let delfromData = {
            title: '删除渠道码',
            num:index,
            url: `work/channel/code/${row.id}`,
            method: "DELETE",
            ids: "",
          };
          this.$modalSure(delfromData)
            .then((res) => {
              this.$Message.success(res.msg);
              if(this.tableData.list.length == 1){
                this.tableFrom.page = this.tableFrom.page -1;
                this.getChannelCode();
              }else{
                this.getChannelCode();
              }
            })
            .catch((res) => {
              this.$Message.error(res.msg);
            });
          break;
        case "3":
          this.clentStatus = true;
          this.clientForm.id = row.id; 
          this.getClient();
          break;
      }
    },
    codeInfo(row){
      getWorkCodeInfo(row.id).then(res=>{
        this.infoData = res.data;
      })
      this.codeInfoStatus = true;
    },
    //获取客户列表
    getClient(){
      this.userLoading = true;
      getWorkCodeClient(this.clientForm).then(res=>{
        this.$set(this,'clientList',res.data);
        this.userLoading = false;
      }).catch((res) => {
        this.userLoading = false;
        this.$Message.error(res.msg);
      });
    },
    //获取分类列表
    getWorkChannelCate(key){
      workChannelCate().then(res=>{
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
        }
        this.channelCateList = res.data;
      })
    },
    bindMenuItem(name,index) {
      this.tableFrom.cate_id = name.id;
      this.current = index;
      this.channelCateList.forEach((el) => {
        el.status = false;
      });
      this.getChannelCode();
    },
    // 显示标签小菜单
    showMenu(item) {
      this.channelCateList.forEach((el) => {
        if (el.id == item.id) {
          el.status = item.status ? false : true;
        } else {
          el.status = false;
        }
      });
    },
    //添加分类
    addChannelCate(){
      this.$modalForm(workCateCreate()).then(() => {
        this.getWorkChannelCate();
      });
    },
    //删除分类
    delChannelCate(item,name,index){
      workCateDel(item.id).then(res=>{
        this.getWorkChannelCate();
        this.$Message.success('删除成功');
      })
    },
    //编辑分类
    labelEdit(item){
      this.$modalForm(workCateEdit(item.id)).then(() =>{
        item.status = false;
        this.getWorkChannelCate(1)
      });
    },
    //获取渠道码列表
    getChannelCode(){
      this.loading = true;
      workChannelCode(this.tableFrom).then(res=>{
        this.tableData = res.data;
        this.loading = false;
      }).catch((res) => {
        this.loading = false;
        this.$Message.error(res.msg);
      });
    },
    //批量移动
    branchCate(){
      this.branchStatus = true;
    },
    branchCateConfirm(){
      let data = {
        cate_id:this.branchCateId,
        ids:this.branchRow.map(item=>{
          return item.id
        })
      };
      workBranchCate(data).then(res=>{
        this.$Message.success("移动成功");
        this.branchRow = [];
        this.getChannelCode();

      })
    },
    selectAll(row) {
      if (row.length) {
        this.branchRow = row;
      }
    },
    handleSelectRow(row){
      this.branchRow = row;
    },
    //搜索
    codeSearchs(){
      this.tableFrom.page = 1;
      this.getChannelCode();
    },
    //分页
    pageChange(index) {
      this.tableFrom.page = index;
      this.getChannelCode();
    },
    //分页
    clientChange(index){
      this.clientForm.page = index;
      this.getClient();
    },
    //下载活码
    downloadCodeImg(){
      let link = document.createElement('a')
      let url =  this.infoData.qrcode_url;  //codeIMG  要下载的路径
      // 这里是将url转成blob地址，
      fetch(url).then(res => res.blob()).then(blob => { // 将链接地址字符内容转变成blob地址
         link.href = URL.createObjectURL(blob)
         link.download = 'pic';
         document.body.appendChild(link)
         link.click()
     })
    },
    downPic(){
      for(let i in this.branchRow){
        let link = document.createElement('a');
        let url =  this.branchRow[i].qrcode_url;
         fetch(url).then(res => res.blob()).then(blob=>{
           link.href = URL.createObjectURL(blob)
           link.download = this.branchRow[i].name;
           document.body.appendChild(link)
           link.click()
         })
      }
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
.auto{
	width calc(100% - 240px)
}
.showOn {
  color: #2d8cf0;
  background: #eff6fe;
}

/deep/.ivu-alert-with-desc{
  margin-bottom: 18px;
}
/deep/ .ivu-menu-vertical.ivu-menu-light:after {
  display: none;
}
/deep/ .ivu-table-wrapper{
  min-height: 400px;
}
/deep/.ivu-alert{
	margin-bottom: 20px !important;
}
.left-wrapper {
  background: #fff;
  border-right: 1px solid #dcdee2;
}
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
.menu-item {
  position: relative;
  display: flex;
  justify-content: space-between;
  word-break: break-all;

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
    z-index: 999;
    position: absolute;
    right: -106px;
    top: -11px;
    width: auto;
    min-width: 121px;
  }
}
.mt-24 {
  margin-top: 24px;
}

.ml-20 {
  margin-left: 20px;
}

.mr-12 {
  margin-right: 12px;
}

.mb-15 {
  margin-bottom: 15px;
}
.pr-10{
  padding-right:10px;
}
.conditions {
  display: flex;
}
.left_container{
  width:240px;
  height:620px;
  border-right:1px solid #eee;
  .qrcode_box{
    width:156px;
    margin-top:36px;
    img{
      width:156px;
      height:156px;
    }
    .qrcode_name{
      font-size: 13px;
      font-family: PingFangSC-Regular, PingFang SC;
      font-weight: 400;
      color: #606266;
      text-align: center;
      margin:12px auto 20px;
    }
  }
}
.right_container{
  flex:1;
  padding:30px;
  box-sizing: border-box;
}
.base_info{
  height: 15px;
  font-size: 15px;
  font-family: PingFangSC-Medium, PingFang SC;
  font-weight: 500;
  color: #303133;
  line-height: 15px;
  padding-left:13px;
  position: relative;
  margin-bottom: 20px;
  &::after{
    content:"";
    position: absolute;
    top:0;
    left:0;
    width:3px;
    height:16px;
    background: #1890FF;
  }
}
.base_cell{
  height: 13px;
  font-size: 13px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #606266;
  line-height: 13px;
  margin-bottom: 16px;
}
.dashed_line{
  margin:25px 0 25px;
  border: 1px dashed #EEEEEE;
}
.welcome_card{
  padding:19px;
  box-sizing: border-box;
  background: #F9F9F9;
  border-radius: 4px;
  color: #606266;
  font-size: 13px;
  margin-bottom:14px;
  img{
    width: 60px;
    height:60px;
  }
}
.iconxiaochengxu{
  color:#1BBE6B;
  font-size:14px;
  display: inline-block;
  margin-right: 5px;
}
</style>
