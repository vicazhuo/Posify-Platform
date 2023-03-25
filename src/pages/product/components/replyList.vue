<template>
	<div style="width: 100%">
		<Modal 
		v-model="modals" 
		scrollable footer-hide closable 
		title="评论回复列表" :mask-closable="false" 
		width="900"
		vertical-center-modal>
			<Form
			  inline
			  ref="replyFrom"
			  :model="replyFrom"
			  :label-width="labelWidth"
			  :label-position="labelPosition"
			  @submit.native.prevent
			>
				<FormItem label="时间选择：">
			        <DatePicker
			          :editable="false"
			          @on-change="onchangeTime"
			          :value="timeVal"
			          format="yyyy/MM/dd"
			          type="daterange"
			          placement="bottom-end"
			          placeholder="自定义时间"
			          style="width: 250px;margin-right:14px;"
			        ></DatePicker>
					<Button type="primary" @click="getList(replyId)">查询</Button>
			    </FormItem>
			</Form>
			<Table :loading="loading" highlight-row no-userFrom-text="暂无数据" no-filtered-userFrom-text="暂无筛选结果"
				ref="selection" :columns="columns" :data="dataList">
				<template slot-scope="{ row }" slot="info">
				  <div class="imgPic acea-row row-middle">
				    <viewer>
				      <div class="tabBox_img"><img v-lazy="row.user.avatar" /></div>
				    </viewer>
				    <div class="info">{{ row.user.nickname }}</div>
				  </div>
				</template>
				<template slot-scope="{ row }" slot="merchantReply">
				  <div>{{row.children?row.children.content:''}}</div>
				</template>
				<template slot-scope="{ row, index }" slot="action">
				  <a @click="reply(row)">回复</a>
				  <Divider type="vertical" />
				  <a @click="del(row,'删除评论',index)">删除</a>
				</template>
			</Table>
			<div class="acea-row row-right page">
				<Page :total="total" show-elevator show-total :current="replyFrom.page" @on-change="pageChange"
					:page-size="replyFrom.limit" />
			</div>
		</Modal>
		<Modal v-model="replyModals" scrollable title="回复内容" closable>
		  <Form
		    ref="contents"
		    :model="contents"
		    :rules="ruleInline"
		    @submit.native.prevent
		  >
		    <FormItem prop="content">
		      <Input
		        v-model="contents.content"
		        type="textarea"
		        :rows="4"
		        placeholder="请输入回复内容"
		      />
		    </FormItem>
		  </Form>
		  <div slot="footer">
		    <Button type="primary" @click="oks">确定</Button>
		    <Button @click="cancels">取消</Button>
		  </div>
		</Modal>
	</div>
</template>
<script>
	import { mapState } from "vuex";
	import {
		productReplycomment,
		productReplySave
	} from "@/api/product"
	export default {
		name: 'userList',
		data() {
			return {
				contents: {
				  content: "",
				},
				ruleInline: {
				  content: [
				    { required: true, message: "请输入回复内容", trigger: "blur" },
				  ],
				},
				replyModals: false,
				modals: false,
				total: 0,
				replyFrom: {
					time: "",
					page: 1,
					limit: 15
				},
				loading: false,
				dataList: [],
				columns: [
					{
						title: "ID",
						key: "id",
						width: 80,
					},
					{
						title: '评论用户',
						slot: 'info',
						minWidth: 90
					},
					{
						title: "评论内容",
						key: "content",
						minWidth: 100,
					},
					{
						title: "后台回复",
						slot: "merchantReply",
						minWidth: 100,
					},
					{
					  title: "操作",
					  slot: "action",
					  minWidth: 50,
					}
				],
				rows: {},
				fromList: {
				  title: "选择时间",
				  custom: true,
				  fromTxt: [
				    { text: "全部", val: "" },
				    { text: "今天", val: "today" },
				    { text: "昨天", val: "yesterday" },
				    { text: "最近7天", val: "lately7" },
				    { text: "最近30天", val: "lately30" },
				    { text: "本月", val: "month" },
				    { text: "本年", val: "year" },
				  ],
				},
				timeVal: [],
				replyId: 0
			}
		},
		computed: {
		  ...mapState("admin/layout", ["isMobile"]),
		  labelWidth() {
		    return this.isMobile ? undefined : 75;
		  },
		  labelPosition() {
		    return this.isMobile ? "top" : "left";
		  },
		},
		created() {
		},
		methods: {
			oks() {
			  this.$refs["contents"].validate((valid) => {
			    if (valid) {
			      productReplySave(this.contents, this.replyId, this.rows.id)
			        .then(async (res) => {
			          this.$Message.success(res.msg);
			          this.replyModals = false;
			          this.$refs["contents"].resetFields();
			          this.getList(this.replyId);
			        })
			        .catch((res) => {
			          this.$Message.error(res.msg);
			        });
			    } else {
			      return false;
			    }
			  });
			},
			// 具体日期
			onchangeTime(e) {
			  this.timeVal = e;
			  this.replyFrom.time = this.timeVal[0] ? this.timeVal.join("-") : "";
			  this.replyFrom.page = 1;
			},
			// 选择时间
			selectChange(tab) {
			  this.replyFrom.time = tab;
			  this.timeVal = [];
			  this.replyFrom.page = 1;
			  this.getList(this.replyId);
			},
			cancels() {
			  this.replyModals = false;
			  this.$refs["contents"].resetFields();
			},
			reply(row){
				this.contents.content = row.children?row.children.content:'';
				this.rows = row;
				this.replyModals = true;
			},
			// 删除
			del (row, tit, num) {
			    let delfromData = {
			        title: tit,
			        num: num,
			        url: `product/reply/delete_comment/${row.id}`,
			        method: 'DELETE',
			        ids: ''
			    };
			    this.$modalSure(delfromData).then((res) => {
			        this.$Message.success(res.msg);
			        this.getList(this.replyId);
			    }).catch(err => {
			        this.$Message.error(err.msg);
			    });
			},
			//评论回复列表
			getList(id) {
				this.replyId = id;
				this.loading = true
				productReplycomment(this.replyFrom,id).then(res => {
					this.loading = false
					this.total = res.data.count;
					this.dataList = res.data.list;
				}).catch(err => {
					this.loading = false
					this.$Message.error(err.msg)
				})
			},
			pageChange(page){
			  this.replyFrom.page = page;
			  this.getList(this.replyId);
			}
		}
	}
</script>

<style lang="less" scoped>
	/deep/.ivu-table {
		height: 300px;
		overflow-x: hidden;
		overflow-y: auto;
	}

	/deep/.ivu-table-overflowX {
		overflow-x: hidden !important;
	}

	.tabBox_img {
		width: 36px;
		height: 36px;
		border-radius: 4px;
		cursor: pointer;
		margin-right: 10px;

		img {
			width: 100%;
			height: 100%;
		}
	}
</style>
