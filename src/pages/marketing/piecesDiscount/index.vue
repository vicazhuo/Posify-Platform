<template>
    <div>
        <Card :bordered="false" dis-hover class="ivu-mt" :padding= "0">
            <div class="new_card_pd">
			<Form
			  ref="discountFrom"
			  inline
			  :model="discountFrom"
			  :label-width="labelWidth"
			  :label-position="labelPosition"
			  @submit.native.prevent
			>
			      <FormItem label="是否有效：">
			        <Select
			          v-model="discountFrom.status"
			          placeholder="请选择"
			          clearable
			          @on-change="discountSearchs"
					  style="width: 250px;"
			        >
			          <Option value="1">是</Option>
			          <Option value="0">否</Option>
			        </Select>
			      </FormItem>
					  <FormItem label="活动类型：">
					    <Select
					      v-model="discountFrom.n_piece_n_discount"
					      placeholder="请选择"
					      clearable
					      @on-change="discountSearchs"
						  style="width: 250px;"
					    >
					      <Option value="1">第2件半价</Option>
					      <Option value="2">买1送1</Option>
								<Option value="3">自定义优惠</Option>
					    </Select>
					  </FormItem>
					  <FormItem label="活动名称：">
					    <Input
					      v-model="discountFrom.name"
					      placeholder="请输入活动名称"
					      @on-search="discountSearchs"
						  style="width: 250px;margin-right:14px"
					    ></Input>
						<Button type="primary" 	@click="discountSearchs()">查询</Button>
					  </FormItem>
			</Form>
			</div>
			</Card>
			<Card :bordered="false" dis-hover class="ivu-mt">
				<Button type="primary" @click="add">添加第N件N折</Button>
            <Table :columns="columns1" :data="list" ref="table" class="ivu-mt"
                   :loading="loading" highlight-row
                   no-userFrom-text="暂无数据"
                   no-filtered-userFrom-text="暂无筛选结果">
                <template slot-scope="{ row, index }" slot="icons">
                    <viewer>
                        <div class="tabBox_img">
                            <img v-lazy="row.icon">
                        </div>
                    </viewer>
                </template>
								<template slot-scope="{ row, index }" slot="n_piece_n_discount">
									<div v-if="row.n_piece_n_discount == 1">第2件半价</div>
									<div v-if="row.n_piece_n_discount == 2">买1送1</div>
									<div v-if="row.n_piece_n_discount == 3">自定义优惠</div>
								</template>
								<template slot-scope="{ row, index }" slot="status">
									<i-switch v-model="row.status" :true-value="1" :false-value="0" size="large" @on-change="onchangeIsShow(row)">
									  <span slot="open">开启</span>
									  <span slot="close">关闭</span>
									</i-switch>
								</template>	
                <template slot-scope="{ row, index }" slot="action">
                    <a @click="edit(row.id)">编辑</a>
                    <Divider type="vertical" />
                    <a @click="del(row,'删除第N件N折活动',index)">删除</a>
                </template>
            </Table>
            <div class="acea-row row-right page">
                <Page :total="total" show-elevator show-total @on-change="pageChange"
                      :page-size="discountFrom.limit"/>
            </div>
        </Card>
    </div>
</template>

<script>
    import { mapState } from 'vuex';
    import { discountList, discountsetStatus } from '@/api/marketing';
    export default {
        name: "piecesDiscount",
        data() {
            return {
                grid: {
                    xl: 7,
                    lg: 7,
                    md: 12,
                    sm: 24,
                    xs: 24
                },
                loading: false,
                columns1: [
                    {
                        title: 'ID',
                        key: 'id',
                        width: 80
                    },
                    {
                        title: '活动名称',
                        key: 'name',
                        minWidth: 100
                    },
										{
												title: '活动类型',
												slot: 'n_piece_n_discount',
												width: 100
										},
										{
										    title: '支付订单',
										    key: 'sum_order',
										    minWidth: 100
										},
										{
												title: '参与客户',
												key: 'sum_user',
												width: 100
										},
										{
												title: '实付金额',
												key: 'sum_pay_price',
												minWidth: 100
										},
										{
												title: '是否开启',
												slot: 'status',
												minWidth: 100
										},
                    {
                        title: '操作',
                        slot: 'action',
                        fixed: 'right',
                        minWidth: 120
                    }
                ],
                discountFrom: {
                    page: 1,
                    limit: 15,
					          name:'',
										status:'',
										n_piece_n_discount:''
                },
                list: [],
                total:0
            }
        },
        computed: {
            ...mapState('admin/layout', [
                'isMobile'
            ]),
            labelWidth () {
                return this.isMobile ? undefined : 96;
            },
            labelPosition () {
                return this.isMobile ? 'top' : 'right';
            }
        },
        created () {
            this.getList();
        },
        methods:{
					  // 删除
					  del(row, tit, num) {
					    let delfromData = {
					      title: tit,
					      num: num,
					      url: `marketing/promotions/del/${row.id}`,
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
					  onchangeIsShow (row) {
							discountsetStatus(row.id,row.status).then(res=>{
								this.$Message.success(res.msg);
							}).catch(err=>{
								this.$Message.error(err.msg);
							})
						},
						// 添加
						add () {
						    this.$router.push({ path: "/admin/marketing/discount/add_pieces/" + 0 });
						},
						discountSearchs(){
							this.discountFrom.page = 1;
							this.list = [];
							this.getList();
						},
            // 单位列表
            getList () {
                this.loading = true;
                discountList(2,this.discountFrom).then(res => {
                    let data = res.data;
                    this.list = data.list;
                    this.total = data.count;
                    this.loading = false;
                }).catch(err => {
                    this.loading = false;
                    this.$Message.error(err.msg);
                })
            },
            pageChange (index) {
                this.discountFrom.page = index;
                this.getList();
            },
			// 搜索
            discountSearchs () {
                this.discountFrom.page = 1;
                this.getList();
            },
            //修改
            edit(id){
							  this.$router.push({ path: "/admin/marketing/discount/add_pieces/" + id });
            }
        }
    }
</script>

<style scoped lang="stylus">

</style>