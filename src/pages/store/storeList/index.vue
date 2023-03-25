<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt tablebox" :padding="16">
      <div class="new_tab">
        <Tabs @on-click="onClickTab">
          <TabPane label="全部" name="all" />
          <TabPane label="营业中" name="1" />
          <!-- <TabPane label="休息中" name="0"/> -->
          <TabPane label="已停业" name="-1" />
        </Tabs>
      </div>
	    <Button type="primary" @click="add">添加门店</Button>
      <div class="table">
        <Table
          :columns="columns"
          :data="orderList"
          ref="table"
          class="ivu-mt"
          :loading="loading"
          highlight-row
          no-userFrom-text="暂无数据"
          no-filtered-userFrom-text="暂无筛选结果"
        >
          <template slot-scope="{ row }" slot="image">
            <img :src="row.image" />
          </template>
          <template slot-scope="{ row, index }" slot="action">
            <a @click="gostore(row)">进入门店</a>
            <Divider type="vertical" />
            <a @click="reset(row)">重置</a>
            <Divider type="vertical" />
            <a @click="operation(row)">{{
              row.is_show == 0 ? "开业" : "停业"
            }}</a>
            <Divider type="vertical" />
            <a @click="edit(row)">编辑</a>
            <Divider type="vertical" />
            <a @click="delte(row, '删除该门店', index)">删除</a>
          </template>
        </Table>
        <div class="acea-row row-right page">
          <Page
            :total="total"
            :current="formValidate.page"
            show-elevator
            show-total
            @on-change="pageChange"
            :page-size="formValidate.limit"
          />
        </div>
      </div>
    </Card>
    <add-store ref="template"></add-store>
  </div>
</template>

<script>
import util from "@/libs/util";
import Setting from "@/setting";
import addStore from "../components/addStore";
import {
  storeListApi,
  storeLogin,
  storeSetShowApi,
  resetApi,
} from "@/api/store";
export default {
  name: "storeList",
  components: {
    addStore,
  },
  data() {
    return {
      BaseURL: Setting.apiBaseURL.replace(/adminapi/, "store/home/"),
      total: 0,
      loading: false,
      formValidate: {
        type: "all",
        page: 1,
        limit: 15,
      },
      columns: [
        {
          title: "ID",
          key: "id",
          width: 60,
        },
        {
          title: "门店图片",
          slot: "image",
          minWidth: 80,
        },
        {
          title: "门店名称",
          key: "name",
          minWidth: 80,
        },
        {
          title: "联系电话",
          key: "phone",
          minWidth: 90,
        },
        {
          title: "门店地址",
          key: "address",
          ellipsis: true,
          minWidth: 150,
        },
        {
          title: "营业时间",
          key: "day_time",
          minWidth: 120,
        },
        {
          title: "营业状态",
          key: "status_name",
          minWidth: 80,
        },
        {
          title: "操作",
          slot: "action",
          fixed: "right",
          minWidth: 240,
          align: "center",
        },
      ],
      orderList: [
        {
          id: "1",
          order_id: "12",
          pay_price: "12",
          status: 1,
          phone: "13000000000",
          address: "陕西省西安市莲湖区大兴西路啊餐厨",
        },
      ],
    };
  },
  mounted() {
    this.getList();
  },
  methods: {
    getList() {
      this.loading = true;
      storeListApi(this.formValidate).then((res) => {
        this.orderList = res.data.list;
        this.total = res.data.count;
        this.loading = false;
      });
    },
    reset(row) {
      this.$modalForm(resetApi(row.id)).then(() => this.getList());
    },
    add() {
      this.$refs.template.title = "添加门店";
      this.$refs.template.add = 1;
      this.$refs.template.isTemplate = true;
      this.$refs.template.cityInfo({ pid: 0 });
    },
    edit(row) {
      this.$refs.template.title = "编辑门店";
      this.$refs.template.isTemplate = true;
      this.$refs.template.getInfo(row.id);
    },
    getExpiresTime(expiresTime) {
      let nowTimeNum = Math.round(new Date() / 1000);
      let expiresTimeNum = expiresTime - nowTimeNum;
      return parseFloat(parseFloat(parseFloat(expiresTimeNum / 60) / 60) / 24);
    },
    // 进入门店
    gostore(item) {
      storeLogin(item.id)
        .then((res) => {
          let data = res.data;
          let expires = data.expires_time;
          util.cookies.setStore("token", data.token, {
            expires: expires,
          });
          util.cookies.setStore("uuid", data.user_info.id, {
            expires: expires,
          });
          util.cookies.setStore("expires_time", expires, {
            expires: expires,
          });
          let storage = window.localStorage;
          storage.setItem("menuListStore", JSON.stringify(data.menus));
          storage.setItem("uniqueAuthStore", JSON.stringify(data.unique_auth));
          let userInfoStore = {
            account: data.user_info.account,
            head_pic: data.user_info.avatar,
            logo: data.logo,
            logoSmall: data.logo_square,
            version: data.version,
          };
          storage.setItem("userInfoStore", JSON.stringify(userInfoStore));
          // menuListStore
          window.open(this.BaseURL);
        })
        .catch((err) => {
          this.$Message.error(err.msg);
        });
    },
    delte(row, tit, num) {
      let delfromData = {
        title: tit,
        num: num,
        url: `store/store/del/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          this.getList();
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    operation(row) {
      let a = 0; //修改营业状态的反值
      if (row.is_show == 0) {
        a = 1;
      }
      if (row.is_show == 1) {
        a = 0;
      }
      storeSetShowApi(row.id, a)
        .then((res) => {
          this.getList();
          this.$Message.success(res.msg);
        })
        .catch((err) => {
          this.$Message.error(res.msg);
        });
    },
    searchs() {},
    onClickTab(e) {
      this.formValidate.page = 1;
      this.formValidate.type = e;
      this.getList();
    },
    //分页
    pageChange(status) {
      this.formValidate.page = status;
      this.getList();
    },
  },
};
</script>

<style scoped lang="stylus">
/deep/.ivu-tabs-nav {
  height: 45px;
}
.tablebox {
  margin-top: 15px;
}
.btnbox {
  padding: 20px 0px 0px 30px;
  .btns {
    width: 99px;
    height: 32px;
    background: #1890ff;
    border-radius: 4px;
    text-align: center;
    line-height: 32px;
    color: #ffffff;
    cursor: pointer;
  }
}
.table {
  padding: 0;
  img {
    width: 40px;
    height: 40px;
  }
}
.search {
  width: 86px;
  height: 32px;
  background: #1890ff;
  border-radius: 4px;
  text-align: center;
  line-height: 32px;
  font-size: 13px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: #ffffff;
  cursor: pointer;
}
.reset {
  width: 86px;
  height: 32px;
  border-radius: 4px;
  border: 1px solid rgba(151, 151, 151, 0.36);
  text-align: center;
  line-height: 32px;
  font-size: 13px;
  font-family: PingFangSC-Regular, PingFang SC;
  font-weight: 400;
  color: rgba(0, 0, 0, 0.85);
  cursor: pointer;
}
.new_tab {
    >>>.ivu-tabs-nav .ivu-tabs-tab{
        padding:4px 16px 20px !important;
        font-weight: 500;
    }
  }
</style>
