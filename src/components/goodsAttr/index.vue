<template>
	<div class="goodList">
		<Form ref="formValidate" :model="formValidate" :label-width="labelWidth" :label-position="labelPosition"
			class="tabform">
			<Row type="flex" :gutter="24">
				<Col v-bind="grid">
				<FormItem label="商品分类：" label-for="pid">
					<Cascader :data="treeSelect" placeholder="请选择商品分类" change-on-select filterable v-width="'200px'"
						@on-change="treeSearchs"></Cascader>
				</FormItem>
				</Col>
				<Col v-bind="grid">
				<FormItem label="商品标签：" label-for="pid">
					<Select v-model="formValidate.store_label_id" style="width: 200px" clearable
						@on-change="userSearchs">
						<Option v-for="item in labelSelect" :value="item.id" :key="item.id">{{ item.label_name }}
						</Option>
					</Select>
				</FormItem>
				</Col>
				<Col v-bind="grid">
				<FormItem label="商品搜索：" label-for="store_name">
					<Input search enter-button placeholder="请输入商品名称,关键字,编号" v-model="formValidate.store_name"
						style="width: 240px" @on-search="userSearchs" />
				</FormItem>
				</Col>
			</Row>
		</Form>

		<div class="vxeTable">
			<vxe-table border="inner" ref="xTree" :column-config="{resizable: true}" row-id="id"
				:tree-config="{children: 'attrValue',reserve:true}" :data="tableList" max-height="400" :checkbox-config="{reserve: true}">
				<vxe-column type="checkbox" title="多选" width="90" tree-node></vxe-column>
				<vxe-column field="id" title="商品ID" width="80"></vxe-column>
				<vxe-column field="image" title="图片" min-width="90">
					<template v-slot="{ row }">
						<viewer>
							<div class="tabBox_img">
								<img v-lazy="row.image" />
							</div>
						</viewer>
					</template>
				</vxe-column>
				<vxe-column field="store_name" title="商品名称" min-width="200">
					<template v-slot="{ row }">
						<Tooltip max-width="500" placement="bottom">
							<span class="line2">{{row.store_name}}</span>
							<p slot="content">{{row.store_name}}</p>
						</Tooltip>
					</template>
				</vxe-column>
				<vxe-column field="product_type" title="商品类型" min-width="100">
					<template v-slot="{ row }">
						<span v-if="row.product_type==0">普通商品</span>
						<span v-if="row.product_type==1">卡密商品</span>
						<span v-if="row.product_type==3">虚拟商品</span>
					</template>
				</vxe-column>
				<vxe-column field="cate_name" title="商品分类" min-width="150">
					<template v-slot="{ row }">
						<Tooltip max-width="200" placement="bottom">
							<span class="line2">{{row.cate_name}}</span>
							<p slot="content">{{row.cate_name}}</p>
						</Tooltip>
					</template>
				</vxe-column>
				<vxe-column field="store_label" title="商品标签" min-width="150">
					<template v-slot="{ row }">
						<Tooltip max-width="500" placement="bottom">
							<span class="line2">{{row.store_label}}</span>
							<p slot="content">{{row.store_label}}</p>
						</Tooltip>
					</template>
				</vxe-column>
			</vxe-table>
			<vxe-pager border size="medium" :page-size="formValidate.limit" :current-page="formValidate.page" :total="total"
				:layouts="['PrevPage', 'JumpNumber', 'NextPage', 'FullJump', 'Total']" @page-change="pageChange">
			</vxe-pager>
		</div>
		<div class="footer" slot="footer">
			<Button type="primary" size="large" long @click="ok">提交</Button>
		</div>
	</div>
</template>

<script>
	import {
		mapState
	} from "vuex";
	import {
		cascaderListApi,
		changeListApi,
		allLabelApi
	} from "@/api/product";
	export default {
		name: "index",
		props: {
			goodsType: {
				type: Number,
				default: 0,
			},
			is_new: {
				type: String,
				default: "",
			},
			diy: {
				type: Boolean,
				default: false,
			},
			isdiy: {
				type: Boolean,
				default: false,
			},
			ischeckbox: {
				type: Boolean,
				default: false,
			},
			datas: {
				type: Object,
				default: function() {
					return {};
				},
			},
		},
		data() {
			return {
				labelSelect: [],
				cateIds: [],
				treeSelect: [],
				formValidate: {
					page: 1,
					limit: 10,
					cate_id: "",
					store_name: "",
					is_new: this.is_new,
					store_label_id: ""
				},
				total: 0,
				loading: false,
				grid: {
					xl: 10,
					lg: 10,
					md: 12,
					sm: 24,
					xs: 24,
				},
				tableList: [],
				currentid: 0,
				productRow: {},
				images: []
			};
		},
		computed: {
			...mapState("admin/layout", ["isMobile"]),
			labelWidth() {
				return this.isMobile ? undefined : 120;
			},
			labelPosition() {
				return this.isMobile ? "top" : "right";
			},
		},
		created() {},
		mounted() {
			this.goodsCategory();
			this.getList();
			this.getAllLabelApi();
		},
		methods: {
			getAllLabelApi() {
				allLabelApi().then(res => {
					this.labelSelect = res.data
				}).catch(err => {
					this.$Message.error(err.msg);
				})
			},
			// 商品分类；
			goodsCategory() {
				cascaderListApi(1)
					.then((res) => {
						this.treeSelect = res.data;
					})
					.catch((res) => {
						this.$Message.error(res.msg);
					});
			},
			pageChange({ currentPage, pageSize }) {
				this.formValidate.page = currentPage;
				this.formValidate.limit = pageSize;
				this.getList();
			},
			// 列表
			getList() {
				this.loading = true;
				if (this.goodsType) {
					this.formValidate.is_presale_product = 0;
					this.formValidate.is_vip_product = 0;
				}
				this.formValidate.cate_id = this.cateIds[this.cateIds.length - 1]
				changeListApi(this.formValidate)
					.then(async (res) => {
						let list = res.data.list;
						list.forEach(item=>{
							item.attrValue.forEach(j=>{
								j.store_name = j.suk,
								j.cate_name = item.cate_name,
								j.store_label = item.store_label
							})
						})
						this.tableList = list;
						this.total = res.data.count;
						this.loading = false;
					})
					.catch((res) => {
						this.loading = false;
						this.$Message.error(res.msg);
					});
			},
			ok() {
				 let selectRecords = this.$refs.xTree.getCheckboxRecords()
				let goodsattr = [];
				selectRecords.forEach(function(item) {
					if(item.hasOwnProperty('product_id')){
						goodsattr.push(item)
					}
				});
				if (goodsattr.length > 0) {
					this.$emit("getProductId", goodsattr);
				} else {
					this.$Message.warning("请先选择商品");
				}
			},
			treeSearchs(value) {
				this.cateIds = value;
				this.formValidate.page = 1;
				this.getList();
			},
			// 表格搜索
			userSearchs() {
				this.formValidate.page = 1;
				this.getList();
			},
			clear() {
				this.productRow.id = "";
				this.currentid = "";
			},
		},
	};
</script>

<style scoped lang="stylus">
	.vxeTable {
		/deep/.vxe-header--row th{
			background #F3F8FE;
		}
	}

	.footer {
		margin: 15px 0;
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

	.tabform {
		>>>.ivu-form-item {
			margin-bottom: 16px !important;
		}
	}

	.btn {
		margin-top: 20px;
		float: right;
	}

	.goodList {
		>>>table {
			width: 100% !important;
		}
	}
</style>
