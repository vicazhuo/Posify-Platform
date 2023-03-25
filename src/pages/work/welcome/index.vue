<template>
  <div>
     <Card :bordered="false" dis-hover class="ivu-mt">
        <Alert closable show-icon>
            <p>1、欢迎语又称新好友自动回复，此处可添加文字、图片、图文链接及小程序，客户来了不用担心冷场！</p>
            <p>2、每个企业成员均可以拥有不同的欢迎语。当通用的欢迎语和个人专属的欢迎语并存的情况下，优先自动回复个人专属的欢迎语。</p>
            <p>3、如果企业在企业微信后台为相关成员配置了可用的欢迎语，使用第三方系统配置欢迎语，则均不起效，推送的还是企业微信官方的。</p>
        </Alert>
     </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
        <div class="acea-row">
           <router-link :to="'/admin/work/addWelcome'">
              <Button type="primary">新建欢迎语</Button>
            </router-link>
            
        </div>
        <Table ref="selection" :columns="columns1" :data="tableData.list" :loading="loading" class="ivu-mt">
          <template slot-scope="{ row }" slot="type">
            <span>{{row.type ? '指定员工' : '通用'}}</span>
          </template>
            <template slot-scope="{ row, index }" slot="action">
                <a @click="editData(row,index)">编辑</a>
                <Divider type="vertical" />
                <a @click="delData(row,index)">删除</a>
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
  </div>
</template>

<script>
import { mapState } from "vuex"; 
import {getWelcomeList} from "@/api/work"
export default {
  name: "",
  data() {
    return {
      loading: false,
      formInline:{},
      columns1: [
        {
          title: "欢迎语内容",
          key: "content",
          minWidth: 80,
        },
        // {
        //   title: "使用成员",
        //   key: "userids",
        //   minWidth: 100,
        //   align: 'center'
        // },
        {
          title: "类型",
          slot: "type",
          minWidth: 80,
        },
        {
          title: "排序",
          key: "sort",
          minWidth: 80,
        },
        {
          title: "创建时间",
          key: "create_time",
          minWidth: 130,
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 130,
        },
      ],
      tableData: [],
      grid: {
        xl: 7,
        lg: 10,
        md: 12,
        sm: 24,
        xs: 24,
      },
      tableFrom: {
        // start_status: "",
        // status: "",
        // store_name: "",
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
    },
  },
  created() {
    this.getList();
  },
  methods: {
    //编辑
    editData(row,index){
      this.$router.push({ path: "/admin/work/addWelcome/" + row.id });
    },
    //删除
    delData(row,index){
      let delfromData = {
        title: '删除欢迎语',
        num:index,
        url: `/work/welcome/${row.id}`,
        method: "DELETE",
        ids: "",
      };
      this.$modalSure(delfromData)
        .then((res) => {
          this.$Message.success(res.msg);
          if(this.tableData.list.length == 1){
            this.tableFrom.page = this.tableFrom.page -1;
            this.getList();
          }else{
            this.getList();
          }
        })
        .catch((res) => {
          this.$Message.error(res.msg);
        });
    },
    getList(){
      this.loading = true;
      getWelcomeList(this.tableFrom).then(res=>{
        this.tableData = res.data;
        this.loading = false;
      }).catch(err=>{
        this.$Message.error(err.msg);
        this.loading = false;
      })
    },
    //分页
    pageChange(index){
      this.tableFrom.page = index;
      this.getList()
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

</style>
