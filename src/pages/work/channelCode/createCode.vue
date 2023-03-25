<template>
  <div>
    <div class="i-layout-page-header">
      <PageHeader class="product_tabs" hidden-breadcrumb>
        <div slot="title">
          <div style="float: left">
            <router-link :to="{ path: '/admin/work/channel_code' }">
              <div class="font-sm after-line">
                <span class="iconfont iconfanhui"></span>
                <span class="pl10">返回</span>
              </div>
            </router-link>
          </div>
          <span class="mr20 ml16">添加企业渠道码</span>
        </div>
      </PageHeader>
    </div>
    <Card :bordered="false" dis-hover class="ivu-mt min-ht">
      <div class="acea-row row-center-wrapper">
        <div class="step_container">
          <Steps :current="current">
            <Step title="基础设置"></Step>
            <Step title="企微成员设置"></Step>
            <Step title="欢迎语设置"></Step>
          </Steps>
        </div>
      </div>
      <div v-if="current == 0">
        <Form
          ref="formItem"
          :model="formItem"
          :label-width="110"
          :label-colon="true"
          :rules="ruleValidate"
        >
          <FormItem label="二维码名称" prop="name">
            <Input
              v-model="formItem.name"
              placeholder="请输入二维码名称"
              style="width: 400px"
            ></Input>
          </FormItem>
          <FormItem label="二维码分类" prop="cate_id">
            <Select v-model="formItem.cate_id" style="width: 400px">
              <Option
                :value="item.id.toString()"
                v-for="(item, index) in channelCateList"
                :key="index"
                >{{ item.name }}</Option
              >
            </Select>
          </FormItem>
          <FormItem label="客户标签" prop="label_id">
            <TreeSelect
              v-model="formItem.label_id"
              :data="labelList"
              v-width="400"
              multiple
            />
          </FormItem>
          <FormItem label="自动添加好友">
            <i-switch v-model="formItem.skip_verify" size="large"  :true-value="1" :false-value="0">
              <span slot="open">开启</span>
              <span slot="close">关闭</span>
            </i-switch>
            <div class="desc">
              开启时，客户添加时无需企业成员确认，自动成为好友
            </div>
          </FormItem>
        </Form>
      </div>
      <div v-if="current == 1">
        <Form :model="formItem" :label-width="110" :label-colon="true">
          <FormItem label="类型" prop="type">
            <RadioGroup v-model="formItem.type" @on-change="changeType">
              <Radio label="0">全天在线</Radio>
              <Radio label="1">自动上下线</Radio>
            </RadioGroup>
            <div class="desc">
              同一个二维码可选择多个客服成员进行接待，客户扫码后随机分配一名客服人员
            </div>
          </FormItem>
          <template v-if="formItem.type == '0'">
            <FormItem label="添加成员">
                <Button icon="md-add" @click="addUser('one')">添加成员</Button>
                <div style="margin-top:10px;">
                    <Tag closable @on-close="handleDel" :name="item.name" size="large" v-for="(item,index) in formItem.userList" :key="index">{{item.name}}</Tag>
                    <!-- <Tag closable @on-close="handleDel" size="large" v-for="(item,index) in formItem.userList" :key="index">{{item.name}}</Tag> -->
                </div>
            </FormItem>
          </template>
          <template v-if="formItem.type == '1'">
            <div v-for="(item,index) in formItem.cycle" :key="index">
              <FormItem>
                <div class="cycle acea-row row-between">
                  <span>周期{{index + 1}}</span>
                  <span class="danger_del" @click="cycleDel(item)">删除</span>
                </div>
                <div>
                  <div class="cycle_item">
                    <FormItem label="添加成员">
                        <Button icon="md-add" @click="addUser('three',index)">添加成员</Button>
                         <div class="cycle_tag_content">
                            <div class="ivu-tag ivu-tag-size-large ivu-tag-default ivu-tag-closable ivu-tag-checked"
                            v-for="(item1,index1) in item.userids" :key="index1">
                              <span class="ivu-tag-text">{{item1.name}}</span>
                              <i class="ivu-icon ivu-icon-ios-close" @click="cycleItemDel(item1.name,index)"></i>
                            </div>
                        </div>
                    </FormItem>
                  </div>
                  <div class="cycle_item">
                    <FormItem label="工作周期">
                      <Select v-model="item.wokr_time" multiple style="width:360px">
                        <Option v-for="item in weekList" :value="item.value" :key="item.value">{{ item.label }}</Option>
                    </Select>
                    </FormItem>
                  </div>
                  <div class="cycle_item">
                    <FormItem label="上班时间">
                      <TimePicker
                       v-model="item.workTime"
                        type="timerange"
                        format="HH:mm"
                        placement="bottom-end"
                        placeholder="请选择上班时间"
                        style="width: 360px"
                      ></TimePicker>
                    </FormItem>
                  </div>
                </div>
              </FormItem>
            </div>
          </template>
          <FormItem v-if="formItem.type == '1'">
            <Button type="primary" @click="pushCycle()">添加工作周期</Button>
          </FormItem>
          <FormItem label="员工添加上限" >
                <i-switch v-model="formItem.add_upper_limit" size="large" :true-value="1" :false-value="0">
                <span slot="open">开启</span>
                <span slot="close">关闭</span>
                </i-switch>
            </FormItem>
            <FormItem v-if="formItem.useridLimit.length && formItem.add_upper_limit">
               <Table border :columns="reserveUser" :data="formItem.useridLimit" width="254" size="small">
                    <template slot-scope="{ row }" slot="name">
                       <!-- <Input v-model="row.name" @on-change="setData($event, index, 'max')"></Input> -->
                       <span>{{row.name}}</span>
                   </template>
                   <template slot-scope="{ row,index }" slot="max">
                       <Input v-model="row.max" type="number" @on-change="setData($event, index, 'max')"></Input>
                   </template>
               </Table>
            </FormItem>
             <FormItem label="备用员工" required>
                <Button icon="md-add" @click="addUser('two')">添加成员</Button>
                 <div style="margin-top:10px;">
                    <Tag closable @on-close="handleClose(index)" :name="item.name" size="large" v-for="(item,index) in formItem.reserve_userid" :key="index">{{item.name}}</Tag>
                </div>
            </FormItem>
        </Form>
      </div>
      <div v-if="current == 2">
        <Form :model="formItem" :label-width="110" :label-colon="true">
          <FormItem label="开关" prop="type">
            <RadioGroup v-model="formItem.welcome_type">
              <Radio label="0">渠道欢迎语</Radio>
              <Radio label="1">默认欢迎语</Radio>
            </RadioGroup>
             <div class="desc">
              关闭后，客户扫该渠道活码，依然可对该客户自动打标签，但仅收到系统的【欢迎语】消息
            </div>
          </FormItem>
          <template v-if="formItem.welcome_type == '0'">
            <FormItem label="欢迎语内容">
            <Input
             v-model="formItem.welcome_words.text.content"
              type="textarea"
              :rows="4"
              style="width: 460px"
            />
          </FormItem>
          <FormItem>
            <Button type="primary" ghost @click="insertName()">插入客户名称</Button>
            <div class="desc">
              可以插入客户名称，在发送给客户之前系统会自动解析成客户的名称
            </div>
          </FormItem>
          <FormItem>
            <Poptip trigger="hover">
              <template slot="content">
                <div class="poptip_content">
                  <div @click="modalPicTap('image')">
                    <div class="add_img acea-row row-center-wrapper">
                      <span class="iconfont icontupian4"></span>
                    </div>
                    <span class="tip_tit">图片</span>
                  </div>
                  <div @click="addRoutine()">
                    <div class="add_routine acea-row row-center-wrapper">
                      <span class="iconfont iconxiaochengxu"></span>
                    </div>
                    <span class="tip_tit">小程序</span>
                  </div>
                </div>
              </template>
              <Button type="success">添加图片/小程序</Button>
            </Poptip>
          </FormItem>
          <FormItem>
             <div v-for="(item,index) in formItem.welcome_words.attachments" :key="index">
               <div class="ivu-tag ivu-tag-dot ivu-tag-size-default ivu-tag-closable ivu-tag-checked" >
                 <span v-if="item.msgtype == 'image'" class="iconfont icontupian4 tag_icon"></span>
                 <span v-if="item.msgtype == 'miniprogram'" class="iconfont iconxiaochengxu tag_icon"></span>
                 <span v-if="item.msgtype == 'image'" class="ivu-tag-text">{{item.image.pic_url}}</span>
                 <span v-if="item.msgtype == 'miniprogram'" class="ivu-tag-text">{{item.miniprogram.title}}</span>
                 <i v-if="item.msgtype == 'image'" class="ivu-icon ivu-icon-ios-close" @click="wordsDel(item.image.pic_url)"></i>
                 <i v-if="item.msgtype == 'miniprogram'" class="ivu-icon ivu-icon-ios-close" @click="wordsDel(item.miniprogram.title)"></i>
               </div>
             </div>
          </FormItem>
          </template>
          <FormItem label="状态">
            <i-switch v-model="formItem.status" size="large" :true-value="1" :false-value="0">
              <span slot="open">开启</span>
              <span slot="close">关闭</span>
            </i-switch>
          </FormItem>
        </Form>
      </div>
    </Card>
    <Card :bordered="false" dis-hover class="fixed-card" :style="{left: `${!menuCollapse?'200px':isMobile?'0':'80px'}`}">
      <div class="acea-row row-center-wrapper">
        <Button @click="back()" v-show="current > 0">上一步</Button>
        <Button
          type="primary"
          @click="next('formItem')"
          v-show="current < 2"
          class="step_btn"
          >下一步</Button
        >
        <Button
          type="primary"
          @click="submit()"
          v-show="current == 2"
          class="step_btn"
          >提交</Button
        >
      </div>
    </Card>
    <Modal
      v-model="modalPic"
      width="960px"
      scrollable
      footer-hide
      closable
      title="logo"
      :mask-closable="false"
      :z-index="9"
    >
      <uploadPictures
        :isChoice="isChoice"
        @getPic="getPic"
        :gridBtn="gridBtn"
        :gridPic="gridPic"
        v-if="modalPic"
      ></uploadPictures>
    </Modal>
    <Modal
      v-model="modalRoutine"
      title="添加小程序消息"
      @on-ok="routineConfirm"
      :z-index="1"
    >
      <Form :model="formItem" :label-width="110">
        <FormItem label="封面图:">
          <div class="picBox" @click="modalPicTap('routine')">
            <div class="pictrue" v-if="rontineObj.miniprogram.pic_url">
              <img v-lazy="rontineObj.miniprogram.pic_url"></div>
            <div class="upLoad" v-else>
              <div class="iconfont">+</div>
            </div>
          </div>
        </FormItem>
        <FormItem label="小程序消息标题:">
          <Input v-model="rontineObj.miniprogram.title"></Input>
        </FormItem>
        <FormItem label="小程序Appid:">
          <Input v-model="rontineObj.miniprogram.appid"></Input>
        </FormItem>
        <FormItem label="小程序功能页:">
          <Input v-model="rontineObj.miniprogram.page"></Input>
        </FormItem>
      </Form>
    </Modal>

      <department
        ref="department"
        :active-department="activeDepartment"
        :is-site="isSite"
        :userList="userList"
        :only-department="onlyDepartment"
        @changeMastart="changeMastart"
      />
  </div>
</template>

<script>
import { mapState } from "vuex";
import uploadPictures from "@/components/uploadPictures";
import department from "@/components/department/index.vue";
import { getNewFormBuildRuleApi } from "@/api/setting";
import { workLabel, workChannelCate,saveWorkCode,getWorkCodeInfo,workCodeUpdate } from "@/api/work";
import { log } from 'util';
export default {
  name: "",
  data() {
    return {
      current: 0,
      formItem: {
        userList:[],//添加成员
        name: "", //名称
        cate_id: 0, //分类ID
        label_id: [], //标签ID
        type: "0", //类型：0=全天在线；1=自动上下线
        userids: [], //全天在线成员
        skip_verify: 0, //自动加好友：0=关闭，1=开启
        add_upper_limit: 0, //员工添加上限0=关闭,1=开启
        useridLimit:[],//员工
        reserve_userid:[],//备用成员
        cycle:[
          {
            wokr_time:[],
            userids:[],
            workTime:""
          },
        ],
        welcome_type:"0",
        welcome_words:{
          text:{
            content: "",
          },
          attachments:[]
        },
        status: 1, //状态：1=开启，0=关闭
      },
      weekList:[
          {value: 1,label: '周一'},
          {value: 2,label: '周二'},
          {value: 3,label: '周三'},
          {value: 4,label: '周四'},
          {value: 5,label: '周五'},
          {value: 6,label: '周六'},
          {value: 0,label: '周日'},
      ],
      rontineObj:{
        msgtype: "miniprogram",
        miniprogram:{
          pic_url:"",
          pic_media_id:"",
          title:"",
          appid:"",
          page:""
        }
      },
      imageObj:{
        msgtype: "image",
        image:{
          media_id:"",
          pic_url:""
        }
      },
      picTit:"",
      ruleValidate: {
        name: [{ required: true, message: "名称不能为空", trigger: "blur" }],
        cate_id:[{ required: true, message: '请选择渠道码分类', trigger: 'change'}],
        type:[{ required: true, message: '请选择类型', trigger: 'change' }],
        label_id:[{required: true, message: '请选择客户标签', trigger: 'change', type: 'array'}]
      },
      //客户标签列表
      labelList: [],
      //渠道码分类列表
      //备用员工表格
      reserveUser:[
        {
          title: "员工名称",
          slot: "name",
          minWidth: 80,
          align: "center",
        },
        {
          title: "上限",
          slot: "max",
          minWidth: 100,
          align: "center",
        },
      ],
      cycleitemIndex:0,
      channelCateList: [],
      modalPic: false,
      modalRoutine: false,
      memberStatus: false,
      isChoice: "单选",
      gridBtn: {
        xl: 4,
        lg: 8,
        md: 8,
        sm: 8,
        xs: 8,
      },
      gridPic: {
        xl: 6,
        lg: 8,
        md: 12,
        sm: 12,
        xs: 12,
      },
      treeData: [],
      frame_id: 0,
      activeDepartment: {},
      isSite: true,
      onlyDepartment: false,
      openType:'',
      userList:[],
      userLimitAll:{},
      switchStatus:false //监听切换全天在线的状态
    };
  },
  components: { uploadPictures, department },
  computed: {
    ...mapState("admin/layout", ["isMobile","menuCollapse"]),
    labelWidth() {
      return this.isMobile ? undefined : 80;
    },
    labelPosition() {
      return this.isMobile ? "top" : "left";
    },

  },
  created() {
    if (this.$route.params.id !== "0" && this.$route.params.id) {
      this.getInfo();
    }
    this.getWorkLabel();
    this.getWorkChannelCate();
  },
  watch:{
    'formItem.type'(val,oldVal){
      if(val !== oldVal){
        this.formItem.userids = [];
        // this.formItem.useridLimit = []
      }
    },
    'formItem.useridLimit'(val,oldVal){
      this.userLimitAll[parseInt(this.formItem.type)] = this.formItem.useridLimit;

    }
  },
  methods: {
    changeType(value){
      this.formItem.useridLimit  = this.userLimitAll[parseInt(value)] || [];
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
        selected: false,
        checked: false,
        children: haveChildren ? org.children.map((i) => this.mapTree(i)) : [],
      };
    },
    //获取分类列表
    getWorkChannelCate() {
      workChannelCate().then((res) => {
        this.channelCateList = res.data;
      });
    },
    //表格Input
    setData(e, index, type){
      e.target.value = e.target.value.replace(/\D/g,'');
      this.formItem.useridLimit[index][type] = e.target.value
    },
    //确认人员
    changeMastart(arr,type){
        if(type == 'one'){
          this.formItem.useridLimit = arr.map(item=>{
            return {
              userid:item.userid,
              name:item.name,
              max:100
            }
          })

          this.formItem.userList = arr.map(item=>{
            return {
              userid:item.userid,
              name:item.name,
            }
          })
          // this.userLimitAll[parseInt(this.formItem.type)] = this.formItem.useridLimit;
        }else if(type == 'two'){
            this.formItem.reserve_userid = arr.map(item=>{
                return {
                  userid:item.userid,
                  name:item.name
                }
            })
        }else if(type == 'three'){
          for (let i = 0; i < this.formItem.cycle.length; i++) {
            if(i == this.cycleitemIndex){
              this.formItem.cycle[i].userids = arr.map(item=>{
               return {
                  userid:item.userid,
                  name:item.name
               }
              })
            }
          }
          for(let c = 0;c < arr.length;c++){
            let userItem = arr[c];
            this.formItem.useridLimit.push(Object.assign({max:100}, userItem));
          }
          let newArr = this.unique( this.formItem.useridLimit,'name')
          this.$set(this.formItem,'useridLimit',newArr);
          this.this.userLimitAll[parseInt(this.formItem.type)] = newArr;
        }
    },
    //tag标签删除成员
    handleDel(e,name){
        let index = this.formItem.userList.findIndex((j) => j.name == name);
        this.formItem.userList.splice(index, 1);
        if(this.formItem.add_upper_limit){
          let i = this.formItem.useridLimit.findIndex((item) => item.name == name);
          this.formItem.useridLimit.splice(i,1);
        }
    },
    //备用员工删除
    handleClose(index){
        this.formItem.reserve_userid.splice(index, 1);
    },
    //周期成员tag删除
    cycleItemDel(name,index){
      this.formItem.cycle.forEach((item,i)=>{
        if(index == i){
          //点击时传的下标与周期数组的下标匹配
          //只在对应的周期内操作
          let index = item.userids.indexOf(name);
          //周期内的员工列表与点击时传的员工姓名进行匹配，找到就返回下标
          item.userids.splice(index,1);
          //删除对应周期内所选员工
        }
      })
      //用传进来的员工姓名匹配员工添加上限表格数组，返回下标
      let i = this.formItem.useridLimit.findIndex((item) => item.name == name);
      //在员工添加上限的数组中删除所选的员工
      this.formItem.useridLimit.splice(i,1);
    },
    //欢迎语tag删除
    wordsDel(name){
      let index = this.formItem.welcome_words.attachments.indexOf(name);
      this.formItem.welcome_words.attachments.splice(index,1);
    },
    //追加周期成员
    pushCycle(){
        this.formItem.cycle.push({
            wokr_time:[],
            userids:"",
            workTime:""
        })
    },
    //删除周期成员
    cycleDel(e,name){
        let index = this.formItem.cycle.indexOf(name);
        this.formItem.cycle.splice(index, 1);
    },
    //插入客户名称
    insertName(){
      this.formItem.welcome_words.text.content = this.formItem.welcome_words.text.content.concat('##客户名称##')
    },
    next(name) {
      if(this.current == 0){
        this.$refs[name].validate((valid) => {
          if (valid) {
              this.current++;
          }
        })
      }else if(this.current == 1){
        if(this.formItem.type == "0" && !this.formItem.userList.length) return this.$Message.error("请添加成员");
        if(this.formItem.type == "1" && !this.formItem.cycle.length) return this.$Message.error("请添加成员");
        if(!this.formItem.reserve_userid.length) return this.$Message.error("备用成员不能为空");
        this.current++
      }
    },
    back() {
      this.current--;
    },
    //详情
    getInfo(){
      getWorkCodeInfo(this.$route.params.id).then(res=>{
        this.formItem = res.data;
        this.formItem.cate_id = this.formItem.cate_id.toString();
        this.formItem.type = this.formItem.type.toString();
        this.formItem.welcome_type = this.formItem.welcome_type.toString();
        let reserveArr = this.userParse(this.formItem.reserve_userid,this.formItem.reserve_user_list,'one');
        this.formItem.reserve_userid = reserveArr;
        this.formItem.useridLimit = res.data.useridLimit;
        this.userLimitAll[parseInt(this.formItem.type)] = res.data.useridLimit;
        this.formItem.userList = res.data.user_list;
        if(this.formItem.type == '0'){
          let personArr = this.userParse(this.formItem.presentUseUserIds,this.formItem.presentUseUserList,'one');
          this.formItem.userids = personArr;
          this.formItem.cycle[0] = {
            userids:[],
            wokr_time:[],
            workTime:[],
          };
        }else{
          this.formItem.cycle = this.formItem.cycle.map(item=>{
            return {
              channel_id:item.channel_id,
              wokr_time:item.wokr_time,
              workTime:[item.start_time,item.end_time],
              userids:item.userItem
            }
          })
          // let limitArr = [],newArr = [],userInfo;
          // this.formItem.cycle.forEach(item=>{
          //   item.userids.forEach(item1=>{
          //     limitArr.push(item1);
          //   })
          // })
          // this.formItem.useridLimit.forEach(item=>{
          //   limitArr.forEach(item1=>{
          //     if(item.userid == item1.userid){
          //       userInfo = item;
          //       userInfo.name = item1.name;
          //       newArr.push(userInfo);
          //     }
          //   })
          // })
        //  this.formItem.useridLimit = newArr;
        // this.$set(this.formItem,'useridLimit',newArr);
        }

      })
    },

    //提交
    submit(){
      if(this.formItem.welcome_type == "0" && this.formItem.welcome_words.text.content == "") return this.$Message.error('请插入客户名称')
      // if(this.formItem.welcome_type == "0" && !this.formItem.welcome_words.attachments.length) return this.$Message.error('请添加小程序或者图片')
      const formData = this.deepClone(this.formItem)
      if(formData.cycle.length){
        formData.cycle = formData.cycle.map(item=>{
          return {
            userids:item.userids.map(item1=>{
              return item1.userid
            }),
            wokr_time:item.wokr_time,
            start_time:item.workTime[0],
            end_time:item.workTime[1],
          }
        })
      }
      if(formData.useridLimit.length){
        formData.useridLimit =  formData.useridLimit.map(item=>{
          return {
            userid:item.userid,
            max:item.max
          }
        })
      }
      if(formData.reserve_userid.length){
        formData.reserve_userid = formData.reserve_userid.map(item=>{
          return item.userid
        })
      }
      if(formData.userList.length){
        formData.userids = formData.userList.map(item=>{
          return item.userid
        })
      }
      formData.type = Number(formData.type);
      formData.welcome_type = Number(formData.welcome_type);
      // console.log(formData);
      if (this.$route.params.id !== "0" && this.$route.params.id) {
        workCodeUpdate(this.$route.params.id,formData).then(res=>{
          this.$Message.success("修改成功");
          this.$router.push('/admin/work/channel_code')
        }).catch(err=>{
          this.$Message.error(err.msg)
          this.loading = false;
        })
      } else if (!this.$route.params.id){
        saveWorkCode(formData).then(res=>{
          this.$Message.success("渠道码新建成功");
          this.$router.push('/admin/work/channel_code')
        }).catch(err=>{
          this.$Message.error(err.msg)
        })
      }
    },
    //添加小程序
    addRoutine() {
      getNewFormBuildRuleApi('routine').then(res => {
          let data = res.data;
          this.rontineObj.miniprogram.pic_url = '';
          this.rontineObj.miniprogram.title = data.routine_name.value;
          this.rontineObj.miniprogram.appid = data.routine_appId.value;
          this.rontineObj.miniprogram.page = '/pages/index/index';
      })
      this.modalRoutine = true;
    },
    //添加图片
    modalPicTap(picTit){
      this.modalPic = true;
      this.picTit = picTit;
    },
    // 选中图片
    getPic(pc) {
      switch (this.picTit) {
        case "image":
          this.imageObj.image.pic_url = pc.att_dir;
          this.formItem.welcome_words.attachments.push(this.imageObj)
          break;
        case "routine":
          this.rontineObj.miniprogram.pic_url = pc.att_dir;
          break;
      }

      this.modalPic = false;
    },
    //小程序添加模态框确认操作
    routineConfirm() {
      const routine = this.deepClone(this.rontineObj);
      this.formItem.welcome_words.attachments.push(routine)
    },
    addUser(type,index) {
        this.$refs.department.memberStatus = true;
        switch (type) {
            case 'one':
              this.userList = this.formItem.userList;
                this.$refs.department.openType = 'one';
                break;
            case 'two':
              this.userList = this.formItem.reserve_userid;
                this.$refs.department.openType = 'two';
                break;
            case 'three':
                this.cycleitemIndex = index;
                if(this.formItem.cycle[index].userids.length){
                  this.userList = this.formItem.cycle[index].userids;
                }
                this.$refs.department.openType = 'three';
                break;
            default:
                break;
        }
    },
    //数组去重
    unique(arr,u_key) {
      let map = new Map()
      arr.forEach((item,index)=>{
        if (!map.has(item[u_key])){
          map.set(item[u_key],item)
        }
      })
      return [...map.values()]
    },
    //深克隆
    deepClone(obj) {
        let newObj = Array.isArray(obj) ? [] : {}
        if (obj && typeof obj === "object") {
            for (let key in obj) {
                if (obj.hasOwnProperty(key)) {
                    newObj[key] = (obj && typeof obj[key] === 'object') ? this.deepClone(obj[key]) : obj[key];
                }
            }
        }
        return newObj
    },
    userParse(arr1,arr2,type){
      let userArr = [],userObj = {};
      if(type == 'one'){
        userArr = arr1.map(item=>{
         return { userid:item}
        })
        arr2.forEach((item1,index1)=>{
          userArr[index1].name = item1;
        })
        return userArr
      }else{
        userArr = arr1.map(item=>{
         return {
           userid:item.userid,
           channel_id:item.channel_id,
           max:item.max
         }
        })
        arr2.forEach((item1,index1)=>{
          userArr[index1].name = item1;
        })
        return userArr
      }
    },
  },
};
</script>

<style scoped lang="stylus">
/deep/ .ivu-table-header thead tr th{
    padding:0;
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

.mt-24 {
  margin-top: 24px;
}
.mt-14{
  margin-top:14px;
}
.ml-40 {
  margin-left: 40px;
}

.mr-12 {
  margin-right: 12px;
}

.mb-30 {
  margin-bottom: 30px;
}

.step_container {
  width: 60%;
  margin: 30px 0 60px;
}

.conditions {
  display: flex;
}

.step_btn {
  margin: 0 10px 0;
}

.desc {
  color: #999;
  font-size: 12px;
  line-height: 17px;
  padding-top: 6px;
}

.cycle {
  width: 510px;
  height: 34px;
  line-height: 34px;
  padding: 0 15px 0;
  font-size: 13px;
  background: #F5F7FA;
}
.cycle_tag_content{
  margin-top:10px;
  margin-left:110px;
}
.danger_del {
  color: #F5222D;
  cursor: pointer;
}

.poptip_content {
  width: 138px;
  height: 94px;
  box-sizing: border-box;
  padding: 17px;
  display: flex;
  justify-content: space-between;
  cursor: pointer;
}

.min-ht {
  min-height: 621px;
}

.add_img {
  width: 40px;
  height: 40px;
  background: rgba(24, 144, 255, 0.1);
  border-radius: 2px;
}

.add_routine {
  width: 40px;
  height: 40px;
  background: rgba(27, 190, 107, 0.1);
  border-radius: 2px;
}

.icontupian4 {
  color: #1890FF;
  font-size: 20px;
}

.iconxiaochengxu {
  color: #1BBE6B;
  font-size: 20px;
}

.tip_tit {
  display: block;
  font-size: 12px;
  text-align: center;
  padding-top: 6px;
}

.cycle_item {
  margin-top: 26px;
}
.tag_icon{
  font-size:14px;
  display:inline-block;
  margin-right:8px;
}
 .fixed-card {
    position: fixed;
    right: 0;
    bottom: 0;
    left: 200px;
    z-index: 9;
    box-shadow: 0 -1px 2px rgb(240, 240, 240);

    /deep/ .ivu-card-body {
        padding: 15px 16px 14px;
    }

    .ivu-form-item {
        margin-bottom: 0;
    }

    /deep/ .ivu-form-item-content {
        margin-right: 124px;
        text-align: center;
    }
}
.picBox
		display: inline-block;
		cursor: pointer;
		.upLoad
			width: 58px;
			height: 58px;
			line-height: 58px;
			border: 1px dotted rgba(0, 0, 0, 0.1);
			border-radius: 4px;
			background: rgba(0, 0, 0, 0.02);
		.pictrue
			width: 60px;
			height: 60px;
			border: 1px dotted rgba(0, 0, 0, 0.1);
			margin-right: 10px;
			img
				width: 100%;
				height: 100%;
		.iconfont
			color: #CCCCCC;
			font-size 26px;
			text-align center
</style>
