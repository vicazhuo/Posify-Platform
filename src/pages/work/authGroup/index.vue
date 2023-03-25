<template>
  <div>
    <Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
      <div class="new_card_pd">
        <Alert closable>
            <p>自动拉群，流程如下：</p>
            <p>1、在本系统选择多个群聊，添加完成后会生成一个群聊二维码，扫码群二维码会随机添加选择的群聊中的一个。</p>
            <p>2、当第一个群达到人数上限（最多500人）后，客户扫群二维码后，会自动创建第二个群。第二个群的名称将使用拉群中的群昵称+群序号生成新的群，群主和群管理员继承上个群。</p>
        </Alert>
        <Form :label-width="96" inline >
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
            <FormItem label="二维码名称">
                <Input v-model="tableFrom.name" placeholder="请输入" style="width:250px;margin-right:14px;" />
                 <Button type="primary" @click="search()">查询</Button>
            </FormItem>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="ivu-mt">
        <div class="acea-row">
           <router-link :to="'/admin/work/addAuthGroup'">
              <Button type="primary">新建拉群</Button>
            </router-link>
        </div>
        <Table ref="selection" :columns="columns1" :data="tableData.list" :loading="loading" class="ivu-mt">
          <template slot-scope="{ row }" slot="qr_code">
            <viewer class="acea-row row-center">
              <div class="tabBox_img">
                <img v-lazy="row.qr_code" />
              </div>
            </viewer>
          </template>
          <template slot-scope="{ row }" slot="auth_group_chat">
            <span>{{row.auth_group_chat ? '自动创建' : '手动创建'}}</span>
          </template>
          <template slot-scope="{ row }" slot="label_list">
             <Tag size="medium" v-for="item in row.label_list" :key="item.label_id">{{item.name}}</Tag>
          </template>
          <template slot-scope="{ row }" slot="chat_list">
            <span v-for="item in row.chat_list" :key="item.chat_id">{{item.name}}</span>
          </template>
          <template slot-scope="{ row, index }" slot="action">
              <a @click="editData(row, index)">编辑</a>
              <Divider type="vertical" />
              <a @click="downItem(row,index)">下载</a>
              <Divider type="vertical" />
              <a @click="delItem(row,index)">删除</a>
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
import {getGroupChatList} from "@/api/work";
import timeOptions from "@/utils/timeOptions";
export default {
  name: "",
  data() {
    return {
      loading: false,
      formInline:{},
      options: timeOptions,
      columns1: [
        {
          title: "二维码",
          slot: "qr_code",
          minWidth: 80,
          align: 'center'
        },
        {
          title: "二维码名称",
          key: "name",
          minWidth: 100,
          align: 'center'
        },
        {
          title: "群名称",
          key: "group_name",
          minWidth: 100,
          align: 'center'
        },
        {
          title: "群聊",
          slot: "chat_list",
          minWidth: 100,
          align: 'center'
        },
        {
          title: "自动创建",
          slot: "auth_group_chat",
          minWidth: 100,
          align: 'center'
        },
        {
          title: "标签",
          slot: "label_list",
          minWidth: 100,
          align: 'center'
        },
        {
          title: "创建时间",
          key: "create_time",
          minWidth: 130,
          align: 'center'
        },
        {
          title: "操作",
          slot: "action",
          // fixed: "right",
          minWidth: 130,
          align: 'center'
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
      timeVal:[],
      tableFrom: {
        name:"",
        create_time:"",
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
    getList(){
      this.loading = true;
      getGroupChatList(this.tableFrom).then(res=>{
        this.tableData = res.data;
        this.loading = false;
      }).catch(err=>{
        this.$Message.error(err.msg);
        this.loading = false;
      })
    },
    search(){
      this.tableFrom.page = 1;
      this.getList();
    },
    onchangeTime(e){
      this.timeVal = e;
      this.tableFrom.create_time = this.timeVal.join("-");
      this.tableFrom.page = 1;
      this.getList();
    },
    downItem(row,index){
      let link = document.createElement('a')
      let url =  row.qr_code;  //codeIMG  要下载的路径
      // 这里是将url转成blob地址，
      fetch(url).then(res => res.blob()).then(blob => { 
        // 将链接地址字符内容转变成blob地址
        link.href = URL.createObjectURL(blob)
        link.download = row.name  
         document.body.appendChild(link)
         link.click()  
     })
    },
    editData(row,index){
      this.$router.push({ path: "/admin/work/addAuthGroup/" + row.id });
    },
    delItem(row,index){
      let delfromData = {
        title: '删除该自动拉群',
        num:index,
        url: `work/group_chat_auth/${row.id}`,
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
    pageChange(index){
      this.tableFrom.page = index;
      this.getList();
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
