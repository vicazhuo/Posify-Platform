<template>
	<div>
		<Card :bordered="false" dis-hover class="ivu-mt" :padding="0">
			<div class="new_card_pd">
				<Form 
				ref="formValidate" 
				:label-width="96" 
				inline 
				@submit.native.prevent>
					<FormItem label="发送时间:">
						<DatePicker 
						:editable="false" 
						@on-change="onchangeTime" 
						:value="timeVal"  
						format="yyyy/MM/dd HH:mm"
						type="datetimerange" 
						placement="bottom-start" 
						placeholder="自定义时间" 
						style="width: 250px;"
						:options="options"></DatePicker>
					</FormItem>
					<FormItem label="群发类型:">
						<Select v-model="tableFrom.client_type" style="width: 250px;" clearable >
							<Option value="0">全部客户</Option>
							<Option value="1">筛选客户</Option>
						</Select>
					</FormItem>
					<FormItem label="名称:">
						<Input v-model="tableFrom.name" style="width: 250px;margin-right:14px;" placeholder="请输入名称"/>
						<Button type="primary" @click="search()">查询</Button>
					</FormItem>
				</Form>
				
			</div>
		</Card>
		<Card :bordered="false" dis-hover class="ivu-mt">
			<router-link :to="'/admin/work/client/add_group'">
			  	<Button type="primary" class="mr-12">新建群发</Button>
			</router-link>
			<Table ref="selection" :columns="columns1" :data="tableData.list" :loading="loading" class="ivu-mt">
				<!--  -->
				<template slot-scope="{ row }" slot="template_type">
					<Tag color="cyan" size="medium" v-if="row.template_type">定时发送</Tag>
					<Tag color="blue" size="medium" v-else>立即发送</Tag>
				</template>
				<template slot-scope="{ row }" slot="send_type">
					<Tag color="green" size="medium" v-if="row.send_type == 1">已发送</Tag>
					<Tag color="red" size="medium" v-if="row.send_type == 0">未发送</Tag>
				<Tooltip max-width="200" placement="bottom">
					<Tag color="orange" size="medium" v-if="row.send_type == -1">未发送成功</Tag>
					<p slot="content">{{row.fail_message}}</p>
				</Tooltip>
				</template>
				<template slot-scope="{ row, index }" slot="action">
					<a @click="sendMsg(row, index)" :disabled="row.send_type == 1">提醒发送</a>
					<Divider type="vertical" />
					<a @click="detailsItem(row,index)">详情</a>
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
	import timeOptions from "@/utils//timeOptions";
	import {getGroupTemplateList,workGroupTemplateSendMsg} from "@/api/work";
	export default {
		data() {
			return {
				options: timeOptions,
				timeVal: [],
				tableFrom: {
					name:"",
					time:"",
					type:0,
					client_type:"",
					page: 1,
					limit: 15,
				},
				total: 0,
				tableList: [],
				loading: false,
				tableData: [],
				columns1: [
                    {
                        title: '群发名称',
                        key: 'name',
                        minWidth: 150
                    },
                    {
                        title: '已发送成员',
                        key: 'user_count',
                        minWidth: 120
                    },
                    {
                        title: '送达客户',
                        key: 'external_user_count',
                        minWidth: 120
                    },
                    {
                        title: '未发送成员',
                        key: 'unuser_count',
                        minWidth: 120
                    },
                    {
                        title: '未送达客户',
                        key: 'external_unuser_count',
                        minWidth: 120
                    },
					{
                        title: '是否发送',
                        slot: 'send_type',
                        minWidth: 120
                    },
					{
                        title: '群发类型',
                        slot: 'template_type',
                        minWidth: 120
                    },
					{
                        title: '发送时间',
                        key: 'update_time',
                        minWidth: 150
                    },
					{
                        title: '创建时间',
                        key: 'create_time',
                        minWidth: 150
                    },
                    {
                        title: '操作',
                        slot: 'action',
                        fixed: 'right',
                        minWidth: 170
                    }
                ],
				
			}
		},
		computed: {
		  ...mapState("admin/layout", ["isMobile"]),
		  labelWidth() {
		    return this.isMobile ? undefined : 96;
		  },
		  labelPosition() {
		    return this.isMobile ? "top" : "right";
		  }
		},
		created() {
			this.getList();
		},
		methods: {
			getList(){
				this.loading = true;
				getGroupTemplateList(this.tableFrom).then(res=>{
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
			// 具体日期
            onchangeTime (e) {
                this.timeVal = e;
				this.tableFrom.time = this.timeVal.join("-");
            },
			pageChange(index){
				this.tableFrom.page = index;
				this.getList();
			},
			// 删除
			delItem(row,index){
				let delfromData = {
					title: '删除该客户群发',
					num:index,
					url: `work/group_template/${row.id}`,
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
			detailsItem(row,index){
				this.$router.push("/admin/work/client/group_info/" + row.id);
			},
			sendMsg(row,index){
				workGroupTemplateSendMsg({
					userid:"",
					time:row.update_time,
					id:row.id
				}).then(res=>{
					this.$Message.success(res.msg)
				}).catch(err=>{
					this.$Message.error(err.msg);
				})
			}
		}
	}
</script>
<style>
	
</style>