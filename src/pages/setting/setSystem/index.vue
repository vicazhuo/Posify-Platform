<template>
    <div class="article-manager">
        <div class="i-layout-page-header">
            <PageHeader class="product_tabs" :title="title" hidden-breadcrumb>
                <div v-if="currentTab" slot="content">
                    <Tabs v-model="currentTab" @on-click="changeTab">
                        <TabPane :icon="item.icon" :label="item.label" :name="item.value.toString()" v-for="(item,index) in headerList" :key="index"/>
                    </Tabs>
                </div>
            </PageHeader>
        </div>
        <Card :bordered="false" dis-hover class="ivu-mt fromBox">
            <Tabs type="card" v-model="childrenId" v-if="headerChildrenList.length" @on-click="changeChildrenTab">
                <TabPane :label="item.label" :name="item.id.toString()" v-for="(item,index) in headerChildrenList" :key="index"></TabPane>
            </Tabs>
            <form-create :option="option"  :rule="rules" @on-submit="onSubmit" v-if="rules.length!==0"></form-create>
            <Spin size="large" fix v-if="spinShow"></Spin>
        </Card>
<!--        <from-submit :validate="ruleValidate" :title="title" :rules="componentsModel">-->

<!--        </from-submit>-->
    </div>
</template>

<script>
    import formCreate from '@form-create/iview'
    import { headerListApi, dataFromApi } from '@/api/setting';
    import request from '@/plugins/request';
    import fromSubmit from '@/components/fromSubmit/fromSubmit.vue';
    import util from '@/libs/util';

    export default {
        name: 'setting_setSystem',
        components: { formCreate: formCreate.$form(),fromSubmit:fromSubmit },
        data () {
            return {
                // ruleValidate: {
                //     test_appid_selete: [
                //         { type:'number',required: true, message: '您的姓名不能为空', trigger: 'change' }
                //     ],
                //     appid: [
                //         { required: true, message: '请输入您的原始密码', trigger: 'blur' }
                //     ]
                // },
                // componentsModel:[
                //     {
                //         title:'站点地址1',
                //         value:0.1,
                //         name:'inputNumber',
                //         field:'site_url_s',
                //         on:{
                //             change(item){
                //                 console.log(item)
                //             }
                //         }
                //     },
                //     {
                //         title:'选择图片',
                //         value:'',
                //         name:'uploadFrame',
                //         field:'image_url_test',
                //         upload: {
                //             url:'/admin/widget.images/index.html',
                //             field:'att_dir',
                //             maxNum:5
                //         },
                //         on:{
                //             change(item){
                //                 console.log(item)
                //             }
                //         }
                //     },
                //     {
                //         title:'上传图片',
                //         value:'',
                //         name:'uploadImage',
                //         field:'image_url',
                //         upload:{
                //             url:'/file/upload',
                //             size:2048,
                //             format:['jpg','jpeg','png','gif'],
                //             headers:{'Authori-zation':'Bearer ' + util.cookies.get('token')},
                //             maxNum:5
                //         },
                //         on:{
                //             change(item){
                //                 console.log(item)
                //             }
                //         }
                //     },
                //     {
                //         title:'测试',
                //         value:1,
                //         name:'switch',
                //         field:'test_appid_switch',
                //         options:[
                //             {trueValue:0,label:'关闭'},
                //             {falseValue:1,label:'打开'},
                //         ],
                //         on:{
                //             change(item){
                //                 console.log(item)
                //             }
                //         },
                //         //关闭打开联动组件
                //         control:[
                //             {
                //                 value:1,
                //                 componentsModel:[
                //                     {
                //                         title:'测试22',
                //                         value:'',
                //                         name:'select',
                //                         field:'test_appid_selete_ssss',
                //                         options:[
                //                             {value:0,label:'关闭'},
                //                             {value:1,label:'打开'},
                //                         ],
                //                         on:{
                //                             change(item){
                //                                 console.log(item)
                //                             }
                //                         }
                //                     },
                //                 ],
                //             },
                //             {
                //                 value:0,
                //                 componentsModel:[
                //
                //                 ],
                //             },
                //         ],
                //     },
                //     {
                //         title:'测试',
                //         value:'1',
                //         name:'select',
                //         field:'test_appid_selete',
                //         options:[
                //             {value:0,label:'关闭'},
                //             {value:1,label:'打开'},
                //         ],
                //         on:{
                //             change(item){
                //                 console.log(item)
                //             }
                //         }
                //     },
                //     {
                //         title:'测试',
                //         value:'1',
                //         name:'radio',
                //         field:'test_appid',
                //         options:[
                //             {value:0,label:'关闭'},
                //             {value:1,label:'打开'},
                //         ],
                //         on:{
                //             change(item){
                //                 console.log(item)
                //             }
                //         }
                //     },
                //     {
                //         title:'微信配置',
                //         name:'card',
                //         componentsModel:[
                //             {
                //                 title:'APPID',
                //                 value:'',
                //                 name:'input',
                //                 field:'appid',
                //                 on:{
                //                     change(item){
                //                         console.log(item)
                //                     }
                //                 }
                //             },
                //             {
                //                 title:'APPstoer',
                //                 value:'',
                //                 name:'input',
                //                 field:'app_stoer',
                //                 on:{
                //                     change(item){
                //                         console.log(item)
                //                     }
                //                 }
                //             },
                //         ],
                //     },
                //     {
                //         title:'网站设置',
                //         name:'card',
                //         componentsModel:[
                //             {
                //                 title:'站点地址',
                //                 value:'https://www.pro.net',
                //                 name:'input',
                //                 field:'site_title',
                //                 on:{
                //                     change(item){
                //                         console.log(item)
                //                     }
                //                 }
                //             },
                //             {
                //                 title:'站点标题',
                //                 value:'CRMEB',
                //                 name:'input',
                //                 field:'site_URL',
                //                 on:{
                //                     change(item){
                //                         console.log(item)
                //                     }
                //                 }
                //             },
                //         ]
                //     }
                // ],
                rules: [],
                option: {
                    form: {
                        labelWidth: 185
                    },
                    submitBtn: {
                        col: {
                            span: 3,
                            push: 3
                        }
                    },
                    global: {
                        upload: {
                            props: {
                                onSuccess (res, file) {
                                    if (res.status === 200) {
                                        file.url = res.data.src;
                                    } else {
                                        this.$Message.error(res.msg);
                                    }
                                }
                            }
                        },
                        frame: {
                            props: {
                                closeBtn: false,
                                okBtn: false
                            }
                        }
                    }
                },
                spinShow: false,
                FromData: null,
                currentTab: '',
                headerList: [],
                headerChildrenList: [],
                childrenId: '',
                title: ''
            }
        },
        created () {
            this.getAllData();
        },
        watch: {
            $route (to, from) {
                this.headerChildrenList = [];
                this.getAllData();
            },
            childrenId () {
                this.getFrom();
            }
        },
        methods: {
            childrenList () {
                let that = this;
                that.headerList.forEach(function (item) {
                    if (item.value.toString() === that.currentTab) {
                        if (item.children === undefined) {
                            that.childrenId = item.id;
                            that.headerChildrenList = [];
                        } else {
                            that.headerChildrenList = item.children
                            that.childrenId = item.children.length ? item.children[0].id.toString() : ''
                        }
                        // that.headerChildrenList = item.children;
                        // that.childrenId = item.children.length ? item.children[0].id.toString() : '';
                    }
                });
            },
            // 头部tab
            getHeader () {
                this.spinShow = true;
                return new Promise((resolve, reject) => {
                    let tab_id = this.$route.params.tab_id;
                    let data = {
                        type: this.$route.params.type ? this.$route.params.type : 0,
                        pid: tab_id || 0
                    };
                    headerListApi(data).then(async res => {
                        let config = res.data.config_tab;
                        this.headerList = config;
                        this.currentTab = config[0].value.toString();
                        this.childrenList();
                        resolve(this.currentTab);
                        this.spinShow = false;
                    }).catch(res => {
                        this.spinShow = false;
                        this.$Message.error(res.msg);
                    })
                });
            },
            // 表单
            getFrom () {
                this.spinShow = true;
                return new Promise((resolve, reject) => {
                    let ids = '';
                    if (this.$route.params.type === '3') {
                        ids = this.$route.params.tab_id
                    } else {
                        if (this.childrenId) {
                            ids = this.childrenId;
                        } else {
                            ids = this.currentTab;
                        }
                    }
                    let data = {
                        tab_id: Number(ids)
                    };
                    let logistics = 'freight/config/edit_basics', agent = 'agent/config/edit_basics',
                        integral = 'marketing/integral_config/edit_basics', sms = 'serve/sms_config/edit_basics',
                        config = 'setting/config/edit_basics';
                    let url = this.$route.name === 'setting_logistics' ? logistics : this.$route.name === 'setting_distributionSet'
                        ? agent : this.$route.name === 'setting_message' ? sms : this.$route.name === 'setting_setSystem' ? config : integral;
                    dataFromApi(data, url).then(async res => {
                        this.spinShow = false;
                        if (res.data.status === false) {
                            return this.$authLapse(res.data);
                        }
                        this.FromData = res.data;
                        this.rules = res.data.rules;
                        this.title = res.data.title;
                    }).catch(res => {
                        this.spinShow = false;
                        this.$Message.error(res.msg);
                    })
                });
            },
            async getAllData () {
                if (this.$route.params.type !== '3') {
                    await this.getHeader();
                } else {
                    this.headerList = [];
                    this.getFrom();
                }
            },
            // 选择
            changeTab () {
                this.childrenList();
            },
            // 二级选择
            changeChildrenTab (name) {
                this.childrenId = name;
            },
            // 提交表单 group
            onSubmit (formData) {
                request({
                    url: this.FromData.action,
                    method: this.FromData.method,
                    data: formData
                }).then(res => {
                    this.$store.dispatch('admin/account/setPageTitle')
                    this.$Message.success(res.msg);
                }).catch(res => {
                    this.$Message.error(res.msg);
                });
            }
        }
    }
</script>

<style scoped lang="stylus">
.ivu-tabs{
    // margin-bottom: 18px;
  }
.fromBox
    min-height 600px;
    // /*>>> .ivu-form-item-label*/
    //    /*width 155px !important*/
    // /*>>> .ivu-form-item-content*/
    //    /*margin-left 155px !important*/
</style>
