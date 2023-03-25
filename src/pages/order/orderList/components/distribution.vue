<template>
	<Modal v-model="modals" scrollable title="请选择门店" width="850" class="order_box" >
		<div class="table">
			<Form
			  ref="formValidate"
			  :model="formValidate"
			  :label-width="100"
			  @submit.native.prevent
			>
				<FormItem label="用户名称：">
					<Input
						search
						enter-button
						placeholder="请输入用户名称/电话/地址"
						v-model="formValidate.keywords"
						style="width: 300px;"
						@on-search="userSearchs"
					></Input>
				</FormItem>
			</Form>
			<Table :columns="columns" :data="orderList" ref="table" class="mt25"
			       :loading="loading" highlight-row
			       no-userFrom-text="暂无数据"
			       no-filtered-userFrom-text="暂无筛选结果">
				<template slot-scope="{ row, index }" slot="image">
				    <img :src="row.image" >
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
		<div slot="footer">
			
		</div>
	</Modal>
</template>

<script>
	import {storeListApi, storeShareApi} from '@/api/store'
	// import {
	// 	putRemarkData, putRechargeRemarkData, putVipRemarkData
	// } from '@/api/store';
	export default {
		name: 'distshow',
		data() {
			return {
				id: 0,
				currentid: 0,
				modals: false,
				loading: false,
				orderList: [],
				total: 0,
				formValidate: {
				  page: 1,
				  limit: 15,
				  keywords:'',
				  id:0
				},
				columns: [
					{
					  title: "选择",
					  // key: "chose",
					  width: 60,
					  align: "center",
					  render: (h, params) => {
					    let uid = params.row.id;
					    let flag = false;
						// this.currentid === uid
					    if (this.currentid === uid) {
					      flag = true;
					    } else {
					      flag = false;
					    }
					    let self = this;
					    return h("div", [
					      h("Radio", {
					        props: {
					          value: flag,
					        },
					        on: {
					          "on-change": () => {
								  self.currentid = uid
								  storeShareApi({oid:this.id,store_id:params.row.id}).then(res=>{
									  this.$Message.success(res.msg)
									  this.modals = false
									  self.currentid = 0
									  this.$parent.getList()
										this.$parent.closeDetail();
								  }).catch(err=>{
									  this.modals = false
									  this.$Message.error(err.msg)
									  self.currentid = 0
								  })
					          },
					        },
					      }),
					    ]);
					  },
					},
					{
						title: 'ID',
						key: 'id',
						minWidth: 50
					},
					{
						title: '门店图片',
						slot: 'image',
						minWidth: 80
					},
					{
						title: '门店名称',
						key: 'name',
						minWidth: 80
					},
					{
						title: '联系电话',
						key: 'phone',
						minWidth: 80
					},
					{
						title: '门店地址',
						key: 'address',
						ellipsis: true,  
						minWidth: 150
					},
					{
						title: '营业时间',
						key: 'day_time',
						minWidth: 120
					},
				    {
				        title: '营业状态',
				        key: 'status_name',
				        minWidth: 80
				    }]
					
			}
		},
		props: {},
		mounted() {
			// this.getList()
		},
		methods: {
			userSearchs(){
				this.formValidate.page = 1;
				this.getList(this.id);
			},
			getList(id){
				this.id = id
				this.formValidate.id = id;
				this.loading = true
				storeListApi(this.formValidate).then(res=>{
					this.orderList = res.data.list
					this.total = res.data.count
					this.loading = false
				})
			},
			cancel(name) {
				this.modals = false;
			},
			putRemark() {
				
			},
			//分页
			pageChange(status) {
			  this.formValidate.page = status;
			  this.getList(this.id)
			}
		}
	}
</script>

<style scoped lang="less">
	.table{padding: 0px 30px 15px 30px; img{width: 40px;height: 40px;}}
</style>
