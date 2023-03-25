<template>
    <div>
        <Form :model="formData" :label-width="76">
            <div class="section">
                <div class="section-hd">基本信息</div>
                <div class="section-bd">
                    <FormItem label="用户编号：">
                        <Input v-model="formData.uid" disabled></Input>
                    </FormItem>
                    <FormItem label="真实姓名：">
                        <Input v-model="formData.real_name" placeholder="请输入"></Input>
                    </FormItem>
                    <FormItem label="手机号码：">
                        <Input v-model="formData.phone" placeholder="请输入"></Input>
                    </FormItem>
                    <FormItem label="生日：">
                        <DatePicker :value="formData.birthday" @on-change="dateChange"></DatePicker>
                    </FormItem>
                    <FormItem label="性别：">
                        <Select v-model="formData.sex">
                            <Option :value="0">保密</Option>
                            <Option :value="1">男</Option>
                            <Option :value="2">女</Option>
                        </Select>
                    </FormItem>
                    <FormItem label="省市区：">
                        <Cascader v-model="addressSelect" :data="addresData" :load-data="loadData" @on-change="addchack"></Cascader>
                    </FormItem>
                    <FormItem label="身份证号：">
                        <Input v-model="formData.card_id" placeholder="请输入"></Input>
                    </FormItem>
                    <FormItem label="详细地址：">
                        <Input v-model="formData.addres" placeholder="请输入"></Input>
                    </FormItem>
                </div>
            </div>
            <div class="section">
                <div class="section-hd">密码设置</div>
                <div class="section-bd">
                    <FormItem label="登录密码：">
                        <Input v-model="formData.pwd" type="password" password placeholder="请输入"></Input>
                    </FormItem>
                    <FormItem label="确认密码：">
                        <Input v-model="formData.true_pwd" type="password" password placeholder="请输入"></Input>
                    </FormItem>
                </div>
            </div>
            <div class="section">
                <div class="section-hd">用户设置</div>
                <div class="section-bd">
                    <FormItem label="推广资格：">
                        <i-switch v-model="formData.spread_open" :true-value="1" :false-value="0" size="large">
                            <span slot="open">启用</span>
                            <span slot="close">禁用</span>
                        </i-switch>
                    </FormItem>
                    <FormItem label="用户状态：">
                        <i-switch v-model="formData.status" :true-value="1" :false-value="0" size="large">
                            <span slot="open">开启</span>
                            <span slot="close">锁定</span>
                        </i-switch>
                    </FormItem>
                    <FormItem label="用户标签：">
                        <div style="display: flex;">
		                        <div class="labelInput acea-row row-between-wrapper" @click="openLabel">
		                        	<div style="width: 90%;">
		                        		<div v-if="dataLabel.length">
		                        			<Tag closable v-for="(item,index) in dataLabel" @on-close="closeLabel(item)">{{item.label_name}}</Tag>
		                        		</div>
		                        		<span class="span" v-else>选择用户关联标签</span>
		                        	</div>
		                        	<div class="iconfont iconxiayi"></div>
		                        </div>
                            <Button type="text" @click="add(1)">添加标签</Button>
                        </div>
                    </FormItem>
                    <FormItem label="用户分组：">
                        <div style="display: flex;">
                            <Select v-model="formData.group_id" placeholder="请选择" :transfer="true">
                                <Option v-for="item in groupList" :key="item.id" :value="item.id">{{ item.group_name }}</Option>
                            </Select>
														<Button type="text" @click="add(2)">添加分组</Button>
                        </div>
                    </FormItem>
                    <FormItem label="用户等级：">
                        <Select v-model="formData.level">
                            <Option v-for="item in levelOptions" :key="item.id" :value="item.grade">{{ item.name }}</Option>
                        </Select>
                    </FormItem>
                    <FormItem label="推广人：">
                        <Input v-model="formData.spread_uid_nickname" @on-clear="clearSpread" clearable placeholder="请选择" icon="ios-arrow-down" @on-focus="editSpread"></Input>
                    </FormItem>
                </div>
            </div>
        </Form>
				<Modal
				  v-model="customerShow"
				  scrollable
				  title="请选择商城用户"
				  :closable="false"
				  width="50%"
				>
				  <customerInfo v-if="customerShow" @imageObject="imageObject"></customerInfo>
				</Modal>
				<!-- 用户标签 -->
				<Modal
				  v-model="labelShow"
				  scrollable
				  title="请选择用户标签"
				  :closable="false"
				  width="320"
				  :footer-hide="true"
					:mask-closable="false"
				>
				  <userLabel ref="userLabel" @activeData="activeData" @close="labelClose"></userLabel>
				</Modal>
    </div>
</template>

<script>
import { userLabelAddApi, groupAddApi, levelListApi, userGroupApi, userLabelApi, putUserApi } from '@/api/user';
import { cityApi } from '@/api/store';
import customerInfo from "@/components/customerInfo";
import userLabel from "@/components/labelList";

export default {
    name: 'userForm',
		components: {
		  customerInfo,
			userLabel
		},
    props: {
        psInfo: Object
    },
    data() {
        return {
					  labelShow:false,
					  customerShow: false,
            formData: {
                uid: this.psInfo.uid,
                real_name: this.psInfo.real_name,
                phone: this.psInfo.phone,
                birthday: this.psInfo.birthday,
                sex: this.psInfo.sex,
                card_id: this.psInfo.card_id,
                addres: this.psInfo.addres,
                pwd: '',
                true_pwd: '',
                spread_open: this.psInfo.spread_open,
                status: this.psInfo.status,
                group_id: 0,
                label_id: [],
                level: this.psInfo.level,
                provincials: '',
                province: 0,
                city: 0,
                area: 0,
                street: 0,
								spread_uid: 0,
								spread_uid_nickname:''
            },
						dataLabel: [],
            addressSelect: [],
            levelOptions: [],
            labelOptions: [],
            addresData: [],
						groupList: []
        };
    },
    watch: {
        psInfo (value) {
            this.formData.uid = value.uid;
            this.formData.real_name = value.real_name;
            this.formData.phone = value.phone;
            this.formData.birthday = value.birthday;
            this.formData.sex = value.sex;
            this.formData.card_id = value.card_id;
            this.formData.addres = value.addres;
            this.formData.spread_open = value.spread_open;
            this.formData.status = value.status;
            this.dataLabel = value.label_id;
            this.formData.level = value.level;
            this.formData.provincials = value.provincials;
            this.formData.province = value.province;
            this.formData.city = value.city;
            this.formData.area = value.area;
            this.formData.street = value.street;
						this.formData.spread_uid_nickname = value.spread_uid_nickname;
						this.formData.spread_uid = value.spread_uid;
            this.formData.group_id = value.group_id;
        }
    },
    created () {
        this.levelList();
        this.groupLists();
        this.labelList();
        this.cityInfo({ pid: 0 });
        if (this.psInfo.province) {
            this.addressSelect.push(this.psInfo.province);
        }
        if (this.psInfo.city) {
            this.addressSelect.push(this.psInfo.city);
        }
        if (this.psInfo.area) {
            this.addressSelect.push(this.psInfo.area);
        }
        if (this.psInfo.street) {
            this.addressSelect.push(this.psInfo.street);
        }
    },
    methods: {
			  clearSpread(){
					this.formData.spread_uid = 0;
					this.formData.spread_uid_nickname = '';
				},
				closeLabel(label){
					let index = this.dataLabel.indexOf(this.dataLabel.filter(d=>d.id == label.id)[0]);
					this.dataLabel.splice(index,1);
				},
				activeData(dataLabel){
					this.labelShow = false;
					this.dataLabel = dataLabel;
				},
				openLabel() {
					this.labelShow = true;
					this.$refs.userLabel.userLabel(JSON.parse(JSON.stringify(this.dataLabel)));
				},
				// 标签弹窗关闭
				labelClose() {
				  this.labelShow = false;
				},
				editSpread(){
					this.customerShow = true;
				},
				imageObject(e) {
				  this.customerShow = false;
				  this.formData.spread_uid = e.uid;
					this.formData.spread_uid_nickname = e.name
				},
        changeMenu (value) {
            this.$emit('change-menu', value);
        },
        // 添加标签、分组
        add (value) {
            switch (value) {
                case 1:
                    this.$modalForm(userLabelAddApi(0)).then(() => {});
                    break;
                case 2:
                    this.$modalForm(groupAddApi(0)).then(() => {
											this.groupLists();
										});
                    break;
            }
        },
        // 会员等级列表
        levelList() {
        let data = {
            page: 1,
            limit: "",
            title: "",
            is_show: 1,
        };
        levelListApi(data).then((res) => {
            this.levelOptions = res.data.list;
        });
        },
        groupLists() {
            let data = {
                page: 1,
                limit: "",
            };
            userGroupApi(data).then((res) => {
                this.groupList = res.data.list;
            });
            },
        labelList() {
            let data = {
                page: 1,
                limit: "",
            };
            userLabelApi(data)
                .then(async (res) => {
                let data = res.data;
                this.labelOptions = data.list;
                })
                .catch((res) => {
                this.loading = false;
                this.$Message.error(res.msg);
                });
            },
        // 省市区数据
        cityInfo(data){
            cityApi(data).then(res=>{
                this.addresData = res.data
            })
        },
        loadData(item, callback) {
            item.loading = true;
            cityApi({pid:item.value}).then(res=>{
                item.children = res.data;
                item.loading = false;
                callback();
            });
        },
        addchack(e,selectedData){
            e.forEach((i,index)=>{
                if(index==0){
                    this.formData.province = i
                }else if(index==1){
                    this.formData.city = i
                }else if(index==2){
                    this.formData.area = i
                }else {
                    this.formData.street = i
                }
            })
            this.formData.provincials = (selectedData.map(o => o.label)).join("/");
        },
        dateChange (value) {
            this.formData.birthday = value;
        },
        // 保存用户信息
        detailsPut () {
					  let activeIds = [];
					  this.dataLabel.forEach((item)=>{
					  	activeIds.push(item.id)
					  });
						this.formData.label_id = activeIds
						if (this.formData.phone && !/^1(3|4|5|7|8|9|6)\d{9}$/.test(this.formData.phone)) {
							return this.$Message.error('请填写正确的手机号');
						}
            putUserApi(this.formData).then(res => {
							  this.$Message.success('修改成功');
                this.$emit('change-menu', '99');
            }).catch(res => {
                this.$Message.error(res.msg);
            });
        }
    }
}
</script>

<style lang="less" scoped>
.section {
    padding: 25px 0 5px;
    border-top: 1px dashed #EEEEEE;

    &:first-child {
        border-top: 0;
    }

    &-hd {
        padding-left: 10px;
        border-left: 3px solid #1890FF;
        font-weight: 500;
        font-size: 14px;
        line-height: 16px;
        color: #303133;
    }

    &-bd {
        display: flex;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .ivu-form-item {
        flex: 0 0 50%;
        margin-bottom: 20px;

        &:nth-child(odd) {
            padding-right: 48px;
        }

        &:nth-child(even) {
            padding-left: 48px;
        }
    }

    .ivu-date-picker {
        width: 100%;
    }

    .ivu-btn-text {
        color: #1890FF;

        &:focus {
            box-shadow: none;
        }
    }
}
.labelInput{
	border: 1px solid #dcdee2;
	width: 100%;
	padding: 0 5px;
	border-radius: 5px;
	min-height: 30px;
	cursor: pointer;
	.span{
		 color: #c5c8ce;
	}
	.iconxiayi{
		font-size: 12px
	}
}
</style>